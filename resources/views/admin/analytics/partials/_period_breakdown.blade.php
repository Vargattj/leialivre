<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-4 border-b border-gray-50 pb-2">
        {{ $title }}
    </h3>
    <div class="space-y-3">
        @forelse($data as $index => $item)
        <div class="flex items-center gap-3 py-1">
            <span class="text-xs font-bold text-gray-400 w-4 text-center">{{ $index + 1 }}</span>
            <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-800 truncate">
                    @if($type === 'book')
                        @if($item->book)
                            <a href="{{ route('admin.books.edit', $item->book) }}" class="hover:text-blue-600">{{ $item->book->title }}</a>
                        @else
                            <span class="italic text-gray-400">Removido</span>
                        @endif
                    @elseif($type === 'category')
                        {{ $item->name }}
                    @elseif($type === 'format')
                        {{ $item->format }}
                    @endif
                </p>
            </div>
            <div class="text-right shrink-0">
                <p class="text-xs font-bold text-gray-600">{{ number_format($item->total) }}</p>
            </div>
        </div>
        @empty
            <p class="text-xs text-gray-400 text-center py-2">Nenhum dado no período.</p>
        @endforelse
    </div>
</div>
