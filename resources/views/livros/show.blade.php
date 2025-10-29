@extends('layouts.app')

@section('title', $book->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Coluna da capa -->
        <div>
            @if($book->cover_url)
            <img 
                src="{{ $book->cover_url }}" 
                alt="{{ $book->title }}"
                class="w-full rounded-lg shadow-lg"
            >
            @else
            <div class="w-full aspect-[3/4] bg-gray-200 rounded-lg flex items-center justify-center">
                <span class="text-8xl">📚</span>
            </div>
            @endif

            <!-- Botões de download -->
            <div class="mt-6 space-y-2">
                <h3 class="font-bold mb-3">Baixar este livro:</h3>
                @foreach($book->activeFiles as $arquivo)
                <a 
                    href="{{ route('download.file', $arquivo->id) }}"
                    class="block w-full px-4 py-3 bg-green-600 text-white text-center rounded-lg hover:bg-green-700 transition"
                >
                    {{ $arquivo->format_icon }} Baixar {{ $arquivo->format }}
                    <span class="text-sm opacity-80">({{ $arquivo->size_readable }})</span>
                </a>
                @endforeach
            </div>

            <!-- Estatísticas -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Downloads:</span>
                        <span class="font-semibold">{{ number_format($book->total_downloads) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Visualizações:</span>
                        <span class="font-semibold">{{ number_format($book->views) }}</span>
                    </div>
                    @if($book->average_rating > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Avaliação:</span>
                        <span class="font-semibold">⭐ {{ number_format($book->average_rating, 1) }}/5</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Coluna de informações -->
        <div class="md:col-span-2">
            <h1 class="text-4xl font-bold mb-2">{{ $book->title }}</h1>
            
            @if($book->subtitle)
            <p class="text-xl text-gray-600 mb-4">{{ $book->subtitle }}</p>
            @endif

            <!-- Autores -->
            <div class="mb-4">
                <span class="text-gray-600">Por:</span>
                @foreach($book->mainAuthors as $autor)
                <a href="{{ route('autores.show', $autor->slug) }}" class="text-blue-600 hover:underline font-semibold">
                    {{ $autor->name }}
                </a>@if(!$loop->last), @endif
                @endforeach
            </div>

            <!-- Categorias -->
            <div class="flex flex-wrap gap-2 mb-6">
                @foreach($book->categories as $categoria)
                <a 
                    href="{{ route('livros.categorias', $categoria->slug) }}"
                    class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full hover:bg-blue-200"
                >
                    {{ $categoria->name }}
                </a>
                @endforeach
            </div>

            <!-- Informações do livro -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    @if($book->publication_year)
                    <div>
                        <dt class="text-gray-600">Ano de Publicação:</dt>
                        <dd class="font-semibold">{{ $book->publication_year }}</dd>
                    </div>
                    @endif

                    @if($book->pages)
                    <div>
                        <dt class="text-gray-600">Páginas:</dt>
                        <dd class="font-semibold">{{ $book->pages }}</dd>
                    </div>
                    @endif

                    @if($book->original_publisher)
                    <div>
                        <dt class="text-gray-600">Editora Original:</dt>
                        <dd class="font-semibold">{{ $book->original_publisher }}</dd>
                    </div>
                    @endif

                    <div>
                        <dt class="text-gray-600">Idioma:</dt>
                        <dd class="font-semibold">{{ $book->original_language }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Sinopse -->
            @if($book->synopsis)
            <div class="mb-6">
                <h2 class="text-2xl font-bold mb-3">Sinopse</h2>
                <p class="text-gray-700 leading-relaxed">{{ $book->synopsis }}</p>
            </div>
            @endif

            <!-- Descrição completa -->
            @if($book->full_description)
            <div class="mb-6">
                <h2 class="text-2xl font-bold mb-3">Sobre este livro</h2>
                <div class="text-gray-700 leading-relaxed prose max-w-none">
                    {!! nl2br(e($book->full_description)) !!}
                </div>
            </div>
            @endif

            <!-- Informação de Domínio Público -->
            @if($book->is_public_domain)
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg mb-6">
                <h3 class="font-bold text-green-800 mb-2">✓ Domínio Público</h3>
                <p class="text-sm text-green-700">
                    Este livro está em domínio público
                    @if($book->public_domain_year)
                        desde {{ $book->public_domain_year }}
                    @endif
                    e pode ser baixado gratuitamente.
                </p>
                @if($book->public_domain_justification)
                <p class="text-xs text-green-600 mt-2">
                    {{ $book->public_domain_justification }}
                </p>
                @endif
            </div>
            @endif

            <!-- Tags -->
            @if($book->tags->count() > 0)
            <div class="mb-6">
                <h3 class="font-bold mb-3">Tags:</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($book->tags as $tag)
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                        #{{ $tag->name }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Livros relacionados (mesma categoria) -->
    <div class="mt-12">
        <h2 class="text-2xl font-bold mb-6">Livros Relacionados</h2>
        <!-- Aqui você pode adicionar uma query para livros da mesma categoria -->
    </div>
</div>
@endsection