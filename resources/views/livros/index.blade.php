@extends('layouts.app')

@section('title')
    @if (isset($category))
        {{ $category->name }} - Livros em Domínio Público
    @elseif(isset($term))
        Busca: "{{ $term }}" - Livros em Domínio Público
    @else
        Biblioteca de Livros em Domínio Público
    @endif
@endsection

@section('seo')
    <x-seo-meta
        title="{{ (isset($category) ? $category->name . ' - ' : (isset($term) ? 'Busca: ' . $term . ' - ' : (isset($sort) && $sort == 'downloads' ? 'Livros Mais Baixados - ' : ''))) . 'Biblioteca de Livros em Domínio Público' }}"
        description="{{ isset($category) ? 'Explore nossa coleção de livros de ' . $category->name . ' gratuitos em domínio público. Baixe clássicos em PDF, EPUB e MOBI.' : (isset($term) ? 'Resultados da busca por ' . $term . ' em nossa biblioteca de livros gratuitos.' : (isset($sort) && $sort == 'downloads' ? 'Confira os livros em domínio público mais baixados em nossa plataforma. Literatura clássica gratuita de alta qualidade.' : 'Navegue por milhares de livros gratuitos em domínio público. Literatura clássica disponível para download em múltiplos formatos.')) }}"
        keywords="livros grátis, domínio público, literatura clássica, download livros, {{ isset($category) ? $category->name . ', ' : '' }}ebooks gratuitos"
    />
@endsection

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-white">
        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-[#004D40] to-[#00695C] text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-8">
                    @if (isset($category))
                        <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $category->name }}</h1>
                        <p class="text-xl text-white/90 max-w-3xl mx-auto">
                            Explore nossa coleção de livros de {{ $category->name }} gratuitos
                        </p>
                    @elseif(isset($term))
                        <h1 class="text-4xl md:text-5xl font-bold mb-4">Resultados da Busca</h1>
                        <p class="text-xl text-white/90 max-w-3xl mx-auto">
                            Encontramos resultados para "{{ $term }}"
                        </p>
                    @else
                        <h1 class="text-4xl md:text-5xl font-bold mb-4">Biblioteca de Livros</h1>
                        <p class="text-xl text-white/90 max-w-3xl mx-auto">
                            Descubra sua próxima grande leitura em nossa vasta coleção de livros gratuitos
                        </p>
                    @endif
                </div>

                <!-- Search Form -->
                <form class="max-w-4xl mx-auto" action="{{ route('livros.buscar') }}" method="GET">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ri-search-line text-gray-400 text-xl"></i>
                        </div>
                        <input name="q" placeholder="Buscar por título, autor, gênero ou palavras-chave..."
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
                
                @if (isset($category))
                    <i class="ri-arrow-right-s-line"></i>
                    <a href="{{ route('categorias.index') }}" class="hover:text-[#004D40] transition-colors">Categorias</a>
                    <i class="ri-arrow-right-s-line"></i>
                    <span class="text-[#004D40] font-medium">{{ $category->name }}</span>
                @elseif(isset($term))
                    <i class="ri-arrow-right-s-line"></i>
                    <a href="{{ route('livros.index') }}" class="hover:text-[#004D40] transition-colors">Livros</a>
                    <i class="ri-arrow-right-s-line"></i>
                    <span class="text-[#004D40] font-medium">Busca: "{{ $term }}"</span>
                @else
                    <i class="ri-arrow-right-s-line"></i>
                    <span class="text-[#004D40] font-medium">Livros</span>
                @endif
            </nav>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Sidebar Filters -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-24">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-[#333333]">Filtros</h2>
                            <button class="text-[#004D40] text-sm font-medium hover:underline">Avançado</button>
                        </div>

                        <!-- Sort By -->
                        <form method="GET" action="{{ request()->url() }}" id="filterForm">
                            @if (request('q'))
                                <input type="hidden" name="q" value="{{ request('q') }}">
                            @endif
                            @if (request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ordenar Por</label>
                                <select name="sort"
                                    class="w-full p-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#004D40] pr-8"
                                    onchange="document.getElementById('filterForm').submit()">
                                    <option value="relevance"
                                        {{ ($sort ?? 'relevance') == 'relevance' ? 'selected' : '' }}>Relevância</option>
                                    <option value="title" {{ ($sort ?? '') == 'title' ? 'selected' : '' }}>Título A-Z
                                    </option>
                                    <option value="author" {{ ($sort ?? '') == 'author' ? 'selected' : '' }}>Autor A-Z
                                    </option>
                                    <option value="year" {{ ($sort ?? '') == 'year' ? 'selected' : '' }}>Mais Recentes
                                    </option>
                                    <option value="downloads" {{ ($sort ?? '') == 'downloads' ? 'selected' : '' }}>Mais
                                        Baixados</option>
                                    <option value="rating" {{ ($sort ?? '') == 'rating' ? 'selected' : '' }}>Melhor
                                        Avaliados</option>
                                </select>
                            </div>

                            <!-- Genre Filter -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Gênero</label>
                                <select name="category"
                                    class="w-full p-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#004D40] pr-8"
                                    onchange="document.getElementById('filterForm').submit()">
                                    <option value="">Todos os Gêneros</option>
                                    @if (isset($popularCategories) && $popularCategories->count() > 0)
                                        @foreach ($popularCategories as $cat)
                                            <option value="{{ $cat->slug }}"
                                                {{ request('category') == $cat->slug ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </form>

                        <!-- Quick Browse -->
                        <div class="mt-8">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Navegação Rápida</h3>
                            <div class="space-y-2">
                                <a href="{{ route('livros.index') }}"
                                    class="w-full text-left p-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 hover:text-[#004D40] transition-colors flex items-center">
                                    <i class="ri-book-open-line mr-2"></i>Literatura Clássica
                                </a>
                                <a href="{{ route('livros.mais-baixados') }}"
                                    class="w-full text-left p-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 hover:text-[#004D40] transition-colors flex items-center">
                                    <i class="ri-download-line mr-2"></i>Mais Baixados
                                </a>
                                <a href="{{ route('autores.index') }}"
                                    class="w-full text-left p-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 hover:text-[#004D40] transition-colors flex items-center">
                                    <i class="ri-team-line mr-2"></i>Autores
                                </a>
                                <a href="{{ route('livros.index') }}?sort=rating"
                                    class="w-full text-left p-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 hover:text-[#004D40] transition-colors flex items-center">
                                    <i class="ri-star-line mr-2"></i>Melhor Avaliados
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Books Grid -->
                <div class="lg:col-span-3">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-[#333333]">
                                @if (isset($category))
                                    Livros em {{ $category->name }}
                                @elseif(isset($term))
                                    Resultados da Busca
                                @else
                                    Explorar Todos os Livros
                                @endif
                            </h2>
                            <p class="text-gray-600 mt-1">
                                {{ $books->total() }}
                                {{ $books->total() == 1 ? 'livro encontrado' : 'livros encontrados' }}
                                @if (isset($totalAuthors) && $totalAuthors)
                                    • {{ $totalAuthors }} {{ $totalAuthors == 1 ? 'autor' : 'autores' }}
                                @endif
                            </p>
                        </div>
                    </div>

                    @if ($books->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                            @foreach ($books as $book)
                                <x-book-card-grid :book="$book" />
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $books->links() }}
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="text-6xl mb-4">📚</div>
                            <h3 class="text-2xl font-bold text-[#333333] mb-2">Nenhum livro encontrado</h3>
                            <p class="text-gray-600 mb-6">
                                @if (isset($term))
                                    Não encontramos livros para "{{ $term }}". Tente uma busca diferente.
                                @else
                                    Não há livros disponíveis no momento.
                                @endif
                            </p>
                            <a href="{{ route('livros.index') }}"
                                class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#004D40] text-white hover:bg-[#00695C] px-6 py-3 rounded-lg">
                                <i class="ri-arrow-left-line mr-2"></i>Ver Todos os Livros
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
