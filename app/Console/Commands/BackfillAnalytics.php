<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackfillAnalytics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:backfill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill analytics events based on old counters';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting backfill for views...');
        $books = \App\Models\Book::where('views', '>', 0)->get();
        foreach ($books as $book) {
            $existingViews = \App\Models\AnalyticsEvent::where('event_type', 'book_view')->where('book_id', $book->id)->count();
            $missingViews = $book->views - $existingViews;
            
            if ($missingViews > 0) {
                $events = [];
                for ($i = 0; $i < $missingViews; $i++) {
                    $timestamp = \Carbon\Carbon::createFromTimestamp(rand($book->created_at->timestamp, time()));
                    $events[] = [
                        'event_type' => 'book_view',
                        'book_id'    => $book->id,
                        'file_id'    => null,
                        'ip_address' => '127.0.0.1',
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }
                foreach (array_chunk($events, 500) as $chunk) {
                    \App\Models\AnalyticsEvent::insert($chunk);
                }
            }
        }

        $this->info('Starting backfill for downloads...');
        $files = \App\Models\File::where('total_downloads', '>', 0)->get();
        foreach ($files as $file) {
            $existingDls = \App\Models\AnalyticsEvent::where('event_type', 'file_download')->where('file_id', $file->id)->count();
            $missingDls = $file->total_downloads - $existingDls;

            if ($missingDls > 0) {
                $events = [];
                for ($i = 0; $i < $missingDls; $i++) {
                    $timestamp = \Carbon\Carbon::createFromTimestamp(rand($file->created_at->timestamp, time()));
                    $events[] = [
                        'event_type' => 'file_download',
                        'book_id'    => $file->book_id,
                        'file_id'    => $file->id,
                        'ip_address' => '127.0.0.1',
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }
                foreach (array_chunk($events, 500) as $chunk) {
                    \App\Models\AnalyticsEvent::insert($chunk);
                }
            }
        }
        
        $this->info('Starting backfill for purchase clicks...');
        $booksWithClicks = \App\Models\Book::where('purchase_clicks', '>', 0)->get();
        foreach ($booksWithClicks as $book) {
            $existingClicks = \App\Models\AnalyticsEvent::where('event_type', 'purchase_click')->where('book_id', $book->id)->count();
            $missingClicks = $book->purchase_clicks - $existingClicks;
            
            if ($missingClicks > 0) {
                $events = [];
                for ($i = 0; $i < $missingClicks; $i++) {
                    $timestamp = \Carbon\Carbon::createFromTimestamp(rand($book->created_at->timestamp, time()));
                    $events[] = [
                        'event_type' => 'purchase_click',
                        'book_id'    => $book->id,
                        'file_id'    => null,
                        'ip_address' => '127.0.0.1',
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }
                foreach (array_chunk($events, 500) as $chunk) {
                    \App\Models\AnalyticsEvent::insert($chunk);
                }
            }
        }

        $this->info('Backfill completed successfully!');
    }
}
