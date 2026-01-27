@props(['book'])

<div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 h-full">
    <div class="flex flex-col h-full">
        <div class="flex gap-4 mb-4">
            @if($book->cover_thumbnail_url || $book->cover_url)
                <img 
                    alt="{{ $book->title }} - Capa do livro{{ $book->mainAuthors->count() > 0 ? ' de ' . $book->mainAuthors->first()->name : '' }}" 
                    class="w-24 h-36 object-contain rounded-lg shadow-md" 
                    src="{{ $book->cover_thumbnail_url ?? $book->cover_url }}"
                    loading="lazy"
                    width="96"
                    height="144"
                >
            @else
                <div class="w-24 h-36 bg-gradient-to-br from-[#004D40]/10 to-[#B8860B]/10 rounded-lg flex items-center justify-center">
                    <span class="text-4xl opacity-50">📚</span>
                </div>
            @endif
            <div class="flex-1">
                <h3 class="text-xl font-bold text-[#333333] mb-2 line-clamp-2">
                    <a href="{{ route('livros.show', $book->slug) }}" class="hover:text-[#004D40] transition-colors">
                        {{ $book->title }}
                    </a>
                </h3>
                @if($book->mainAuthors->count() > 0)
                    <p class="text-[#004D40] font-semibold mb-1">
                        por {{ $book->mainAuthors->first()->name }}
                    </p>
                @endif
                @if($book->publication_year || $book->categories->count() > 0)
                    <p class="text-gray-600 text-sm mb-2">
                        @if($book->publication_year){{ $book->publication_year }}@endif
                        @if($book->publication_year && $book->categories->count() > 0) • @endif
                        @if($book->categories->count() > 0){{ $book->categories->first()->name }}@endif
                    </p>
                @endif
                <div class="flex items-center gap-4 text-sm text-gray-500">
                    @if($book->average_rating > 0)
                        <span class="flex items-center">
                            <i class="ri-star-fill text-[#B8860B] mr-1"></i>{{ number_format($book->average_rating, 1) }}
                        </span>
                    @endif
                    <span class="flex items-center">
                        <i class="ri-download-line mr-1"></i>{{ number_format($book->total_downloads) }}
                    </span>
                </div>
            </div>
        </div>
        @if($book->synopsis)
            <p class="text-gray-600 text-sm mb-4 flex-1 line-clamp-3">
                {{ Str::limit($book->synopsis, 120) }}
            </p>
        @endif
        <div class="space-y-3">
            <div class="flex items-center justify-between text-sm text-gray-500">
                @if($book->pages)
                    <span>{{ $book->pages }} páginas</span>
                @endif
                @if($book->original_language)
                    <span>{{ $book->original_language }}</span>
                @endif
            </div>
            @if($book->activeFiles->count() > 0)
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($book->activeFiles->take(3) as $file)
                        <span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">{{ $file->format }}</span>
                    @endforeach
                </div>
            @endif
            <div class="flex space-x-2">
                <a href="{{ route('livros.show', $book->slug) }}" class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap border-2 border-[#004D40] text-[#004D40] hover:bg-[#004D40] hover:text-white px-3 py-1.5 text-sm rounded-md flex-1">
                    <i class="ri-eye-line mr-2"></i>Ver Detalhes
                </a>
            </div>
        </div>
    </div>
</div>

