<x-layout :profile="$profile" title="Projects" description="Selected projects by {{ $profile->name }}.">
    <section class="mx-auto max-w-5xl px-4 py-20 sm:px-6">
        <x-section-heading eyebrow="Work" title="Projects" />

        @if ($projects->isEmpty())
            <p class="text-ink-400">Nothing published yet — check back soon.</p>
        @else
            <div class="grid gap-6 md:grid-cols-2">
                @foreach ($projects as $project)
                    <x-project-card :project="$project" />
                @endforeach
            </div>
            <div class="mt-10">{{ $projects->links() }}</div>
        @endif
    </section>
</x-layout>
