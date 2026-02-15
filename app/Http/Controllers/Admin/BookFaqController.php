<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookFaq;
use Illuminate\Http\Request;

class BookFaqController extends Controller
{
    /**
     * Display a listing of FAQs for a specific book.
     */
    public function index(Book $book)
    {
        $faqs = $book->faqs()->ordered()->get();

        return view('admin.books.faqs.index', compact('book', 'faqs'));
    }

    /**
     * Show the form for creating a new FAQ.
     */
    public function create(Book $book)
    {
        return view('admin.books.faqs.create', compact('book'));
    }

    /**
     * Store a newly created FAQ in storage.
     */
    public function store(Request $request, Book $book)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['book_id'] = $book->id;
        $validated['order'] = $validated['order'] ?? ($book->faqs()->max('order') ?? -1) + 1;
        $validated['is_active'] = $request->has('is_active') ? true : false;

        BookFaq::create($validated);

        return redirect()
            ->route('admin.books.edit', $book)
            ->with('success', 'FAQ criada com sucesso!');
    }

    /**
     * Show the form for editing the specified FAQ.
     */
    public function edit(Book $book, BookFaq $faq)
    {
        // Ensure the FAQ belongs to this book
        if ($faq->book_id !== $book->id) {
            abort(404);
        }

        return view('admin.books.faqs.edit', compact('book', 'faq'));
    }

    /**
     * Update the specified FAQ in storage.
     */
    public function update(Request $request, Book $book, BookFaq $faq)
    {
        // Ensure the FAQ belongs to this book
        if ($faq->book_id !== $book->id) {
            abort(404);
        }

        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $faq->update($validated);

        return redirect()
            ->route('admin.books.edit', $book)
            ->with('success', 'FAQ atualizada com sucesso!');
    }

    /**
     * Remove the specified FAQ from storage.
     */
    public function destroy(Book $book, BookFaq $faq)
    {
        // Ensure the FAQ belongs to this book
        if ($faq->book_id !== $book->id) {
            abort(404);
        }

        $faq->delete();

        return redirect()
            ->route('admin.books.edit', $book)
            ->with('success', 'FAQ removida com sucesso!');
    }
}
