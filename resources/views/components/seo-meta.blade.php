@props([
    'title' => config('app.name'),
    'description' => '',
    'keywords' => '',
    'image' => null,
    'url' => null,
    'type' => 'website',
    'locale' => 'pt_BR',
    'author' => null,
    'jsonLd' => [],
    'ogAdditional' => [],
    'twitterCard' => 'summary_large_image',
    'twitterSite' => null,
    'twitterCreator' => null,
    'index' => true,
    'follow' => true,
])

@php
    use App\Helpers\SeoHelper;
    
    // Set defaults
    $url = $url ?? url()->current();
    $image = $image ?? asset('images/og-default.jpg');
    $siteName = config('app.name');
    
    // Sanitize description
    $description = SeoHelper::sanitizeDescription($description, 160);
    
    // Prepare data for helpers
    $metaData = [
        'title' => $title,
        'description' => $description,
        'keywords' => $keywords,
        'image' => $image,
        'url' => $url,
        'type' => $type,
        'locale' => $locale,
        'site_name' => $siteName,
        'og_additional' => $ogAdditional,
        'twitter_card' => $twitterCard,
        'twitter_site' => $twitterSite,
        'twitter_creator' => $twitterCreator,
    ];
@endphp

{{-- Basic Meta Tags --}}
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">

{{-- Title --}}
<title>{{ $title }}</title>

{{-- Meta Description --}}
@if($description)
<meta name="description" content="{{ $description }}">
@endif

{{-- Meta Keywords --}}
@if($keywords)
<meta name="keywords" content="{{ $keywords }}">
@endif

{{-- Author --}}
@if($author)
<meta name="author" content="{{ $author }}">
@endif

{{-- Canonical URL --}}
{!! SeoHelper::generateCanonical($url) !!}

{{-- Robots --}}
{!! SeoHelper::generateRobots($index, $follow) !!}

{{-- Open Graph Meta Tags --}}
{!! SeoHelper::generateOpenGraph($metaData) !!}

{{-- Twitter Card Meta Tags --}}
{!! SeoHelper::generateTwitterCard($metaData) !!}

{{-- JSON-LD Structured Data --}}
@foreach($jsonLd as $schema)
    @if(is_array($schema) && isset($schema['type']) && isset($schema['data']))
        {!! SeoHelper::generateJsonLd($schema['type'], $schema['data']) !!}
    @endif
@endforeach

{{-- Additional head content --}}
{{ $slot }}
