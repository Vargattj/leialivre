@extends('layouts.app')

@section('title')
    Categorias - Leia Livre
@endsection

@section('seo')
    <x-seo-meta
        title="Categorias de Livros em Domínio Público - Leia Livre"
        description="Explore livros gratuitos organizados por categorias: Romance, Mistério, Poesia, História, Filosofia e muito mais. Baixe clássicos em múltiplos formatos."
        keywords="categorias de livros, gêneros literários, livros grátis, literatura clássica, domínio público"
    />
@endsection

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-white">
        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-[#004D40] to-[#00695C] text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-8">
                    @if(isset($search) && $search)
                        <h1 class="text-4xl md:text-5xl font-bold mb-4">Resultados da Busca</h1>
                        <p class="text-xl text-white/90 max-w-3xl mx-auto">
                            Encontramos categorias para "{{ $search }}"
                        </p>
                    @else
                        <h1 class="text-4xl md:text-5xl font-bold mb-4">Explorar Categorias</h1>
                        <p class="text-xl text-white/90 max-w-3xl mx-auto">
                            Navegue por nossa vasta coleção de livros organizados por gênero e assunto
                        </p>
                    @endif
                </div>
                <!-- Search Form -->
                <form class="max-w-4xl mx-auto" action="{{ route('categorias.index') }}" method="GET">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ri-search-line text-gray-400 text-xl"></i>
                        </div>
                        <input name="q" placeholder="Buscar por categoria..."
                            class="block w-full pl-12 pr-4 py-4 text-lg border-0 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-white/20"
                            type="text" value="{{ request('q') }}">
                    </div>
                </form>
            </div>
        </section>

        <!-- Breadcrumb -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center space-x-2 text-sm text-gray-600">
                <a href="{{ route('home') }}" class="hover:text-[#004D40] transition-colors">Início</a>
                <i class="ri-arrow-right-s-line"></i>
                <span class="text-[#004D40] font-medium">Categorias</span>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-[#333333]">
                        @if (request('q'))
                            Resultados da Busca
                        @else
                            Todas as Categorias
                        @endif
                    </h2>
                    <p class="text-gray-600 mt-1">
                        {{ $categories->total() }}
                        {{ $categories->total() == 1 ? 'categoria encontrada' : 'categorias encontrados' }}
                    </p>
                </div>

                <!-- Sort -->
                <form method="GET" action="{{ route('categorias.index') }}" id="sortForm" class="flex items-center">
                    @if (request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif
                    <label class="mr-2 text-sm text-gray-600">Ordenar por:</label>
                    <select name="sort"
                        class="p-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#B8860B] pr-8"
                        onchange="document.getElementById('sortForm').submit()">
                        <option value="name" {{ ($sort ?? 'name') == 'name' ? 'selected' : '' }}>Nome (A-Z)</option>
                        <option value="books_count" {{ ($sort ?? '') == 'books_count' ? 'selected' : '' }}>Quantidade de Livros</option>
                    </select>
                </form>
            </div>

            @if ($categories->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6 mb-8">
                    @php
                        $icons = [
                            'ri-book-open-line',
                            'ri-search-eye-line',
                            'ri-heart-line',
                            'ri-compass-line',
                            'ri-rocket-line',
                            'ri-lightbulb-line',
                            'ri-time-line',
                            'ri-quill-pen-line'
                        ];
                    @endphp
                    
                    @foreach ($categories as $category)
                        <a href="{{ route('livros.categorias', $category->slug) }}" class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-all duration-300 text-center cursor-pointer group hover:-translate-y-1">
                            <div class="w-14 h-14 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-[#004D40] transition-colors duration-300">
                                <i class="{{ $icons[$loop->index % count($icons)] }} text-2xl text-[#004D40] group-hover:text-white transition-colors duration-300"></i>
                            </div>
                            <h3 class="text-base font-bold text-[#333333] mb-1 group-hover:text-[#004D40] transition-colors line-clamp-2 min-h-[3rem] flex items-center justify-center">{{ $category->name }}</h3>
                            <p class="text-[#B8860B] text-sm font-medium">{{ number_format($category->books_count, 0, ',', '.') }} livros</p>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $categories->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <div class="text-6xl mb-4">📂</div>
                    <h3 class="text-2xl font-bold text-[#333333] mb-2">Nenhuma categoria encontrada</h3>
                    <p class="text-gray-600 mb-6">
                        @if (request('q'))
                            Não encontramos categorias para "{{ request('q') }}". Tente uma busca diferente.
                        @else
                            Não há categorias cadastradas no momento.
                        @endif
                    </p>
                    <a href="{{ route('categorias.index') }}"
                        class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#004D40] text-white hover:bg-[#00695C] px-6 py-3 rounded-lg">
                        <i class="ri-arrow-left-line mr-2"></i>Limpar Filtros
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
