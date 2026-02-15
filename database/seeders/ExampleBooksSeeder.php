<?php

// ============================================
// database/seeders/ExampleBooksSeeder.php
// ============================================

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Tag;
use App\Models\File;
use Illuminate\Database\Seeder;

class ExampleBooksSeeder extends Seeder
{
    public function run(): void
    {
        // ========================================
        // AUTOR 1: Machado de Assis
        // ========================================
        $machado = Author::create([
            'name' => 'Machado de Assis',
            'full_name' => 'Joaquim Maria Machado de Assis',
            'biography' => 'Considerado o maior escritor brasileiro de todos os tempos, Machado de Assis foi romancista, contista, cronista, poeta, teatrólogo e crítico literário. Fundador da Academia Brasileira de Letras, sua obra é marcada pela profundidade psicológica e ironia refinada.',
            'birth_date' => '1839-06-21',
            'death_date' => '1908-09-29',
            'nationality' => 'Brazil',
        ]);

        // ========================================
        // LIVRO 1: Dom Casmurro
        // ========================================
        $domCasmurro = Book::create([
            'title' => 'Dom Casmurro',
            'publication_year' => 1899,
            'original_language' => 'pt-BR',
            'synopsis' => 'Narrado em primeira pessoa por Bento Santiago, o livro conta a história de seu amor por Capitu e a dúvida sobre uma possível traição.',
            'full_description' => 'Dom Casmurro é uma das obras-primas de Machado de Assis e um dos romances mais importantes da literatura brasileira. A narrativa é conduzida por Bentinho, que relembra sua vida desde a infância, seu amor por Capitu, o casamento e a crescente desconfiança de traição. O romance é célebre pela ambiguidade: traiu ou não traiu? Machado deixa a questão em aberto, explorando a natureza humana, o ciúme e a memória.',
            'pages' => 256,
            'isbn' => '978-8535911664',
            'is_public_domain' => true,
            'public_domain_year' => 1978,
            'is_featured' => true,
            'is_active' => true,
        ]);

        $domCasmurro->authors()->attach($machado->id, [
            'contribution_type' => 'author',
            'order' => 1
        ]);

        $novel = Category::where('name', 'Novel')->first();
        if ($novel) {
            $domCasmurro->categories()->attach($novel->id, ['is_primary' => true]);
        }

        $tags1 = ['século-19', 'romance-brasileiro', 'realismo', 'traição', 'ciúme'];
        foreach ($tags1 as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $domCasmurro->tags()->attach($tag->id);
        }

        File::create([
            'book_id' => $domCasmurro->id,
            'format' => 'PDF',
            'size_bytes' => 2500000,
            'size_readable' => '2.5 MB',
            'file_url' => 'https://example.com/dom-casmurro.pdf',
            'quality' => 'high',
            'is_active' => true,
        ]);

        File::create([
            'book_id' => $domCasmurro->id,
            'format' => 'EPUB',
            'size_bytes' => 1200000,
            'size_readable' => '1.2 MB',
            'file_url' => 'https://example.com/dom-casmurro.epub',
            'quality' => 'high',
            'is_active' => true,
        ]);

        // ========================================
        // LIVRO 2: Memórias Póstumas de Brás Cubas
        // ========================================
        $memorias = Book::create([
            'title' => 'Memórias Póstumas de Brás Cubas',
            'publication_year' => 1881,
            'original_language' => 'pt-BR',
            'synopsis' => 'Um defunto autor narra suas memórias de forma irônica e filosófica, revolucionando a literatura brasileira.',
            'full_description' => 'Memórias Póstumas de Brás Cubas é considerado o marco inicial do Realismo no Brasil. Narrado por um defunto que decide contar sua vida, o romance rompe com as convenções literárias da época. Brás Cubas relata suas experiências, amores, frustrações e reflexões sobre a sociedade com uma ironia mordaz e um pessimismo filosófico que marcaram a fase madura de Machado de Assis.',
            'pages' => 368,
            'isbn' => '978-8535911671',
            'is_public_domain' => true,
            'public_domain_year' => 1978,
            'is_featured' => true,
            'is_active' => true,
        ]);

        $memorias->authors()->attach($machado->id, [
            'contribution_type' => 'author',
            'order' => 1
        ]);

        if ($novel) {
            $memorias->categories()->attach($novel->id, ['is_primary' => true]);
        }

        $tags2 = ['século-19', 'romance-brasileiro', 'realismo', 'filosofia', 'ironia'];
        foreach ($tags2 as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $memorias->tags()->attach($tag->id);
        }

        File::create([
            'book_id' => $memorias->id,
            'format' => 'PDF',
            'size_bytes' => 3200000,
            'size_readable' => '3.2 MB',
            'file_url' => 'https://example.com/memorias-postumas.pdf',
            'quality' => 'high',
            'is_active' => true,
        ]);

        File::create([
            'book_id' => $memorias->id,
            'format' => 'EPUB',
            'size_bytes' => 1500000,
            'size_readable' => '1.5 MB',
            'file_url' => 'https://example.com/memorias-postumas.epub',
            'quality' => 'high',
            'is_active' => true,
        ]);

        File::create([
            'book_id' => $memorias->id,
            'format' => 'MOBI',
            'size_bytes' => 1600000,
            'size_readable' => '1.6 MB',
            'file_url' => 'https://example.com/memorias-postumas.mobi',
            'quality' => 'high',
            'is_active' => true,
        ]);

        // ========================================
        // AUTOR 2: Aluísio Azevedo
        // ========================================
        $aluisio = Author::create([
            'name' => 'Aluísio Azevedo',
            'full_name' => 'Aluísio Tancredo Gonçalves de Azevedo',
            'biography' => 'Romancista, contista, cronista, diplomata e caricaturista brasileiro, Aluísio Azevedo é considerado o principal representante do Naturalismo no Brasil. Sua obra mais famosa, O Cortiço, retrata com realismo a vida nos cortiços cariocas do século XIX.',
            'birth_date' => '1857-04-14',
            'death_date' => '1913-01-21',
            'nationality' => 'Brazil',
        ]);

        // ========================================
        // LIVRO 3: O Cortiço
        // ========================================
        $cortico = Book::create([
            'title' => 'O Cortiço',
            'publication_year' => 1890,
            'original_language' => 'pt-BR',
            'synopsis' => 'Romance naturalista que retrata a vida em um cortiço carioca, mostrando a influência do meio sobre os personagens.',
            'full_description' => 'O Cortiço é a obra-prima de Aluísio Azevedo e o principal romance naturalista brasileiro. A narrativa acompanha a vida dos moradores de um cortiço no Rio de Janeiro, mostrando como o ambiente e as condições sociais influenciam o comportamento humano. Com uma linguagem direta e descritiva, o autor retrata as mazelas sociais, a exploração, a sensualidade e a luta pela sobrevivência na sociedade brasileira do século XIX.',
            'pages' => 272,
            'isbn' => '978-8508040506',
            'is_public_domain' => true,
            'public_domain_year' => 1983,
            'is_featured' => true,
            'is_active' => true,
        ]);

        $cortico->authors()->attach($aluisio->id, [
            'contribution_type' => 'author',
            'order' => 1
        ]);

        if ($novel) {
            $cortico->categories()->attach($novel->id, ['is_primary' => true]);
        }

        $tags3 = ['século-19', 'romance-brasileiro', 'naturalismo', 'crítica-social', 'rio-de-janeiro'];
        foreach ($tags3 as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $cortico->tags()->attach($tag->id);
        }

        File::create([
            'book_id' => $cortico->id,
            'format' => 'PDF',
            'size_bytes' => 2800000,
            'size_readable' => '2.8 MB',
            'file_url' => 'https://example.com/o-cortico.pdf',
            'quality' => 'high',
            'is_active' => true,
        ]);

        File::create([
            'book_id' => $cortico->id,
            'format' => 'EPUB',
            'size_bytes' => 1300000,
            'size_readable' => '1.3 MB',
            'file_url' => 'https://example.com/o-cortico.epub',
            'quality' => 'high',
            'is_active' => true,
        ]);

        $this->command->info('✅ Seeder executado com sucesso!');
        $this->command->info('📚 Livros criados: Dom Casmurro, Memórias Póstumas de Brás Cubas, O Cortiço');
        $this->command->info('✍️  Autores criados: Machado de Assis, Aluísio Azevedo');
    }
}