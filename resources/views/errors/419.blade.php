<x-error-page :profile="$profile" code="419" title="Page expired">
    The form sat open for too long and its security token expired. Go back, reload the page and try again.

    <x-slot:actions>
        <a href="{{ route('home') }}#contact" class="rounded-md bg-accent-400 px-5 py-2.5 text-sm font-medium text-ink-950 hover:bg-accent-300">Back to the contact form</a>
        <a href="{{ route('home') }}" class="rounded-md border border-white/10 px-5 py-2.5 text-sm font-medium text-white hover:border-white/30">Back home</a>
    </x-slot:actions>
</x-error-page>
