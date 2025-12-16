<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::whereHas('books', function($q) {
            $q->active();
        })->withCount(['books' => function($q) {
            $q->active();
        }]);

        // Search
        $search = $request->input('q');
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Sorting
        $sort = $request->input('sort', 'name');
        switch ($sort) {
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'books_count':
                $query->orderBy('books_count', 'desc');
                break;
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $categories = $query->paginate(24)->withQueryString();

        return view('categorias.index', compact('categories', 'sort', 'search'));
    }
}
