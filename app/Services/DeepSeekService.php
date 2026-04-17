<?php

namespace App\Services;

use App\Models\AiChat;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Report;
use App\Models\AgentService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepSeekService
{
    use \App\Traits\AiContextTrait;
    /**
     * Generate a reply for a support chat.
     */
    public function generateReply(AiChat $chat)
    {
        $user = $chat->user;
        $userName = $user ? $user->first_name . ' ' . $user->last_name : 'Valued User';
        $reference = $chat->reference;
        
        $fullContext = $this->fetchFullUserContext($reference);
        
        // Fetch last 15 messages for history context
        $messages = AiChat::support()
            ->where('reference', $reference)
            ->latest()
            ->take(15)
            ->get()
            ->reverse(); // Keep chronological order for the AI

        $systemPrompt = $this->getAiSystemPrompt('support', $fullContext, $userName);

        $chatHistory = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        foreach ($messages as $msg) {
            $chatHistory[] = [
                'role' => $msg->role === 'user' ? 'user' : 'assistant',
                'content' => $msg->content
            ];
        }

        $apiKey = config('services.deepseek.key');
        $baseUrl = rtrim(config('services.deepseek.url', 'https://api.deepseek.com'), '/');
        
        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post($baseUrl . '/chat/completions', [
                    'model' => 'deepseek-chat',
                    'messages' => $chatHistory,
                    'temperature' => 0.7,
                    'max_tokens' => 800
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content') ?? 'I apologize, but I am unable to process your request at the moment. Please try again later.';
            }

            Log::error('DeepSeek API Error', ['response' => $response->body()]);
            return 'I apologize, but our AI support system is currently experiencing technical difficulties. Please hold on; a human agent will review your ticket soon.';

        } catch (\Exception $e) {
            Log::error('DeepSeek API Exception', ['message' => $e->getMessage()]);
            return 'I apologize, but our AI support system is currently experiencing technical difficulties. Please hold on; a human agent will review your ticket soon.';
        }
    }
}
