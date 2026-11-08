{{--
    /llms.txt (https://llmstxt.org). Served as text/plain, so values are printed raw
    (not HTML-escaped) and squished onto one line to keep the Markdown structure intact.
--}}
@php($line = fn (?string $text): string => \Illuminate\Support\Str::squish((string) $text))
# {!! $line($profile->name) !!}

> {!! \Illuminate\Support\Str::finish($line($profile->headline), '.') !!}{!! $profile->tagline ? ' '.$line($profile->tagline) : '' !!}

{!! $line($profile->bio) !!}
@if ($profile->location)

Based in {!! $line($profile->location) !!}.
@endif

## Pages

- [Home]({!! route('home') !!}): profile, skills, experience and contact form
- [Projects]({!! route('projects.index') !!}): all published projects
@if ($projects->isNotEmpty())

## Projects

@foreach ($projects as $project)
- [{!! $line($project->title) !!}]({!! route('projects.show', $project) !!}): {!! $line($project->summary) !!}
@endforeach
@endif
@if ($skills->isNotEmpty())

## Skills

@foreach ($skills as $category => $categorySkills)
- {!! $category !!}: {!! $categorySkills->map(fn ($skill): string => $line($skill->name))->implode(', ') !!}
@endforeach
@endif
@if ($experiences->isNotEmpty())

## Experience

@foreach ($experiences as $experience)
- {!! $line($experience->role) !!} at {!! $line($experience->company) !!} ({!! $experience->period() !!}): {!! $line($experience->summary) !!}
@endforeach
@endif
@if ($certifications->isNotEmpty())

## Certifications

@foreach ($certifications as $certification)
- {!! $line($certification->name) !!} ({!! $line($certification->issuer) !!})
@endforeach
@endif
@if ($profile->socialLinks() !== [])

## Links

@foreach ($profile->socialLinks() as $network => $url)
- [{!! $line($network) !!}]({!! $url !!})
@endforeach
@endif

## Optional

- [Sitemap]({!! route('sitemap') !!})
