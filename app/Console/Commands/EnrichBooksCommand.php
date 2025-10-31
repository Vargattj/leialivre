<?php

// ============================================
// app/Console/Commands/EnrichBooksCommand.php
// ============================================

namespace App\Console\Commands;

use App\Services\BookEnrichmentService;
use App\Jobs\EnrichBookJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EnrichBooksCommand extends Command
{
    protected $signature = 'books:enrich 
                            {id? : Gutenberg book ID}
                            {--batch : Process batch import}
                            {--queue : Run in background queue}
                            {--list= : Comma-separated list of IDs}
                            {--range= : Range of IDs (e.g., 1000-1100)}
                            {--test : Test mode (only enrichment, no import)}';

    protected $description = 'Enrich and import books with data from multiple sources';

    private $enrichmentService;

    public function __construct(BookEnrichmentService $service)
    {
        parent::__construct();
        $this->enrichmentService = $service;
    }

    public function handle()
    {
        $this->info("📚 Book Enrichment System v2.0");
        $this->newLine();

        $testMode = $this->option('test');
        $useQueue = $this->option('queue');

        // Determine IDs to process
        $ids = $this->getIdsToProcess();

        if (empty($ids)) {
            $this->error("No book IDs provided!");
            $this->info("Usage examples:");
            $this->line("  php artisan books:enrich 55752");
            $this->line("  php artisan books:enrich --list=55752,1342,11");
            $this->line("  php artisan books:enrich --range=1000-1100");
            $this->line("  php artisan books:enrich --batch (Brazilian classics)");
            return 1;
        }

        $this->info("📋 Processing " . count($ids) . " book(s)");
        $this->newLine();

        if ($useQueue) {
            return $this->processWithQueue($ids);
        }

        return $testMode ? $this->testEnrichment($ids) : $this->processSync($ids);
    }

    /**
     * Get IDs to process based on options
     */
    private function getIdsToProcess(): array
    {
        // Single ID
        if ($this->argument('id')) {
            return [(int)$this->argument('id')];
        }

        // Batch (Brazilian classics)
        if ($this->option('batch')) {
            return $this->getBrazilianClassicsIds();
        }

        // List
        if ($this->option('list')) {
            $list = explode(',', $this->option('list'));
            return array_map('intval', $list);
        }

        // Range
        if ($this->option('range')) {
            return $this->parseRange($this->option('range'));
        }

        return [];
    }

    /**
     * Brazilian classics Gutenberg IDs
     */
    private function getBrazilianClassicsIds(): array
    {
        return [
            55752,  // Dom Casmurro - Machado de Assis
            54829,  // Memórias Póstumas de Brás Cubas - Machado de Assis
            55749,  // Quincas Borba - Machado de Assis
            28866,  // O Cortiço - Aluísio Azevedo
            // Add more Brazilian classics here
        ];
    }

    /**
     * Parse range string (e.g., "1000-1100")
     */
    private function parseRange(string $range): array
    {
        if (!preg_match('/^(\d+)-(\d+)$/', $range, $matches)) {
            $this->error("Invalid range format. Use: 1000-1100");
            return [];
        }

        $start = (int)$matches[1];
        $end = (int)$matches[2];

        if ($start >= $end || ($end - $start) > 100) {
            $this->error("Range too large or invalid. Max: 100 books");
            return [];
        }

        return range($start, $end);
    }

    /**
     * Process synchronously with progress bar
     */
    private function processSync(array $ids): int
    {
        $bar = $this->output->createProgressBar(count($ids));
        $bar->setFormat('verbose');

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                $this->info("\n\n📖 Processing book ID: {$id}");
                
                $book = $this->enrichmentService->enrichAndImport($id);
                
                $this->info("✅ Success: {$book->title}");
                $this->line("   Authors: " . $book->authors->pluck('name')->join(', '));
                $this->line("   Categories: " . $book->categories->pluck('name')->join(', '));
                $this->line("   Tags: " . $book->tags->count());
                
                $success++;
                
                // Rate limiting - be nice to APIs
                sleep(2);
                
            } catch (\Exception $e) {
                $failed++;
                $errors[] = ['id' => $id, 'error' => $e->getMessage()];
                $this->error("✗ Failed: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info("📊 SUMMARY");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total processed', count($ids)],
                ['✅ Successful', $success],
                ['✗ Failed', $failed],
            ]
        );

        // Show errors if any
        if (!empty($errors)) {
            $this->newLine();
            $this->error("❌ ERRORS:");
            $this->table(
                ['Book ID', 'Error'],
                array_map(fn($e) => [$e['id'], Str::limit($e['error'], 80)], $errors)
            );
        }

        return $failed > 0 ? 1 : 0;
    }

    /**
     * Process with queue (background)
     */
    private function processWithQueue(array $ids): int
    {
        foreach ($ids as $id) {
            EnrichBookJob::dispatch($id)->onQueue('book-enrichment');
        }

        $this->info("✅ " . count($ids) . " jobs dispatched to queue 'book-enrichment'");
        $this->line("Monitor with: php artisan queue:work --queue=book-enrichment");

        return 0;
    }

    /**
     * Test enrichment without importing
     */
    private function testEnrichment(array $ids): int
    {
        foreach ($ids as $id) {
            $this->info("\n📖 Testing enrichment for ID: {$id}");
            
            try {
                $enriched = $this->enrichmentService->enrichBook($id);
                
                $this->line("Title: {$enriched['title']}");
                $this->line("Authors: " . collect($enriched['authors'])->pluck('name')->join(', '));
                $this->line("Sources: " . implode(', ', $enriched['sources']));
                $this->line("Categories: " . implode(', ', $enriched['final_categories']));
                $this->line("Tags: " . count($enriched['final_tags']) . " tags");
                $this->line("Description: " . Str::limit($enriched['final_description_pt'], 150));
                
                $this->info("✅ Test successful");
                
            } catch (\Exception $e) {
                $this->error("✗ Test failed: {$e->getMessage()}");
            }

            $this->newLine();
        }

        return 0;
    }
}