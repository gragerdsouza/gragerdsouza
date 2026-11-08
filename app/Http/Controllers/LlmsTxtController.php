<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Http\Response;

/**
 * Serves /llms.txt: a Markdown summary of the site for AI assistants (https://llmstxt.org).
 *
 * Generated from the database, so it always matches what the admin has published.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class LlmsTxtController
{
    /**
     * Handle the incoming request.
     *
     * @return Response The plain-text Markdown response.
     */
    public function __invoke(): Response
    {
        return response()
            ->view('llms', [
                'profile' => Profile::current(),
                'projects' => Project::query()->published()->ordered()->get(),
                'skills' => Skill::query()->ordered()->get()->groupBy(fn (Skill $skill): string => $skill->category->getLabel()),
                'experiences' => Experience::query()->latestFirst()->get(),
                'certifications' => Certification::query()->current()->latestFirst()->get(),
            ])
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
