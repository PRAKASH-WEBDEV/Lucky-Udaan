<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') · Staff · {{ setting('site_name') }}</title>
    <link rel="icon" href="{{ upload_url(setting('favicon')) ?? asset('favicon.png') }}">
    @vite(['resources/scss/admin.scss', 'resources/js/admin.js'])
</head>
<body class="bg-light">
@yield('page')
</body>
</html>
