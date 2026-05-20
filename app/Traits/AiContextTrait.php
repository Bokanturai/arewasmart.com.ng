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

        // 6b. Data Plans & Reliability
        $dataPlansContext = $this->fetchDataPlansContext();

        // 7. Recent Interactions (Unified Memory)
        $interactionContext = $this->fetchRecentInteractionsContext($userId);

        return "--- AUTHORIZED USER CONTEXT (ID: $userId | NAME: $userName) ---\n\n" .
               "CURRENT FOCUS RECORD:\n$recordContext\n\n" .
               "$financialContext\n\n" .
               "$vaContext\n\n" .
               "$reportContext\n\n" .
               "SERVICE CATALOGUE & PRICING:\n$serviceContext\n\n" .
               "DATA PLANS RELIABILITY:\n$dataPlansContext\n\n" .
               "RECENT UNIFIED INTERACTIONS:\n$interactionContext\n\n" .
               "RECENT ACTIVITY (LAST 5 DAYS):\n$recentActivity\n\n" .
               "--- END OF AUTHORIZED CONTEXT ---";
    }

    /**
     * Decode and normalise a transaction's metadata JSON into a clean array.
     * Returns an empty array if metadata is absent or unparseable.
     */
    private function resolveTransactionMeta(Transaction $tx): array
    {
        $raw = $tx->metadata;
        if (!$raw) return [];
        $decoded = is_array($raw) ? $raw : json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Build a rich detail string for one transaction.
     * Top-level transaction fields are always included.
     * Metadata fields are added ONLY when the information is not already
     * present on the main record — metadata is strictly a fallback source.
     */
    private function buildTransactionContext(Transaction $tx): string
    {
        $meta = $this->resolveTransactionMeta($tx);

        $lines = [
            "Ref:         {$tx->transaction_ref}",
            "Amount:      ₦" . number_format($tx->amount, 2),
            "Status:      {$tx->status}",
            "Date:        " . $tx->created_at->format('d M Y, h:i A'),
            "Description: {$tx->description}",
        ];

        // Net amount / discount (from DB column first)
        $netAmount = $tx->net_amount ?? null;
        if ($netAmount && $netAmount != $tx->amount) {
            $discount = $tx->amount - $netAmount;
            $lines[] = "Net Paid:    ₦" . number_format($netAmount, 2) . " (saved ₦" . number_format($discount, 2) . ")";
        }

        // ── Metadata fallback — only append if not already in description ──

        // Phone / recipient number
        $phone = $meta['phone'] ?? $meta['phone_number'] ?? $meta['mobileno'] ?? null;
        if ($phone && !str_contains($tx->description ?? '', $phone)) {
            $lines[] = "Phone:       {$phone}";
        }

        // Network / provider / exam name
        $network = $meta['network'] ?? $meta['service'] ?? $meta['exam_name'] ?? $meta['service_type'] ?? null;
        if ($network && !str_contains(strtolower($tx->description ?? ''), strtolower($network))) {
            $lines[] = "Network:     " . strtoupper($network);
        }

        // Receiver info (P2P transfer / bank withdrawal)
        $receiverName = $meta['account_name'] ?? $meta['receiver_name'] ?? null;
        $receiverAcct = $meta['account_no'] ?? $meta['receiver_wallet'] ?? $meta['account_number'] ?? null;
        $bankName     = $meta['bankName'] ?? $meta['bank_name'] ?? null;
        if ($receiverName) $lines[] = "Receiver:    {$receiverName}";
        if ($receiverAcct) $lines[] = "Account:     {$receiverAcct}";
        if ($bankName)     $lines[] = "Bank:        {$bankName}";

        // Educational PIN / token
        $pin = $meta['purchased_code'] ?? $meta['purchased_pin'] ?? $meta['token'] ?? null;
        if ($pin && $pin !== 'Check History') {
            $lines[] = "PIN/Token:   {$pin}";
        }

        // Serial number (WAEC / NECO / NABTED / JAMB)
        $serial = $meta['serial_number'] ?? $meta['serial'] ?? null;
        if ($serial) {
            $lines[] = "Serial No:   {$serial}";
        }

        // Profile ID (JAMB)
        $profileId = $meta['profile_id'] ?? null;
        if ($profileId) {
            $lines[] = "Profile ID:  {$profileId}";
        }

        // Meter number (electricity)
        $meter = $meta['meter_number'] ?? $meta['billersCode'] ?? null;
        if ($meter) {
            $lines[] = "Meter No:    {$meter}";
        }

        // Electricity prepaid token (different from edu pin)
        $elecToken = $meta['token'] ?? null;
        if ($elecToken && !$pin && $meter) {
            $lines[] = "Elec Token:  {$elecToken}";
        }

        // IUC number (cable TV)
        $iuc = $meta['iuc'] ?? $meta['iuc_number'] ?? null;
        if ($iuc) {
            $lines[] = "IUC Number:  {$iuc}";
        }

        return implode("\n", $lines);
    }

    private function fetchSpecificRecordContext($reference, $userId)
    {
        if (!$reference) return "No specific record reference provided.";

        $service = AgentService::where('reference', $reference)->where('user_id', $userId)->first();
        if ($service) {
            return "Type: Agent Service ({$service->service_type})\nName: {$service->service_name}\nStatus: {$service->status}\nAmount: ₦" . number_format($service->amount, 2) . "\nRef: {$service->reference}\nDescription: {$service->description}";
        }

        // For a specific transaction, always include full metadata-enriched detail
        $transaction = Transaction::where('transaction_ref', $reference)->where('user_id', $userId)->first();
        if ($transaction) {
            return "Type: Transaction\n" . $this->buildTransactionContext($transaction);
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
        $services = Service::where('is_active', true)->with(['fields' => function ($q) {
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

    private function fetchDataPlansContext()
    {
        try {
            $sme    = \Illuminate\Support\Facades\DB::table('sme_datas')->where('status', 'enabled')->get();
            $normal = \Illuminate\Support\Facades\DB::table('data_variations')->where('status', 'enabled')->get();

            $output = "AVAILABLE DATA PLANS (Reliability Info):\n";
            if ($sme->isEmpty() && $normal->isEmpty()) return "DATA PLANS: No enabled plans found in database.";

            foreach ($sme as $p) {
                $reliability = $p->failure_count == 0 ? "Excellent" : ($p->failure_count < 3 ? "Good" : "Unstable");
                $output .= "- SME [Network: {$p->network}]: {$p->size} (Ref ID: {$p->data_id}, Type: {$p->plan_type}) [Reliability: {$reliability}]\n";
            }
            foreach ($normal as $p) {
                $network = explode('-', $p->service_id)[0] ?? 'unknown';
                $output .= "- Normal [Network: {$network}]: {$p->name} (Ref Code: {$p->variation_code}) [Reliability: Excellent]\n";
            }
            return $output;
        } catch (\Exception $e) {
            return "DATA PLANS: Error fetching plans.";
        }
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
            $time  = $chat->created_at->format('m-d H:i');
            $type  = strtoupper($chat->type);
            $role  = strtoupper($chat->role);
            $output .= "[$time] $type | $role: {$chat->content}\n";
        }
        return $output;
    }

    private function fetchRecentActivity($userId)
    {
        $fiveDaysAgo  = now()->subDays(5);
        $transactions = Transaction::where('user_id', $userId)
            ->where('created_at', '>=', $fiveDaysAgo)
            ->latest()
            ->take(20)
            ->get();
        $services = AgentService::where('user_id', $userId)
            ->where('created_at', '>=', $fiveDaysAgo)
            ->latest()
            ->take(20)
            ->get();

        if ($transactions->isEmpty() && $services->isEmpty()) return "No activity in the last 5 days.";

        $output = "TRANSACTIONS:\n";
        foreach ($transactions as $tx) {
            // Core line — always from the main transaction record
            $line = "- [{$tx->created_at->format('m-d h:i')}] {$tx->description}: ₦" . number_format($tx->amount, 2) . " [{$tx->status}]";

            // ── Metadata fallback: append extra details ONLY when not in description ──
            $meta   = $this->resolveTransactionMeta($tx);
            $extras = [];

            $phone = $meta['phone'] ?? $meta['phone_number'] ?? $meta['mobileno'] ?? null;
            if ($phone && !str_contains($tx->description ?? '', $phone)) {
                $extras[] = "Phone: {$phone}";
            }

            $network = $meta['network'] ?? $meta['service'] ?? $meta['exam_name'] ?? null;
            if ($network && !str_contains(strtolower($tx->description ?? ''), strtolower($network))) {
                $extras[] = "Network: " . strtoupper($network);
            }

            $pin = $meta['purchased_code'] ?? $meta['purchased_pin'] ?? $meta['token'] ?? null;
            if ($pin && $pin !== 'Check History') {
                $extras[] = "PIN: {$pin}";
            }

            $serial = $meta['serial_number'] ?? $meta['serial'] ?? null;
            if ($serial) {
                $extras[] = "Serial: {$serial}";
            }

            $meter = $meta['meter_number'] ?? $meta['billersCode'] ?? null;
            if ($meter && !$pin) {
                $extras[] = "Meter: {$meter}";
                $elecToken = $meta['token'] ?? null;
                if ($elecToken) $extras[] = "Token: {$elecToken}";
            }

            $receiverName = $meta['account_name'] ?? $meta['receiver_name'] ?? null;
            $bankName     = $meta['bankName'] ?? $meta['bank_name'] ?? null;
            if ($receiverName) $extras[] = "To: {$receiverName}";
            if ($bankName)     $extras[] = "Bank: {$bankName}";

            $netAmount = $tx->net_amount ?? null;
            if ($netAmount && $netAmount != $tx->amount) {
                $extras[] = "Paid: ₦" . number_format($netAmount, 2);
            }

            if (!empty($extras)) {
                $line .= " [" . implode(' | ', $extras) . "]";
            }

            $output .= $line . "\n";
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
        $today  = now()->format('l, d F Y');
        $userId = auth()->id();

        $rules = <<<TEXT
AREWA SMART CORE RULES:
1. Nature: We are a premium digital service intermediary (NIN, BVN, Utilities, Gift cards and Agency Banking).
2. Refunds: Strictly for system errors by Arewa Smart. User errors or 3rd party API failures are non-refundable.
3. Tone: Expert, warm, highly professional. Use Nigerian business etiquette (respectful and direct). Be concise.
4. Security: You can propose transactions but NOT execute them. The user must confirm with their PIN. Never ask for the PIN.
5. Pricing Rule: You have access to service prices in the context. ONLY mention prices if the user specifically asks for them.
6. Usage Guide:
   - NIN Validation: Requires 11-digit NIN. Non-refundable.
   - IPE Clearance: Requires Tracking ID (min 15 chars). Includes auto-refund if API fails.
   - CAC Registration: Requires business name, type, and document uploads (NIN, Signature, Passport).
   - Status: Users can manually click the "Check Status" icon in history to refresh results.
   - Response: Your answers MUST be short (1-3 sentences max) and understandable.
7. Action Proposals:
   - You can initiate Airtime, Data, P2P Transfers, or Virtual Account creation by outputting a JSON block at the very end of your response.
   - For P2P: {"action": "p2p_transfer", "params": {"wallet_id": "...", "amount": 0, "description": "..."}}
   - For Airtime: {"action": "airtime", "params": {"phone_number": "...", "network": "...", "amount": 0}}
   - For Normal Data: {"action": "data_purchase", "params": {"data_type": "normal", "phone_number": "...", "network": "...", "bundle_code": "...", "plan_name": "...", "amount": 0}}
   - For SME Data: {"action": "data_purchase", "params": {"data_type": "sme", "phone_number": "...", "network": "...", "plan_id": "...", "plan_name": "...", "plan_type": "...", "amount": 0}}
   - For Virtual Account: {"action": "virtual_account", "params": {}}
   - Proactive Account Creation: If the user asks about funding their wallet or account details but the context shows "VIRTUAL ACCOUNTS: None assigned", you MUST explain that they don't have one yet and output the `virtual_account` action JSON to help them create it.
   - Data Plan Preference: Always prioritize and suggest "Normal" data plans as the best option. ONLY offer or show "SME" data plans if the user specifically mentions "SME".
   - Data Reliability Advice: Always advise the user to buy data plans that have a low failure record (marked as "Excellent" or "Good" reliability in context). If a plan is "Unstable", warn them or suggest a better alternative.
   - Network MUST be one of: mtn, airtel, glo, 9mobile. (Use MTN, AIRTEL etc for SME).
   - Phone Validation: Always ensure the phone number provided is exactly 11 digits before initiating any purchase.
   - Balance Check: You have access to the user's wallet balance. If they ask for a plan that costs more than their balance, inform them and suggest a cheaper plan or tell them to fund their account. Output the `virtual_account` action if they need to fund and don't have an account.
   - Tell the user: "I can initiate this for you. Please confirm the details to proceed." (Omit PIN mention for virtual accounts).
8. Personalization: You have access to RECENT UNIFIED INTERACTIONS. Use this history to remember user concerns, previous requests, and patterns across Support, Global, and Comments.
9. Transaction Details: Each transaction entry in RECENT ACTIVITY may include additional details in square brackets (Phone, Network, PIN, Serial, Meter, Token, etc.) sourced from the transaction metadata. When a user asks for a specific detail (their WAEC PIN, serial number, electricity token, recipient name, etc.), scan these entries and provide the exact value. If the detail is not in the context, acknowledge it is unavailable and direct them to support.

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
            return $base . "\n\nTASK: You are a Dashboard Assistant. Help the user navigate the platform, explain features (NIN, BVN, Wallet), or answer questions about their account status and history. When a user asks for a transaction-specific detail (PIN, serial number, phone number, electricity token, meter number, recipient, etc.), always check the RECENT ACTIVITY and CURRENT FOCUS RECORD in your context first. Provide the exact value if available; do not make it up.";
        }

        return $base . "\n\nTASK: Answer the user's question accurately using the provided context.";
    }
}
