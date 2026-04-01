<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DiagnoseCovers extends Command
{
    protected $signature = 'covers:diagnose
                            {--slug= : Slug do livro específico para investigar}
                            {--limit=10 : Número máximo de livros a verificar}';

    protected $description = 'Diagnostica problemas com capas geradas automaticamente';

    public function handle(): int
    {
        $slug  = $this->option('slug');
        $limit = (int) $this->option('limit');

        $this->newLine();
        $this->info('=== Diagnóstico de Capas Geradas ===');
        $this->newLine();

        // 1. Verificar configuração de disco
        $this->line('<fg=cyan>1. Configuração do disco "public":</>');
        $diskConfig = config('filesystems.disks.public');
        $this->line('   Driver : ' . ($diskConfig['driver'] ?? '???'));
        $this->line('   Root   : ' . ($diskConfig['root'] ?? '???'));
        $this->line('   URL    : ' . ($diskConfig['url'] ?? '???'));
        $this->newLine();

        // 2. Verificar link simbólico
        $this->line('<fg=cyan>2. Link simbólico storage → public/storage:</>');
        $publicStoragePath = public_path('storage');
        $storageAppPublic  = storage_path('app/public');

        if (is_link($publicStoragePath)) {
            $target = readlink($publicStoragePath);
            $this->line("   Symlink existe: {$publicStoragePath} → {$target}");
            $this->line('   Target real   : ' . realpath($publicStoragePath));
            $this->line('   storage/app/public: ' . $storageAppPublic);
        } elseif (is_dir($publicStoragePath)) {
            $this->warn('   ⚠ public/storage é uma pasta REAL, não um symlink!');
        } else {
            $this->error('   ✗ public/storage NÃO existe! Execute: php artisan storage:link');
        }
        $this->newLine();

        // 3. Verificar pasta covers
        $this->line('<fg=cyan>3. Pasta covers em storage/app/public/covers:</>');
        $coversPath = storage_path('app/public/covers');
        if (is_dir($coversPath)) {
            $files = glob($coversPath . '/*.webp');
            $this->line("   ✓ Pasta existe | Arquivos .webp: " . count($files));
            foreach (array_slice($files, 0, 5) as $f) {
                $this->line('     - ' . basename($f) . ' (' . number_format(filesize($f) / 1024, 1) . ' KB)');
            }
            if (count($files) > 5) {
                $this->line('     ... e mais ' . (count($files) - 5) . ' arquivo(s)');
            }
        } else {
            $this->error('   ✗ Pasta covers NÃO existe!');
        }
        $this->newLine();

        // 4. Verificar livros com use_generated_cover = true
        $this->line('<fg=cyan>4. Livros com "Usar capa gerada" ativado:</>');
        $query = Book::where('use_generated_cover', true);
        if ($slug) {
            $query->where('slug', $slug);
        }
        $books = $query->limit($limit)->get();

        $this->line("   Total encontrado(s): {$books->count()}");
        $this->newLine();

        if ($books->isEmpty()) {
            $this->warn('   Nenhum livro com use_generated_cover = true.');
            $this->newLine();
        }

        foreach ($books as $book) {
            $this->line("<fg=yellow>── Livro: {$book->title} (slug: {$book->slug})</>");

            // generated_cover_path no banco
            $this->line('   generated_cover_path (BD)  : ' . ($book->getRawOriginal('generated_cover_path') ?? '<null>'));

            // Verificar existe no disco
            $genPath = $book->getRawOriginal('generated_cover_path');
            if ($genPath) {
                $exists = Storage::disk('public')->exists($genPath);
                $realPath = storage_path('app/public/' . $genPath);
                $this->line('   Arquivo existe (disk)      : ' . ($exists ? '<fg=green>✓ SIM</>' : '<fg=red>✗ NÃO</>'));
                $this->line('   Caminho físico             : ' . $realPath);
                $this->line('   Caminho físico existe?     : ' . (file_exists($realPath) ? '<fg=green>✓ SIM</>' : '<fg=red>✗ NÃO</>'));
                if ($exists) {
                    $url = Storage::disk('public')->url($genPath);
                    $this->line('   URL gerada (disk)          : ' . $url);
                }
            } else {
                $this->warn('   ⚠ generated_cover_path é NULL no banco!');
            }

            // O que o accessor retorna
            $coverUrl   = $book->cover;
            $thumbUrl   = $book->cover_thumb;
            $isFallback = str_contains($coverUrl, 'cover-placeholder');
            $this->line('   cover_url (BD)             : ' . ($book->getRawOriginal('cover_url') ?? '<null>'));
            $this->line('   cover_thumbnail_url (BD)   : ' . ($book->getRawOriginal('cover_thumbnail_url') ?? '<null>'));
            $this->line('   use_generated_cover (BD)   : ' . ($book->use_generated_cover ? 'true' : 'false'));
            $this->line('   accessor cover             : ' . $coverUrl . ($isFallback ? ' <fg=red>(PLACEHOLDER!)</>' : ' <fg=green>(OK)</>'));
            $this->line('   accessor cover_thumb       : ' . $thumbUrl);
            $this->newLine();
        }

        // 5. Resumo geral
        $this->line('<fg=cyan>5. Resumo geral da base:</>');
        $total         = Book::count();
        $withGenPath   = Book::whereNotNull('generated_cover_path')->count();
        $withUseFlag   = Book::where('use_generated_cover', true)->count();
        $withBoth      = Book::where('use_generated_cover', true)->whereNotNull('generated_cover_path')->count();
        $this->line("   Total de livros            : {$total}");
        $this->line("   Com generated_cover_path   : {$withGenPath}");
        $this->line("   Com use_generated_cover    : {$withUseFlag}");
        $this->line("   Com ambos (ideal)          : {$withBoth}");

        $this->newLine();
        $this->info('=== Diagnóstico concluído ===');

        return self::SUCCESS;
    }
}
