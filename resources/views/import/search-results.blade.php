<!-- ============================================ -->
<!-- resources/views/import/search-results.blade.php -->
<!-- ============================================ -->

@extends('layouts.app')

@section('title', 'Search Results - Import Books')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('import.index') }}" class="text-blue-600 hover:underline">
            ← Back to Import
        </a>
    </div>

    <h1 class="text-3xl font-bold mb-2">Search Results</h1>
    <p class="text-gray-600 mb-8">
        Found {{ count($books) }} results for "{{ $query }}"
    </p>

    @if(empty($books))
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <p class="text-yellow-800">No books found. Try a different search term.</p>
    </div>
    @else
    <div class="grid grid-cols-1 gap-6">
        @foreach($books as $book)
        <div class="bg-white rounded-lg shadow-md p-6 flex gap-6">
            <!-- Cover -->
            <div class="flex-shrink-0">
                @if($source === 'openlibrary' && isset($book['cover_i']))
                <img 
                    src="https://covers.openlibrary.org/b/id/{{ $book['cover_i'] }}-M.jpg" 
                    alt="Cover"
                    class="w-24 h-36 object-cover rounded"
                >
                @elseif($source === 'gutenberg' && isset($book['formats']['image/jpeg']))
                <img 
                    src="{{ $book['formats']['image/jpeg'] }}" 
                    alt="Cover"
                    class="w-24 h-36 object-cover rounded"
                >
                @else
                <div class="w-24 h-36 bg-gray-200 rounded flex items-center justify-center">
                    <span class="text-4xl">📚</span>
                </div>
                @endif
            </div>

            <!-- Info -->
            <div class="flex-1">
                <h3 class="text-xl font-bold mb-2">
                    {{ $book['title'] ?? 'Unknown Title' }}
                </h3>

                @if($source === 'openlibrary')
                    <p class="text-gray-600 mb-2">
                        <strong>Author:</strong> 
                        {{ isset($book['author_name']) ? implode(', ', $book['author_name']) : 'Unknown' }}
                    </p>
                    <p class="text-gray-600 mb-2">
                        <strong>First Published:</strong> 
                        {{ $book['first_publish_year'] ?? 'Unknown' }}
                    </p>
                @elseif($source === 'gutenberg')
                    <p class="text-gray-600 mb-2">
                        <strong>Author:</strong> 
                        {{ isset($book['authors'][0]) ? $book['authors'][0]['name'] : 'Unknown' }}
                    </p>
                    <p class="text-gray-600 mb-2">
                        <strong>Downloads:</strong> 
                        {{ number_format($book['download_count'] ?? 0) }}
                    </p>
                    <p class="text-gray-600 mb-2">
                        <strong>Languages:</strong> 
                        {{ implode(', ', $book['languages'] ?? []) }}
                    </p>
                @endif

                @if(isset($book['subject']) && !empty($book['subject']))
                <div class="mb-3">
                    <strong class="text-sm text-gray-600">Subjects:</strong>
                    <div class="flex flex-wrap gap-1 mt-1">
                        @foreach(array_slice($book['subject'], 0, 5) as $subject)
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">
                            {{ $subject }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Import Button -->
            <div class="flex-shrink-0 flex items-center">
                <form action="{{ route('import.do') }}" method="POST">
                    @csrf
                    <input type="hidden" name="source" value="{{ $source }}">
                    @if($source === 'openlibrary')
                        <input type="hidden" name="id" value="{{ $book['key'] }}">
                    @else
                        <input type="hidden" name="id" value="{{ $book['id'] }}">
                    @endif
                    
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 whitespace-nowrap"
                    >
                        📥 Import Book
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection