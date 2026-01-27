@extends('layouts.app')

@section('title', $book->title)

@section('seo')
    <x-seo-meta
        title="{{ $book->title }} - {{ $book->mainAuthors->pluck('name')->join(', ') }} | Leia Livre"
        description="{{ Str::limit('Baixe o livro ' . $book->title . ' de ' . $book->mainAuthors->pluck('name')->join(', ') . ' gratuitamente em domínio público. Disponível em PDF, EPUB e MOBI.', 155) }}"
        :image="$book->cover_url ?? $book->cover_thumbnail_url"
        :author="$book->mainAuthors->pluck('name')->join(', ')"
        type="book"
        :jsonLd="[
            [
                'type' => 'Book',
                'data' => [
                    'name' => $book->title,
                    'url' => route('livros.show', $book->slug),
                    'author' => $book->mainAuthors->pluck('name')->toArray(),
                    'image' => $book->cover_url ?? $book->cover_thumbnail_url,
                    'description' => $book->synopsis,
                    'genre' => $book->categories->pluck('name')->toArray(),
                    'datePublished' => $book->publication_year,
                    'inLanguage' => $book->original_language,
                    'aggregateRating' => $book->total_ratings > 0 ? [
                        '@type' => 'AggregateRating',
                        'ratingValue' => number_format($book->average_rating, 1),
                        'reviewCount' => $book->total_ratings,
                        'bestRating' => '5',
                        'worstRating' => '1'
                    ] : null,
                ]
            ]
        ]"
    />
@endsection

@section('content')
    <!-- Breadcrumb -->
    <div class="bg-white/80 backdrop-blur-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center space-x-2 text-sm text-[#333333]">
                <a href="{{ route('home') }}" class="hover:text-[#004D40] transition-colors">Início</a>
                <i class="ri-arrow-right-s-line text-gray-400"></i>
                <a href="{{ route('livros.index') }}" class="hover:text-[#004D40] transition-colors">Livros</a>
                @if ($book->categories->count() > 0)
                    <i class="ri-arrow-right-s-line text-gray-400"></i>
                    <a href="{{ route('livros.categorias', $book->categories->first()->slug) }}"
                        class="hover:text-[#004D40] transition-colors">
                        {{ $book->categories->first()->name }}
                    </a>
                @endif
                <i class="ri-arrow-right-s-line text-gray-400"></i>
                <span class="text-[#B8860B] font-medium">{{ Str::limit($book->title, 50) }}</span>
            </nav>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-white to-[#FDFBF6] py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 items-start">
                <!-- Cover -->
                <div class="lg:col-span-2">
                    <div class="text-center lg:text-left">
                        @if ($book->cover_url || $book->cover_thumbnail_url)
                            <img alt="{{ $book->title }}"
                                class="w-full max-w-md mx-auto lg:mx-0 rounded-xl shadow-2xl object-cover"
                                src="{{ $book->cover_url ?? $book->cover_thumbnail_url }}">
                        @else
                            <div
                                class="w-full max-w-md mx-auto lg:mx-0 aspect-[2/3] bg-gradient-to-br from-[#004D40]/10 to-[#B8860B]/10 rounded-xl shadow-2xl flex items-center justify-center">
                                <span class="text-9xl opacity-50">📚</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Book Info -->
                <div class="lg:col-span-3 space-y-8">
                    <div>
                        <h1 class="text-5xl font-bold text-[#333333] mb-4 leading-tight">{{ $book->title }}</h1>
                        @if ($book->subtitle)
                            <p class="text-2xl text-gray-600 mb-4">{{ $book->subtitle }}</p>
                        @endif

                        <!-- Authors -->
                        @if ($book->mainAuthors->count() > 0)
                            <p class="text-2xl text-[#B8860B] mb-6">
                                por
                                @foreach ($book->mainAuthors as $author)
                                    <a href="{{ route('autores.show', $author->slug) }}"
                                        class="hover:text-[#004D40] transition-colors">
                                        {{ $author->name }}
                                    </a>
                                    @if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                            </p>
                        @endif

                        <!-- Stats -->
                        <div class="flex flex-wrap items-center gap-6 mb-6">
                            @if ($book->average_rating > 0)
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="ri-star-{{ $i <= round($book->average_rating) ? 'fill' : 'line' }} text-[#B8860B] text-2xl"></i>
                                        @endfor
                                    </div>
                                    <span class="text-[#333333] font-bold text-xl">{{ number_format($book->average_rating, 1) }}</span>
                                    <span class="text-gray-500 text-sm">({{ $book->total_ratings }} {{ $book->total_ratings == 1 ? 'avaliação' : 'avaliações' }})</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="ri-star-line text-gray-300 text-2xl"></i>
                                        @endfor
                                    </div>
                                    <span class="text-gray-500 text-sm">Sem avaliações</span>
                                </div>
                            @endif
                            <div class="text-[#333333]">
                                <span class="font-semibold text-[#004D40]">{{ number_format($book->total_downloads) }}</span>
                                downloads
                            </div>
                            @if ($book->pages)
                                <div class="text-[#333333]">
                                    <span class="font-semibold text-[#004D40]">{{ $book->pages }}</span> páginas
                                </div>
                            @endif
                        </div>

                        
                        <!-- Meta Info -->
                        <div class="flex flex-wrap gap-3 mb-8">
                            @if ($book->publication_year)
                                <span
                                    class="bg-white/80 backdrop-blur-sm px-4 py-2 rounded-full border border-gray-200 text-sm">
                                    <i class="ri-calendar-line mr-2 text-[#B8860B]"></i>{{ $book->publication_year }}
                                </span>
                            @endif
                            @if ($book->categories->count() > 0)
                                @foreach ($book->categories->take(2) as $category)
                                    <a href="{{ route('livros.categorias', $category->slug) }}"
                                        class="bg-white/80 backdrop-blur-sm px-4 py-2 rounded-full border border-gray-200 text-sm hover:border-[#004D40] transition-colors">
                                        <i class="ri-bookmark-line mr-2 text-[#B8860B]"></i>{{ $category->name }}
                                    </a>
                                @endforeach
                            @endif
                            @if ($book->original_language)
                                <span
                                    class="bg-white/80 backdrop-blur-sm px-4 py-2 rounded-full border border-gray-200 text-sm">
                                    <i class="ri-global-line mr-2 text-[#B8860B]"></i>
                                    {{ $book->original_language == 'pt' || $book->original_language == 'pt-BR' ? 'Português' : 'Inglês' }}
                                </span>
                            @endif
                            @if ($book->is_public_domain)
                                <span
                                    class="bg-white/80 backdrop-blur-sm px-4 py-2 rounded-full border border-gray-200 text-sm">
                                    <i class="ri-creative-commons-line mr-2 text-[#B8860B]"></i>Dominio Público
                                </span>
                            @endif
                        </div>
                    </div>

                    @if ($book->synopsis || $book->full_description)
                    <section>
                        <div class="prose prose-lg max-w-none">
                            @if ($book->synopsis)
                                <p class="text-[#333333] leading-relaxed text-lg mb-6 line-clamp-4" style="-webkit-line-clamp: 4; display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $book->synopsis }}
                                    
                                </p>
                            @endif
      
                        </div>
                    </section>
                @endif
                    <!-- Download Section -->
                    @if ($book->activeFiles->count() > 0)
                        @php
                            // Ordem de preferência dos formatos (do mais popular para o menos popular)
                            $formatPriority = [
                                'PDF' => 1,
                                'EPUB' => 2,
                                'MOBI' => 3,
                                'AZW' => 4,
                                'AZW3' => 5,
                                'TXT' => 6,
                                'RTF' => 7,
                                'DOC' => 8,
                                'DOCX' => 9,
                                'HTML' => 10,
                                'HTM' => 11,
                                'ZIP' => 12,
                            ];
                            
                            // Ordenar arquivos por prioridade
                            $sortedFiles = $book->activeFiles->sortBy(function ($file) use ($formatPriority) {
                                $format = strtoupper($file->format);
                                return $formatPriority[$format] ?? 999; // Formatos não listados vão para o final
                            })->values();
                            
                            $selectedFormat = $sortedFiles->first();
                        @endphp
                        <div class="bg-white/60 backdrop-blur-sm rounded-2xl p-8 border border-gray-200">
                            <h3 class="text-xl font-semibold text-[#333333] mb-6">Baixar Este Livro</h3>
                            <div class="mb-6">
                                <div class="flex flex-wrap gap-3">
                                    @foreach ($sortedFiles as $file)
                                        <button
                                            onclick="selectFormat('{{ $file->format }}', '{{ $file->id }}', '{{ route('download.file', $file->id) }}', '{{ $file->size_readable ?? '' }}')"
                                            class="format-btn px-4 py-2 text-sm rounded-lg border transition-all duration-200 {{ $file->id === $selectedFormat->id ? 'bg-[#004D40] text-white border-[#004D40] shadow-lg' : 'bg-white text-[#333333] border-gray-300 hover:border-[#004D40] hover:shadow-md' }}"
                                            data-format="{{ $file->format }}" data-file-id="{{ $file->id }}"
                                            data-download-url="{{ route('download.file', $file->id) }}"
                                            data-size="{{ $file->size_readable ?? '' }}">
                                            <span class="font-medium">{{ $file->format }}</span>
                                            @if ($file->size_readable)
                                                <span class="text-xs opacity-75 ml-1">({{ $file->size_readable }})</span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            <a id="downloadBtn" href="{{ route('download.file', $selectedFormat->id) }}"
                                onclick="trackDownload('{{ $book->id }}', {{ json_encode($book->title) }}, this.dataset.format)"
                                data-format="{{ $selectedFormat->format }}"
                                class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#B8860B] hover:bg-[#A0750A] text-white text-lg py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 w-full">
                                <i class="ri-download-cloud-line mr-3 text-xl"></i>
                                Baixar Formato {{ $selectedFormat->format }}
                            </a>
                            <p id="downloadInfo" class="text-sm text-gray-600 mt-3 text-center">
                                Download gratuito • Sem necessidade de registro
                                @if ($selectedFormat->size_readable)
                                    • {{ $selectedFormat->size_readable }}
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-16">
            <!-- Left Column - Main Content -->
            <div class="lg:col-span-2 space-y-12">
                <!-- About Section -->
                @if ($book->synopsis || $book->full_description)
                    <section>
                        <h2 class="text-3xl font-bold text-[#333333] mb-6">Sobre Este Livro</h2>
                        <div class="prose prose-lg max-w-none">
                            @if ($book->synopsis)
                                <p class="text-[#333333] leading-relaxed text-lg mb-6">{{ $book->synopsis }}</p>
                            @endif
                            @if ($book->full_description)
                                <div class="text-[#333333] leading-relaxed text-lg">
                                    {!! nl2br(e($book->full_description)) !!}
                                </div>
                            @endif
                        </div>
                    </section>
                @endif

                <!-- Author Section -->
                @if ($book->mainAuthors->count() > 0)
                    @foreach ($book->mainAuthors->take(1) as $author)
                        <section>
                            <h2 class="text-3xl font-bold text-[#333333] mb-6">Sobre o Autor</h2>
                            <div class="bg-white/60 backdrop-blur-sm rounded-2xl p-8 border border-gray-200">
                                <div class="flex flex-col md:flex-row items-start space-y-6 md:space-y-0 md:space-x-8">
                                    @if ($author->photo_url)
                                        <img alt="{{ $author->name }}"
                                            class="w-32 h-32 rounded-2xl object-cover shadow-lg flex-shrink-0 mx-auto md:mx-0"
                                            src="{{ $author->photo_url }}">
                                    @else
                                        <div
                                            class="w-32 h-32 rounded-2xl bg-gradient-to-br from-[#004D40]/10 to-[#B8860B]/10 flex items-center justify-center shadow-lg flex-shrink-0 mx-auto md:mx-0">
                                            <i class="ri-user-line text-5xl text-[#004D40] opacity-50"></i>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h3 class="text-2xl font-semibold text-[#333333] mb-4">
                                            <a href="{{ route('autores.show', $author->slug) }}"
                                                class="hover:text-[#004D40] transition-colors">
                                                {{ $author->name }}
                                            </a>
                                        </h3>
                                        @if ($author->biography)
                                            <p class="text-[#333333] leading-relaxed mb-6">
                                                {{ Str::limit($author->biography, 300) }}</p>
                                        @endif
                                        <div class="flex flex-wrap gap-6 text-sm">
                                            <div class="flex items-center">
                                                <i class="ri-book-line mr-2 text-[#B8860B]"></i>
                                                <span class="font-medium">{{ $author->books->count() }} Obras
                                                    Publicadas</span>
                                            </div>
                                            @if ($author->birth_date)
                                                <div class="flex items-center">
                                                    <i class="ri-calendar-line mr-2 text-[#B8860B]"></i>
                                                    <span class="font-medium">
                                                        {{ $author->birth_date->format('Y') }}
                                                        @if ($author->death_date)
                                                            - {{ $author->death_date->format('Y') }}
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif
                                            @if ($author->nationality)
                                                <div class="flex items-center">
                                                    <i class="ri-award-line mr-2 text-[#B8860B]"></i>
                                                    <span class="font-medium">{{ $author->nationality }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    @endforeach
                @endif

                <!-- Rating Section -->
                <section id="rating-section">
                    <h2 class="text-3xl font-bold text-[#333333] mb-6">Avaliações</h2>
                    
                    <!-- Rating Form -->
                    <div class="bg-white/60 backdrop-blur-sm rounded-2xl p-8 border border-gray-200 mb-8">
                        <h3 class="text-xl font-semibold text-[#333333] mb-4">Avaliar Este Livro</h3>
                        
                        <div id="rating-form">
                            <!-- Star Rating -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-[#333333] mb-3">Sua Avaliação</label>
                                <div class="flex items-center gap-2">
                                    <div class="flex gap-1" id="star-rating">
                                        <button type="button" class="star-btn text-4xl text-gray-300 hover:text-[#B8860B] transition-colors" data-rating="1">
                                            <i class="ri-star-line"></i>
                                        </button>
                                        <button type="button" class="star-btn text-4xl text-gray-300 hover:text-[#B8860B] transition-colors" data-rating="2">
                                            <i class="ri-star-line"></i>
                                        </button>
                                        <button type="button" class="star-btn text-4xl text-gray-300 hover:text-[#B8860B] transition-colors" data-rating="3">
                                            <i class="ri-star-line"></i>
                                        </button>
                                        <button type="button" class="star-btn text-4xl text-gray-300 hover:text-[#B8860B] transition-colors" data-rating="4">
                                            <i class="ri-star-line"></i>
                                        </button>
                                        <button type="button" class="star-btn text-4xl text-gray-300 hover:text-[#B8860B] transition-colors" data-rating="5">
                                            <i class="ri-star-line"></i>
                                        </button>
                                    </div>
                                    <span id="rating-text" class="text-lg font-medium text-[#333333] ml-2"></span>
                                </div>
                                <input type="hidden" id="rating-value" value="0">
                            </div>

                            <!-- Comment (Optional) -->
                            <div class="mb-6">
                                <label for="rating-comment" class="block text-sm font-medium text-[#333333] mb-2">
                                    Comentário (Opcional)
                                </label>
                                <textarea 
                                    id="rating-comment" 
                                    rows="3" 
                                    maxlength="500"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#004D40] focus:ring-2 focus:ring-[#004D40]/20 transition-colors resize-none"
                                    placeholder="Compartilhe sua opinião sobre este livro..."></textarea>
                                <p class="text-sm text-gray-500 mt-1">
                                    <span id="comment-count">0</span>/500 caracteres
                                </p>
                            </div>

                            <!-- Submit Button -->
                            <button 
                                id="submit-rating-btn"
                                onclick="submitRating()"
                                class="w-full bg-[#B8860B] hover:bg-[#A0750A] text-white font-medium py-3 px-6 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                Enviar Avaliação
                            </button>

                            <!-- Message Area -->
                            <div id="rating-message" class="mt-4 hidden"></div>
                        </div>

                        <!-- Already Rated Message -->
                        <div id="already-rated" class="hidden">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                                <i class="ri-information-line text-2xl text-blue-600 mb-2"></i>
                                <p class="text-blue-800 font-medium">Você já avaliou este livro.</p>
                                <p class="text-blue-600 text-sm mt-1">Obrigado pela sua contribuição!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Ratings Display -->
                    @if ($book->total_ratings > 0)
                    <div class="bg-white/60 backdrop-blur-sm rounded-2xl p-8 border border-gray-200">
                        <h3 class="text-xl font-semibold text-[#333333] mb-6">
                            Avaliações dos Leitores
                            <span class="text-[#B8860B]" id="total-ratings-display">({{ $book->total_ratings }})</span>
                        </h3>

                            <!-- Average Rating -->
                            <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-200">
                                <div class="text-center">
                                    <div class="text-5xl font-bold text-[#B8860B]" id="average-rating-display">
                                        {{ number_format($book->average_rating, 1) }}
                                    </div>
                                    <div class="flex items-center justify-center mt-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="ri-star-{{ $i <= round($book->average_rating) ? 'fill' : 'line' }} text-[#B8860B] text-xl"></i>
                                        @endfor
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">de 5 estrelas</p>
                                </div>
                            </div>

                            <!-- Recent Ratings -->
                            <div id="ratings-list" class="space-y-4">
                                @foreach ($book->ratings()->orderBy('created_at', 'desc')->take(5)->get() as $rating)
                                    <div class="border-b border-gray-200 pb-4 last:border-0">
                                        <div class="flex items-center gap-2 mb-2">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="ri-star-{{ $i <= $rating->rating ? 'fill' : 'line' }} text-[#B8860B]"></i>
                                            @endfor
                                            <span class="text-sm text-gray-500 ml-2">
                                                {{ $rating->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        @if ($rating->comment)
                                            <p class="text-[#333333] leading-relaxed">{{ $rating->comment }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>
            </div>

            <!-- Right Column - Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-8">
                    <!-- Book Information -->
                    <div class="bg-white/60 backdrop-blur-sm rounded-2xl p-6 border border-gray-200">
                        <h3 class="text-xl font-semibold text-[#333333] mb-6">Informações do Livro</h3>
                        <div class="space-y-4">
                            @if ($book->categories->count() > 0)
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="font-medium text-[#333333]">Gênero</span>
                                    <span class="text-[#B8860B] font-medium">{{ $book->categories->first()->name }}</span>
                                </div>
                            @endif
                            @if ($book->publication_year)
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="font-medium text-[#333333]">Publicado</span>
                                    <span class="text-[#333333]">{{ $book->publication_year }}</span>
                                </div>
                            @endif
                            @if ($book->original_language)
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="font-medium text-[#333333]">Idioma</span>
                                    <span class="text-[#333333]">{{ $book->original_language == 'pt' || $book->original_language == 'pt-BR' ? 'Português' : 'Inglês' }}</span>
                                </div>
                            @endif
                            @if ($book->pages)
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="font-medium text-[#333333]">Páginas</span>
                                    <span class="text-[#333333]">{{ $book->pages }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="font-medium text-[#333333]">Downloads</span>
                                <span
                                    class="text-[#004D40] font-semibold">{{ number_format($book->total_downloads) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="font-medium text-[#333333]">Visualizações</span>
                                <span class="text-[#004D40] font-semibold">{{ number_format($book->views) }}</span>
                            </div>
                            @if ($book->is_public_domain)
                                <div class="flex justify-between items-center py-2">
                                    <span class="font-medium text-[#333333]">Licença</span>
                                    <span class="text-[#333333]">Domínio Público</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Share Section -->
                    <div class="bg-white/60 backdrop-blur-sm rounded-2xl p-6 border border-gray-200">
                        <h3 class="text-xl font-semibold text-[#333333] mb-4">Compartilhar Este Livro</h3>
                        <div class="flex space-x-3">
                            <button onclick="shareBook()"
                                class="flex-1 bg-[#004D40] hover:bg-[#00695C] text-white py-3 px-4 rounded-lg transition-colors">
                                <i class="ri-share-line"></i>
                            </button>
                            <button
                                class="flex-1 bg-[#B8860B] hover:bg-[#A0750A] text-white py-3 px-4 rounded-lg transition-colors">
                                <i class="ri-bookmark-line"></i>
                            </button>
                            <button onclick="window.print()"
                                class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-3 px-4 rounded-lg transition-colors">
                                <i class="ri-printer-line"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tags -->
                    @if ($book->tags->count() > 0)
                        <div class="bg-white/60 backdrop-blur-sm rounded-2xl p-6 border border-gray-200">
                            <h3 class="text-xl font-semibold text-[#333333] mb-4">Etiquetas</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($book->tags as $tag)
                                    <span class="px-3 py-1 bg-[#004D40]/10 text-[#004D40] rounded-full text-sm">
                                        #{{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Related Books -->
        @php
            $relatedBooks = \App\Models\Book::with(['authors', 'categories', 'activeFiles'])
                ->active()
                ->where('id', '!=', $book->id)
                ->whereHas('categories', function ($query) use ($book) {
                    if ($book->categories->count() > 0) {
                        $query->whereIn('categories.id', $book->categories->pluck('id'));
                    }
                })
                ->take(3)
                ->get();

            // If not enough related books, get most downloaded
            if ($relatedBooks->count() < 3) {
                $additionalBooks = \App\Models\Book::with(['authors', 'categories', 'activeFiles'])
                    ->active()
                    ->where('id', '!=', $book->id)
                    ->whereNotIn('id', $relatedBooks->pluck('id'))
                    ->orderBy('total_downloads', 'desc')
                    ->take(3 - $relatedBooks->count())
                    ->get();
                $relatedBooks = $relatedBooks->merge($additionalBooks);
            }
        @endphp

        @if ($relatedBooks->count() > 0)
            <section class="mt-8 mb-16">
                <h2 class="text-3xl font-bold text-[#333333] mb-8">Você Também Pode Gostar</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach ($relatedBooks as $relatedBook)
                        <x-book-card :book="$relatedBook" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <script>
        function selectFormat(format, fileId, downloadUrl, size) {
            // Update button styles
            document.querySelectorAll('.format-btn').forEach(btn => {
                btn.classList.remove('bg-[#004D40]', 'text-white', 'border-[#004D40]', 'shadow-lg');
                btn.classList.add('bg-white', 'text-[#333333]', 'border-gray-300');
            });

            // Highlight selected format
            const selectedBtn = document.querySelector(`[data-file-id="${fileId}"]`);
            selectedBtn.classList.remove('bg-white', 'text-[#333333]', 'border-gray-300');
            selectedBtn.classList.add('bg-[#004D40]', 'text-white', 'border-[#004D40]', 'shadow-lg');

            // Update download button
            const downloadBtn = document.getElementById('downloadBtn');
            downloadBtn.href = downloadUrl;
            downloadBtn.dataset.format = format;
            
            // Update button text and size info
            let sizeText = size ? ` • ${size}` : '';
            downloadBtn.innerHTML = `<i class="ri-download-cloud-line mr-3 text-xl"></i>Baixar Formato ${format}`;
            
            // Update size info below button
            const sizeInfo = document.querySelector('#downloadBtn').nextElementSibling;
            if (sizeInfo && sizeInfo.tagName === 'P') {
                let sizeInfoText = 'Download gratuito • Sem necessidade de registro';
                if (size) {
                    sizeInfoText += ` • ${size}`;
                }
                sizeInfo.textContent = sizeInfoText;
            }
        }

        function shareBook() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $book->title }}',
                    text: '{{ Str::limit($book->synopsis ?? $book->title, 100) }}',
                    url: window.location.href
                });
            } else {
                // Fallback: copy to clipboard
                navigator.clipboard.writeText(window.location.href);
                alert('Link copiado para a área de transferência!');
            }
        }

        // Rating System
        let selectedRating = 0;
        const bookId = {{ $book->id }};

        // Check if user can rate on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkCanRate();
            setupRatingInteractions();
        });

        function setupRatingInteractions() {
            // Star rating interaction
            const starButtons = document.querySelectorAll('.star-btn');
            starButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    selectedRating = parseInt(this.dataset.rating);
                    updateStarDisplay(selectedRating);
                    document.getElementById('rating-value').value = selectedRating;
                });

                // Hover effect
                btn.addEventListener('mouseenter', function() {
                    const rating = parseInt(this.dataset.rating);
                    updateStarDisplay(rating, true);
                });
            });

            // Reset stars on mouse leave
            document.getElementById('star-rating').addEventListener('mouseleave', function() {
                updateStarDisplay(selectedRating);
            });

            // Comment character counter
            const commentTextarea = document.getElementById('rating-comment');
            commentTextarea.addEventListener('input', function() {
                document.getElementById('comment-count').textContent = this.value.length;
            });
        }

        function updateStarDisplay(rating, isHover = false) {
            const starButtons = document.querySelectorAll('.star-btn');
            const ratingText = document.getElementById('rating-text');
            
            const ratingLabels = ['', 'Péssimo', 'Ruim', 'Bom', 'Muito Bom', 'Excelente'];
            
            starButtons.forEach((btn, index) => {
                const star = btn.querySelector('i');
                const btnRating = parseInt(btn.dataset.rating);
                
                if (btnRating <= rating) {
                    star.classList.remove('ri-star-line');
                    star.classList.add('ri-star-fill');
                    btn.classList.remove('text-gray-300');
                    btn.classList.add('text-[#B8860B]');
                } else {
                    star.classList.remove('ri-star-fill');
                    star.classList.add('ri-star-line');
                    btn.classList.remove('text-[#B8860B]');
                    btn.classList.add('text-gray-300');
                }
            });

            if (rating > 0) {
                ratingText.textContent = ratingLabels[rating];
            } else {
                ratingText.textContent = '';
            }
        }

        async function checkCanRate() {
            try {
                const response = await fetch(`/ratings/book/${bookId}/can-rate`);
                const data = await response.json();
                
                if (!data.can_rate) {
                    document.getElementById('rating-form').classList.add('hidden');
                    document.getElementById('already-rated').classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error checking rating status:', error);
            }
        }

        async function submitRating() {
            const rating = selectedRating;
            const comment = document.getElementById('rating-comment').value.trim();
            const submitBtn = document.getElementById('submit-rating-btn');
            const messageDiv = document.getElementById('rating-message');

            // Validation
            if (rating === 0) {
                showMessage('Por favor, selecione uma avaliação de 1 a 5 estrelas.', 'error');
                return;
            }

            // Disable button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';

            try {
                const response = await fetch(`/ratings/book/${bookId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        rating: rating,
                        comment: comment
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showMessage(data.message, 'success');
                    
                    // Update UI
                    document.getElementById('rating-form').classList.add('hidden');
                    document.getElementById('already-rated').classList.remove('hidden');
                    
                    // Update average rating display
                    if (data.data) {
                        document.getElementById('average-rating-display').textContent = data.data.average_rating;
                        document.getElementById('total-ratings-display').textContent = `(${data.data.total_ratings})`;
                    }

                    // Reload page after 2 seconds to show new rating
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showMessage(data.message, 'error');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Enviar Avaliação';
                }
            } catch (error) {
                console.error('Error submitting rating:', error);
                showMessage('Erro ao enviar avaliação. Tente novamente.', 'error');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Enviar Avaliação';
            }
        }

        function showMessage(message, type) {
            const messageDiv = document.getElementById('rating-message');
            messageDiv.classList.remove('hidden');
            
            const bgColor = type === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
            const textColor = type === 'success' ? 'text-green-800' : 'text-red-800';
            const icon = type === 'success' ? 'ri-checkbox-circle-line' : 'ri-error-warning-line';
            
            messageDiv.className = `mt-4 ${bgColor} border rounded-lg p-4`;
            messageDiv.innerHTML = `
                <div class="flex items-center gap-2">
                    <i class="${icon} text-xl"></i>
                    <p class="${textColor} font-medium">${message}</p>
                </div>
            `;
        }
    </script>
@endsection
