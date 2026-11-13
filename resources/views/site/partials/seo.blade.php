@php
    $seo = seo();
    $title = $seo->fullTitle();
    $description = $seo->metaDescription();
    $canonical = $seo->canonical ?? url()->current().(request()->has('page') && request('page') > 1 ? '?page='.(int) request('page') : '');
    $image = $seo->ogImage();
    $siteName = setting('site_name', config('app.name'));
    $sameAs = array_values(array_filter([setting('facebook'), setting('instagram'), setting('youtube'), setting('twitter'), setting('linkedin')]));

    $org = array_filter([
        '@context' => 'https://schema.org',
        '@type' => ['NGO', 'Organization'],
        '@id' => url('/').'#organization',
        'name' => $siteName,
        'alternateName' => 'Lucky Udaan',
        'url' => url('/'),
        'logo' => upload_url(setting('logo')),
        'slogan' => setting('tagline'),
        'description' => setting('seo_description'),
        'email' => setting('email'),
        'telephone' => phone_link(setting('phone')),
        'foundingDate' => setting('founded_year'),
        'founder' => setting('contact_person') ? ['@type' => 'Person', 'name' => setting('contact_person')] : null,
        'nonprofitStatus' => 'Nonprofit',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => setting('address'),
            'addressLocality' => setting('city'),
            'addressRegion' => setting('state'),
            'postalCode' => setting('pincode'),
            'addressCountry' => 'IN',
        ],
        'geo' => setting('latitude') ? ['@type' => 'GeoCoordinates', 'latitude' => (float) setting('latitude'), 'longitude' => (float) setting('longitude')] : null,
        'areaServed' => [['@type' => 'City', 'name' => 'Gonda'], ['@type' => 'State', 'name' => 'Uttar Pradesh']],
        'contactPoint' => [['@type' => 'ContactPoint', 'contactType' => 'customer support', 'telephone' => phone_link(setting('phone')), 'email' => setting('email'), 'areaServed' => 'IN', 'availableLanguage' => ['en', 'hi']]],
        'sameAs' => $sameAs ?: null,
    ]);
    $schemas = [$org];
    if (request()->routeIs('home')) {
        $schemas[] = ['@context' => 'https://schema.org', '@type' => 'WebSite', 'name' => $siteName, 'url' => url('/'), 'publisher' => ['@id' => url('/').'#organization']];
    }
    if ($seo->breadcrumbs) {
        $items = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')]];
        foreach ($seo->breadcrumbs as $i => [$name, $link]) {
            $items[] = array_filter(['@type' => 'ListItem', 'position' => $i + 2, 'name' => $name, 'item' => $link ?? url()->current()]);
        }
        $schemas[] = ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items];
    }
    $schemas = array_merge($schemas, $seo->schemas);
@endphp
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
@if (setting('seo_keywords'))<meta name="keywords" content="{{ setting('seo_keywords') }}">@endif
<link rel="canonical" href="{{ $canonical }}">
<meta name="robots" content="{{ $seo->noindex || ! app()->environment('production') ? 'noindex, nofollow' : 'index, follow, max-image-preview:large' }}">
<meta name="geo.region" content="IN-UP">
<meta name="geo.placename" content="{{ setting('city', 'Gonda') }}, {{ setting('state', 'Uttar Pradesh') }}">
@if (setting('latitude'))<meta name="geo.position" content="{{ setting('latitude') }};{{ setting('longitude') }}"><meta name="ICBM" content="{{ setting('latitude') }}, {{ setting('longitude') }}">@endif
@if (setting('google_verification'))<meta name="google-site-verification" content="{{ setting('google_verification') }}">@endif
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="en_IN">
<meta property="og:type" content="{{ $seo->type }}">
<meta property="og:title" content="{{ $seo->title ?: $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
@if ($image)<meta property="og:image" content="{{ $image }}"><meta name="twitter:image" content="{{ $image }}">@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seo->title ?: $title }}">
<meta name="twitter:description" content="{{ $description }}">
@foreach ($schemas as $schema)
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endforeach
