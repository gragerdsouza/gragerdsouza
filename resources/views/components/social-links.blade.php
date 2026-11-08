@props(['links' => []])

@if (count($links) > 0)
    <ul class="flex flex-wrap gap-4" aria-label="Social links">
        @foreach ($links as $network => $url)
            <li>
                <a href="{{ $url }}" class="capitalize hover:text-accent-300" rel="me noopener noreferrer" target="_blank">{{ $network }}</a>
            </li>
        @endforeach
    </ul>
@endif
