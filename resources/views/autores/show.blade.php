<!-- ============================================ -->
<!-- resources/views/autores/show.blade.php -->
<!-- Perfil do autor -->
<!-- ============================================ -->

@extends('layouts.app')

@section('title', $author->name)

@section('content')
    @php
        // Calcular estatísticas do autor
        $totalDownloads = $author->books->sum('total_downloads');
        $yearsInPublicDomain = $author->death_date ? $author->death_date->diffInYears(now()) : 0;
        $booksWithRating = $author->books->where('average_rating', '>', 0);
        $averageRating = $booksWithRating->count() > 0 ? $booksWithRating->avg('average_rating') : 0;
        $publishedWorks = $author->books->count();
        
        // Formatar downloads
        $formattedDownloads = $totalDownloads >= 1000 ? number_format($totalDownloads / 1000, $totalDownloads >= 1000000 ? 1 : 0) . ($totalDownloads >= 1000000 ? 'M' : 'K') : $totalDownloads;
        
        // Anos de vida
        $lifetimeYears = '';
        if ($author->birth_date && $author->death_date) {
            $lifetimeYears = $author->birth_date->format('Y') . ' - ' . $author->death_date->format('Y');
        } elseif ($author->birth_date) {
            $lifetimeYears = $author->birth_date->format('Y');
        }
    @endphp

    <div class="min-h-screen bg-[#FDFBF6]">
        <!-- Breadcrumb -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <nav class="flex items-center space-x-2 text-sm">
                    <a href="{{ route('home') }}" class="text-[#004D40] hover:text-[#00695C] transition-colors">Início</a>
                    <i class="ri-arrow-right-s-line text-gray-400"></i>
                    <a href="{{ route('autores.index') }}" class="text-[#004D40] hover:text-[#00695C] transition-colors">Autores</a>
                    <i class="ri-arrow-right-s-line text-gray-400"></i>
                    <span class="text-gray-600">{{ $author->name }}</span>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Perfil do Autor -->
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-12">
                <div class="flex flex-col lg:flex-row items-center lg:items-start gap-8">
                    <!-- Foto do Autor -->
                    <div class="flex-shrink-0">
                        @if ($author->photo_url)
                            <img alt="{{ $author->name }}" 
                                class="w-48 h-48 rounded-full object-cover shadow-xl"
                                src="{{ $author->photo_url }}">
                        @else
                            <div class="w-48 h-48 rounded-full bg-gradient-to-br from-[#004D40]/10 to-[#B8860B]/10 flex items-center justify-center shadow-xl">
                                <i class="ri-user-line text-6xl text-[#004D40] opacity-50"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Informações do Autor -->
                    <div class="flex-1 text-center lg:text-left">
                        <h1 class="text-4xl font-bold text-[#333333] mb-4">{{ $author->name }}</h1>
                        
                        <!-- Badges -->
                        <div class="flex flex-wrap justify-center lg:justify-start gap-4 mb-6">
                            @if ($lifetimeYears)
                                <span class="bg-[#004D40]/10 text-[#004D40] px-4 py-2 rounded-full text-sm font-medium">
                                    {{ $lifetimeYears }}
                                </span>
                            @endif
                            
                            @if ($author->nationality)
                                <span class="bg-[#B8860B]/10 text-[#B8860B] px-4 py-2 rounded-full text-sm font-medium">
                                    {{ $author->nationality }}
                                </span>
                            @endif
                            
                            @php
                                $authorCategories = $author->books->flatMap(function($book) {
                                    return $book->categories;
                                })->unique('id')->take(2);
                            @endphp
                            @if ($authorCategories->count() > 0)
                                <span class="bg-gray-100 text-gray-700 px-4 py-2 rounded-full text-sm font-medium">
                                    {{ $authorCategories->pluck('name')->join(', ') }}
                                </span>
                            @endif
                        </div>

                        <!-- Biografia -->
                        @if ($author->biography)
                            <p class="text-gray-600 text-lg leading-relaxed mb-8">{{ $author->biography }}</p>
                        @endif

                        <!-- Estatísticas -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-[#004D40] mb-1">{{ $publishedWorks }}</div>
                                <div class="text-sm text-gray-600">Obras Publicadas</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-[#B8860B] mb-1">{{ $formattedDownloads }}</div>
                                <div class="text-sm text-gray-600">Total de Downloads</div>
                            </div>
                            @if ($yearsInPublicDomain > 0)
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-[#004D40] mb-1">
                                        {{ intval($yearsInPublicDomain) }}
                                    </div>
                                    <div class="text-sm text-gray-600">Anos em Domínio Público</div>
                                </div>
                            @endif
                            @if ($averageRating > 0)
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-[#B8860B] mb-1">{{ number_format($averageRating, 1) }}</div>
                                    <div class="text-sm text-gray-600">Avaliação Média</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Obras do Autor -->
            @if ($author->books->count() > 0)
                <div class="mb-12">
                    <h2 class="text-3xl font-bold text-[#333333] mb-8">Obras de {{ $author->name }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($author->books as $livro)
                            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 h-full">
                                <div class="flex flex-col h-full">
                                    <!-- Capa do Livro -->
                                    <div class="mb-4">
                                        @if ($livro->cover_thumbnail_url)
                                            <img alt="{{ $livro->title }}" 
                                                class="w-full h-64 object-cover rounded-lg mb-4"
                                                src="{{ $livro->cover_thumbnail_url }}">
                                        @else
                                            <div class="w-full h-64 bg-gradient-to-br from-[#004D40]/10 to-[#B8860B]/10 rounded-lg mb-4 flex items-center justify-center">
                                                <span class="text-6xl opacity-50">📚</span>
                                            </div>
                                        @endif
                                        
                                        <h3 class="text-xl font-bold text-[#333333] mb-2">
                                            <a href="{{ route('livros.show', $livro->slug) }}" class="hover:text-[#004D40] transition-colors">
                                                {{ $livro->title }}
                                            </a>
                                        </h3>
                                        
                                        @if ($livro->publication_year)
                                            <p class="text-[#004D40] font-semibold mb-1">{{ $livro->publication_year }}</p>
                                        @endif
                                        
                                        @if ($livro->categories->count() > 0)
                                            <p class="text-[#B8860B] text-sm font-medium mb-3">
                                                {{ $livro->categories->pluck('name')->join(', ') }}
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Sinopse -->
                                    @if ($livro->synopsis)
                                        <p class="text-gray-600 text-sm mb-4 flex-1 line-clamp-3">
                                            {{ Str::limit($livro->synopsis, 150) }}
                                        </p>
                                    @endif

                                    <!-- Estatísticas do Livro -->
                                    <div class="grid grid-cols-2 gap-4 mb-4 text-center">
                                        <div class="bg-[#004D40]/5 rounded-lg p-3">
                                            <div class="text-lg font-bold text-[#004D40] mb-1">
                                                {{ $livro->total_downloads >= 1000 ? number_format($livro->total_downloads / 1000, $livro->total_downloads >= 1000000 ? 1 : 0) . ($livro->total_downloads >= 1000000 ? 'M' : 'K') : number_format($livro->total_downloads) }}
                                            </div>
                                            <div class="text-xs text-gray-600">Downloads</div>
                                        </div>
                                        @if ($livro->average_rating > 0)
                                            <div class="bg-[#B8860B]/5 rounded-lg p-3">
                                                <div class="text-lg font-bold text-[#B8860B] mb-1">{{ number_format($livro->average_rating, 1) }}</div>
                                                <div class="text-xs text-gray-600">Avaliação</div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Botão Ver Detalhes -->
                                    <a href="{{ route('livros.show', $livro->slug) }}">
                                        <button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#004D40] text-white hover:bg-[#00695C] focus:ring-2 focus:ring-[#004D40]/20 px-3 py-1.5 text-sm rounded-md w-full">
                                            <i class="ri-eye-line mr-2"></i>Ver Detalhes
                                        </button>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Timeline da Vida -->
            @if ($author->birth_date || $author->death_date || $author->biography)
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-[#333333] mb-8">Linha do Tempo</h3>
                    <div class="space-y-6">
                        @if ($author->birth_date)
                            <div class="flex items-center gap-4">
                                <div class="w-4 h-4 bg-[#004D40] rounded-full flex-shrink-0"></div>
                                <div>
                                    <div class="font-semibold text-[#333333]">{{ $author->birth_date->format('Y') }}</div>
                                    <div class="text-gray-600 text-sm">
                                        Nascimento{{ $author->nationality ? ' em ' . $author->nationality : '' }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($author->biography)
                            <div class="flex items-center gap-4">
                                <div class="w-4 h-4 bg-[#B8860B] rounded-full flex-shrink-0"></div>
                                <div>
                                    <div class="font-semibold text-[#333333]">Carreira Literária</div>
                                    <div class="text-gray-600 text-sm">
                                        @php
                                            $careerCategories = $author->books->flatMap(function($book) {
                                                return $book->categories;
                                            })->unique('id')->take(3);
                                        @endphp
                                        @if ($careerCategories->count() > 0)
                                            Especializado em {{ $careerCategories->pluck('name')->join(', ') }}
                                        @else
                                            Autor renomado com obras consagradas
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($author->death_date)
                            <div class="flex items-center gap-4">
                                <div class="w-4 h-4 bg-[#004D40] rounded-full flex-shrink-0"></div>
                                <div>
                                    <div class="font-semibold text-[#333333]">{{ $author->death_date->format('Y') }}</div>
                                    <div class="text-gray-600 text-sm">Falecimento, deixando um legado literário duradouro</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
