<?php

namespace App\Jobs;

use App\Services\BookEnrichmentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnrichBookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; // 2 minutes
    public $tries = 3;
    public $backoff = [60, 120, 300]; // Retry delays in seconds

    protected $gutenbergId;

    public function __construct(int $gutenbergId)
    {
        $this->gutenbergId = $gutenbergId;
    }

    public function handle(BookEnrichmentService $service)
    {
        Log::info("Starting book enrichment job", ['gutenberg_id' => $this->gutenbergId]);

        try {
            $book = $service->enrichAndImport($this->gutenbergId);
            
            Log::info("Book enrichment job completed", [
                'gutenberg_id' => $this->gutenbergId,
                'book_id' => $book->id,
                'title' => $book->title
            ]);

        } catch (\Exception $e) {
            Log::error("Book enrichment job failed", [
                'gutenberg_id' => $this->gutenbergId,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception)
    {
        Log::error("Book enrichment job permanently failed", [
            'gutenberg_id' => $this->gutenbergId,
            'error' => $exception->getMessage()
        ]);

        // TODO: Send notification to admin
        // Notification::route('mail', config('mail.admin'))
        //     ->notify(new BookImportFailedNotification($this->gutenbergId));
    }
}