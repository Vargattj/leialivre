?>

<!-- ============================================ -->
<!-- resources/views/import/index.blade.php -->
<!-- ============================================ -->
@extends('layouts.app')

@section('title', 'Import Books')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold mb-8">📚 Import Books from Public APIs</h1>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
        ✓ {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">
        ✗ {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Open Library Search -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center mb-4">
                <img src="https://openlibrary.org/static/images/openlibrary-logo-tighter.svg" 
                     alt="Open Library" 
                     class="h-8 mr-3">
                <h2 class="text-2xl font-bold">Open Library</h2>
            </div>
            
            <p class="text-gray-600 mb-6">
                Search millions of books, including many Brazilian classics.
            </p>

            <form action="{{ route('import.search.openlibrary') }}" method="GET">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Search by title or author:</label>
                    <input 
                        type="text" 
                        name="query" 
                        placeholder="e.g., Machado de Assis" 
                        class="w-full px-4 py-2 border rounded-lg"
                        value="{{ request('query') }}"
                        minlength="2"
                        required
                    >
                </div>
                <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    🔍 Search on Open Library
                </button>
            </form>
        </div>

        <!-- Project Gutenberg Search -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center mb-4">
                <span class="text-4xl mr-3">📖</span>
                <h2 class="text-2xl font-bold">Project Gutenberg</h2>
            </div>
            
            <p class="text-gray-600 mb-6">
                70,000+ free eBooks in the public domain.
            </p>

            <form action="{{ route('import.search.gutenberg') }}" method="GET">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Search by title or author:</label>
                    <input 
                        type="text" 
                        name="query" 
                        placeholder="e.g., Shakespeare" 
                        class="w-full px-4 py-2 border rounded-lg"
                        value="{{ request('query') }}"
                        minlength="2"
                        required
                    >
                </div>
                <button type="submit" class="w-full px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    🔍 Search on Gutenberg
                </button>
            </form>
        </div>
    </div>

    <!-- Batch Import Section -->
    {{-- <div class="mt-8 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg shadow-md p-6 border border-green-200">
        <h2 class="text-2xl font-bold mb-4">🇧🇷 Quick Import: Brazilian Classics</h2>
        
        <p class="text-gray-700 mb-4">
            Import a curated collection of Brazilian literature classics with one click:
        </p>

        <ul class="mb-6 space-y-1 text-gray-700">
            <li>• Dom Casmurro - Machado de Assis</li>
            <li>• Memórias Póstumas de Brás Cubas - Machado de Assis</li>
            <li>• Quincas Borba - Machado de Assis</li>
            <li>• And more...</li>
        </ul>

        <form action="{{ route('import.brazilian') }}" method="POST" 
              onsubmit="return confirm('This will import multiple books. Continue?')">
            @csrf
            <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                📚 Import Brazilian Classics Collection
            </button>
        </form>

        <p class="mt-4 text-sm text-gray-600">
            ⚠️ This process may take a few minutes. Books will be automatically categorized.
        </p>
    </div> --}}

    <!-- API Information -->
    <div class="mt-8 bg-gray-50 rounded-lg p-6">
        <h3 class="text-xl font-bold mb-4">📌 About the APIs</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-semibold mb-2">Open Library</h4>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Millions of books worldwide</li>
                    <li>• Rich metadata and covers</li>
                    <li>• Author biographies</li>
                    <li>• Multiple languages</li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold mb-2">Project Gutenberg</h4>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• 70,000+ free eBooks</li>
                    <li>• Multiple formats (EPUB, PDF, TXT)</li>
                    <li>• Direct download links</li>
                    <li>• Classic literature focus</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection