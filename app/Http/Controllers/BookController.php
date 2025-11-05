<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Author;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // List all active books
    public function index(Request $request)
    {
        $query = Book::with(['authors', 'mainAuthors', 'categories', 'activeFiles'])
            ->active();

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.slug', $request->category);
            });
        }

        // Sorting
        $sort = $request->input('sort', 'relevance');
        switch ($sort) {
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'author':
                // Order by first author name
                $query->orderByRaw('(SELECT name FROM authors WHERE id IN (SELECT author_id FROM book_author WHERE book_id = books.id LIMIT 1)) ASC');
                break;
            case 'year':
                $query->orderBy('publication_year', 'desc');
                break;
            case 'downloads':
                $query->orderBy('total_downloads', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            default: // relevance
                $query->orderBy('total_downloads', 'desc');
                break;
        }

        $books = $query->paginate(18);

        // Get popular categories for filters
        $popularCategories = Category::whereHas('books', function($q) {
                $q->where('is_active', true);
            })
            ->withCount(['books' => function($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('books_count', 'desc')
            ->take(7)
            ->get();

        // Calculate stats
        $totalBooks = Book::where('is_active', true)->count();
        $totalAuthors = Author::whereHas('books', function($q) {
            $q->where('is_active', true);
        })->distinct()->count();

        return view('livros.index', compact('books', 'popularCategories', 'totalBooks', 'totalAuthors', 'sort'));
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
        
        $query = Book::with(['authors', 'categories', 'activeFiles'])
            ->active()
            ->search($term);

        // Sorting
        $sort = $request->input('sort', 'relevance');
        switch ($sort) {
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'author':
                // Order by first author name
                $query->orderByRaw('(SELECT name FROM authors WHERE id IN (SELECT author_id FROM book_author WHERE book_id = books.id LIMIT 1)) ASC');
                break;
            case 'year':
                $query->orderBy('publication_year', 'desc');
                break;
            case 'downloads':
                $query->orderBy('total_downloads', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            default: // relevance
                $query->orderBy('total_downloads', 'desc');
                break;
        }

        $books = $query->paginate(18);

        // Get popular categories for filters
        $popularCategories = Category::whereHas('books', function($q) {
                $q->where('is_active', true);
            })
            ->withCount(['books' => function($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('books_count', 'desc')
            ->take(7)
            ->get();

        // Calculate stats
        $totalBooks = Book::where('is_active', true)->count();
        $totalAuthors = Author::whereHas('books', function($q) {
            $q->where('is_active', true);
        })->distinct()->count();

        return view('livros.index', compact('books', 'term', 'popularCategories', 'totalBooks', 'totalAuthors', 'sort'));
    }

    // Books by category
    public function byCategory(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $query = $category->books()
            ->with(['authors', 'mainAuthors', 'categories', 'activeFiles'])
            ->active();

        // Sorting
        $sort = $request->input('sort', 'relevance');
        switch ($sort) {
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'author':
                // Order by first author name
                $query->orderByRaw('(SELECT name FROM authors WHERE id IN (SELECT author_id FROM book_author WHERE book_id = books.id LIMIT 1)) ASC');
                break;
            case 'year':
                $query->orderBy('publication_year', 'desc');
                break;
            case 'downloads':
                $query->orderBy('total_downloads', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            default: // relevance
                $query->orderBy('total_downloads', 'desc');
                break;
        }

        $books = $query->paginate(18);

        // Get popular categories for filters
        $popularCategories = Category::whereHas('books', function($q) {
                $q->where('is_active', true);
            })
            ->withCount(['books' => function($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('books_count', 'desc')
            ->take(7)
            ->get();

        // Calculate stats
        $totalBooks = Book::where('is_active', true)->count();
        $totalAuthors = Author::whereHas('books', function($q) {
            $q->where('is_active', true);
        })->distinct()->count();

        return view('livros.index', compact('category', 'books', 'popularCategories', 'totalBooks', 'totalAuthors', 'sort'));
    }

    // Most downloaded books
    public function mostDownloaded(Request $request)
    {
        $query = Book::with(['authors', 'categories', 'activeFiles'])
            ->active()
            ->orderBy('total_downloads', 'desc');

        $books = $query->paginate(18);

        // Get popular categories for filters
        $popularCategories = Category::whereHas('books', function($q) {
                $q->where('is_active', true);
            })
            ->withCount(['books' => function($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('books_count', 'desc')
            ->take(7)
            ->get();

        // Calculate stats
        $totalBooks = Book::where('is_active', true)->count();
        $totalAuthors = Author::whereHas('books', function($q) {
            $q->where('is_active', true);
        })->distinct()->count();

        $sort = 'downloads';

        return view('livros.index', compact('books', 'popularCategories', 'totalBooks', 'totalAuthors', 'sort'));
    }

    // Featured books (Home page)
    public function featured()
    {
        // Get featured books first, then fill with most downloaded if needed
        $featuredBooks = Book::with(['authors', 'categories', 'activeFiles'])
            ->active()
            ->featured()
            ->orderBy('total_downloads', 'desc')
            ->take(6)
            ->get();

        // If we don't have enough featured books, fill with most downloaded
        if ($featuredBooks->count() < 6) {
            $mostDownloaded = Book::with(['authors', 'categories', 'activeFiles'])
                ->active()
                ->whereNotIn('id', $featuredBooks->pluck('id'))
                ->orderBy('total_downloads', 'desc')
                ->take(6 - $featuredBooks->count())
                ->get();
            
            $featuredBooks = $featuredBooks->merge($mostDownloaded);
        }

        // Get featured authors (authors with most active books)
        $featuredAuthors = Author::whereHas('books', function($query) {
                $query->where('is_active', true);
            })
            ->with(['books' => function($query) {
                $query->where('is_active', true);
            }])
            ->withCount(['books' => function($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('books_count', 'desc')
            ->take(3)
            ->get()
            ->map(function($author) {
                // Calculate total downloads for this author's active books
                $totalDownloads = $author->books()
                    ->where('is_active', true)
                    ->sum('total_downloads');
                
                $author->total_downloads = $totalDownloads;
                return $author;
            });

        return view('welcome', compact('featuredBooks', 'featuredAuthors'));
    }
}
