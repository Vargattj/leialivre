<?php

namespace App\Observers;

use App\Jobs\GenerateBookCover;
use App\Models\Book;

class BookObserver
{
    public function created(Book $book): void
    {
        GenerateBookCover::dispatch($book)->onQueue('covers');
    }

    public function updated(Book $book): void
    {
        // Regenera se algum dado importante mudou ou se ainda não tem capa gerada
        if ($book->wasChanged(['title', 'publication_year']) || empty($book->generated_cover_path)) {
            GenerateBookCover::dispatch($book)->onQueue('covers');
        }
    }
}
