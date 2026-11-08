<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Response;

/**
 * Serves /robots.txt: open to crawlers in production, closed everywhere else.
 *
 * The admin panel is deliberately not listed (robots.txt is public and would
 * advertise its path); it sends an X-Robots-Tag: noindex header instead.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class RobotsTxtController
{
    /**
     * Handle the incoming request.
     *
     * @return Response The plain-text robots rules.
     */
    public function __invoke(): Response
    {
        $lines = app()->isProduction()
            ? ['User-agent: *', 'Allow: /', '', 'Sitemap: '.route('sitemap'), '# AI-readable summary: '.route('llms')]
            : ['User-agent: *', 'Disallow: /'];

        return response(implode("\n", $lines)."\n")
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
