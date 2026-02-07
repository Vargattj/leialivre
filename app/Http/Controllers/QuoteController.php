<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuoteController extends Controller
{
    /**
     * Display quotes for a specific book
     */
    public function byBook($bookSlug)
    {
        $book = \App\Models\Book::where('slug', $bookSlug)
            ->with(['activeQuotes.author'])
            ->firstOrFail();

        return view('quotes.by-book', compact('book'));
    }

    /**
     * Display quotes for a specific author
     */
    public function byAuthor($authorSlug)
    {
        $author = \App\Models\Author::where('slug', $authorSlug)
            ->with(['activeQuotes.book'])
            ->firstOrFail();

        return view('quotes.by-author', compact('author'));
    }

    /**
     * Display a single quote
     */
    public function show($id)
    {
        $quote = \App\Models\Quote::with(['book', 'author'])
            ->where('is_active', true)
            ->findOrFail($id);

        return view('quotes.show', compact('quote'));
    }

    /**
     * Display all quotes (paginated)
     */
    public function index()
    {
        $quotes = \App\Models\Quote::with(['book', 'author'])
            ->active()
            ->ordered()
            ->paginate(20);

        return view('quotes.index', compact('quotes'));
    }
}
