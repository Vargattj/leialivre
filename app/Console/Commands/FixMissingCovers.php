<?php

namespace App\Console\Commands;

use App\Jobs\GenerateBookCover;
use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixMissingCovers extends Command
{
    protected $signature = 'covers:fix-missing
                            {--dry-run : Apenas lista livros afetados, sem gerar}
                            {--slug= : Processa apenas o livro com este slug}';

    protected $description = 'Detecta e regenera capas faltando para livros com use_generated_cover = true';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $slug   = $this->option('slug');

        $query = Book::where('use_generated_cover', true)->with('categories');

        if ($slug) {
            $query->where('slug', $slug);
        }

        $books = $query->get();

        if ($books->isEmpty()) {
            $this->warn('Nenhum livro com use_generated_cover = true encontrado.');
            return self::SUCCESS;
        }

        $missing = $books->filter(function ($book) {
            $path = $book->getRawOriginal('generated_cover_path');
            if (!$path) return true;
            return !Storage::disk('public')->exists($path);
        });

        $this->info("Livros com use_generated_cover = true : {$books->count()}");
        $this->info("Capas faltando ou path nulo           : {$missing->count()}");
        $this->newLine();

        if ($missing->isEmpty()) {
            $this->info('✅ Todas as capas estão presentes!');
            return self::SUCCESS;
        }

        foreach ($missing as $book) {
            $path = $book->getRawOriginal('generated_cover_path');
            $this->line(" - {$book->title} (slug: {$book->slug})");
            $this->line("   generated_cover_path: " . ($path ?? '<null>'));

            if (!$dryRun) {
                GenerateBookCover::dispatch($book)->onQueue('covers');
                $this->line('   → Job despachado para a fila "covers"');
            }
        }

        $this->newLine();

        if ($dryRun) {
            $this->warn('[dry-run] Nenhuma ação foi tomada. Execute sem --dry-run para despachar os jobs.');
        } else {
            $this->info("✅ {$missing->count()} job(s) despachado(s) para a fila 'covers'.");
            $this->line('   Execute: php artisan queue:work --queue=covers');
        }

        return self::SUCCESS;
    }
}
