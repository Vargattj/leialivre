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
            $query->where(function ($q) use ($search) {
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
            'title'            => 'required|string|max:255',
            'subtitle'         => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'pages'            => 'nullable|integer|min:1',
            'original_language'=> 'nullable|string|max:255',
            'synopsis'         => 'nullable|string',
            'cover'            => 'nullable|image|max:2048',
            'authors'          => 'required|array',
            'authors.*'        => 'exists:authors,id',
            'categories'       => 'required|array',
            'categories.*'     => 'exists:categories,id',
            'purchase_url'     => 'nullable|url|max:2048',
            // Validação de arquivos
            'files_data'                => 'nullable|array',
            'files_data.*.format'       => 'required_with:files_data.*|string|in:PDF,EPUB,MOBI,TXT',
            'files_data.*.source_type'  => 'required_with:files_data.*|string|in:url,upload',
            'files_data.*.url'          => 'nullable|url|max:2048',
            'files_data.*.file'         => 'nullable|file|max:102400|extensions:pdf,epub,mobi,txt',
        ]);

        // Handle Cover
        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('covers');
            $validated['cover_url'] = Storage::url($path);
        }

        $validated['use_generated_cover'] = $request->boolean('use_generated_cover');

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
            foreach ($request->files_data as $index => $fileData) {
                if (empty($fileData['format'])) {
                    continue;
                }

                $this->createBookFile($book, $fileData, $index, $request);
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
        $book->load(['authors', 'categories', 'files', 'faqs']);

        return view('admin.books.edit', compact('book', 'authors', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'subtitle'         => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'pages'            => 'nullable|integer|min:1',
            'original_language'=> 'nullable|string|max:255',
            'synopsis'         => 'nullable|string',
            'cover'            => 'nullable|image|max:2048',
            'authors'          => 'required|array',
            'authors.*'        => 'exists:authors,id',
            'categories'       => 'required|array',
            'categories.*'     => 'exists:categories,id',
            'purchase_url'     => 'nullable|url|max:2048',
            // Validação de novos arquivos
            'new_files'                => 'nullable|array',
            'new_files.*.format'       => 'required_with:new_files.*|string|in:PDF,EPUB,MOBI,TXT',
            'new_files.*.source_type'  => 'required_with:new_files.*|string|in:url,upload',
            'new_files.*.url'          => 'nullable|url|max:2048',
            'new_files.*.file'         => 'nullable|file|max:102400|extensions:pdf,epub,mobi,txt',
        ]);

        // Handle Cover
        if ($request->hasFile('cover')) {
            if ($book->cover_url) {
                $oldPath = str_replace('/storage/', '', $book->cover_url);
                Storage::delete($oldPath);
            }
            $path = $request->file('cover')->store('covers');
            $validated['cover_url'] = Storage::url($path);
        }

        $validated['use_generated_cover'] = $request->boolean('use_generated_cover');

        $book->update($validated);

        // Sync Relationships
        $book->authors()->sync($request->authors);

        $categoriesData = [];
        foreach ($request->categories as $index => $categoryId) {
            $categoriesData[$categoryId] = ['is_primary' => $index === 0];
        }
        $book->categories()->sync($categoriesData);

        // Handle File Deletion (exclui do bucket se necessário)
        if ($request->has('delete_files')) {
            $filesToDelete = $book->files()->whereIn('id', $request->delete_files)->get();
            foreach ($filesToDelete as $file) {
                if ($file->storage_path) {
                    Storage::disk('r2')->delete($file->storage_path);
                }
                $file->delete();
            }
        }

        // Handle new files
        if ($request->has('new_files')) {
            foreach ($request->new_files as $index => $fileData) {
                if (empty($fileData['format'])) {
                    continue;
                }

                $this->createBookFile($book, $fileData, $index, $request, 'new_files');
            }
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
            Storage::delete($oldPath);
        }

        // Remove arquivos do bucket antes de deletar os registros
        foreach ($book->files as $file) {
            if ($file->storage_path) {
                Storage::disk('r2')->delete($file->storage_path);
            }
        }

        $book->files()->delete();
        $book->delete();

        return redirect()->route('admin.books.index')
            ->with('success', 'Livro excluído com sucesso!');
    }

    /**
     * Cria um registro de arquivo para um livro.
     * Lida com upload para o bucket (prioridade) ou URL externa.
     */
    private function createBookFile(Book $book, array $fileData, int $index, Request $request, string $inputName = 'files_data'): void
    {
        $format      = strtoupper($fileData['format']);
        $sourceType  = $fileData['source_type'] ?? 'url';
        $storagePath = null;
        $fileUrl     = null;

        if ($sourceType === 'upload') {
            $uploadedFile = $request->file("{$inputName}.{$index}.file");
            if ($uploadedFile) {
                $extension   = strtolower($uploadedFile->getClientOriginalExtension());
                $storagePath = "books/{$book->id}/" . strtolower($format) . '/' . Str::uuid() . ".{$extension}";

                Storage::disk('r2')->put($storagePath, file_get_contents($uploadedFile->getRealPath()), 'public');
            }
        } else {
            $fileUrl = $fileData['url'] ?? null;
        }

        // Só cria o registro se há ao menos uma fonte válida
        if (!$storagePath && !$fileUrl) {
            return;
        }

        $book->files()->create([
            'format'       => $format,
            'file_url'     => $fileUrl,
            'storage_path' => $storagePath,
            'is_active'    => true,
        ]);
    }
}
