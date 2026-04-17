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
                ['content' => $response['answer']]
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
            'content' => $question
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
                'content' => $response['answer']
            ]);
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
            'content' => $message
        ]);

        $fullContext = $this->fetchFullUserContext();
        $systemPrompt = $this->getAiSystemPrompt('chat', $fullContext, $userName);

        $response = $this->callDeepseek($systemPrompt, $message, $history);

        if ($response['success']) {
            AiChat::create([
                'user_id' => $userId,
                'role' => 'assistant',
                'type' => 'global',
                'content' => $response['answer']
            ]);
            return response()->json($response);
        }

        return $this->error($response['message'] ?? 'AI Service Error', 500);
    }

    /**
     * Systematically fetch all relevant database context for the user.
     * DEPRECATED: Use AiContextTrait instead.
     */
    private function fetchUserFinancialContext()
    {
        // Handled by AiContextTrait
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
