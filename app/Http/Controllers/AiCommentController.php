<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\AgentService;
use App\Models\Transaction;
use App\Models\AiChat;
use App\Models\Report;
use App\Models\VirtualAccount;
use App\Traits\HttpResponses;

class AiCommentController extends Controller
{
    use HttpResponses;
    /**
     * Summarize an administrative comment using AI.
     */
    public function summarize(Request $request)
    {
        $request->validate([
            'comment' => 'required|string',
            'reference' => 'nullable|string',
        ]);

        $comment = $request->input('comment');
        $reference = $request->input('reference');

        $fullContext = $this->fetchFullUserContext($reference);

        $user = auth()->user();
        $userName = $user ? $user->first_name . ' ' . $user->last_name : 'Valued User';
        $systemPrompt = $this->getSystemPrompt('summarize', $fullContext, $userName);

        $response = $this->callDeepseek($systemPrompt, "Provide a friendly, very brief greeting and offer assistance. Do NOT provide a summary yet. Just say you are ready to help with any questions about this transaction: \"$comment\"");

        if ($response['success']) {
            // Save the intro as the first assistant message if not already present
            AiChat::updateOrCreate(
                ['user_id' => auth()->id(), 'reference' => $reference, 'role' => 'assistant', 'type' => 'comment'],
                ['content' => $response['answer']]
            );
            return $this->success($response);
        }

        return $this->error($response['message'] ?? 'AI Service Error', 500);
    }

    /**
     * Handle follow-up questions from the user.
     */
    public function ask(Request $request)
    {
        $request->validate([
            'comment' => 'required|string',
            'question' => 'required|string',
            'reference' => 'nullable|string',
        ]);

        $comment = $request->input('comment');
        $question = $request->input('question');
        $reference = $request->input('reference');
        $userId = auth()->id();

        // Save User Message
        AiChat::create([
            'user_id' => $userId,
            'reference' => $reference,
            'role' => 'user',
            'type' => 'comment',
            'content' => $question
        ]);

        $fullContext = $this->fetchFullUserContext($reference);

        $user = auth()->user();
        $userName = $user ? $user->first_name . ' ' . $user->last_name : 'Valued User';

        $systemPrompt = $this->getSystemPrompt('ask', $fullContext, $userName);

        // Fetch last 20 messages for history context
        $dbHistory = AiChat::comment()
            ->where('user_id', $userId)
            ->where('reference', $reference)
            ->latest()
            ->take(20)
            ->get(['role', 'content'])
            ->reverse()
            ->toArray();

        // Rename 'content' to 'content' for Deepseek compatibility (which it already is)
        $history = array_map(function($msg) {
            return ['role' => $msg['role'], 'content' => $msg['content']];
        }, $dbHistory);

        $response = $this->callDeepseek($systemPrompt, $question, $history);

        if ($response['success']) {
            // Save Assistant Message
            AiChat::create([
                'user_id' => $userId,
                'reference' => $reference,
                'role' => 'assistant',
                'type' => 'comment',
                'content' => $response['answer']
            ]);
            return $this->success($response);
        }

        return $this->error($response['message'] ?? 'AI Service Error', 500);
    }

    /**
     * Fetch chat history for a specific reference.
     */
    public function fetchHistory(Request $request)
    {
        $reference = $request->query('reference');
        $userId = auth()->id();

        $chats = AiChat::comment()
            ->where('user_id', $userId)
            ->where('reference', $reference)
            ->oldest()
            ->get(['role', 'content']);

        return $this->success([
            'success' => true,
            'history' => $chats
        ]);
    }

    /**
     * Get the system prompt based on the action.
     */
    private function getSystemPrompt($action, $context = '', $userName = 'Valued User')
    {
        $today = now()->format('l, d F Y');
        $userId = auth()->id();
        $termsAndConditions = <<<TEXT
AREWA SMART CORE RULES (TERMS & CONDITIONS):
1. Nature: Digital service platform & intermediary (NOT a bank).
2. Refunds: Only for system errors caused by Arewa Smart. NOT for user error or 3rd party failures.
3. Transactions: Final and Irreversible once processed.
4. Services: NIN (Validation, Modification, IPE), BVN (Search, Reports), Agency Banking, Airtime/Data.
5. Charges: Apply once a request is successfully processed.
6. Etiquette: Extremely professional, Nigerian business style, highly respectful.
7. Security: You are a VIEW-ONLY assistant. You cannot delete, update, or post new transactions.
TEXT;

        $basePrompt = <<<TEXT
You are 'Arewa Smart AI Guide', a premium, highly professional virtual assistant for Arewa Smart Idea Ltd.

Your Mission & Response Strategy (Strictly Reactive):
1. REACTIVE ONLY: Do NOT provide summaries, detailed analysis, or financial reports proactively. Wait for the user to ask a specific question.
2. 95% FOCUS: When asked, dedicate the vast majority of your response to the user's specific question. Be direct, empathetic, and provide technical clarity.
3. 5% FOCUS: Only at the very END of your response, you may include a single, brief sentence encouraging the user to explore more services or reach out again.
4. TONE: Expert, warm, premium. Use Nigerian business etiquette. Highly respectful of the user's time.
5. WHATSAPP RULE: NEVER provide the WhatsApp support number unless the user explicitly asks for 'human support', 'manual support', or a person to talk to.
6. DATA PRIVACY & AUTHORIZATION: You are officially authorized with VIEW-ONLY access to all database systems for the authenticated user. You may reference any information provided in the context (Transactions, Services, Reports, Wallet, Profile) to assist the user. 
7. SECURITY BOUNDARY: You MUST NOT attempt to access or reveal data that does not belong to User ID: $userId. Stay strictly within the provided user context.

Strict Formatting Rules:
- Start your response with: "Dear User $userName,"
- Use standard Markdown for professional formatting.
- Use double newlines between paragraphs for clear visual separation.
- Date Context: Today is $today.
TEXT;

        if ($action === 'summarize') {
            return $basePrompt . "\n\nTask: Provide a very brief, friendly greeting (2 sentences max). Mention that you have access to the records and are ready to answer any specific questions the user has about this transaction/service. DO NOT show any details yet.";
        }

        return $basePrompt . "\n\nTask: Answer the user's specific question. Only provide information that was directly requested.";
    }

    /**
     * Fetch context from database based on reference.
     */
    private function fetchContext($reference)
    {
        if (!$reference)
            return "No specific record reference provided.";

        $userId = auth()->id();

        // Security: Ensure the service belongs to the authenticated user
        $service = AgentService::where('reference', $reference)
            ->where('user_id', $userId)
            ->first();
            
        if ($service) {
            return "Service: {$service->service_name}\nStatus: {$service->status}\nAmount: {$service->amount}\nDescription: {$service->description}\nRef: {$service->reference}";
        }

        // Security: Ensure the transaction belongs to the authenticated user
        $transaction = Transaction::where('transaction_ref', $reference)
            ->where('user_id', $userId)
            ->first();
            
        if ($transaction) {
            return "Transaction: {$transaction->description}\nStatus: {$transaction->status}\nAmount: {$transaction->amount}\nPayer: {$transaction->payer_name}\nRef: {$transaction->transaction_ref}";
        }

        return "Reference provided ($reference) but no matching record found for your account.";
    }

    /**
     * Systematically fetch all relevant database context for the user.
     * SCOPED to user_id for security.
     */
    private function fetchFullUserContext($reference = null)
    {
        $userId = auth()->id();
        if (!$userId) return "No authenticated user session found.";

        // 1. Specific Record Context (if ref provided)
        $recordContext = $this->fetchContext($reference);

        // 2. Financial & Profile Context
        $financialContext = $this->fetchUserFinancialContext();

        // 3. Virtual Accounts Context
        $virtualAccounts = VirtualAccount::where('user_id', $userId)->get();
        $vaContext = "VIRTUAL ACCOUNTS:\n";
        if ($virtualAccounts->isEmpty()) {
            $vaContext .= "- No virtual accounts assigned yet.\n";
        } else {
            foreach ($virtualAccounts as $va) {
                $vaContext .= "- {$va->bankName}: {$va->accountNo} ({$va->accountName}) [{$va->status}]\n";
            }
        }

        // 4. Reports & Complaints Context (Last 10)
        $reports = Report::where('user_id', $userId)->latest()->take(10)->get();
        $reportContext = "USER REPORTS & COMPLAINTS (Last 10):\n";
        if ($reports->isEmpty()) {
            $reportContext .= "- No reports found.\n";
        } else {
            foreach ($reports as $r) {
                $reportContext .= "- [{$r->created_at->format('Y-m-d')}] {$r->type} - {$r->description} [{$r->status}]\n";
            }
        }

        // 5. Recent Activity (Transactions & Services)
        $recentActivity = $this->fetchRecentUserActivity();

        return "--- USER AUTHENTICATED CONTEXT (USER ID: $userId) ---\n\n" . 
               "CURRENT FOCUS RECORD:\n$recordContext\n\n" .
               "$financialContext\n\n" .
               "$vaContext\n\n" .
               "$reportContext\n\n" .
               "RECENT ACTIVITY LOGS:\n$recentActivity\n\n" .
               "--- END OF AUTHORIZED CONTEXT ---";
    }

    /**
     * Fetch the user's recent history for broader AI context.
     */
    private function fetchRecentUserActivity()
    {
        $userId = auth()->id();
        if (!$userId) return "No authenticated user.";

        $fiveDaysAgo = now()->subDays(5);

        // Fetch last 30 transactions
        $transactions = Transaction::where('user_id', $userId)
            ->where('created_at', '>=', $fiveDaysAgo)
            ->latest()
            ->take(30)
            ->get();

        // Fetch last 30 agent services (NIN/BVN etc)
        $services = AgentService::where('user_id', $userId)
            ->where('created_at', '>=', $fiveDaysAgo)
            ->latest()
            ->take(30)
            ->get();

        if ($transactions->isEmpty() && $services->isEmpty()) {
            return "No activity found in the last 5 days.";
        }

        $totalSpent = $transactions->sum('amount');
        $totalServices = $services->count();
        $totalServiceAmount = $services->sum('amount');

        $summaryStats = "ACTIVITY SUMMARY (Last 5 Days):\n" .
                        "- Total Expenditure: ₦" . number_format($totalSpent, 2) . "\n" .
                        "- Total Agent Services: $totalServices requested (₦" . number_format($totalServiceAmount, 2) . ")\n\n";

        $output = "TRANSACTION LOGS:\n";
        foreach ($transactions as $tx) {
            $output .= "- [{$tx->created_at->format('Y-m-d H:i')}] {$tx->description}: ₦" . number_format($tx->amount, 2) . " [{$tx->status}]\n";
        }

        $output .= "\nAGENT SERVICES LOGS:\n";
        foreach ($services as $s) {
            $output .= "- [{$s->created_at->format('Y-m-d H:i')}] {$s->service_name} ({$s->service_type}): ₦" . number_format($s->amount, 2) . " [{$s->status}]\n";
        }

        return $summaryStats . $output;
    }

    /**
     * Fetch user's financial and referral context.
     */
    private function fetchUserFinancialContext()
    {
        $user = auth()->user();
        if (!$user) return "No authenticated user info available.";

        $wallet = $user->wallet;
        $balance = $wallet ? $wallet->available_balance : 0;
        $bonus = $wallet ? $wallet->bonus : 0;
        
        $referralCode = $user->referral_code ?? 'Contact Support';
        $referralBonus = $user->referral_bonus ?? 0;

        return "USER DASHBOARD DATA:\n" .
               "- Available Wallet Balance: ₦" . number_format($balance, 2) . "\n" .
               "- Account Bonus: ₦" . number_format($bonus, 2) . "\n" .
               "- Total Referral Earnings: ₦" . number_format($referralBonus, 2) . "\n" .
               "- PERSONAL REFERRAL CODE: $referralCode\n" .
               "INCENTIVE:\n" .
               "- Users can earn extra bonuses by inviting friends to Arewa Smart using their referral code. Each successful referral adds to their bonus balance.";
    }

    /**
     * Call the Deepseek API.
     */
    private function callDeepseek($systemPrompt, $userMessage, $history = [])
    {
        try {
            $apiKey = config('services.deepseek.key');
            $baseUrl = rtrim(config('services.deepseek.url', 'https://api.deepseek.com'), '/');

            if (!$apiKey) {
                return ['success' => false, 'message' => 'AI Service temporarily unavailable. Please try again later or contact support.'];
            }

            $messages = [['role' => 'system', 'content' => $systemPrompt]];

            // Add history if available (Filtering out duplicates of the current message)
            foreach ($history as $msg) {
                if (isset($msg['role']) && isset($msg['content']) && 
                   ($msg['role'] !== 'user' || $msg['content'] !== $userMessage)) {
                    $messages[] = $msg;
                }
            }

            $messages[] = ['role' => 'user', 'content' => $userMessage];

            $response = Http::timeout(45)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($baseUrl . '/chat/completions', [
                    'model' => 'deepseek-chat',
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 1000,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                return [
                    'success' => true,
                    'answer' => $content
                ];
            }

            Log::error('Deepseek API Error in AiCommentController', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return ['success' => false, 'message' => 'Our AI is momentarily busy. Please try again in a few seconds.'];

        } catch (\Exception $e) {
            Log::error('Deepseek Exception in AiCommentController: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Something went wrong with the AI service.'];
        }
    }
}
