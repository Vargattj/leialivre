<?php

// ============================================
// app/Http/Controllers/ImportController.php
// ============================================

namespace App\Http\Controllers;

use App\Services\BookImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ImportController extends Controller
{
    protected $importService;

    public function __construct(BookImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Show import form
     */
    public function index()
    {
        return view('import.index');
    }

    /**
     * Search on Open Library
     */
    public function searchOpenLibrary(Request $request)
    {
        $query = trim((string)($request->input('query', $request->query('query')) ?? ''));
        if ($query === '' || mb_strlen($query) < 2) {
            return redirect()->route('import.index')->with('error', 'Informe ao menos 2 caracteres para buscar.');
        }

        try {
            $response = Http::get('https://openlibrary.org/search.json', [
                'q' => $query,
                'limit' => 20,
            ]);

            $data = $response->json();
            $books = $data['docs'] ?? [];

            return view('import.search-results', [
                'books' => $books,
                'query' => $query,
                'source' => 'openlibrary'
            ]);

        } catch (\Exception $e) {
            return redirect()->route('import.index')->with('error', 'Erro ao buscar: ' . $e->getMessage());
        }
    }

    /**
     * Search on Gutenberg
     */
    public function searchGutenberg(Request $request)
    {
        $query = trim((string)($request->input('query', $request->query('query')) ?? ''));
        if ($query === '' || mb_strlen($query) < 2) {
            return redirect()->route('import.index')->with('error', 'Informe ao menos 2 caracteres para buscar.');
        }

        try {
            $response = Http::get('https://gutendex.com/books', [
                'search' => $query,
                'languages' => 'pt',
            ]);

            $data = $response->json();
            $books = $data['results'] ?? [];

            return view('import.search-results', [
                'books' => $books,
                'query' => $query,
                'source' => 'gutenberg'
            ]);

        } catch (\Exception $e) {
            return redirect()->route('import.index')->with('error', 'Erro ao buscar: ' . $e->getMessage());
        }
    }

    /**
     * Import selected book
     */
    public function import(Request $request)
    {
        $request->validate([
            'source' => 'required|in:openlibrary,gutenberg',
            'id' => 'required|string',
        ]);

        try {
            $source = $request->input('source');
            $id = $request->input('id');
            Log::info($source);
            Log::info($id);
            if ($source === 'openlibrary') {
                $book = $this->importService->importFromOpenLibrary($id);
                Log::info($book);
            } else {
                $book = $this->importService->importFromGutenberg((int)$id);
            }

            return redirect()
                ->route('livros.show', $book->slug)
                ->with('success', "Book '{$book->title}' imported successfully!");

        } catch (\Exception $e) {
            Log::error('Import error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error importing: ' . $e->getMessage());
        }
    }

    /**
     * Batch import Brazilian classics
     */
    public function importBrazilian()
    {
        try {
            $result = $this->importService->importBrazilianClassics();

            $message = "Imported " . count($result['imported']) . " books successfully.";
            
            if (!empty($result['failed'])) {
                $message .= " Failed: " . count($result['failed']);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Error in batch import: ' . $e->getMessage());
        }
    }
}





