@php($retryAfter = $exception->getHeaders()['Retry-After'] ?? null)

<x-error-page :profile="$profile" code="429" title="Too many requests">
    You're going a little fast.
    @if ($retryAfter)
        Try again in {{ $retryAfter }} seconds.
    @else
        Try again in a minute.
    @endif
</x-error-page>
