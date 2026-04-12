<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\AgentService;
use App\Models\Transaction;

class AiCommentController extends Controller
{
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

        $context = $this->fetchContext($reference);
        $financialContext = $this->fetchUserFinancialContext();
        $recentActivity = $this->fetchRecentUserActivity();
        $fullContext = $context . "\n\n" . $financialContext . "\n\nRecent User Activity (Last 5 Days):\n" . $recentActivity;

        $user = auth()->user();
        $userName = $user ? $user->first_name . ' ' . $user->last_name : 'Valued User';
        $systemPrompt = $this->getSystemPrompt('summarize', $fullContext, $userName);

        $response = $this->callDeepseek($systemPrompt, "Please provide a professional transaction summary and analysis for matches in the last 5 days if visible, otherwise focus on the specific context: \"$comment\"");

        return response()->json($response);
    }

    /**
     * Handle follow-up questions from the user.
     */
    public function ask(Request $request)
    {
        $request->validate([
            'comment' => 'required|string',
            'question' => 'required|string',
            'history' => 'nullable|array',
            'reference' => 'nullable|string',
        ]);

        $comment = $request->input('comment');
        $question = $request->input('question');
        $history = $request->input('history', []);
        $reference = $request->input('reference');

        $recordContext = $this->fetchContext($reference);
        $financialContext = $this->fetchUserFinancialContext();
        $recentActivity = $this->fetchRecentUserActivity();
        $fullContext = "Context: \"$comment\"\n\n" . $recordContext . "\n\n" . $financialContext . "\n\nRecent User Activity (Last 5 Days):\n" . $recentActivity;

        $user = auth()->user();
        $userName = $user ? $user->first_name . ' ' . $user->last_name : 'Valued User';

        $systemPrompt = $this->getSystemPrompt('ask', $fullContext, $userName);

        $response = $this->callDeepseek($systemPrompt, $question, $history);

        return response()->json($response);
    }

    /**
     * Get the system prompt based on the action.
     */
    private function getSystemPrompt($action, $context = '', $userName = 'Valued User')
    {
        $today = now()->format('l, d F Y');
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

Your Mission & Primary Aim:
1. ENCOURAGE the user to do more transactions and explore more services.
2. Provide high-level professional summaries of their activity.
3. Answer questions respectfully while building trust in Arewa Smart.
4. REFERRALS: Encourage the user to invite friends using their specific 'Referral Code' mentioned in the context to earn bonuses.
5. Tone: Expert, warm, premium. Use Nigerian business etiquette.

Strict Formatting Rules:
- Start your response with: "Dear User $userName,"
- Acknowledge specific Dashboard Totals (Balance/Bonuses) and Referral Code from the context.
- For summaries, include a section: "Analysis of your activity:" 
- List services (e.g., Airtime, Data, Verification, Validation) with their corresponding amounts.
- Use HTML line breaks (<br>) to separate different services or sections for easy understanding.
- Always end with an encouraging note to do more transactions or refer a friend.

Date Context: Today is $today.
Platform Context: $termsAndConditions
Record/History Context: $context
TEXT;

        if ($action === 'summarize') {
            return $basePrompt . "\n\nTask: Summarize the provided transaction information professionally. Be concise but detailed about service types and amounts. Show the user you value their business.";
        }

        return $basePrompt . "\n\nTask: Answer the user's specific question using the provided context. Be helpful, direct, and maintain the professional 'Arewa Smart' persona.";
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
