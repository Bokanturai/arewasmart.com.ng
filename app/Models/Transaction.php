<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model


{
    protected $table = 'transactions'; 
    
    protected $fillable = [
        'transaction_ref', 
        'payer_name',
        'referenceId',
        'user_id', 
        'amount', 
        'fee',
        'net_amount',
        'description',
        'type', 
        'status', 
        'metadata', 
        'performed_by',
        'approved_by',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the standardized status name.
     */
    public function getNormalizedStatusAttribute()
    {
        return \App\Helpers\StatusHelper::normalize($this->status);
    }

    /**
     * Get the Bootstrap color for the status.
     */
    public function getStatusColorAttribute()
    {
        return \App\Helpers\StatusHelper::color($this->status);
    }

    /**
     * Bootstrap the model and its events.
     */
    protected static function booted()
    {
        static::saved(function ($transaction) {
            // Only trigger if status is completed
            if ($transaction->status === 'completed') {
                // Find any pending referral bonus where this user is the referred user
                $pendingReferrals = \App\Models\BonusHistory::where('referred_user_id', $transaction->user_id)
                    ->where('status', 'pending')
                    ->where('type', 'referral')
                    ->get();

                foreach ($pendingReferrals as $bonus) {
                    // Count qualified transactions for the referred user (debit >= 1000 or credit >= 2000)
                    $completedCount = \App\Models\Transaction::where('user_id', $transaction->user_id)
                        ->where('status', 'completed')
                        ->where(function ($query) {
                            $query->where(function ($q) {
                                $q->where('type', 'debit')
                                  ->where('amount', '>=', 1000);
                            })->orWhere(function ($q) {
                                $q->where('type', 'credit')
                                  ->where('amount', '>=', 2000);
                            });
                        })
                        ->count();

                    if ($completedCount >= 5) {
                        \Illuminate\Support\Facades\DB::beginTransaction();
                        try {
                            // Update status of the bonus history to success
                            $bonus->status = 'success';
                            $bonus->save();

                            // Credit the referrer's wallet's bonus column with locking
                            $wallet = \App\Models\Wallet::where('user_id', $bonus->user_id)->lockForUpdate()->first();
                            if ($wallet) {
                                $wallet->bonus = ($wallet->bonus ?? 0) + $bonus->amount;
                                $wallet->save();
                            }

                            \Illuminate\Support\Facades\DB::commit();
                            \Illuminate\Support\Facades\Log::info("Referral milestone reached for user ID: {$transaction->user_id}. Referrer ID: {$bonus->user_id} successfully credited with ₦{$bonus->amount} bonus.");
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\DB::rollBack();
                            \Illuminate\Support\Facades\Log::error("Failed to credit referral bonus for User ID {$bonus->user_id}: " . $e->getMessage());
                        }
                    }
                }
            }
        });
    }
}
