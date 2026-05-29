<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BonusHistory;
use App\Models\Wallet;
use App\Models\User;

class ReferralController extends Controller
{
    /**
     * Display the referral dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;

        // 1. Process Pending Bonuses
        $pendingBonuses = BonusHistory::where('user_id', $userId)
            ->where('status', 'pending')
            ->get();

        foreach ($pendingBonuses as $bonus) {
            // Count qualified transactions by the referred user (debit >= 1000 or credit >= 2000)
            $transactionCount = \App\Models\Transaction::where('user_id', $bonus->referred_user_id)
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

            if ($transactionCount >= 5) {
                \DB::beginTransaction();
                try {
                    // Update bonus status
                    $bonus->status = 'success';
                    $bonus->save();

                    // Credit the referrer's wallet bonus
                    $wallet = Wallet::where('user_id', $userId)->first();
                    if ($wallet) {
                        $wallet->bonus = ($wallet->bonus ?? 0) + $bonus->amount;
                        $wallet->save();
                    }

                    \DB::commit();
                } catch (\Exception $e) {
                    \DB::rollBack();
                    // Log error if needed
                }
            }
        }

        // Total pending invitations (where transactions < 5)
        $pendingCount = BonusHistory::where('user_id', $userId)
            ->where('type', 'referral')
            ->where('status', 'pending')
            ->count();

        // Total referral earnings (only successful/credited ones)
        $totalEarnings = BonusHistory::where('user_id', $userId)
            ->where('type', 'referral')
            ->where('status', 'success')
            ->sum('amount');

        // Current wallet and bonus balance
        $wallet = Wallet::where('user_id', $userId)->first();
        
        // Fetch all referral history with referred user details
        $bonusHistory = BonusHistory::where('user_id', $userId)
            ->with('referredUser')
            ->orderBy('id', 'desc')
            ->get();

        // For each bonus in history, attach the current qualified transaction count of the referred user
        foreach ($bonusHistory as $history) {
            $history->referred_user_transaction_count = \App\Models\Transaction::where('user_id', $history->referred_user_id)
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
        }

        $referralLink = config('app.url') . '/register?ref=' . $user->referral_code;

        $referralAmount = $user->referral_bonus > 0
            ? $user->referral_bonus
            : (\DB::table('referral_bonus')->value('bonus') ?? 500.00);

        return view('referral.index', compact(
            'user',
            'pendingCount',
            'totalEarnings',
            'wallet',
            'bonusHistory',
            'referralLink',
            'referralAmount'
        ));
    }

    /**
     * Update the user's custom referral code
     */
    public function updateReferralCode(Request $request)
    {
        $request->validate([
            'referral_code' => ['required', 'string', 'max:20', 'alpha_num'],
        ], [
            'referral_code.required' => 'The referral code is required.',
            'referral_code.max' => 'The referral code must not exceed 20 characters.',
            'referral_code.alpha_num' => 'The referral code must contain only letters and numbers.',
        ]);

        $user = Auth::user();
        $newCode = trim($request->referral_code);

        // Case-insensitive uniqueness check excluding current user
        $exists = User::whereRaw('LOWER(referral_code) = ?', [strtolower($newCode)])
            ->where('id', '!=', $user->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'username already use by another user');
        }

        $user->referral_code = $newCode;
        $user->save();

        return back()->with('success', 'Referral code updated successfully!');
    }

    /**
     * Check if a referral code is available
     */
    public function checkReferralCode(Request $request)
    {
        $code = trim($request->query('code'));

        if (empty($code)) {
            return response()->json(['available' => false, 'message' => 'Code cannot be empty.']);
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $code)) {
            return response()->json(['available' => false, 'message' => 'Alphanumeric characters only.']);
        }

        if (strlen($code) > 20) {
            return response()->json(['available' => false, 'message' => 'Maximum 20 characters.']);
        }

        $exists = User::whereRaw('LOWER(referral_code) = ?', [strtolower($code)])
            ->where('id', '!=', Auth::id())
            ->exists();

        if ($exists) {
            return response()->json(['available' => false, 'message' => 'username already use by another user']);
        }

        return response()->json(['available' => true, 'message' => 'Referral code is available!']);
    }
}
