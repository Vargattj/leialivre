@props(['author'])

<div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 text-center">
    <div class="mb-6">
        @if($author->photo_url)
            <img 
                alt="{{ $author->name }} - Foto do autor{{ $author->nationality ? ' (' . $author->nationality . ')' : '' }}" 
                class="w-32 h-32 rounded-full mx-auto mb-4 object-cover shadow-lg" 
                src="{{ $author->photo_url }}"
                loading="lazy"
                width="128"
                height="128"
            >
        @else
            <div class="w-32 h-32 rounded-full mx-auto mb-4 bg-gradient-to-br from-[#004D40]/10 to-[#B8860B]/10 flex items-center justify-center shadow-lg">
                <i class="ri-user-line text-5xl text-[#004D40] opacity-50"></i>
            </div>
        @endif
        <h3 class="text-2xl font-bold text-[#333333] mb-2">
            <a href="{{ route('autores.show', $author->slug) }}" class="hover:text-[#004D40] transition-colors">
                {{ $author->name }}
            </a>
        </h3>
        @if($author->birth_date)
            <p class="text-[#004D40] font-semibold mb-1">
                {{ $author->birth_date->format('Y') }}
                @if($author->death_date)
                    - {{ $author->death_date->format('Y') }}
                @endif
            </p>
        @endif
        @if($author->nationality)
            <p class="text-gray-600 text-sm mb-4">{{ $author->nationality }}</p>
        @endif
        @if($author->books->count() > 0)
            @php
                $primaryCategory = $author->books->first()->categories->first();
            @endphp
            @if($primaryCategory)
                <p class="text-[#B8860B] font-medium text-sm">{{ $primaryCategory->name }}</p>
            @endif
        @endif
    </div>
    @if($author->biography)
        <p class="text-gray-600 text-sm mb-6 line-clamp-4">
            {{ Str::limit($author->biography, 150) }}
        </p>
    @endif
    <div class="grid grid-cols-2 gap-4 mb-6 text-center">
        <div class="bg-[#004D40]/5 rounded-lg p-3">
            <div class="text-2xl font-bold text-[#004D40] mb-1">{{ $author->books_count ?? $author->books->count() }}</div>
            <div class="text-xs text-gray-600">Livros</div>
        </div>
        <div class="bg-[#B8860B]/5 rounded-lg p-3">
            <div class="text-2xl font-bold text-[#B8860B] mb-1">
                @if(isset($author->total_downloads) && $author->total_downloads > 0)
                    @if($author->total_downloads >= 1000)
                        {{ number_format($author->total_downloads / 1000, 1) }}K
                    @else
                        {{ number_format($author->total_downloads) }}
                    @endif
                @else
                    0
                @endif
            </div>
            <div class="text-xs text-gray-600">Downloads</div>
        </div>
    </div>
    <a href="{{ route('autores.show', $author->slug) }}">
        <button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#004D40] text-white hover:bg-[#00695C] focus:ring-2 focus:ring-[#004D40]/20 px-3 py-1.5 text-sm rounded-md w-full">
            <i class="ri-book-open-line mr-2"></i>Ver Obras
        </button>
    </a>
</div>

