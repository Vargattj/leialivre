<!-- ============================================ -->
<!-- resources/views/livros/index.blade.php -->
<!-- Lista de livros -->
<!-- ============================================ -->

@extends('layouts.app')

@section('title')
    @if(isset($category))
        {{ $category->name }} - Livros em Domínio Público
    @elseif(isset($term))
        Busca: "{{ $term }}" - Livros em Domínio Público
    @else
        Biblioteca de Livros em Domínio Público
    @endif
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    @if(isset($category))
        <h1 class="text-4xl font-bold mb-8">Livros em: {{ $category->name }}</h1>
    @elseif(isset($term))
        <h1 class="text-4xl font-bold mb-8">Resultados da busca: "{{ $term }}"</h1>
    @else
        <h1 class="text-4xl font-bold mb-8">Livros em Domínio Público</h1>
    @endif

    <!-- Formulário de busca -->
    <form action="{{ route('livros.buscar') }}" method="GET" class="mb-8">
        <div class="flex gap-2">
            <input 
                type="text" 
                name="q" 
                placeholder="Buscar livros ou autores..." 
                class="flex-1 px-4 py-2 border rounded-lg"
                value="{{ request('q') }}"
            >
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Buscar
            </button>
        </div>
    </form>

    <!-- Grade de livros -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($books as $book)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
            <!-- Capa do livro -->
            @if($book->cover_thumbnail_url)
            <img 
                src="{{ $book->cover_thumbnail_url }}" 
                alt="{{ $book->title }}"
                class="w-full h-64 object-cover"
            >
            @else
            <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                <span class="text-6xl">📚</span>
            </div>
            @endif

            <!-- Informações -->
            <div class="p-4">
                <h3 class="font-bold text-lg mb-2 line-clamp-2">
                  <a href="{{ route('livros.show', $book->slug) }}" class="hover:text-blue-600">
                        {{ $book->title }}
                    </a> 
                </h3>
                
                <p class="text-gray-600 text-sm mb-2">
                    {{ $book->authors_names }}
                </p>

                <p class="text-gray-500 text-xs mb-3 line-clamp-2">
                    {{ $book->synopsis }}
                </p>

                <!-- Badges de categoria -->
                <div class="flex flex-wrap gap-1 mb-3">
                    @foreach($book->categories->take(2) as $categoria)
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">
                        {{ $categoria->name }}
                    </span>
                    @endforeach
                </div>

                <!-- Formatos disponíveis -->
                {{-- <div class="flex gap-2">
                    @foreach($book->available_at as $formato)
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">
                        {{ $formato }}
                    </span>
                    @endforeach
                </div> --}}

                <!-- Estatísticas -->
                <div class="flex justify-between items-center mt-3 text-xs text-gray-500">
                    <span>📥 {{ number_format($book->total_downloads) }} downloads</span>
                    @if($book->average_rating > 0)
                    <span>⭐ {{ number_format($book->average_rating, 1) }}</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Paginação -->
    <div class="mt-8">
        {{ $books->links() }}
    </div>
</div>
@endsection




