<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds hardening HTTP headers to every response.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class SecurityHeaders
{
    /**
     * Pass the request on, then add security headers to the response.
     *
     * @param Request $request The incoming HTTP request.
     * @param Closure(Request): Response $next The next middleware in the pipeline.
     *
     * @return Response The response with security headers added.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
