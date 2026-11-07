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
        <nav class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-4 py-3 sm:px-6 sm:py-4" aria-label="Main">
            <a href="/" class="shrink-0 font-mono text-sm font-semibold tracking-tight text-white">
                <span class="text-accent-400">~/</span><span class="hidden min-[420px]:inline">{{ \Illuminate\Support\Str::of($profile->name)->lower()->slug() }}</span>
            </a>
            {{-- Anchors are host-relative ("/#stack") so they work from any page and any domain. --}}
            <ul class="-mr-2 flex items-center gap-0.5 overflow-x-auto text-sm text-ink-300 sm:gap-1">
                @foreach (['about' => 'About', 'stack' => 'Stack', 'experience' => 'Experience'] as $section => $label)
                    <li><a class="nav-link" data-nav-link href="/#{{ $section }}">{{ $label }}</a></li>
                @endforeach
                <li><a class="nav-link" href="{{ route('projects.index') }}" @if (request()->routeIs('projects.*')) aria-current="page" @endif>Projects</a></li>
                <li class="pl-1"><a class="nav-cta" data-nav-link href="/#contact">Contact</a></li>
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
