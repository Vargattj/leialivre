<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\StudyGuide;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Cria um StudyGuide publicado com conteúdo mockado para testar o funil do
 * guia de estudo (popup → captura → download → landing → checkout) sem
 * depender de conteúdo editorial real nem de credenciais de R2.
 *
 * Rodar com: php artisan db:seed --class=StudyGuideDemoSeeder
 */
class StudyGuideDemoSeeder extends Seeder
{
    public function run(): void
    {
        $book = Book::where('slug', 'memorias-postumas-de-bras-cubas')->first()
            ?? Book::where('is_active', true)->first();

        if (! $book) {
            $this->command?->warn('Nenhum livro encontrado — rode o ExampleBooksSeeder antes.');
            return;
        }

        $disk = filled(config('filesystems.disks.r2.key')) ? 'r2' : 'public';
        $path = 'guides/guia-teste-' . $book->slug . '.pdf';

        if (! Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->put($path, $this->mockPdf($book->title));
        }

        $guide = StudyGuide::updateOrCreate(
            ['book_id' => $book->id],
            [
                'slug'          => 'guia-teste-' . $book->slug,
                'title'         => 'Guia de Estudo (MOCK): ' . $book->title,
                'free_pdf_path' => $path,
                'checkout_url'  => null, // cai na landing — ver seção E do spec
                'price_cents'   => 2990,
                'is_published'  => true,
                'published_at'  => now(),
            ]
        );

        $this->command?->info("Guia de teste pronto para \"{$book->title}\".");
        $this->command?->info('Livro:  /livros/' . $book->slug);
        $this->command?->info('Landing: /guias/' . $guide->slug);
        $this->command?->info('(disco usado para o PDF mockado: ' . $disk . ')');
    }

    private function mockPdf(string $bookTitle): string
    {
        $text = '(Guia de Estudo MOCK - ' . str_replace([')', '('], '', $bookTitle) . ')';
        $stream = "BT /F1 18 Tf 50 700 Td {$text} Tj ET";

        $objects = [
            1 => "<</Type/Catalog/Pages 2 0 R>>",
            2 => "<</Type/Pages/Kids[3 0 R]/Count 1>>",
            3 => "<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Resources<</Font<</F1 5 0 R>>>>/Contents 4 0 R>>",
            4 => "<</Length " . strlen($stream) . ">>stream\n{$stream}\nendstream",
            5 => "<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objects as $id => $body) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "{$id} 0 obj\n{$body}\nendobj\n";
        }

        $xrefStart = strlen($pdf);
        $count = count($objects) + 1;

        $pdf .= "xref\n0 {$count}\n0000000000 65535 f \n";
        foreach ($objects as $id => $body) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$id]);
        }

        $pdf .= "trailer<</Size {$count}/Root 1 0 R>>\nstartxref\n{$xrefStart}\n%%EOF";

        return $pdf;
    }
}
