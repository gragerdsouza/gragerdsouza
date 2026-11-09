<x-error-page :profile="$profile" code="405" title="Method not allowed">
    This address doesn't accept a {{ request()->method() }} request.
</x-error-page>
