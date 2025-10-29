<!-- ============================================ -->
<!-- resources/views/autores/index.blade.php -->
<!-- Lista de autores -->
<!-- ============================================ -->

@extends('layouts.app')

@section('title')
    @if(request()->routeIs('autores.brasileiros'))
        Autores Brasileiros
    @else
        Autores
    @endif
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold mb-8">
        @if(request()->routeIs('autores.brasileiros'))
            Autores Brasileiros
        @else
            Autores
        @endif
    </h1>

    <!-- Grade de autores -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($authors as $author)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
            <div class="p-6">
                <!-- Foto do autor -->
                <div class="flex items-center gap-4 mb-4">
                    @if($author->photo_url)
                    <img 
                        src="{{ $author->photo_url }}" 
                        alt="{{ $author->name }}"
                        class="w-16 h-16 rounded-full object-cover"
                    >
                    @else
                    <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center">
                        <span class="text-3xl">👤</span>
                    </div>
                    @endif

                    <div class="flex-1">
                        <h3 class="font-bold text-lg">
                            <a href="{{ route('autores.show', $author->slug) }}" class="hover:text-blue-600">
                                {{ $author->name }}
                            </a>
                        </h3>
                        
                        @if($author->nationality)
                        <p class="text-gray-600 text-sm">🌍 {{ $author->nationality }}</p>
                        @endif
                    </div>
                </div>

                <!-- Informações adicionais -->
                <div class="space-y-2 text-sm text-gray-600 mb-4">
                    @if($author->birth_date)
                    <div>
                        <span class="font-semibold">Nascimento:</span> 
                        {{ $author->birth_date->format('d/m/Y') }}
                    </div>
                    @endif
                    
                    @if($author->death_date)
                    <div>
                        <span class="font-semibold">Falecimento:</span> 
                        {{ $author->death_date->format('d/m/Y') }}
                    </div>
                    @endif

                    <div>
                        <span class="font-semibold">Obras:</span> 
                        {{ $author->books_count }} {{ $author->books_count === 1 ? 'obra' : 'obras' }}
                    </div>
                </div>

                <!-- Badge de domínio público -->
                @if($author->is_public_domain)
                <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                    ✓ Domínio Público
                </span>
                @endif

                <!-- Biografia resumida -->
                @if($author->biography)
                <p class="text-gray-500 text-xs mt-4 line-clamp-3">
                    {{ Str::limit($author->biography, 120) }}
                </p>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Paginação -->
    <div class="mt-8">
        {{ $authors->links() }}
    </div>
</div>
@endsection
