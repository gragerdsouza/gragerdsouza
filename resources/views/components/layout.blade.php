@props(['profile', 'title' => null, 'description' => null])

@php
    $pageTitle = $title ? $title.' · '.$profile->name : $profile->name.' — '.$profile->headline;
    $metaDescription = $description ?? $profile->tagline ?? $profile->headline;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary">
    <meta name="theme-color" content="#0b0f14">
    <link rel="sitemap" type="application/xml" href="{{ route('sitemap') }}">

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-ink-950 font-sans text-ink-100 antialiased selection:bg-accent-400/30">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-accent-400 focus:px-3 focus:py-2 focus:text-ink-950">Skip to content</a>

    <header class="sticky top-0 z-40 border-b border-white/5 bg-ink-950/80 backdrop-blur">
        <nav class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4 sm:px-6" aria-label="Main">
            <a href="{{ route('home') }}" class="font-mono text-sm font-semibold tracking-tight text-white">
                <span class="text-accent-400">~/</span>{{ \Illuminate\Support\Str::of($profile->name)->lower()->slug() }}
            </a>
            <ul class="flex items-center gap-5 text-sm text-ink-300">
                <li class="hidden sm:block"><a class="hover:text-white" href="{{ route('home') }}#stack">Stack</a></li>
                <li class="hidden sm:block"><a class="hover:text-white" href="{{ route('home') }}#experience">Experience</a></li>
                <li><a class="hover:text-white" href="{{ route('projects.index') }}">Projects</a></li>
                <li><a class="rounded-md bg-accent-400 px-3 py-1.5 font-medium text-ink-950 hover:bg-accent-300" href="{{ route('home') }}#contact">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main id="main">
        {{ $slot }}
    </main>

    <footer class="border-t border-white/5">
        <div class="mx-auto flex max-w-5xl flex-col gap-4 px-4 py-8 text-sm text-ink-400 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <p>&copy; {{ now()->year }} {{ $profile->name }}. Built with Laravel, tested with Pest, shipped by CI.</p>
            <x-social-links :links="$profile->social_links ?? []" />
        </div>
    </footer>
</body>
</html>
