<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AutoCheckNinStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-check-nin-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting NIN Status Auto-Check...');

        // 1. Pending / Processing Checks (Every 1 hour)
        // Fetch records that are pending/processing and haven't been updated in the last hour?
        // User said "check status each 1h". If I run this command hourly, I can just check all pending/processing.
        // But to be safe and avoid rate limits, maybe I check those updated <= 1h ago? No, check all pending.
        
        $pendingRecords = \App\Models\AgentService::whereIn('service_type', ['ipe', 'IPE'])
            ->whereIn('status', ['pending', 'processing'])
            ->get();

        $this->info("Found " . $pendingRecords->count() . " pending/processing records.");

        foreach ($pendingRecords as $record) {
            $this->checkStatus($record);
        }

        // 2. Successful Checks (Recheck 2 times, after 2h each)
        // Condition: Status = successful AND recheck_count < 2 AND updated_at <= 2 hours ago
        // Actually, "after 2h each" means if it was updated (verified successful) 2 hours ago, check again.
        
        $successfulRecords = \App\Models\AgentService::whereIn('service_type', ['ipe', 'IPE'])
            ->where('status', 'successful')
            ->where('recheck_count', '<', 2)
            ->where('updated_at', '<=', now()->subHours(2)) 
            ->get();

        $this->info("Found " . $successfulRecords->count() . " successful records for recheck.");

        foreach ($successfulRecords as $record) {
            $this->checkStatus($record, true);
        }

        $this->info('Auto-Check Completed.');
    }

    private function checkStatus($record, $isRecheck = false)
    {
        try {
            $apiKey = env('NIN_API_KEY');
            $url = ''; // Determine URL
            $payload = [];

            // Normalize service_type check
            $type = strtolower($record->service_type);
            
            if (str_contains($type, 'nin') && str_contains($type, 'validation')) {
                $url = 'https://s8v.ng/api/validation/status';
                $payload = ['nin' => $record->nin, 'token' => $apiKey];
            } elseif (str_contains($type, 'ipe') || str_contains($type, 'clearance')) {
                $url = 'https://www.s8v.ng/api/clearance/status';
                $payload = ['tracking_id' => $record->tracking_id, 'token' => $apiKey];
            } else {
                return; // Unknown type
            }

            $response = \Illuminate\Support\Facades\Http::post($url, $payload);
            $apiResponse = $response->json();
            
            // Normalize status
            $newStatus = 'pending'; // default
            if (isset($apiResponse['status'])) {
                $newStatus = $this->normalizeStatus($apiResponse['status']);
            } elseif (isset($apiResponse['response'])) {
                $newStatus = $this->normalizeStatus($apiResponse['response']);
            } else {
                 // If no status in response, maybe error or keep old?
                 // If rechecking success and API fails, maybe keep old status but log error?
                 // Pending logic: keep pending.
                 $newStatus = $record->status;
            }

            // Update Logic
            if ($isRecheck) {
                // If it's a recheck loop
                // Increment recheck_count regardless of outcome to prevent infinite loops if status doesn't change?
                // User said "recheck ... 2 time".
                
                $updateData = [
                    'recheck_count' => $record->recheck_count + 1,
                    // Update status if changed? Yes.
                    // If it changes from successful to failed, should we revert it?
                    // User implies verifying it STAYS successful or catching reversals.
                ];
                
                if ($newStatus !== $record->status) {
                    $updateData['status'] = $newStatus;
                    $updateData['comment'] = $this->cleanApiResponse($apiResponse);
                    $this->info("Record {$record->id} status changed from {$record->status} to {$newStatus}");
                } else {
                     $this->info("Record {$record->id} status verified as {$newStatus}");
                }
                
                // Touches updated_at automatically, ensuring the next check is 2h from NOW.
                $record->update($updateData);

            } else {
                // Pending check loop
                if ($newStatus !== $record->status) {
                    $record->update([
                        'status' => $newStatus,
                        'comment' => $this->cleanApiResponse($apiResponse),
                        // If it becomes successful, recheck_count is 0 by default, so it enters the recheck loop later.
                    ]);
                    $this->info("Record {$record->id} updated to {$newStatus}");
                }
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("AutoCheckNinStatus Error ID {$record->id}: " . $e->getMessage());
        }
    }

    private function normalizeStatus($status): string
    {
        $s = strtolower(trim((string) $status));
        return match ($s) {
            'successful', 'success', 'resolved', 'approved', 'completed' => 'successful',
            'processing', 'in_progress', 'in-progress', 'pending', 'submitted', 'new' => 'processing',
            'failed', 'rejected', 'error', 'declined', 'invalid', 'no record' => 'failed',
            default => 'pending',
        };
    }

    private function cleanApiResponse($response): string
    {
        if (is_array($response)) {
            $message = $response['message'] ?? '';
            $data = $response['data'] ?? [];

            // Safe extraction from nested 'data' or top-level
            $nin = '';
            $reply = '';

            if (is_array($data)) {
                $nin = $data['nin'] ?? '';
                $reply = $data['reply'] ?? '';
            }

            // Fallbacks for top-level if not in data
            if (!$nin) $nin = $response['nin'] ?? '';
            if (!$reply) $reply = $response['reply'] ?? '';

            $parts = array_filter([
                $message,
                $nin ? "NIN: $nin" : null,
                $reply ? "Reply: $reply" : null
            ]);

            $combined = implode(' - ', $parts);

            if ($combined) {
                return strip_tags($combined);
            }

            $jsonString = json_encode($response, JSON_PRETTY_PRINT);
        } else {
            $jsonString = (string) $response;
        }

        $cleanResponse = str_replace(['{', '}', '"', "'"], '', $jsonString);
        $cleanResponse = preg_replace('/\s+/', ' ', $cleanResponse);
        return trim(strip_tags($cleanResponse));
    }
}
