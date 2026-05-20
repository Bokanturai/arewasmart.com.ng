<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
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
    use HttpResponses, \App\Traits\AiContextTrait;
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
        $systemPrompt = $this->getAiSystemPrompt('summarize', $fullContext, $userName);

        $response = $this->callDeepseek($systemPrompt, "Please summarize and explain this official feedback in simple terms. Also analyze it based on my history if applicable. Feedback: \"$comment\"");

        if ($response['success']) {
            // Save the summary as the first assistant message if not already present
            AiChat::updateOrCreate(
                ['user_id' => auth()->id(), 'reference' => $reference, 'role' => 'assistant', 'type' => 'comment'],
                ['content' => $response['answer'], 'status' => 'replied']
            );
            return response()->json($response);
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
            'content' => $question,
            'status' => 'open'
        ]);

        $fullContext = $this->fetchFullUserContext($reference);

        $user = auth()->user();
        $userName = $user ? $user->first_name . ' ' . $user->last_name : 'Valued User';

        $systemPrompt = $this->getAiSystemPrompt('ask', $fullContext, $userName);

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
                'content' => $response['answer'],
                'status' => 'replied'
            ]);

            // Update linked report status if exists
            Report::where('ref', $reference)->where('user_id', $userId)->update(['status' => 'replied']);

            return response()->json($response);
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

        return response()->json([
            'success' => true,
            'history' => $chats
        ]);
    }

    /**
     * Global AI Concierge Chat.
     */
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string']);
        $message = $request->input('message');
        $userId = auth()->id();
        $user = auth()->user();
        $userName = $user->first_name . ' ' . $user->last_name;

        // Fetch history (Global context)
        $history = AiChat::where('user_id', $userId)
            ->where('type', 'global')
            ->latest()
            ->take(10)
            ->get(['role', 'content'])
            ->reverse()
            ->toArray();

        // Save User Msg
        AiChat::create([
            'user_id' => $userId,
            'role' => 'user',
            'type' => 'global',
            'content' => $message,
            'status' => 'AI instant'
        ]);

        $fullContext = $this->fetchFullUserContext();
        $systemPrompt = $this->getAiSystemPrompt('chat', $fullContext, $userName);

        $response = $this->callDeepseek($systemPrompt, $message, $history);

        if ($response['success']) {
            AiChat::create([
                'user_id' => $userId,
                'role' => 'assistant',
                'type' => 'global',
                'content' => $response['answer'],
                'status' => 'AI instant'
            ]);
            return response()->json($response);
        }

        return $this->error($response['message'] ?? 'AI Service Error', 500);
    }

    /**
     * Persist a message to the AI chat history.
     */
    public function saveMessage(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'role' => 'required|in:user,assistant',
            'type' => 'required|string',
        ]);

        $chat = AiChat::create([
            'user_id' => auth()->id(),
            'role' => $request->role,
            'type' => $request->type,
            'content' => $request->content,
            'status' => 'saved'
        ]);

        return response()->json(['success' => true, 'chat' => $chat]);
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
                    
                    // Map non-standard roles (like 'admin') to 'assistant' for Deepseek compatibility
                    $role = $msg['role'];
                    if (!in_array($role, ['system', 'user', 'assistant', 'tool'])) {
                        $role = 'assistant';
                    }

                    $messages[] = ['role' => $role, 'content' => $msg['content']];
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

    /**
     * Fetch a rendered receipt card for the AI widget.
     */
    public function getReceipt(Request $request)
    {
        $ref = $request->input('ref');
        if (!$ref) return response()->json(['success' => false, 'message' => 'Reference required'], 400);

        $tx = Transaction::where('transaction_ref', $ref)
            ->where('user_id', auth()->id())
            ->first();

        if (!$tx) return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);

        // ── Base data from main transaction record ─────────────────────────────
        $data = [
            'ref'          => $tx->transaction_ref,
            'amount'       => $tx->amount,
            'paid'         => ($tx->net_amount > 0) ? $tx->net_amount : $tx->amount,
            'date'         => $tx->created_at,
            'receiverName' => null,
            'serviceName'  => 'Service Purchase',
            'network'      => 'N/A',
            'mobile'       => 'N/A',
            'token'        => null,
            'serial'       => null,
        ];

        // ── Metadata as fallback when top-level fields are missing ─────────────
        $meta = is_array($tx->metadata) ? $tx->metadata : json_decode($tx->metadata ?? '[]', true);

        if (isset($meta['service']) && $meta['service'] === 'withdrawal' || str_starts_with($tx->transaction_ref, 'WDL')) {
            // ── Withdrawal ──────────────────────────────────────────────────────
            $data['serviceName']  = 'Wallet Withdrawal';
            $data['network']      = $meta['bankName'] ?? 'Bank Transfer';
            $data['mobile']       = $meta['account_no'] ?? 'N/A';
            $data['receiverName'] = $meta['account_name'] ?? null;
        } else {
            // ── All other services ───────────────────────────────────────────────

            // Network / provider (metadata fallback)
            $data['network'] = $meta['network']
                ?? $meta['service']
                ?? $meta['exam_name']
                ?? $data['network'];

            // Phone / recipient (metadata fallback)
            $data['mobile'] = $meta['phone_number']
                ?? $meta['phone']
                ?? $meta['mobileno']
                ?? $meta['account_number']
                ?? $data['mobile'];

            // PIN / token — check all possible metadata keys
            $data['token'] = $meta['token']
                ?? $meta['purchased_code']
                ?? $meta['purchased_pin']
                ?? $data['token'];

            // Serial number (WAEC / NECO / NABTED / JAMB) — metadata fallback only
            $data['serial'] = $meta['serial_number'] ?? $meta['serial'] ?? null;

            // Profile ID (JAMB) — stored in mobile if no phone
            if (isset($meta['profile_id']) && $data['mobile'] === 'N/A') {
                $data['mobile'] = $meta['profile_id'];
            }

            // Receiver info (P2P transfer)
            if (isset($meta['service']) && $meta['service'] === 'P2P') {
                $data['serviceName']  = 'P2P Transfer';
                $data['mobile']       = $meta['receiver_wallet'] ?? 'N/A';
                $data['receiverName'] = $meta['receiver_name'] ?? null;
            }

            // ── Service name detection (using enriched data) ─────────────────────
            if (!isset($meta['service']) || $meta['service'] !== 'P2P') {
                $networkLower = strtolower($data['network']);
                $eduKeywords  = ['waec', 'neco', 'nabted', 'nabteb', 'jamb'];
                $isEdu = $data['token'] && collect($eduKeywords)->contains(
                    fn($k) => str_contains($networkLower, $k)
                );

                if ($isEdu) {
                    $data['serviceName'] = strtoupper($data['network']) . ' Pin Purchase';
                } elseif (str_contains($networkLower, 'data')) {
                    $data['serviceName'] = 'Data Purchase';
                } elseif ($data['token']) {
                    $data['serviceName'] = 'Educational Pin';
                } elseif ($data['network'] !== 'N/A') {
                    $data['serviceName'] = 'Airtime Purchase';
                }
            }
        }

        $html = view('ai.receipt_card', $data)->render();

        return response()->json([
            'success' => true,
            'html'    => $html,
        ]);
    }
}
