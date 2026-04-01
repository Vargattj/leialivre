<?php

namespace App\Jobs;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class GenerateBookCover implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 30;
    public int $tries   = 2;

    public function __construct(protected Book $book)
    {
    }

    public function handle(): void
    {
        $book = $this->book;

        $categoryName = $book->categories->first()?->name ?? 'Romance';

        $payload = json_encode([
            'slug'     => $book->slug,
            'title'    => $book->title,
            'author'   => $book->authorsNames,
            'year'     => $book->publication_year,
            'category' => $categoryName,
        ], JSON_UNESCAPED_UNICODE);

        $scriptPath  = base_path('scripts/generate-cover.js');
        $projectRoot = base_path();

        $process = new Process(
            ['node', $scriptPath, $payload],
            $projectRoot,
            null,
            null,
            $this->timeout
        );

        $process->run();

        if (!$process->isSuccessful()) {
            $errorOutput = trim($process->getErrorOutput() ?: $process->getOutput());

            Log::error('GenerateBookCover: falha ao gerar capa', [
                'book_id'  => $book->id,
                'slug'     => $book->slug,
                'exit_code' => $process->getExitCode(),
                'error'    => $errorOutput,
            ]);

            return;
        }

        $book->generated_cover_path = "covers/{$book->slug}.webp";
        $book->saveQuietly();

        Log::info('GenerateBookCover: capa gerada com sucesso', [
            'book_id' => $book->id,
            'slug'    => $book->slug,
            'path'    => $book->generated_cover_path,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('GenerateBookCover: job falhou definitivamente', [
            'book_id' => $this->book->id,
            'slug'    => $this->book->slug,
            'error'   => $exception->getMessage(),
        ]);
    }
}
