@extends('layouts.app')

@section('title')
    @if(request()->routeIs('autores.brasileiros'))
        Autores Brasileiros - Leia Livre
    @else
        Autores - Leia Livre
    @endif
@endsection

@section('seo')
    <x-seo-meta
        title="{{ request()->routeIs('autores.brasileiros') ? 'Autores Brasileiros - Leia Livre' : 'Autores Renomados - Leia Livre' }}"
        description="Descubra a vida e obra dos maiores escritores da história. Explore biografias e baixe livros gratuitos de {{ request()->routeIs('autores.brasileiros') ? 'Machado de Assis, Lima Barreto e outros autores brasileiros.' : 'Shakespeare, Dostoiévski, Machado de Assis e mais.' }}"
        keywords="autores, escritores, biographies, literatura clássica, domínio público, {{ request()->routeIs('autores.brasileiros') ? 'autores brasileiros, literatura brasileira' : '' }}"
    />
@endsection

@section('content')
<!-- Breadcrumb -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex items-center space-x-2 text-sm">
            <a href="{{ route('home') }}" class="text-[#004D40] hover:text-[#00695C] transition-colors">Início</a>
            <i class="ri-arrow-right-s-line text-gray-400"></i>
            <span class="text-gray-600">Autores</span>
        </nav>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-12">
        <h1 class="text-5xl font-bold text-[#333333] mb-6">
            @if(request()->routeIs('autores.brasileiros'))
                Autores Brasileiros
            @else
                Autores Renomados
            @endif
        </h1>
        <p class="text-xl text-gray-600 max-w-4xl mx-auto leading-relaxed">
            Descubra as mentes brilhantes por trás das maiores obras literárias. Explore biografias completas, bibliografias detalhadas e baixe seus clássicos atemporais que moldaram a cultura e o pensamento humano.
        </p>
    </div>

    <!-- Category Filters -->
    @if(isset($popularCategories) && $popularCategories->count() > 0)
        <div class="mb-12">
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('autores.index') }}" 
                   class="px-6 py-3 rounded-full font-medium transition-all duration-200 {{ !isset($categorySlug) || !$categorySlug ? 'bg-[#004D40] text-white shadow-lg' : 'bg-white text-[#004D40] border border-[#004D40]/20 hover:bg-[#004D40]/5' }}">
                    Todos
                </a>
                @foreach($popularCategories as $category)
                    <a href="{{ route('autores.index', ['category' => $category->slug]) }}" 
                       class="px-6 py-3 rounded-full font-medium transition-all duration-200 {{ (isset($categorySlug) && $categorySlug === $category->slug) ? 'bg-[#004D40] text-white shadow-lg' : 'bg-white text-[#004D40] border border-[#004D40]/20 hover:bg-[#004D40]/5' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Authors Grid -->
    @if($authors->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            @foreach($authors as $author)
                <x-author-card :author="$author" />
            @endforeach
        </div>
    @else
        <div class="text-center py-16">
            <p class="text-xl text-gray-600 mb-4">Nenhum autor encontrado.</p>
            <a href="{{ route('autores.index') }}" class="text-[#004D40] hover:text-[#00695C] font-medium">
                Ver todos os autores
            </a>
        </div>
    @endif

    <!-- Pagination -->
    @if($authors->hasPages())
        <div class="mb-12">
            {{ $authors->links() }}
        </div>
    @endif

    <!-- Statistics -->
    @if(isset($stats))
        <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
            <h2 class="text-3xl font-bold text-[#333333] mb-8">Nossa Coleção de Autores</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="text-4xl font-bold text-[#004D40] mb-2">{{ number_format($stats['total_authors']) }}+</div>
                    <div class="text-gray-600">Autores em Destaque</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-[#B8860B] mb-2">{{ number_format($stats['total_works']) }}+</div>
                    <div class="text-gray-600">Obras Publicadas</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-[#004D40] mb-2">{{ $stats['total_genres'] }}</div>
                    <div class="text-gray-600">Gêneros Literários</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-[#B8860B] mb-2">
                        @if($stats['total_downloads'] >= 1000000)
                            {{ number_format($stats['total_downloads'] / 1000000, 1) }}M
                        @elseif($stats['total_downloads'] >= 1000)
                            {{ number_format($stats['total_downloads'] / 1000, 1) }}K
                        @else
                            {{ number_format($stats['total_downloads']) }}
                        @endif
                    </div>
                    <div class="text-gray-600">Total de Downloads</div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
