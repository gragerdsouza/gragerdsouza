@props(['project'])

<article class="group relative flex flex-col rounded-xl border border-white/5 bg-ink-900/60 p-6 transition hover:border-accent-400/40 hover:bg-ink-900">
    <h3 class="text-lg font-semibold text-white">
        <a href="{{ route('projects.show', $project) }}" class="after:absolute after:inset-0">{{ $project->title }}</a>
    </h3>
    <p class="mt-2 flex-1 text-sm leading-relaxed text-ink-300">{{ $project->summary }}</p>
    @if ($project->tech_stack)
        <ul class="mt-4 flex flex-wrap gap-2" aria-label="Tech stack">
            @foreach ($project->tech_stack as $tech)
                <li class="rounded-full border border-white/10 px-2.5 py-0.5 font-mono text-xs text-ink-300">{{ $tech }}</li>
            @endforeach
        </ul>
    @endif
</article>
