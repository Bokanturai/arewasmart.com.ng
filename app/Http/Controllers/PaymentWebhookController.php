<?php

namespace App\Http\Controllers;

use App\Helpers\signatureHelper;
use App\Jobs\ProcessVatCharge;
use App\Mail\PaymentNotifyMail;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VirtualAccount;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class PaymentWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Log raw body first (helps debugging if payload parsing fails)
        Log::info('Palmpay RAW Webhook Body: '.$request->getContent());

        $payload = $request->all();
        Log::info('Palmpay webhook hit:', ['payload' => $payload]);

        // Verify the signature
        if (! $this->verifySignature($payload)) {
            Log::warning('Invalid webhook signature received', ['payload' => $payload]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        try {
            $this->processReservedAccountTransaction($payload);
            return response('success', 200)->header('Content-Type', 'text/plain');
        } catch (\Throwable $e) {
            Log::error('Error processing webhook: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'payload' => $payload
            ]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    private function verifySignature($data)
    {
        if (!isset($data['sign'])) {
            return false;
        }

        $sign = $data['sign'];

        $verifyResults = signatureHelper::verify_callback_signature($data, $sign, config('keys.public'));

        return $verifyResults === true;
    }


    
    private function processReservedAccountTransaction($payload)
    {
        if (isset($payload['orderId']) && ($payload['transType'] ?? null) == 41) {
            Log::info('[PAYOUT]:', ['payload' => $payload]);
            // $this->handlePayout($payload);
        } else {
            Log::info('[PAYIN]:', ['payload' => $payload]);

            $virtualAccountNo = $payload['virtualAccountNo'] ?? null;
            $orderNo          = $payload['orderNo'] ?? null;
            $amountPaid       = isset($payload['orderAmount']) ? $payload['orderAmount'] / 100 : 0;
            $payerBankName    = $payload['payerBankName'] ?? '';
            $payerAccountName = $payload['payerAccountName'] ?? '';
            $service_description = 'Your wallet has been credited with ₦' . number_format($amountPaid, 2);
            $orderStatus      = $payload['orderStatus'] ?? 0;

            if (! $virtualAccountNo || ! $orderNo) {
                Log::warning('Webhook missing accountNo or orderNo', ['payload' => $payload]);
                return;
            }

            $response = VirtualAccount::select('user_id')
                ->where('accountNo', $virtualAccountNo)
                ->first();

            if ($response) {
                $this->createTransactionForReservedAccount(
                    $response->user_id,
                    $orderNo,
                    $amountPaid,
                    $payerBankName,
                    $payerAccountName,
                    $service_description,
                    $orderStatus,
                    $payload
                );
            } else {
                Log::warning('Virtual account not found for accountNo: '.$virtualAccountNo, ['payload' => $payload]);
            }
        }
    }

    private function updateTransaction($orderNo, $amountPaid, $payerBankName, $payerAccountName, $service_description, $orderStatus, $userId, $payload)
    {
        $status = $orderStatus == 1 ? 'completed' : 'pending';

        $user = User::find($userId);
        $performedBy = $user ? $user->first_name . ' ' . $user->last_name : 'System';

        Transaction::where('transaction_ref', $orderNo)
            ->update([
                'description'         => $service_description,
                'amount'              => $amountPaid,
                'payer_name'          => $payerAccountName,
                'status'              => $status,
                'performed_by'        => $performedBy,
                'metadata'            => $payload,
                'updated_at'          => Carbon::now(),
            ]);

        Log::info('Transaction updated for '.$orderNo);
    }

    private function insertTransaction($userId, $orderNo, $amountPaid, $payerAccountName, $payerBankName, $service_description, $payload)
    {
        $user = User::find($userId);
        $performedBy = $user ? $user->first_name . ' ' . $user->last_name : 'System';

        Transaction::create([
            'user_id'        => $userId,
            'payer_name'     => $payerAccountName,
            'transaction_ref'=> $orderNo,
            'type'           => 'credit', // Lowercase as per migration enum
            'description'    => $service_description,
            'amount'         => $amountPaid,
            'status'         => 'completed',
            'performed_by'   => $performedBy,
            'metadata'       => $payload,
            'created_at'     => Carbon::now(),
            'updated_at'     => Carbon::now(),
        ]);

        Log::info('New transaction inserted for '.$orderNo);
    }

    private function createTransactionForReservedAccount($userId, $orderNo, $amountPaid, $payerBankName, $payerAccountName, $service_description, $orderStatus, $payload)
    {
        try {
            DB::transaction(function () use ($userId, $orderNo, $amountPaid, $payerBankName, $payerAccountName, $service_description, $orderStatus, $payload) {
                // 1. Idempotency Check (Pessimistic Lock on orderNo)
                $existingTransaction = Transaction::where('transaction_ref', $orderNo)->lockForUpdate()->first();

                if ($existingTransaction) {
                    Log::info("Transaction $orderNo already exists. Updating status only.");
                    $this->updateTransaction($orderNo, $amountPaid, $payerBankName, $payerAccountName, $service_description, $orderStatus, $userId, $payload);
                    return;
                }

                // 2. Rate Limit Check (3-minute rule)
                // Check if any credit transaction was completed for this user in the last 3 minutes
                $recentCredit = Transaction::where('user_id', $userId)
                    ->where('type', 'credit')
                    ->where('status', 'completed')
                    ->where('created_at', '>=', now()->subMinutes(3))
                    ->exists();

                if ($recentCredit) {
                    Log::warning("Rate limit hit for user $userId. Skipping order $orderNo (Only one funding per 3 minutes allowed).");
                    return;
                }

                // 3. Process Funding (Only if orderStatus is success)
                if ($orderStatus != 1) {
                    Log::warning("Order $orderNo received with non-success status: $orderStatus. Skipping credit.");
                    $this->insertTransaction($userId, $orderNo, $amountPaid, $payerAccountName, $payerBankName, $service_description, $payload);
                    return;
                }

                $this->insertTransaction($userId, $orderNo, $amountPaid, $payerAccountName, $payerBankName, $service_description, $payload);
                $this->updateWalletBalance($userId, $amountPaid);

                // 4. Levy Charge Logic (10,000 threshold)
                if ($amountPaid >= 10000) {
                    $chargeAmount = 50;
                    $chargeDesc = 'transaction levy charge';
                    $chargeRef = 'CHG-' . strtoupper(Str::random(10));
                    
                    $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();
                    if ($wallet) {
                        $wallet->decrement('balance', $chargeAmount);
                        
                        Transaction::create([
                            'user_id' => $userId,
                            'transaction_ref' => $chargeRef,
                            'type' => 'debit',
                            'amount' => $chargeAmount,
                            'description' => $chargeDesc,
                            'status' => 'completed',
                            'performed_by' => 'System',
                            'metadata' => ['related_transaction' => $orderNo, 'type' => 'levy_charge'],
                        ]);
                    }
                    
                    ProcessVatCharge::dispatch($userId)->delay(now()->addMinute());
                }
            });

            // 5. Send Notification (outside transaction to avoid delays)
            $this->sendNotificationAndEmail($userId, $amountPaid, $orderNo, $payerBankName, 'Topup');

        } catch (\Exception $e) {
            Log::error("Failed to process reserved account transaction $orderNo: " . $e->getMessage());
            throw $e;
        }
    }

    private function updateWalletBalance($userId, $amountPaid)
    {
        $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();
        if ($wallet) {
            $wallet->increment('balance', $amountPaid);

            Log::info('Wallet updated for user '.$userId.' with amount '.$amountPaid);
        } else {
            Log::warning('Wallet not found for user ID: '.$userId);
        }
    }


    private function sendNotificationAndEmail($userId, $amountPaid, $orderNo, $bankName, $type)
    {
        $user = User::find($userId);
        if ($user && $user->email) {
            $mail_data = [
                'type'     => $type,
                'amount'   => number_format($amountPaid, 2),
                'ref'      => $orderNo,
                'bankName' => $bankName,
            ];

            try {
                Mail::to($user->email)->send(new PaymentNotifyMail($mail_data));
                Log::info('Payment notification sent to '.$user->email.' for transaction '.$orderNo);
            } catch (TransportExceptionInterface $e) {
                Log::error('Error sending email for transaction '.$orderNo.': '.$e->getMessage());
            }
        } else {
            Log::warning('No email found for user '.$userId.' while sending notification');
        }
    }
}
