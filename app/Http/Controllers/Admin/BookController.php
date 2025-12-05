<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Book::with(['mainAuthors', 'categories', 'activeFiles']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('subtitle', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('categories', fn($q) => $q->where('id', $request->category));
        }

        $books = $query->latest()->paginate(15);
        $categories = Category::orderBy('name')->get();

        return view('admin.books.index', compact('books', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $authors = Author::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        
        return view('admin.books.create', compact('authors', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'synopsis' => 'nullable|string',
            'cover' => 'nullable|image|max:2048',
            'authors' => 'required|array',
            'authors.*' => 'exists:authors,id',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            // Files validation
            'files' => 'nullable|array',
            'files.*.format' => 'required|string|in:PDF,EPUB,MOBI,TXT',
            'files.*.url' => 'required|url',
        ]);

        // Handle Cover
        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('covers', 'public');
            $validated['cover_url'] = Storage::url($path);
        }

        // Create Book
        $book = Book::create($validated);

        // Sync Relationships
        $book->authors()->sync($request->authors);
        
        // Sync Categories (First one is primary)
        $categoriesData = [];
        foreach ($request->categories as $index => $categoryId) {
            $categoriesData[$categoryId] = ['is_primary' => $index === 0];
        }
        $book->categories()->sync($categoriesData);

        // Handle Files
        if (!empty($request->files_data)) {
            foreach ($request->files_data as $fileData) {
                if (!empty($fileData['url']) && !empty($fileData['format'])) {
                    $book->files()->create([
                        'format' => $fileData['format'],
                        'file_url' => $fileData['url'],
                        'is_active' => true
                    ]);
                }
            }
        }

        return redirect()->route('admin.books.index')
            ->with('success', 'Livro criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        return view('admin.books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        $authors = Author::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $book->load(['authors', 'categories', 'files']);

        return view('admin.books.edit', compact('book', 'authors', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'synopsis' => 'nullable|string',
            'cover' => 'nullable|image|max:2048',
            'authors' => 'required|array',
            'authors.*' => 'exists:authors,id',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            // Files validation handled manually for simplicity in update
        ]);

        // Handle Cover
        if ($request->hasFile('cover')) {
            if ($book->cover_url) {
                $oldPath = str_replace('/storage/', '', $book->cover_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('cover')->store('covers', 'public');
            $validated['cover_url'] = Storage::url($path);
        }

        $book->update($validated);

        // Sync Relationships
        $book->authors()->sync($request->authors);
        
        $categoriesData = [];
        foreach ($request->categories as $index => $categoryId) {
            $categoriesData[$categoryId] = ['is_primary' => $index === 0];
        }
        $book->categories()->sync($categoriesData);

        // Handle Files (Add new ones)
        if ($request->has('new_files')) {
            foreach ($request->new_files as $fileData) {
                if (!empty($fileData['url']) && !empty($fileData['format'])) {
                    $book->files()->create([
                        'format' => $fileData['format'],
                        'file_url' => $fileData['url'],
                        'is_active' => true
                    ]);
                }
            }
        }

        // Handle File Deletion
        if ($request->has('delete_files')) {
            $book->files()->whereIn('id', $request->delete_files)->delete();
        }

        return redirect()->route('admin.books.index')
            ->with('success', 'Livro atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        if ($book->cover_url) {
            $oldPath = str_replace('/storage/', '', $book->cover_url);
            Storage::disk('public')->delete($oldPath);
        }

        $book->files()->delete();
        $book->delete();

        return redirect()->route('admin.books.index')
            ->with('success', 'Livro excluído com sucesso!');
    }
}
