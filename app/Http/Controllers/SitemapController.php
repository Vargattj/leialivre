<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class SitemapController extends Controller
{
    /**
     * Generate and return the sitemap.xml
     */
    public function index(): Response
    {
        // Get all active books with their update dates
        $books = Book::active()
            ->select('slug', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Get all authors with their update dates
        $authors = Author::select('slug', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Get all categories
        $categories = Category::select('slug', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Build the sitemap XML
        $sitemap = $this->generateSitemapXml($books, $authors, $categories);

        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate the sitemap XML content
     */
    private function generateSitemapXml($books, $authors, $categories): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Home page (highest priority, changes daily)
        $xml .= $this->addUrl(
            route('home'),
            now()->toAtomString(),
            'daily',
            '1.0'
        );

        // Main pages
        $xml .= $this->addUrl(
            route('livros.index'),
            now()->toAtomString(),
            'daily',
            '0.9'
        );

        $xml .= $this->addUrl(
            route('autores.index'),
            now()->toAtomString(),
            'weekly',
            '0.9'
        );

        // About and Contact pages (if they exist)
        if (Route::has('about.index')) {
            $xml .= $this->addUrl(
                route('about.index'),
                now()->toAtomString(),
                'monthly',
                '0.5'
            );
        }

        if (Route::has('contact.index')) {
            $xml .= $this->addUrl(
                route('contact.index'),
                now()->toAtomString(),
                'monthly',
                '0.5'
            );
        }

        // Book pages (high priority, changes weekly)
        foreach ($books as $book) {
            $xml .= $this->addUrl(
                route('livros.show', $book->slug),
                $book->updated_at->toAtomString(),
                'weekly',
                '0.8'
            );
        }

        // Author pages (medium priority, changes monthly)
        foreach ($authors as $author) {
            $xml .= $this->addUrl(
                route('autores.show', $author->slug),
                $author->updated_at->toAtomString(),
                'monthly',
                '0.7'
            );
        }

        // Category pages (if you have category routes)
        foreach ($categories as $category) {
            $xml .= $this->addUrl(
                route('livros.categorias', $category->slug),
                $category->updated_at->toAtomString(),
                'weekly',
                '0.6'
            );
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Add a URL entry to the sitemap
     */
    private function addUrl(string $loc, string $lastmod, string $changefreq, string $priority): string
    {
        $xml = '<url>';
        $xml .= '<loc>' . htmlspecialchars($loc) . '</loc>';
        $xml .= '<lastmod>' . $lastmod . '</lastmod>';
        $xml .= '<changefreq>' . $changefreq . '</changefreq>';
        $xml .= '<priority>' . $priority . '</priority>';
        $xml .= '</url>';

        return $xml;
    }
}
