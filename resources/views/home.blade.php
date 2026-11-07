<x-layout :profile="$profile">
    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 hero-glow"></div>
        <div class="relative mx-auto max-w-5xl px-4 pb-20 pt-24 sm:px-6 sm:pt-32">
            @if ($profile->available_for_work)
                <p class="inline-flex items-center gap-2 rounded-full border border-accent-400/30 bg-accent-400/10 px-3 py-1 font-mono text-xs text-accent-300">
                    <span class="size-1.5 animate-pulse rounded-full bg-accent-400"></span> Available for new work
                </p>
            @endif
            <h1 class="mt-6 text-4xl font-semibold tracking-tight text-white sm:text-6xl">{{ $profile->name }}</h1>
            <p class="mt-4 font-mono text-base text-accent-300 sm:text-lg">{{ $profile->headline }}</p>
            @if ($profile->tagline)
                <p class="mt-6 max-w-2xl text-lg leading-relaxed text-ink-300">{{ $profile->tagline }}</p>
            @endif
            <div class="mt-10 flex flex-wrap gap-3">
                <a href="#contact" class="rounded-md bg-accent-400 px-5 py-2.5 text-sm font-medium text-ink-950 hover:bg-accent-300">Get in touch</a>
                <a href="{{ route('projects.index') }}" class="rounded-md border border-white/10 px-5 py-2.5 text-sm font-medium text-white hover:border-white/30">See projects</a>
                @if ($profile->resume_url)
                    <a href="{{ $profile->resume_url }}" class="rounded-md px-5 py-2.5 text-sm font-medium text-ink-300 hover:text-white" target="_blank" rel="noopener">Résumé ↗</a>
                @endif
            </div>
        </div>
    </section>

    {{-- About --}}
    <section id="about" class="mx-auto max-w-5xl px-4 py-16 sm:px-6">
        <x-section-heading eyebrow="01 · About" title="Developer across the stack" />
        <div class="grid gap-10 md:grid-cols-[1fr_auto]">
            <div class="max-w-2xl space-y-4 text-base leading-relaxed text-ink-300">
                {!! nl2br(e($profile->bio)) !!}
            </div>
            @if ($profile->avatarUrl())
                <img src="{{ $profile->avatarUrl() }}" alt="{{ $profile->name }}" class="size-40 rounded-2xl object-cover ring-1 ring-white/10">
            @endif
        </div>
    </section>

    {{-- Stack --}}
    @if ($skills->isNotEmpty())
        <section id="stack" class="mx-auto max-w-5xl px-4 py-16 sm:px-6">
            <x-section-heading eyebrow="02 · Stack" title="Tools I ship with" />
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($skills as $category => $group)
                    <div class="rounded-xl border border-white/5 bg-ink-900/60 p-5">
                        <h3 class="font-mono text-xs uppercase tracking-widest text-ink-400">{{ $category }}</h3>
                        <ul class="mt-4 flex flex-wrap gap-2">
                            @foreach ($group as $skill)
                                <li class="rounded-md bg-white/5 px-2.5 py-1 text-sm text-ink-100">{{ $skill->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Experience --}}
    @if ($experiences->isNotEmpty())
        <section id="experience" class="mx-auto max-w-5xl px-4 py-16 sm:px-6">
            <x-section-heading eyebrow="03 · Experience" title="Where I've worked" />
            <ol class="relative space-y-10 border-l border-white/10 pl-6">
                @foreach ($experiences as $experience)
                    <li class="relative">
                        <span @class(['absolute -left-[1.85rem] top-1.5 size-3 rounded-full ring-4 ring-ink-950', 'bg-accent-400' => $experience->isCurrent(), 'bg-ink-500' => ! $experience->isCurrent()])></span>
                        <p class="font-mono text-xs text-ink-400">{{ $experience->period() }}@if ($experience->location) · {{ $experience->location }}@endif</p>
                        <h3 class="mt-1 text-lg font-semibold text-white">{{ $experience->role }} <span class="text-ink-400">@ {{ $experience->company }}</span></h3>
                        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-ink-300">{{ $experience->summary }}</p>
                    </li>
                @endforeach
            </ol>
        </section>
    @endif

    {{-- Featured projects --}}
    @if ($projects->isNotEmpty())
        <section id="projects" class="mx-auto max-w-5xl px-4 py-16 sm:px-6">
            <x-section-heading eyebrow="04 · Work" title="Featured projects" />
            <div class="grid gap-6 md:grid-cols-2">
                @foreach ($projects as $project)
                    <x-project-card :project="$project" />
                @endforeach
            </div>
            <a href="{{ route('projects.index') }}" class="mt-8 inline-block font-mono text-sm text-accent-300 hover:text-accent-200">All projects →</a>
        </section>
    @endif

    {{-- Contact --}}
    <section id="contact" class="mx-auto max-w-5xl px-4 py-16 sm:px-6">
        <x-section-heading eyebrow="05 · Contact" title="Let's build something" />
        <div class="grid gap-10 md:grid-cols-[1fr_1.4fr]">
            <div class="space-y-3 text-ink-300">
                <p>Have a project, role or idea? Send a message — it lands straight in my inbox.</p>
                <p class="font-mono text-sm"><a class="text-accent-300 hover:text-accent-200" href="mailto:{{ $profile->email }}">{{ $profile->email }}</a></p>
                @if ($profile->location)
                    <p class="text-sm text-ink-400">Based in {{ $profile->location }}</p>
                @endif
            </div>

            <form method="POST" action="{{ route('contact.store') }}" class="space-y-4" novalidate>
                @csrf
                @if (session('status'))
                    <p role="status" class="rounded-md border border-accent-400/30 bg-accent-400/10 px-4 py-3 text-sm text-accent-200">{{ session('status') }}</p>
                @endif

                <div class="hidden" aria-hidden="true">
                    <label for="website">Website</label>
                    <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-form-field name="name" label="Name" autocomplete="name" required />
                    <x-form-field name="email" label="Email" type="email" autocomplete="email" required />
                </div>
                <x-form-field name="subject" label="Subject" />
                <x-form-field name="message" label="Message" type="textarea" required />

                <button type="submit" class="rounded-md bg-accent-400 px-5 py-2.5 text-sm font-medium text-ink-950 hover:bg-accent-300">Send message</button>
            </form>
        </div>
    </section>
</x-layout>
