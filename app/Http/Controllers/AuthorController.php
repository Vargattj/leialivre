<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    // List authors
    public function index()
    {
        $authors = Author::withCount('books')
            ->withBooks()
            ->orderBy('name')
            ->paginate(30);

        return view('autores.index', compact('authors'));
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
    public function brazilian()
    {
        $authors = Author::brazilian()
            ->withCount('books')
            ->withBooks()
            ->orderBy('name')
            ->paginate(30);

        return view('autores.index', compact('authors'));
    }
}

