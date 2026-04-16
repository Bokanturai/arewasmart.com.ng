<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\AgentService;
use App\Models\Transaction;
use App\Models\AiChat;

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

        if ($response['success']) {
            // Save the summary as the first assistant message if not already present
            AiChat::updateOrCreate(
                ['user_id' => auth()->id(), 'reference' => $reference, 'role' => 'assistant', 'type' => 'comment'],
                ['content' => $response['answer']]
            );
        }

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

        $recordContext = $this->fetchContext($reference);
        $financialContext = $this->fetchUserFinancialContext();
        $recentActivity = $this->fetchRecentUserActivity();
        $fullContext = "Context: \"$comment\"\n\n" . $recordContext . "\n\n" . $financialContext . "\n\nRecent User Activity (Last 5 Days):\n" . $recentActivity;

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
        }

        return response()->json($response);
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

        return response()->json([
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
        $termsAndConditions = <<<TEXT
AREWA SMART CORE RULES (TERMS & CONDITIONS):
1. Nature: Digital service platform & intermediary (NOT a bank).
2. Refunds: Only for system errors caused by Arewa Smart. NOT for user error or 3rd party failures.
3. Transactions: Final and Irreversible once processed.
4. Services: NIN (Validation, Modification, IPE), BVN (Search, Reports), Agency Banking, Airtime/Data.
5. Charges: Apply once a request is successfully processed.
6. Etiquette: Extremely professional, Nigerian business style, highly respectful.
7. Security: You are a VIEW-ONLY assistant. You cannot delete, update, or post new transactions.
8. MANUAL SUPPORT: If the user explicitly asks for human/manual support or if you cannot resolve their complex issue, politely direct them to contact Arewa Smart human support via WhatsApp at 08064333983 (WhatsApp only).
TEXT;

        $basePrompt = <<<TEXT
You are 'Arewa Smart AI Guide', a premium, highly professional virtual assistant for Arewa Smart Idea Ltd. 

Your Mission & Response Strategy (95/5 Rule):
1. 95% FOCUS: Dedicate the vast majority of your response to addressing the user's specific question, concern, or complaint. Be direct, empathetic, and provide technical clarity based on the provided context.
2. 5% FOCUS: Only at the very END of your response, you may include a single, brief sentence encouraging the user to explore more services or reach out again.
3. If the user is COMPLAINING (e.g., failed transaction, pending status), your entire response should be empathetic and explanatory. Do NOT market to them if they are frustrated.
4. Tone: Expert, warm, premium. Use Nigerian business etiquette. Highly respectful of the user's time.

Strict Formatting Rules:
- Start your response with: "Dear User $userName,"
- Use standard Markdown for professional formatting (e.g., **bold** for emphasis, bullet points for lists).
- Use double newlines between paragraphs for clear visual separation.
- Date Context: Today is $today.
TEXT;

        if ($action === 'summarize') {
            return $basePrompt . "\n\nTask: Provide a detailed professional summary of the user's recent activity. Focus 95% on the data analysis and 5% on a subtle closing encouragement.";
        }

        return $basePrompt . "\n\nTask: Answer the user's specific question or complaint. Dedicate 95% of your energy to solving their concern or providing clarification. 5% focus on a polite sign-off.";
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
