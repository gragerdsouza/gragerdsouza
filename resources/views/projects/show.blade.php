<x-layout :profile="$profile" :title="$project->title" :description="$project->summary">
    <article class="mx-auto max-w-3xl px-4 py-20 sm:px-6">
        <a href="{{ route('projects.index') }}" class="font-mono text-sm text-ink-400 hover:text-white">← All projects</a>
        <h1 class="mt-6 text-3xl font-semibold tracking-tight text-white sm:text-4xl">{{ $project->title }}</h1>
        <p class="mt-4 text-lg text-ink-300">{{ $project->summary }}</p>

        <div class="mt-6 flex flex-wrap items-center gap-3">
            @foreach ($project->tech_stack ?? [] as $tech)
                <span class="rounded-full border border-white/10 px-2.5 py-0.5 font-mono text-xs text-ink-300">{{ $tech }}</span>
            @endforeach
        </div>

        <div class="mt-6 flex gap-4 text-sm">
            @if ($project->url)
                <a href="{{ $project->url }}" class="text-accent-300 hover:text-accent-200" target="_blank" rel="noopener">Live site ↗</a>
            @endif
            @if ($project->repository_url)
                <a href="{{ $project->repository_url }}" class="text-accent-300 hover:text-accent-200" target="_blank" rel="noopener">Source code ↗</a>
            @endif
        </div>

        <div class="prose-portfolio mt-12">
            {!! $project->bodyHtml() !!}
        </div>
    </article>
</x-layout>
