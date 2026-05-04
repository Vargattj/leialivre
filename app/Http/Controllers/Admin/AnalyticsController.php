<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\File;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // ── Totais gerais ────────────────────────────────────────────────
        $totalDownloads  = Book::sum('total_downloads');
        $totalViews      = Book::sum('views');
        $totalBooks      = Book::count();
        $activeBooksCount = Book::where('is_active', true)->count();
        $totalRatings    = Rating::count();
        $averageRating   = Rating::avg('rating');

        // ── Top 10 livros mais baixados ──────────────────────────────────
        $topDownloadedBooks = Book::with(['mainAuthors', 'categories'])
            ->where('total_downloads', '>', 0)
            ->orderByDesc('total_downloads')
            ->limit(10)
            ->get();

        // ── Top 10 livros mais visualizados ─────────────────────────────
        $topViewedBooks = Book::with(['mainAuthors'])
            ->where('views', '>', 0)
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        // ── Downloads por formato (a partir da tabela files) ─────────────
        $downloadsByFormat = File::select('format', DB::raw('SUM(total_downloads) as total'))
            ->groupBy('format')
            ->orderByDesc('total')
            ->get();

        // ── Livros com atividade de download recente ─────────────────────
        // "Recentemente baixados" = os que foram modificados mais recentemente
        // e têm downloads (proxy, pois não há tabela de eventos de download)
        $recentlyDownloaded = Book::with(['mainAuthors', 'activeFiles'])
            ->where('total_downloads', '>', 0)
            ->orderByDesc('updated_at')
            ->limit(15)
            ->get();

        // ── Top 10 livros mais bem avaliados (com mínimo de avaliações) ──
        $topRatedBooks = Book::with(['mainAuthors'])
            ->where('total_ratings', '>=', 1)
            ->orderByDesc('average_rating')
            ->orderByDesc('total_ratings')
            ->limit(10)
            ->get();

        // ── Top 10 livros mais clicados (Compra) ─────────────────────────
        $topClickedBooks = Book::with(['mainAuthors'])
            ->where('purchase_clicks', '>', 0)
            ->orderByDesc('purchase_clicks')
            ->limit(10)
            ->get();

        // ── Métricas de Clicks em Compra (Total) ─────────────────────────
        $clicksTotal = Book::sum('purchase_clicks');

        // ── Dados por Período ──────────────────────────────────────────────
        $now = now();
        
        $downloadsToday = \App\Models\AnalyticsEvent::where('event_type', 'file_download')->whereDate('created_at', $now->toDateString())->count();
        $viewsToday     = \App\Models\AnalyticsEvent::where('event_type', 'book_view')->whereDate('created_at', $now->toDateString())->count();
        $clicksToday    = \App\Models\AnalyticsEvent::where('event_type', 'purchase_click')->whereDate('created_at', $now->toDateString())->count();

        $downloadsWeek  = \App\Models\AnalyticsEvent::where('event_type', 'file_download')->where('created_at', '>=', $now->copy()->startOfWeek())->count();
        $viewsWeek      = \App\Models\AnalyticsEvent::where('event_type', 'book_view')->where('created_at', '>=', $now->copy()->startOfWeek())->count();
        $clicksWeek     = \App\Models\AnalyticsEvent::where('event_type', 'purchase_click')->where('created_at', '>=', $now->copy()->startOfWeek())->count();

        $downloadsMonth = \App\Models\AnalyticsEvent::where('event_type', 'file_download')->where('created_at', '>=', $now->copy()->startOfMonth())->count();
        $viewsMonth     = \App\Models\AnalyticsEvent::where('event_type', 'book_view')->where('created_at', '>=', $now->copy()->startOfMonth())->count();
        $clicksMonth    = \App\Models\AnalyticsEvent::where('event_type', 'purchase_click')->where('created_at', '>=', $now->copy()->startOfMonth())->count();

        // Helper function for period breakdowns (Top 5)
        $getPeriodBreakdown = function ($startDate) {
            $books = \App\Models\AnalyticsEvent::where('analytics_events.event_type', 'file_download')
                ->where('analytics_events.created_at', '>=', $startDate)
                ->select('analytics_events.book_id', DB::raw('count(*) as total'))
                ->groupBy('analytics_events.book_id')->orderByDesc('total')->limit(5)->with('book')->get();

            $categories = \App\Models\AnalyticsEvent::where('analytics_events.event_type', 'file_download')
                ->where('analytics_events.created_at', '>=', $startDate)
                ->join('book_category', 'analytics_events.book_id', '=', 'book_category.book_id')
                ->join('categories', 'book_category.category_id', '=', 'categories.id')
                ->select('categories.name', DB::raw('count(*) as total'))
                ->groupBy('categories.id', 'categories.name')->orderByDesc('total')->limit(5)->get();

            $formats = \App\Models\AnalyticsEvent::where('analytics_events.event_type', 'file_download')
                ->where('analytics_events.created_at', '>=', $startDate)
                ->join('files', 'analytics_events.file_id', '=', 'files.id')
                ->select('files.format', DB::raw('count(*) as total'))
                ->groupBy('files.format')->orderByDesc('total')->limit(5)->get();

            return compact('books', 'categories', 'formats');
        };

        $breakdownToday = $getPeriodBreakdown($now->toDateString());
        $breakdownWeek  = $getPeriodBreakdown($now->copy()->startOfWeek());
        $breakdownMonth = $getPeriodBreakdown($now->copy()->startOfMonth());

        // ── Avaliações mais recentes ──────────────────────────────────────
        $recentRatings = Rating::with('book')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // ── Livros sem downloads ──────────────────────────────────────────
        $booksWithNoDownloads = Book::where('is_active', true)
            ->where('total_downloads', 0)
            ->count();

        // ── Downloads por categoria ───────────────────────────────────────
        $downloadsByCategory = DB::table('books')
            ->join('book_category', 'books.id', '=', 'book_category.book_id')
            ->join('categories', 'book_category.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(books.total_downloads) as total'))
            ->where('books.total_downloads', '>', 0)
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // ── Conversion rate: visualizações → downloads ───────────────────
        $conversionRate = $totalViews > 0
            ? round(($totalDownloads / $totalViews) * 100, 1)
            : 0;

        // ── Formatos disponíveis (total de arquivos ativos) ───────────────
        $filesByFormat = File::select('format', DB::raw('COUNT(*) as count'))
            ->where('is_active', true)
            ->groupBy('format')
            ->orderByDesc('count')
            ->get();

        return view('admin.analytics.index', compact(
            'totalDownloads',
            'totalViews',
            'totalBooks',
            'activeBooksCount',
            'totalRatings',
            'averageRating',
            'topDownloadedBooks',
            'topViewedBooks',
            'downloadsByFormat',
            'recentlyDownloaded',
            'topRatedBooks',
            'recentRatings',
            'booksWithNoDownloads',
            'downloadsByCategory',
            'conversionRate',
            'filesByFormat',
            'topClickedBooks',
            'clicksTotal',
            'downloadsToday', 'viewsToday', 'clicksToday',
            'downloadsWeek', 'viewsWeek', 'clicksWeek',
            'downloadsMonth', 'viewsMonth', 'clicksMonth',
            'breakdownToday', 'breakdownWeek', 'breakdownMonth'
        ));
    }
}
