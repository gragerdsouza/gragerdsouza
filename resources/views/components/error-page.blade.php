@props(['profile', 'code', 'title'])

@php
    $path = \Illuminate\Support\Str::limit('/'.ltrim(request()->path(), '/'), 60);
    $statusText = \Symfony\Component\HttpFoundation\Response::$statusTexts[(int) $code] ?? 'Error';
@endphp

{{-- Shared shell for resources/views/errors/*: the site layout plus a terminal-style status readout. --}}
<x-layout :profile="$profile" :title="$title">
    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 hero-glow"></div>
        <div class="relative mx-auto max-w-5xl px-4 pb-24 pt-20 sm:px-6 sm:pt-28">
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-accent-400">Error {{ $code }}</p>
            <h1 class="mt-3 text-4xl font-semibold tracking-tight text-white sm:text-6xl">{{ $title }}</h1>
            <div class="mt-5 max-w-2xl text-lg leading-relaxed text-ink-300">{{ $slot }}</div>

            <div class="mt-10 max-w-2xl overflow-hidden rounded-xl border border-white/5 bg-ink-900/60 shadow-xl shadow-black/30" aria-hidden="true">
                <div class="flex gap-1.5 border-b border-white/5 px-4 py-3">
                    <span class="size-2.5 rounded-full bg-white/10"></span>
                    <span class="size-2.5 rounded-full bg-white/10"></span>
                    <span class="size-2.5 rounded-full bg-white/10"></span>
                </div>
                <pre class="overflow-x-auto px-4 py-4 font-mono text-sm leading-7 text-ink-300"><span class="text-accent-400">~/{{ \Illuminate\Support\Str::of($profile->name)->lower()->slug() }}</span> <span class="text-ink-500">$</span> curl -X {{ request()->method() }} {{ $path }}
<span class="text-white">HTTP/2 {{ $code }}</span> <span class="text-accent-300">{{ $statusText }}</span><span class="ml-1 inline-block h-4 w-2 translate-y-0.5 bg-accent-400 motion-safe:animate-pulse"></span></pre>
            </div>

            <div class="mt-10 flex flex-wrap gap-3">
                @isset($actions)
                    {{ $actions }}
                @else
                    <a href="{{ route('home') }}" class="rounded-md bg-accent-400 px-5 py-2.5 text-sm font-medium text-ink-950 hover:bg-accent-300">Back home</a>
                    <a href="{{ route('projects.index') }}" class="rounded-md border border-white/10 px-5 py-2.5 text-sm font-medium text-white hover:border-white/30">See projects</a>
                @endisset
            </div>
        </div>
    </section>
</x-layout>
