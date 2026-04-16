<?php

namespace App\Jobs;

use App\Models\AiChat;
use App\Services\DeepSeekService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ProcessAISupportReply implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $chat;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(AiChat $chat)
    {
        $this->chat = $chat;
    }

    /**
     * Execute the job.
     */
    public function handle(DeepSeekService $deepSeekService)
    {
        $reference = $this->chat->reference;
        
        // Indicate to the frontend that AI is typing
        Cache::put('admin_typing_' . $reference, true, now()->addMinutes(5));

        try {
            $reply = $deepSeekService->generateReply($this->chat);

            AiChat::create([
                'user_id' => $this->chat->user_id,
                'reference' => $reference,
                'type' => 'support',
                'role' => 'assistant',
                'content' => $reply,
            ]);
            
            // Mark the ticket as answered (update the head record)
            $ticketHead = AiChat::support()
                ->where('reference', $reference)
                ->whereNotNull('subject')
                ->first();
                
            if ($ticketHead) {
                $ticketHead->update([
                    'status' => 'answered',
                    'updated_at' => now(),
                ]);
            }

        } finally {
            // Unset typing indicator
            Cache::forget('admin_typing_' . $reference);
        }
    }
}
