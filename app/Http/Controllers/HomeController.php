<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Contracts\View\View;

/**
 * Renders the one-page portfolio: profile, stack, experience and featured work.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class HomeController
{
    /**
     * Handle the incoming request.
     *
     * @return View The rendered response.
     */
    public function __invoke(): View
    {
        return view('home', [
            'profile' => Profile::current(),
            'skills' => Skill::query()->ordered()->get()->groupBy(fn (Skill $skill): string => $skill->category->getLabel()),
            'experiences' => Experience::query()->latestFirst()->get(),
            'certifications' => Certification::query()->current()->latestFirst()->get(),
            'projects' => Project::query()->published()->featured()->ordered()->limit(6)->get(),
        ]);
    }
}
