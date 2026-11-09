<x-error-page :profile="$profile" :code="$exception->getStatusCode()" :title="\Symfony\Component\HttpFoundation\Response::$statusTexts[$exception->getStatusCode()] ?? 'Server error'">
    Something went wrong on the server. Please try again in a moment.
</x-error-page>
