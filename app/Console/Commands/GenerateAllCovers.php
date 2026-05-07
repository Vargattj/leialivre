<?php

namespace App\Console\Commands;

use App\Jobs\GenerateBookCover;
use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateAllCovers extends Command
{
    protected $signature = 'covers:generate
                            {--force : Gera capas mesmo para livros que já possuem capa gerada}';

    protected $description = 'Gera capas automáticas para todos os livros que ainda não possuem a versão gerada';

    public function handle(): int
    {
        $force = $this->option('force');

        $query = Book::with('categories')
            ->active();

        if (!$force) {
            $query->whereNull('generated_cover_path');
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('Nenhum livro encontrado para processar.');
            return self::SUCCESS;
        }

        $this->info("📚 {$total} livros encontrados.");
        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% | %message%');
        $bar->setMessage('Iniciando...');
        $bar->start();

        // Garante que a pasta de destino existe
        Storage::makeDirectory('covers');

        $dispatched = 0;

        $query->chunk(50, function ($books) use ($bar, &$dispatched) {
            foreach ($books as $book) {
                GenerateBookCover::dispatch($book)->onQueue('covers');
                $dispatched++;
                $bar->setMessage($book->title);
                $bar->advance();
            }
        });

        $bar->setMessage('Concluído!');
        $bar->finish();

        $this->newLine(2);
        $this->info("✅ {$dispatched} jobs despachados para a fila 'covers'.");
        $this->line("   Execute: php artisan queue:work --queue=covers");

        return self::SUCCESS;
    }
}
