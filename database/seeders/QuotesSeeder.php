<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some books and their authors to add quotes
        $machadoDeAssis = \App\Models\Author::where('name', 'LIKE', '%Machado de Assis%')->first();

        if ($machadoDeAssis) {
            // Get Machado de Assis books
            $books = $machadoDeAssis->books()->take(3)->get();

            foreach ($books as $book) {
                // Add 2-3 quotes per book
                $quotesData = [
                    [
                        'text' => 'O destino não é só dramaturgo, é também o seu próprio contra-regra, isto é, designa a entrada dos personagens em cena, dá-lhes as cartas e outros objetos, e executa dentro os sinais correspondentes ao diálogo, uma trovoada, um carro, um tiro.',
                        'page_number' => null,
                    ],
                    [
                        'text' => 'Não consultes dicionários. Casmurro não está aqui no sentido que eles lhe dão, mas no que lhe pôs o vulgo de homem calado e metido consigo.',
                        'page_number' => null,
                    ],
                    [
                        'text' => 'A vida é uma ópera e uma grande ópera. O tenor e o barítono lutam pelo soprano, em presença do baixo e dos comprimários, quando não são o soprano e o contralto que lutam pelo tenor, em presença do mesmo baixo e dos mesmos comprimários.',
                        'page_number' => null,
                    ],
                ];

                foreach ($quotesData as $index => $quoteData) {
                    \App\Models\Quote::create([
                        'text' => $quoteData['text'],
                        'book_id' => $book->id,
                        'author_id' => $machadoDeAssis->id,
                        'page_number' => $quoteData['page_number'],
                        'is_active' => true,
                        'order' => $index,
                    ]);
                }
            }
        }

        $this->command->info('Quotes seeded successfully!');
    }
}
