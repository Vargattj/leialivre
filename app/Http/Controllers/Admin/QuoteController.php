<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quotes = \App\Models\Quote::with(['book', 'author'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.quotes.index', compact('quotes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $books = \App\Models\Book::orderBy('title')->get();
        $authors = \App\Models\Author::orderBy('name')->get();

        return view('admin.quotes.create', compact('books', 'authors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'book_id' => 'required|exists:books,id',
            'author_id' => 'required|exists:authors,id',
            'page_number' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        \App\Models\Quote::create($validated);

        return redirect()->route('admin.quotes.index')
            ->with('success', 'Citação criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $quote = \App\Models\Quote::with(['book', 'author'])->findOrFail($id);

        return view('admin.quotes.show', compact('quote'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $quote = \App\Models\Quote::findOrFail($id);
        $books = \App\Models\Book::orderBy('title')->get();
        $authors = \App\Models\Author::orderBy('name')->get();

        return view('admin.quotes.edit', compact('quote', 'books', 'authors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $quote = \App\Models\Quote::findOrFail($id);

        $validated = $request->validate([
            'text' => 'required|string',
            'book_id' => 'required|exists:books,id',
            'author_id' => 'required|exists:authors,id',
            'page_number' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        $quote->update($validated);

        return redirect()->route('admin.quotes.index')
            ->with('success', 'Citação atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $quote = \App\Models\Quote::findOrFail($id);
        $quote->delete();

        return redirect()->route('admin.quotes.index')
            ->with('success', 'Citação removida com sucesso!');
    }
}
