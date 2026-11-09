@php
    // `php artisan down --retry=N` passes $retryAfter when pre-rendering (--render), or a Retry-After header otherwise.
    $retryAfter ??= isset($exception) ? ($exception->getHeaders()['Retry-After'] ?? null) : null;
    $minutes = is_numeric($retryAfter) ? max(1, (int) ceil($retryAfter / 60)) : null;
@endphp

<x-error-page :profile="$profile" code="503" title="Back soon">
    The site is down for a quick update.
    @if ($minutes)
        Please check back in about {{ $minutes }} {{ \Illuminate\Support\Str::plural('minute', $minutes) }}.
    @else
        Please check back in a few minutes.
    @endif

    <x-slot:actions>
        <a href="{{ route('home') }}" class="rounded-md bg-accent-400 px-5 py-2.5 text-sm font-medium text-ink-950 hover:bg-accent-300">Try again</a>
    </x-slot:actions>
</x-error-page>
