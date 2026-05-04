@extends('layouts.admin')

@section('title', 'Analytics')

@section('content')
<div x-data="{ tab: 'geral' }" class="space-y-8">

    {{-- Header & Tabs --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Analytics</h1>
            <p class="text-sm text-gray-500 mt-1">Métricas de acesso, downloads e vendas</p>
        </div>
        
        <div class="flex bg-white rounded-lg p-1 shadow-sm border border-gray-100">
            <button @click="tab = 'geral'" 
                :class="tab === 'geral' ? 'bg-gray-100 text-gray-800 font-semibold shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2 text-sm rounded-md transition-all duration-200">
                <i class="ri-pie-chart-2-line mr-1"></i> Visão Geral
            </button>
            <button @click="tab = 'periodo'" 
                :class="tab === 'periodo' ? 'bg-gray-100 text-gray-800 font-semibold shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2 text-sm rounded-md transition-all duration-200">
                <i class="ri-calendar-event-line mr-1"></i> Por Período
            </button>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- TAB: VISÃO GERAL (TOTALS)                                 --}}
    {{-- ========================================================= --}}
    <div x-show="tab === 'geral'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="space-y-8" x-cloak>
        
        {{-- ── KPI Cards Principais ─────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Downloads totais --}}
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Downloads</span>
                    <div class="p-2 bg-blue-50 rounded-lg">
                        <i class="ri-download-cloud-2-line text-blue-500 text-lg"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($totalDownloads) }}</p>
                <p class="text-xs text-gray-400 mt-1">todos os formatos</p>
            </div>

            {{-- Visualizações --}}
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Visualizações</span>
                    <div class="p-2 bg-purple-50 rounded-lg">
                        <i class="ri-eye-line text-purple-500 text-lg"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($totalViews) }}</p>
                <p class="text-xs text-gray-400 mt-1">páginas de livros</p>
            </div>

            {{-- Clicks em Comprar --}}
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-[#004D40]">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Cliques Compra</span>
                    <div class="p-2 bg-[#004D40]/10 rounded-lg">
                        <i class="ri-shopping-cart-2-line text-[#004D40] text-lg"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($clicksTotal) }}</p>
                <p class="text-xs text-gray-400 mt-1">botão externo</p>
            </div>

            {{-- Conversão --}}
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-emerald-500">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Conv. (Download)</span>
                    <div class="p-2 bg-emerald-50 rounded-lg">
                        <i class="ri-line-chart-line text-emerald-500 text-lg"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ $conversionRate }}%</p>
                <p class="text-xs text-gray-400 mt-1">views → download</p>
            </div>

        </div>

        {{-- ── Segunda linha de KPIs ─────────────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Livros Ativos</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($activeBooksCount) }}</p>
                <p class="text-xs text-gray-400 mt-1">de {{ number_format($totalBooks) }} cadastrados</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Sem Downloads</p>
                <p class="text-2xl font-bold text-red-500">{{ number_format($booksWithNoDownloads) }}</p>
                <p class="text-xs text-gray-400 mt-1">livros ativos sem uso</p>
            </div>
            @foreach($filesByFormat as $fmt)
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Arquivos {{ $fmt->format }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($fmt->count) }}</p>
                <p class="text-xs text-gray-400 mt-1">ativos no acervo</p>
            </div>
            @endforeach
        </div>

        {{-- ── Rankings ─────────────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Top 10 mais baixados --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-700 mb-5 flex items-center gap-2">
                    <i class="ri-download-cloud-2-line text-blue-500"></i> Top Baixados
                </h2>
                <div class="space-y-3">
                    @forelse($topDownloadedBooks as $index => $book)
                    <div class="flex items-center gap-3 py-2 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <span class="text-xs font-bold {{ $index < 3 ? 'text-blue-500' : 'text-gray-400' }} w-4 text-center">{{ $index + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate"><a href="{{ route('admin.books.edit', $book) }}" class="hover:text-blue-600">{{ $book->title }}</a></p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-blue-600">{{ number_format($book->total_downloads) }}</p>
                        </div>
                    </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Nenhum dado.</p>
                    @endforelse
                </div>
            </div>

            {{-- Top 10 mais visualizados --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-700 mb-5 flex items-center gap-2">
                    <i class="ri-eye-line text-purple-500"></i> Top Visualizados
                </h2>
                <div class="space-y-3">
                    @forelse($topViewedBooks as $index => $book)
                    <div class="flex items-center gap-3 py-2 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <span class="text-xs font-bold {{ $index < 3 ? 'text-purple-500' : 'text-gray-400' }} w-4 text-center">{{ $index + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate"><a href="{{ route('admin.books.edit', $book) }}" class="hover:text-purple-600">{{ $book->title }}</a></p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-purple-600">{{ number_format($book->views) }}</p>
                        </div>
                    </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Nenhum dado.</p>
                    @endforelse
                </div>
            </div>

            {{-- Top 10 mais clicados para compra --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-700 mb-5 flex items-center gap-2">
                    <i class="ri-shopping-cart-2-line text-[#004D40]"></i> Top Cliques Compra
                </h2>
                <div class="space-y-3">
                    @forelse($topClickedBooks as $index => $book)
                    <div class="flex items-center gap-3 py-2 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <span class="text-xs font-bold {{ $index < 3 ? 'text-[#004D40]' : 'text-gray-400' }} w-4 text-center">{{ $index + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate"><a href="{{ route('admin.books.edit', $book) }}" class="hover:text-[#004D40]">{{ $book->title }}</a></p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-[#004D40]">{{ number_format($book->purchase_clicks) }}</p>
                        </div>
                    </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Nenhum dado.</p>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ── Downloads por formato + por categoria ────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Downloads por formato --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-700 mb-5 flex items-center gap-2">
                    <i class="ri-pie-chart-line text-blue-500"></i>
                    Downloads por Formato
                </h2>
                @php
                    $fmtColors = ['PDF' => 'bg-blue-500', 'EPUB' => 'bg-purple-500', 'MOBI' => 'bg-emerald-500', 'TXT' => 'bg-amber-500'];
                    $fmtTotal  = $downloadsByFormat->sum('total') ?: 1;
                @endphp
                <div class="space-y-4">
                    @forelse($downloadsByFormat as $fmt)
                        @php
                            $pct   = round(($fmt->total / $fmtTotal) * 100, 1);
                            $color = $fmtColors[$fmt->format] ?? 'bg-gray-400';
                        @endphp
                        <div>
                            <div class="flex justify-between items-center text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ $fmt->format }}</span>
                                <span class="text-gray-500">{{ number_format($fmt->total) }} <span class="text-gray-400">({{ $pct }}%)</span></span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5">
                                <div class="{{ $color }} h-2.5 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Nenhum download registrado ainda.</p>
                    @endforelse
                </div>
            </div>

            {{-- Downloads por categoria --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-700 mb-5 flex items-center gap-2">
                    <i class="ri-folder-chart-line text-purple-500"></i>
                    Downloads por Categoria
                </h2>
                @php $catTotal = $downloadsByCategory->sum('total') ?: 1; @endphp
                <div class="space-y-4">
                    @forelse($downloadsByCategory as $cat)
                        @php $pct = round(($cat->total / $catTotal) * 100, 1); @endphp
                        <div>
                            <div class="flex justify-between items-center text-sm mb-1">
                                <span class="font-medium text-gray-700 truncate max-w-[60%]">{{ $cat->name }}</span>
                                <span class="text-gray-500">{{ number_format($cat->total) }} <span class="text-gray-400">({{ $pct }}%)</span></span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5">
                                <div class="bg-purple-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Nenhum dado disponível.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ── Livros com atividade recente + Top avaliados ──────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Livros com downloads recentes --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-700 mb-5 flex items-center gap-2">
                    <i class="ri-time-line text-emerald-500"></i>
                    Atividade Recente de Downloads
                </h2>
                <div class="space-y-3">
                    @forelse($recentlyDownloaded as $book)
                    <div class="flex items-center gap-3 py-2 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">
                                <a href="{{ route('admin.books.edit', $book) }}" class="hover:text-blue-600 transition-colors">
                                    {{ $book->title }}
                                </a>
                            </p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <p class="text-xs text-gray-400">{{ $book->updated_at->diffForHumans() }}</p>
                                @foreach($book->activeFiles->pluck('format')->unique() as $fmt)
                                    <span class="text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded">{{ $fmt }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-emerald-600">{{ number_format($book->total_downloads) }}</p>
                            <p class="text-xs text-gray-400">total</p>
                        </div>
                    </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Nenhum download registrado.</p>
                    @endforelse
                </div>
            </div>

            {{-- Top avaliados --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-700 mb-5 flex items-center gap-2">
                    <i class="ri-star-fill text-amber-400"></i>
                    Mais Bem Avaliados
                </h2>
                <div class="space-y-3">
                    @forelse($topRatedBooks as $index => $book)
                    <div class="flex items-center gap-3 py-2 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">
                                <a href="{{ route('admin.books.edit', $book) }}" class="hover:text-blue-600 transition-colors">
                                    {{ $book->title }}
                                </a>
                            </p>
                            <p class="text-xs text-gray-400 truncate">{{ $book->mainAuthors->pluck('name')->join(', ') }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="flex items-center gap-1 justify-end">
                                <i class="ri-star-fill text-amber-400 text-sm"></i>
                                <span class="text-sm font-bold text-gray-800">{{ number_format($book->average_rating, 1) }}</span>
                            </div>
                            <p class="text-xs text-gray-400">{{ $book->total_ratings }} avaliações</p>
                        </div>
                    </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Nenhuma avaliação recebida.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ── Avaliações recentes ───────────────────────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-5 flex items-center gap-2">
                <i class="ri-chat-3-line text-blue-500"></i>
                Avaliações Recentes
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left font-semibold text-gray-500 pb-3 pr-4">Livro</th>
                            <th class="text-center font-semibold text-gray-500 pb-3 px-4">Nota</th>
                            <th class="text-left font-semibold text-gray-500 pb-3 px-4">Comentário</th>
                            <th class="text-right font-semibold text-gray-500 pb-3 pl-4">Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRatings as $rating)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                            <td class="py-3 pr-4">
                                @if($rating->book)
                                    <a href="{{ route('admin.books.edit', $rating->book) }}" class="font-medium text-gray-800 hover:text-blue-600 transition-colors truncate block max-w-[200px]">
                                        {{ $rating->book->title }}
                                    </a>
                                @else
                                    <span class="text-gray-400 italic">Livro removido</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="ri-star-{{ $i <= $rating->rating ? 'fill' : 'line' }} text-amber-400 text-xs"></i>
                                    @endfor
                                </div>
                            </td>
                            <td class="py-3 px-4 text-gray-500 italic max-w-xs">
                                <span class="truncate block max-w-[250px]">
                                    {{ $rating->comment ? '"' . $rating->comment . '"' : '—' }}
                                </span>
                            </td>
                            <td class="py-3 pl-4 text-right text-gray-400 whitespace-nowrap">
                                {{ $rating->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-400">Nenhuma avaliação registrada ainda.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- TAB: POR PERÍODO                                          --}}
    {{-- ========================================================= --}}
    <div x-show="tab === 'periodo'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="space-y-8" x-cloak>
        
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-md mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="ri-information-line text-blue-400 text-lg"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        O rastreamento por período coleta eventos individuais em tempo real. Dados anteriores à implementação desta métrica não estão incluídos nestes filtros temporais, por isso os totais abaixo podem diferir dos totais globais.
                    </p>
                </div>
            </div>
        </div>

        <div x-data="{ period: 'week' }">
            {{-- Period Navigation --}}
            <div class="flex justify-center mb-8">
                <div class="inline-flex bg-white rounded-lg p-1 shadow-sm border border-gray-100">
                    <button @click="period = 'today'" :class="period === 'today' ? 'bg-blue-50 text-blue-700 font-semibold shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-6 py-2 text-sm rounded-md transition-all duration-200">
                        Hoje
                    </button>
                    <button @click="period = 'week'" :class="period === 'week' ? 'bg-blue-50 text-blue-700 font-semibold shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-6 py-2 text-sm rounded-md transition-all duration-200">
                        Esta Semana
                    </button>
                    <button @click="period = 'month'" :class="period === 'month' ? 'bg-blue-50 text-blue-700 font-semibold shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-6 py-2 text-sm rounded-md transition-all duration-200">
                        Este Mês
                    </button>
                </div>
            </div>

            {{-- ───────────────────────────────────────────────────────── --}}
            {{-- PERÍODO: HOJE --}}
            <div x-show="period === 'today'" x-cloak class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Downloads Hoje</p>
                        <p class="text-3xl font-bold text-blue-600">{{ number_format($downloadsToday) }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Visualizações Hoje</p>
                        <p class="text-3xl font-bold text-purple-600">{{ number_format($viewsToday) }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Cliques Compra Hoje</p>
                        <p class="text-3xl font-bold text-[#004D40]">{{ number_format($clicksToday) }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    @include('admin.analytics.partials._period_breakdown', ['title' => 'Top Livros Baixados (Hoje)', 'data' => $breakdownToday['books'], 'type' => 'book'])
                    @include('admin.analytics.partials._period_breakdown', ['title' => 'Top Categorias (Hoje)', 'data' => $breakdownToday['categories'], 'type' => 'category'])
                    @include('admin.analytics.partials._period_breakdown', ['title' => 'Top Formatos (Hoje)', 'data' => $breakdownToday['formats'], 'type' => 'format'])
                </div>
            </div>

            {{-- ───────────────────────────────────────────────────────── --}}
            {{-- PERÍODO: ESTA SEMANA --}}
            <div x-show="period === 'week'" x-cloak class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Downloads na Semana</p>
                        <p class="text-3xl font-bold text-blue-600">{{ number_format($downloadsWeek) }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Visualizações na Semana</p>
                        <p class="text-3xl font-bold text-purple-600">{{ number_format($viewsWeek) }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Cliques Compra na Semana</p>
                        <p class="text-3xl font-bold text-[#004D40]">{{ number_format($clicksWeek) }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    @include('admin.analytics.partials._period_breakdown', ['title' => 'Top Livros Baixados (Semana)', 'data' => $breakdownWeek['books'], 'type' => 'book'])
                    @include('admin.analytics.partials._period_breakdown', ['title' => 'Top Categorias (Semana)', 'data' => $breakdownWeek['categories'], 'type' => 'category'])
                    @include('admin.analytics.partials._period_breakdown', ['title' => 'Top Formatos (Semana)', 'data' => $breakdownWeek['formats'], 'type' => 'format'])
                </div>
            </div>

            {{-- ───────────────────────────────────────────────────────── --}}
            {{-- PERÍODO: ESTE MÊS --}}
            <div x-show="period === 'month'" x-cloak class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Downloads no Mês</p>
                        <p class="text-3xl font-bold text-blue-600">{{ number_format($downloadsMonth) }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Visualizações no Mês</p>
                        <p class="text-3xl font-bold text-purple-600">{{ number_format($viewsMonth) }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Cliques Compra no Mês</p>
                        <p class="text-3xl font-bold text-[#004D40]">{{ number_format($clicksMonth) }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    @include('admin.analytics.partials._period_breakdown', ['title' => 'Top Livros Baixados (Mês)', 'data' => $breakdownMonth['books'], 'type' => 'book'])
                    @include('admin.analytics.partials._period_breakdown', ['title' => 'Top Categorias (Mês)', 'data' => $breakdownMonth['categories'], 'type' => 'category'])
                    @include('admin.analytics.partials._period_breakdown', ['title' => 'Top Formatos (Mês)', 'data' => $breakdownMonth['formats'], 'type' => 'format'])
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
