@props(['book'])

<div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden group cursor-pointer hover:shadow-lg transition-all duration-300">
    <a href="{{ route('livros.show', $book->slug) }}">
    <div class="relative overflow-hidden">
        @if($book->cover_thumbnail_url || $book->cover_url)
            <img 
                alt="{{ $book->title }}" 
                class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-300" 
                src="{{ $book->cover_thumbnail_url ?? $book->cover_url }}"
            >
        @else
            <div class="w-full h-64 bg-gradient-to-br from-[#004D40]/10 to-[#B8860B]/10 flex items-center justify-center">
                <span class="text-6xl opacity-50">📚</span>
            </div>
        @endif
        
        @if($book->categories->count() > 0)
            <div class="absolute top-3 right-3">
                <span class="bg-white/90 backdrop-blur-sm text-[#004D40] text-xs font-medium px-2 py-1 rounded-full">
                    {{ $book->categories->first()->name }}
                </span>
            </div>
        @endif
        
        @if($book->publication_year)
            <div class="absolute top-3 left-3">
                <span class="bg-black/70 text-white text-xs font-medium px-2 py-1 rounded-full">
                    {{ $book->publication_year }}
                </span>
            </div>
        @endif
    </div>
    
    <div class="p-4">
        <h3 class="text-lg font-bold text-[#333333] mb-2 group-hover:text-[#004D40] transition-colors line-clamp-2">
            <a href="{{ route('livros.show', $book->slug) }}">
                {{ $book->title }}
            </a>
        </h3>
        
        @if($book->mainAuthors->count() > 0)
            <p class="text-gray-600 text-sm mb-3">
                por {{ $book->mainAuthors->first()->name }}
            </p>
        @endif
        
        @if($book->synopsis)
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                {{ Str::limit($book->synopsis, 120) }}
            </p>
        @endif
        
        <div class="flex items-center justify-between text-sm">
            <div class="flex items-center text-gray-500">
                <i class="ri-download-line mr-1"></i>
                {{ number_format($book->total_downloads) }}
            </div>
            @if($book->average_rating > 0)
                <div class="flex items-center text-[#B8860B]">
                    <i class="ri-star-fill mr-1"></i>
                    {{ number_format($book->average_rating, 1) }}
                </div>
            @endif
            @if($book->pages)
                <div class="text-gray-500">{{ $book->pages }} páginas</div>
            @endif
        </div>
    </div>
</a>
</div>
