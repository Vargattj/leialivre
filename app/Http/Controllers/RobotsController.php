<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * Generate and return the robots.txt
     */
    public function index(): Response
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /download/\n";
        $content .= "\n";
        $content .= "# Sitemap\n";
        $content .= "Sitemap: " . url('/sitemap.xml') . "\n";

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}
