<!-- ============================================ -->
<!-- resources/views/autores/show.blade.php -->
<!-- Perfil do autor -->
<!-- ============================================ -->

@extends('layouts.app')

@section('title', $author->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Cabeçalho do autor -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-8">
        <div class="flex items-start gap-6">
            @if($author->photo_url)
            <img 
                src="{{ $author->photo_url }}" 
                alt="{{ $author->name }}"
                class="w-32 h-32 rounded-full object-cover"
            >
            @else
            <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center">
                <span class="text-5xl">👤</span>
            </div>
            @endif

            <div class="flex-1">
                <h1 class="text-4xl font-bold mb-2">{{ $author->name }}</h1>
                
                @if($author->full_name && $author->full_name !== $author->name)
                <p class="text-gray-600 mb-2">{{ $author->full_name }}</p>
                @endif

                <div class="flex gap-4 text-sm text-gray-600 mb-4">
                    @if($author->birth_date)
                    <span>📅 {{ $author->birth_date->format('d/m/Y') }}</span>
                    @endif
                    
                    @if($author->death_date)
                    <span>† {{ $author->death_date->format('d/m/Y') }}</span>
                    @endif

                    @if($author->nationality)
                    <span>🌍 {{ $author->nationality }}</span>
                    @endif
                </div>

                @if($author->is_public_domain)
                <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                    ✓ Obras em Domínio Público
                </span>
                @endif
            </div>
        </div>

        @if($author->biography)
        <div class="mt-6">
            <h2 class="text-xl font-bold mb-3">Biografia</h2>
            <p class="text-gray-700 leading-relaxed">{{ $author->biography }}</p>
        </div>
        @endif
    </div>

    <!-- Obras do autor -->
    <div>
        <h2 class="text-2xl font-bold mb-6">
            Obras de {{ $author->name }} 
            <span class="text-gray-500 text-lg">({{ $author->books->count() }})</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($author->books as $livro)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                @if($livro->cover_thumbnail_url)
                <img 
                    src="{{ $livro->cover_thumbnail_url }}" 
                    alt="{{ $livro->title }}"
                    class="w-full h-64 object-cover"
                >
                @else
                <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                    <span class="text-6xl">📚</span>
                </div>
                @endif

                <div class="p-4">
                    <h3 class="font-bold text-lg mb-2">
                        <a href="{{ route('livros.show', $livro->slug) }}" class="hover:text-blue-600">
                            {{ $livro->title }}
                        </a>
                    </h3>

                    @if($livro->publication_year)
                    <p class="text-gray-600 text-sm mb-2">{{ $livro->publication_year }}</p>
                    @endif

                    <div class="flex gap-2">
                        @foreach($livro->available_formats as $formato)
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">
                            {{ $formato }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
