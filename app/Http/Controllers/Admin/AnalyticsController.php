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
        ));
    }
}
