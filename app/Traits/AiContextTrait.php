<?php

namespace App\Traits;

use App\Models\AgentService;
use App\Models\Transaction;
use App\Models\AiChat;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\Report;
use App\Models\VirtualAccount;
use Illuminate\Support\Facades\Log;

trait AiContextTrait
{
    /**
     * Fetch the full database context for the authenticated user.
     */
    public function fetchFullUserContext($reference = null)
    {
        $user = auth()->user();
        if (!$user) return "No authenticated user session found.";

        $userId = $user->id;
        $userName = $user->first_name . ' ' . $user->last_name;

        // 1. Specific Record Context (if ref provided)
        $recordContext = $this->fetchSpecificRecordContext($reference, $userId);

        // 2. Financial Context
        $financialContext = $this->fetchFinancialContext($user);

        // 3. Virtual Accounts Context
        $vaContext = $this->fetchVirtualAccountsContext($userId);

        // 4. Reports Context
        $reportContext = $this->fetchReportsContext($userId);

        // 5. Recent Activity
        $recentActivity = $this->fetchRecentActivity($userId);

        // 6. Service Catalogue (Conditional Pricing)
        $serviceContext = $this->fetchServiceCatalogue($user);

        // 7. Recent Interactions (Unified Memory)
        $interactionContext = $this->fetchRecentInteractionsContext($userId);

        return "--- AUTHORIZED USER CONTEXT (ID: $userId | NAME: $userName) ---\n\n" . 
               "CURRENT FOCUS RECORD:\n$recordContext\n\n" .
               "$financialContext\n\n" .
               "$vaContext\n\n" .
               "$reportContext\n\n" .
               "SERVICE CATALOGUE & PRICING:\n$serviceContext\n\n" .
               "RECENT UNIFIED INTERACTIONS:\n$interactionContext\n\n" .
               "RECENT ACTIVITY (LAST 5 DAYS):\n$recentActivity\n\n" .
               "--- END OF AUTHORIZED CONTEXT ---";
    }

    private function fetchSpecificRecordContext($reference, $userId)
    {
        if (!$reference) return "No specific record reference provided.";

        $service = AgentService::where('reference', $reference)->where('user_id', $userId)->first();
        if ($service) {
            return "Type: Agent Service ({$service->service_type})\nName: {$service->service_name}\nStatus: {$service->status}\nAmount: ₦" . number_format($service->amount, 2) . "\nRef: {$service->reference}\nDescription: {$service->description}";
        }

        $transaction = Transaction::where('transaction_ref', $reference)->where('user_id', $userId)->first();
        if ($transaction) {
            return "Type: Transaction\nDescription: {$transaction->description}\nStatus: {$transaction->status}\nAmount: ₦" . number_format($transaction->amount, 2) . "\nPayer: {$transaction->payer_name}\nRef: {$transaction->transaction_ref}";
        }

        $report = Report::where('ref', $reference)->where('user_id', $userId)->first();
        if ($report) {
            return "Type: User Report/Complaint ({$report->type})\nDescription: {$report->description}\nStatus: {$report->status}\nRef: {$report->ref}\nAmount: ₦" . number_format($report->amount, 2) . "\nDate: " . $report->created_at->toDayDateTimeString();
        }

        return "Reference $reference not found in your records.";
    }

    private function fetchFinancialContext($user)
    {
        $wallet = $user->wallet;
        $balance = $wallet ? $wallet->balance : 0;
        $bonus = $wallet ? $wallet->bonus : 0;
        
        return "FINANCIAL SUMMARY:\n" .
               "- Available Balance: ₦" . number_format($balance, 2) . "\n" .
               "- Account Bonus: ₦" . number_format($bonus, 2) . "\n" .
               "- Referral Earnings: ₦" . number_format($user->referral_bonus ?? 0, 2) . "\n" .
               "- Referral Code: " . ($user->referral_code ?? 'N/A') . "\n" .
               "PROMOTION: Users earn ₦500 bonus for each successfully verified referral.";
    }

    private function fetchVirtualAccountsContext($userId)
    {
        $accounts = VirtualAccount::where('user_id', $userId)->get();
        if ($accounts->isEmpty()) return "VIRTUAL ACCOUNTS: None assigned.";
        
        $output = "VIRTUAL ACCOUNTS (For Deposits):\n";
        foreach ($accounts as $acc) {
            $output .= "- {$acc->bankName}: {$acc->accountNo} ({$acc->accountName}) [{$acc->status}]\n";
        }
        return $output;
    }

    private function fetchReportsContext($userId)
    {
        $reports = Report::where('user_id', $userId)->latest()->take(10)->get();
        if ($reports->isEmpty()) return "COMPLAINTS/REPORTS: None found.";

        $output = "RECENT COMPLAINTS/REPORTS:\n";
        foreach ($reports as $r) {
            $output .= "- [{$r->created_at->format('Y-m-d')}] {$r->type}: {$r->description} [{$r->status}]\n";
        }
        return $output;
    }

    private function fetchServiceCatalogue($user)
    {
        $role = $user->role ?? 'user';
        $services = Service::where('is_active', true)->with(['fields' => function($q) {
            $q->where('is_active', true);
        }])->get();

        if ($services->isEmpty()) return "SERVICES: No active services available.";

        $output = "";
        foreach ($services as $s) {
            $output .= "SERVICE: {$s->name}\n";
            foreach ($s->fields as $f) {
                $price = $f->getPriceForUserType($role);
                $output .= " - {$f->field_name}: ₦" . number_format($price, 2) . " (Ref: {$f->field_code})\n";
            }
            $output .= "\n";
        }
        return $output;
    }

    private function fetchRecentInteractionsContext($userId)
    {
        $chats = AiChat::where('user_id', $userId)
            ->latest()
            ->take(15)
            ->get(['role', 'content', 'type', 'created_at']);

        if ($chats->isEmpty()) return "INTERACTIONS: No previous interactions found.";

        $output = "RECENT USER INTERACTIONS (Last 15 messages across all channels):\n";
        foreach ($chats->reverse() as $chat) {
            $time = $chat->created_at->format('m-d H:i');
            $type = strtoupper($chat->type);
            $role = strtoupper($chat->role);
            $output .= "[$time] $type | $role: {$chat->content}\n";
        }
        return $output;
    }

    private function fetchRecentActivity($userId)
    {
        $fiveDaysAgo = now()->subDays(5);
        $transactions = Transaction::where('user_id', $userId)->where('created_at', '>=', $fiveDaysAgo)->latest()->take(20)->get();
        $services = AgentService::where('user_id', $userId)->where('created_at', '>=', $fiveDaysAgo)->latest()->take(20)->get();

        if ($transactions->isEmpty() && $services->isEmpty()) return "No activity in the last 5 days.";

        $output = "TRANSACTIONS:\n";
        foreach ($transactions as $tx) {
            $output .= "- [{$tx->created_at->format('m-d h:i')}] {$tx->description}: ₦" . number_format($tx->amount, 2) . " [{$tx->status}]\n";
        }

        $output .= "\nAGENT SERVICES:\n";
        foreach ($services as $s) {
            $output .= "- [{$s->created_at->format('m-d h:i')}] {$s->service_name}: ₦" . number_format($s->amount, 2) . " [{$s->status}]\n";
        }

        return $output;
    }

    /**
     * Get the universal system prompt for Arewa Smart AI.
     */
    public function getAiSystemPrompt($action, $context = '', $userName = 'Valued User')
    {
        $today = now()->format('l, d F Y');
        $userId = auth()->id();
        
        $rules = <<<TEXT
AREWA SMART CORE RULES:
1. Nature: We are a premium digital service intermediary (NIN, BVN, Utilities, Gift cards and Agency Banking).
2. Refunds: Strictly for system errors by Arewa Smart. User errors or 3rd party API failures are non-refundable.
3. Tone: Expert, warm, highly professional. Use Nigerian business etiquette (respectful and direct). Be concise.
4. Security: You have VIEW-ONLY access. You cannot update records or process payments.
5. Pricing Rule: You have access to service prices in the context. ONLY mention prices if the user specifically asks for them.
6. Usage Guide:
   - NIN Validation: Requires 11-digit NIN. Non-refundable.
   - IPE Clearance: Requires Tracking ID (min 15 chars). Includes auto-refund if API fails.
   - CAC Registration: Requires business name, type, and document uploads (NIN, Signature, Passport).
   - Status: Users can manually click the "Check Status" icon in history to refresh results.
   - Response: Your answers MUST be short (1-3 sentences max) and understandable.
7. Personalization: You have access to RECENT UNIFIED INTERACTIONS. Use this history to remember user concerns, previous requests, and patterns across Support, Global, and Comments.
TEXT;

        $base = <<<TEXT
You are 'Arewa Smart AI Guide', the official premium virtual assistant for Arewa Smart Idea Ltd.

YOUR GUIDELINES:
- BE AUTHENTIC: Use the provided context to give personalized answers. Use the interaction history to show you "remember" the user.
- BE CONCISE: Use a short format. Avoid long statements. Be direct.
- BE PROFESSIONAL: Start with "Dear User $userName," unless it's a quick follow-up.
- PRIVACY: Only discuss data belonging to User ID $userId.

CURRENT DATE: $today

$rules

$context
TEXT;

        if ($action === 'summarize') {
            return $base . "\n\nTASK: Summarize the official feedback for this transaction and explain it simply. Advise on next steps.";
        }
        
        if ($action === 'support') {
            return $base . "\n\nTASK: You are providing formal technical support. Be empathetic but stick to platform terms. Help the user resolve their issue or explain the status of their request.";
        }

        if ($action === 'chat') {
            return $base . "\n\nTASK: You are a Dashboard Assistant. Help the user navigate the platform, explain features (NIN, BVN, Wallet), or answer questions about their account status and history.";
        }

        return $base . "\n\nTASK: Answer the user's question accurately using the provided context.";
    }
}
