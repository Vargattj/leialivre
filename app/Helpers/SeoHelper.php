<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class SeoHelper
{
    /**
     * Sanitize and limit text for meta descriptions
     */
    public static function sanitizeDescription(?string $text, int $maxLength = 160): string
    {
        if (empty($text)) {
            return '';
        }

        // Remove HTML tags
        $text = strip_tags($text);
        
        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Trim
        $text = trim($text);
        
        // Limit length
        if (strlen($text) > $maxLength) {
            $text = Str::limit($text, $maxLength, '...');
        }
        
        return $text;
    }

    /**
     * Generate meta tags for a page
     */
    public static function generateMetaTags(array $data): array
    {
        $defaults = [
            'title' => config('app.name'),
            'description' => '',
            'keywords' => '',
            'image' => asset('images/og-default.jpg'),
            'url' => url()->current(),
            'type' => 'website',
            'locale' => 'pt_BR',
            'site_name' => config('app.name'),
        ];

        return array_merge($defaults, $data);
    }

    /**
     * Generate Open Graph meta tags
     */
    public static function generateOpenGraph(array $data): string
    {
        $meta = self::generateMetaTags($data);
        
        $tags = [
            'og:title' => $meta['title'],
            'og:description' => $meta['description'],
            'og:type' => $meta['type'],
            'og:url' => $meta['url'],
            'og:image' => $meta['image'],
            'og:locale' => $meta['locale'],
            'og:site_name' => $meta['site_name'],
        ];

        // Add additional Open Graph tags if provided
        if (isset($data['og_additional'])) {
            $tags = array_merge($tags, $data['og_additional']);
        }

        $html = '';
        foreach ($tags as $property => $content) {
            if (!empty($content)) {
                $html .= '<meta property="' . $property . '" content="' . htmlspecialchars($content) . '">' . "\n";
            }
        }

        return $html;
    }

    /**
     * Generate Twitter Card meta tags
     */
    public static function generateTwitterCard(array $data): string
    {
        $meta = self::generateMetaTags($data);
        
        $tags = [
            'twitter:card' => $data['twitter_card'] ?? 'summary_large_image',
            'twitter:title' => $meta['title'],
            'twitter:description' => $meta['description'],
            'twitter:image' => $meta['image'],
        ];

        // Add Twitter site/creator if provided
        if (isset($data['twitter_site'])) {
            $tags['twitter:site'] = $data['twitter_site'];
        }
        if (isset($data['twitter_creator'])) {
            $tags['twitter:creator'] = $data['twitter_creator'];
        }

        $html = '';
        foreach ($tags as $name => $content) {
            if (!empty($content)) {
                $html .= '<meta name="' . $name . '" content="' . htmlspecialchars($content) . '">' . "\n";
            }
        }

        return $html;
    }

    /**
     * Generate JSON-LD structured data
     */
    public static function generateJsonLd(string $type, array $data): string
    {
        $jsonLd = match($type) {
            'WebSite' => self::generateWebSiteSchema($data),
            'Organization' => self::generateOrganizationSchema($data),
            'Book' => self::generateBookSchema($data),
            'Person' => self::generatePersonSchema($data),
            'BreadcrumbList' => self::generateBreadcrumbSchema($data),
            'ItemList' => self::generateItemListSchema($data),
            default => null,
        };

        if (!$jsonLd) {
            return '';
        }

        return '<script type="application/ld+json">' . "\n" . 
               json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . 
               "\n" . '</script>';
    }

    /**
     * Generate WebSite schema
     */
    private static function generateWebSiteSchema(array $data): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $data['name'] ?? config('app.name'),
            'description' => $data['description'] ?? '',
            'url' => $data['url'] ?? url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => $data['search_url'] ?? url('/livros/buscar?q={search_term_string}'),
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * Generate Organization schema
     */
    private static function generateOrganizationSchema(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $data['name'] ?? config('app.name'),
            'url' => $data['url'] ?? url('/'),
        ];

        if (isset($data['logo'])) {
            $schema['logo'] = $data['logo'];
        }

        if (isset($data['sameAs'])) {
            $schema['sameAs'] = $data['sameAs'];
        }

        return $schema;
    }

    /**
     * Generate Book schema
     */
    private static function generateBookSchema(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Book',
            'name' => $data['name'],
            'url' => $data['url'] ?? url()->current(),
        ];

        // Optional fields
        $optionalFields = [
            'author', 'datePublished', 'description', 'inLanguage',
            'numberOfPages', 'image', 'publisher', 'genre', 'isbn'
        ];

        foreach ($optionalFields as $field) {
            if (isset($data[$field])) {
                $schema[$field] = $data[$field];
            }
        }

        // Add aggregate rating if available
        if (isset($data['aggregateRating'])) {
            $schema['aggregateRating'] = $data['aggregateRating'];
        }

        // Add offers (free download)
        if (isset($data['offers'])) {
            $schema['offers'] = $data['offers'];
        }

        return $schema;
    }

    /**
     * Generate Person schema
     */
    private static function generatePersonSchema(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $data['name'],
            'url' => $data['url'] ?? url()->current(),
        ];

        // Optional fields
        $optionalFields = [
            'description', 'birthDate', 'deathDate', 'nationality',
            'image', 'sameAs', 'worksFor'
        ];

        foreach ($optionalFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $schema[$field] = $data[$field];
            }
        }

        return $schema;
    }

    /**
     * Generate BreadcrumbList schema
     */
    private static function generateBreadcrumbSchema(array $items): array
    {
        $listItems = [];
        
        foreach ($items as $index => $item) {
            $listItems[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $listItems,
        ];
    }

    /**
     * Generate ItemList schema
     */
    private static function generateItemListSchema(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $data['name'],
            'numberOfItems' => $data['numberOfItems'],
            'itemListElement' => $data['items'],
        ];

        return $schema;
    }

    /**
     * Generate canonical URL
     */
    public static function generateCanonical(?string $url = null): string
    {
        $canonicalUrl = $url ?? url()->current();
        return '<link rel="canonical" href="' . htmlspecialchars($canonicalUrl) . '">';
    }

    /**
     * Generate robots meta tag
     */
    public static function generateRobots(bool $index = true, bool $follow = true): string
    {
        $content = ($index ? 'index' : 'noindex') . ', ' . ($follow ? 'follow' : 'nofollow');
        return '<meta name="robots" content="' . $content . '">';
    }
}
