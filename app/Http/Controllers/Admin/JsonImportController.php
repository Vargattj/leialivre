<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class JsonImportController extends Controller
{
    /**
     * Campos permitidos para importação do modelo Book
     */
    protected $allowedBookFields = [
        'title',
        'subtitle',
        'original_title',
        'publication_year',
        'original_publisher',
        'original_language',
        'synopsis',
        'full_description',
        'isbn',
        'pages',
        'is_public_domain',
        'public_domain_year',
        'public_domain_justification',
        'cover_url',
        'cover_thumbnail_url',
    ];

    /**
     * Display the JSON import form.
     */
    public function index()
    {
        $authors = Author::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.import-json.index', compact('authors', 'categories'));
    }

    /**
     * Preview the JSON data before importing.
     */
    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'json_data' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'O campo JSON é obrigatório.',
            ], 422);
        }

        $jsonData = json_decode($request->json_data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                'success' => false,
                'message' => 'JSON inválido: ' . json_last_error_msg(),
            ], 422);
        }

        // Ensure it's an array
        if (!is_array($jsonData)) {
            $jsonData = [$jsonData];
        }

        // If it's an associative array (single book), wrap it
        if (!empty($jsonData) && !isset($jsonData[0])) {
            $jsonData = [$jsonData];
        }

        $books = [];
        $errors = [];

        foreach ($jsonData as $index => $bookData) {
            if (!isset($bookData['title']) || empty($bookData['title'])) {
                $errors[] = "Livro #" . ($index + 1) . ": O campo 'title' é obrigatório.";
                continue;
            }

            // Filter only allowed fields
            $filteredBook = [];
            foreach ($this->allowedBookFields as $field) {
                if (isset($bookData[$field])) {
                    $filteredBook[$field] = $bookData[$field];
                }
            }

            $books[] = $filteredBook;
        }

        return response()->json([
            'success' => true,
            'books' => $books,
            'total' => count($books),
            'errors' => $errors,
        ]);
    }

    /**
     * Import books from JSON.
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'json_data' => 'required|string',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
        ], [
            'json_data.required' => 'O campo JSON é obrigatório.',
            'author_id.required' => 'Selecione um autor.',
            'author_id.exists' => 'O autor selecionado não existe.',
            'category_id.required' => 'Selecione uma categoria.',
            'category_id.exists' => 'A categoria selecionada não existe.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $jsonData = json_decode($request->json_data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()
                ->with('error', 'JSON inválido: ' . json_last_error_msg())
                ->withInput();
        }

        // Normalize to array
        if (!is_array($jsonData)) {
            $jsonData = [$jsonData];
        }

        if (!empty($jsonData) && !isset($jsonData[0])) {
            $jsonData = [$jsonData];
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($jsonData as $index => $bookData) {
                if (!isset($bookData['title']) || empty($bookData['title'])) {
                    $skipped++;
                    $errors[] = "Livro #" . ($index + 1) . ": Título obrigatório.";
                    continue;
                }

                // Filter only allowed fields
                $filteredBook = [];
                foreach ($this->allowedBookFields as $field) {
                    if (isset($bookData[$field])) {
                        $filteredBook[$field] = $bookData[$field];
                    }
                }

                // Download cover image if URL is provided
                if (!empty($filteredBook['cover_url'])) {
                    $downloadedCover = $this->downloadImage($filteredBook['cover_url'], 'covers');
                    if ($downloadedCover) {
                        $filteredBook['cover_url'] = $downloadedCover;
                    }
                }

                // Download thumbnail image if URL is provided
                if (!empty($filteredBook['cover_thumbnail_url'])) {
                    $downloadedThumbnail = $this->downloadImage($filteredBook['cover_thumbnail_url'], 'covers/thumbnails');
                    if ($downloadedThumbnail) {
                        $filteredBook['cover_thumbnail_url'] = $downloadedThumbnail;
                    }
                }

                // Generate slug
                $filteredBook['slug'] = Str::slug($filteredBook['title']);

                // Check for duplicate slug
                $existingSlug = Book::where('slug', $filteredBook['slug'])->exists();
                if ($existingSlug) {
                    $filteredBook['slug'] = $filteredBook['slug'] . '-' . Str::random(4);
                }

                // Set defaults
                $filteredBook['is_active'] = true;

                // Create the book
                $book = Book::create($filteredBook);

                // Attach author
                $book->authors()->attach($request->author_id, [
                    'contribution_type' => 'author',
                    'order' => 1,
                ]);

                // Attach category (as primary)
                $book->categories()->attach($request->category_id, [
                    'is_primary' => true,
                ]);

                $imported++;
            }

            DB::commit();

            $message = "{$imported} livro(s) importado(s) com sucesso!";
            if ($skipped > 0) {
                $message .= " {$skipped} livro(s) foram ignorados.";
            }

            return redirect()->route('admin.import-json.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erro ao importar livros: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Create a new author inline.
     */
    public function createAuthor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nationality' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $author = Author::create([
            'name' => $request->name,
            'nationality' => $request->nationality ?? 'Brasil',
        ]);

        return response()->json([
            'success' => true,
            'author' => [
                'id' => $author->id,
                'name' => $author->name,
            ],
        ]);
    }

    /**
     * Create a new category inline.
     */
    public function createCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
            ],
        ]);
    }

    /**
     * Download an image from a URL and save it to storage.
     * 
     * @param string $url The image URL
     * @param string $directory The storage directory (e.g., 'covers')
     * @return string|null The storage URL or null on failure
     */
    private function downloadImage($url, $directory = 'covers')
    {
        try {
            // Validate URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return null;
            }

            // Download the image
            $response = Http::timeout(30)->get($url);

            if (!$response->successful()) {
                return null;
            }

            // Get the image content
            $imageContent = $response->body();

            // Determine file extension from content type or URL
            $contentType = $response->header('Content-Type');
            $extension = $this->getExtensionFromContentType($contentType);

            if (!$extension) {
                // Fallback: try to get extension from URL
                $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
                $extension = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])
                    ? strtolower($extension)
                    : 'jpg';
            }

            // Generate unique filename
            $filename = Str::random(40) . '.' . $extension;
            $path = $directory . '/' . $filename;

            // Save to storage
            Storage::disk('public')->put($path, $imageContent);

            // Return the public URL
            return Storage::url($path);

        } catch (\Exception $e) {
            // Log error but don't fail the import
            \Log::warning('Failed to download image: ' . $url . ' - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get file extension from content type.
     * 
     * @param string|null $contentType
     * @return string|null
     */
    private function getExtensionFromContentType($contentType)
    {
        if (!$contentType) {
            return null;
        }

        $mimeMap = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];

        return $mimeMap[$contentType] ?? null;
    }
}
