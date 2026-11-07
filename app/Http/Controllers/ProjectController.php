<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Project;
use Illuminate\Contracts\View\View;

/**
 * Public project listing and detail pages.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class ProjectController
{
    /**
     * Display a paginated list of published projects.
     *
     * @return View The projects index view.
     */
    public function index(): View
    {
        return view('projects.index', [
            'profile' => Profile::current(),
            'projects' => Project::query()->published()->ordered()->paginate(12),
        ]);
    }

    /**
     * Display a single published project.
     *
     * @param Project $project The project resolved from the URL slug.
     *
     * @return View The project detail view.
     */
    public function show(Project $project): View
    {
        abort_unless($project->isPublished(), 404);

        return view('projects.show', [
            'profile' => Profile::current(),
            'project' => $project,
        ]);
    }
}
