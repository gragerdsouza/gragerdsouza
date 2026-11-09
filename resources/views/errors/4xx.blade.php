<x-error-page :profile="$profile" :code="$exception->getStatusCode()" :title="\Symfony\Component\HttpFoundation\Response::$statusTexts[$exception->getStatusCode()] ?? 'Request error'">
    The request couldn't be completed.
</x-error-page>
