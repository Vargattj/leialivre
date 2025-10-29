<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // List all active books
    public function index()
    {
        $books = Book::with(['authors', 'categories', 'activeFiles'])
            ->active()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livros.index', compact('books'));
    }

    // Show book details
    public function show($slug)
    {
        $book = Book::with([
            'authors', 
            'categories', 
            'tags', 
            'activeFiles'
        ])
        ->where('slug', $slug)
        ->firstOrFail();

        // Increment views
        $book->incrementViews();

        return view('livros.show', compact('book'));
    }

    // Search books
    public function search(Request $request)
    {
        $term = $request->input('q');
        
        $books = Book::with(['authors', 'categories'])
            ->active()
            ->search($term)
            ->paginate(20);

        return view('livros.index', compact('books', 'term'));
    }

    // Books by category
    public function byCategory($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $books = $category->books()
            ->with(['authors', 'activeFiles'])
            ->active()
            ->paginate(20);

        return view('livros.index', compact('category', 'books'));
    }

    // Most downloaded books
    public function mostDownloaded()
    {
        $books = Book::with(['authors', 'categories'])
            ->active()
            ->orderBy('total_downloads', 'desc')
            ->paginate(20);

        return view('livros.index', compact('books'));
    }

    // Featured books
    public function featured()
    {
        $books = Book::with(['authors', 'categories', 'activeFiles'])
            ->featured()
            ->active()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livros.index', compact('books'));
    }
}
