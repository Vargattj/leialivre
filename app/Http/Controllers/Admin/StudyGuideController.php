<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\StudyGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudyGuideController extends Controller
{
    /**
     * Cria ou atualiza o guia de estudo de um livro (1:1).
     *
     * Form próprio, separado do form do livro: salvar o livro nunca toca no
     * guia, e vice-versa.
     */
    public function update(Request $request, Book $book)
    {
        // Inputs prefixados com guide_: o form do livro, na mesma página, tem
        // um campo "title" — sem prefixo o old() de um vazaria para o outro.
        $validated = $request->validate([
            'guide_title'        => 'required|string|max:255',
            'guide_pdf'          => 'nullable|file|mimes:pdf|max:20480',
            'guide_checkout_url' => 'nullable|url|max:2048',
            'guide_price_reais'  => 'nullable|numeric|min:0|max:9999',
        ], [], [
            'guide_title'        => 'título',
            'guide_pdf'          => 'PDF',
            'guide_checkout_url' => 'link de checkout',
            'guide_price_reais'  => 'preço',
        ]);

        $guide = $book->guide;
        $path = $guide?->free_pdf_path;

        if ($request->hasFile('guide_pdf')) {
            $path = $this->storePdf($request->file('guide_pdf'), $book);
        }

        $isPublished = $request->boolean('guide_is_published');

        // Publicar sem arquivo colocaria a oferta no ar para dar 404 depois do
        // usuário já ter entregado o e-mail: lead queimado e dado sujo.
        if ($isPublished && blank($path)) {
            return back()
                ->withInput()
                ->withErrors(['guide_pdf' => 'Anexe o PDF antes de publicar o guia.'], 'guide');
        }

        StudyGuide::updateOrCreate(
            ['book_id' => $book->id],
            [
                // Slug gerado a partir do livro e nunca reescrito: ele vai
                // impresso nos PDFs via /checkout/{slug}.
                'slug'          => $guide?->slug ?? Str::substr('guia-' . $book->slug, 0, 255),
                'title'         => $validated['guide_title'],
                'free_pdf_path' => $path,
                'checkout_url'  => $validated['guide_checkout_url'] ?? null,
                'price_cents'   => isset($validated['guide_price_reais'])
                    ? (int) round((float) $validated['guide_price_reais'] * 100)
                    : null,
                'is_published'  => $isPublished,
                'published_at'  => $isPublished ? ($guide?->published_at ?? now()) : null,
            ]
        );

        return redirect()
            ->route('admin.books.edit', $book)
            ->with('success', 'Guia de estudo salvo!');
    }

    /**
     * Sobe o PDF sem visibilidade pública: o guia é liberado em troca do
     * e-mail, então uma URL direta do bucket furaria o funil.
     */
    private function storePdf($uploadedFile, Book $book): string
    {
        $path = "guides/{$book->id}/" . Str::uuid() . '.pdf';

        Storage::disk(StudyGuide::disk())
            ->put($path, file_get_contents($uploadedFile->getRealPath()));

        return $path;
    }
}
