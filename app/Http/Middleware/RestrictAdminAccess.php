<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

/**
 * Hides the admin panel from every IP outside the configured allowlist.
 *
 * Blocked visitors get a plain 404, so the admin's existence is not revealed.
 * With an empty allowlist the panel stays reachable and is protected by
 * password + two-factor authentication alone.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class RestrictAdminAccess
{
    /**
     * Abort with 404 unless the client IP is allowlisted (or no allowlist is set).
     *
     * @param Request $request The incoming HTTP request.
     * @param Closure(Request): Response $next The next middleware in the pipeline.
     *
     * @return Response The admin response for allowed clients.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var list<string> $allowedIps */
        $allowedIps = config()->array('portfolio.admin.allowed_ips');

        abort_if(
            $allowedIps !== [] && ! IpUtils::checkIp((string) $request->ip(), $allowedIps),
            Response::HTTP_NOT_FOUND,
        );

        return $next($request);
    }
}
