<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    // List authors
    public function index(Request $request)
    {
        $categorySlug = $request->input('category');
        
        $query = Author::whereHas('books', function($q) {
                $q->where('is_active', true);
            })
            ->with(['books' => function($query) {
                $query->where('is_active', true)->with('categories');
            }])
            ->withCount(['books' => function($query) {
                $query->where('is_active', true);
            }]);
        
        // Filter by category if provided
        if ($categorySlug) {
            $query->whereHas('books.categories', function($q) use ($categorySlug) {
                $q->where('categories.slug', $categorySlug);
            });
        }
        
        $authors = $query->orderBy('name')
            ->paginate(30)
            ->withQueryString();
        
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
        
        // Calculate statistics
        $stats = [
            'total_authors' => Author::whereHas('books', function($q) {
                $q->where('is_active', true);
            })->count(),
            'total_works' => \App\Models\Book::where('is_active', true)->count(),
            'total_genres' => Category::whereHas('books', function($q) {
                $q->where('is_active', true);
            })->count(),
            'total_downloads' => \App\Models\Book::where('is_active', true)->sum('total_downloads'),
        ];
        
        // Calculate total downloads for each author
        $authors->getCollection()->transform(function($author) {
            $author->total_downloads = $author->books()
                ->where('is_active', true)
                ->sum('total_downloads');
            return $author;
        });

        return view('autores.index', compact('authors', 'popularCategories', 'stats', 'categorySlug'));
    }

    // Author profile
    public function show($slug)
    {
        $author = Author::where('slug', $slug)
            ->with(['books' => function($query) {
                $query->active()->with(['categories', 'activeFiles']);
            }])
            ->firstOrFail();

        return view('autores.show', compact('author'));
    }

    // Brazilian authors
    public function brazilian(Request $request)
    {
        $categorySlug = $request->input('category');
        
        $query = Author::brazilian()
            ->whereHas('books', function($q) {
                $q->where('is_active', true);
            })
            ->with(['books' => function($query) {
                $query->where('is_active', true)->with('categories');
            }])
            ->withCount(['books' => function($query) {
                $query->where('is_active', true);
            }]);
        
        // Filter by category if provided
        if ($categorySlug) {
            $query->whereHas('books.categories', function($q) use ($categorySlug) {
                $q->where('categories.slug', $categorySlug);
            });
        }
        
        $authors = $query->orderBy('name')
            ->paginate(30)
            ->withQueryString();
        
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
        
        // Calculate statistics for Brazilian authors
        $stats = [
            'total_authors' => Author::brazilian()
                ->whereHas('books', function($q) {
                    $q->where('is_active', true);
                })->count(),
            'total_works' => \App\Models\Book::where('is_active', true)
                ->whereHas('authors', function($q) {
                    $q->where('nationality', 'Brazil');
                })->count(),
            'total_genres' => Category::whereHas('books', function($q) {
                $q->where('is_active', true)
                  ->whereHas('authors', function($q) {
                      $q->where('nationality', 'Brazil');
                  });
            })->count(),
            'total_downloads' => \App\Models\Book::where('is_active', true)
                ->whereHas('authors', function($q) {
                    $q->where('nationality', 'Brazil');
                })
                ->sum('total_downloads'),
        ];
        
        // Calculate total downloads for each author
        $authors->getCollection()->transform(function($author) {
            $author->total_downloads = $author->books()
                ->where('is_active', true)
                ->sum('total_downloads');
            return $author;
        });

        return view('autores.index', compact('authors', 'popularCategories', 'stats', 'categorySlug'));
    }
}

