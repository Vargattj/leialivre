<?php

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Services\IndexNowService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class IndexNowSubmitAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'indexnow:submit-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Submit all project URLs to IndexNow (Bing)';

    /**
     * Execute the console command.
     */
    public function handle(IndexNowService $indexNowService)
    {
        $this->info('Gathering URLs...');

        $urls = [];

        // Home page
        $urls[] = route('home');

        // Main index pages
        $urls[] = route('livros.index');
        $urls[] = route('autores.index');
        $urls[] = route('categorias.index');

        // Book pages
        $this->info('Gathering book URLs...');
        Book::active()->chunk(100, function ($books) use (&$urls) {
            foreach ($books as $book) {
                $urls[] = route('livros.show', $book->slug);
            }
        });

        // Author pages
        $this->info('Gathering author URLs...');
        Author::chunk(100, function ($authors) use (&$urls) {
            foreach ($authors as $author) {
                $urls[] = route('autores.show', $author->slug);
            }
        });

        // Category pages
        $this->info('Gathering category URLs...');
        Category::chunk(100, function ($categories) use (&$urls) {
            foreach ($categories as $category) {
                $urls[] = route('livros.categorias', $category->slug);
            }
        });

        // Static pages
        if (Route::has('about.index')) {
            $urls[] = route('about.index');
        }
        if (Route::has('contact.index')) {
            $urls[] = route('contact.index');
        }

        $total = count($urls);
        $this->info("Found {$total} URLs. Submitting to IndexNow...");

        // IndexNow limits to 10,000 URLs per request
        $chunks = array_chunk($urls, 10000);
        
        foreach ($chunks as $index => $chunk) {
            $this->info('Submitting chunk ' . ($index + 1) . ' of ' . count($chunks) . '...');
            $success = $indexNowService->submit($chunk);
            
            if ($success) {
                $this->info('Chunk submitted successfully!');
            } else {
                $this->error('Failed to submit chunk.');
            }
        }

        $this->info('IndexNow submission completed.');
    }
}
