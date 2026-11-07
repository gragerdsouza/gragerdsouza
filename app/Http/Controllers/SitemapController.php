<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Response;

/**
 * Serves sitemap.xml for search engines.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class SitemapController
{
    /**
     * Handle the incoming request.
     *
     * @return Response The rendered response.
     */
    public function __invoke(): Response
    {
        return response()
            ->view('sitemap', ['projects' => Project::query()->published()->ordered()->get()])
            ->header('Content-Type', 'application/xml');
    }
}
