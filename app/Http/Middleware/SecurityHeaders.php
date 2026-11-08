<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds hardening HTTP headers, including a Content-Security-Policy, to every response.
 *
 * Public pages get a strict nonce-based policy: only scripts and styles served by this
 * site (or carrying this request's nonce) may run, which neutralises injected markup.
 * The admin panel and health page get a baseline policy, because Filament's Alpine
 * components rely on inline scripts.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class SecurityHeaders
{
    /**
     * Directives sent with every response, whatever the page.
     */
    private const array BASELINE_POLICY = [
        'base-uri' => ["'self'"],
        'form-action' => ["'self'"],
        'frame-ancestors' => ["'self'"],
        'object-src' => ["'none'"],
    ];

    /**
     * Generate the CSP nonce, pass the request on, then add security headers to the response.
     *
     * @param Request $request The incoming HTTP request.
     * @param Closure(Request): Response $next The next middleware in the pipeline.
     *
     * @return Response The response with security headers added.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set before rendering so @vite / @fonts tags carry this request's nonce.
        $nonce = Vite::useCspNonce();

        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy($request, $nonce));
        $response->headers->remove('X-Powered-By');

        if ($this->isAdminArea($request)) {
            // Keep the admin out of search results without listing its path in robots.txt.
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=63072000; includeSubDomains');
        }

        return $response;
    }

    /**
     * Build the Content-Security-Policy header value for this request.
     *
     * @param Request $request The incoming HTTP request.
     * @param string $nonce This request's CSP nonce.
     *
     * @return string The serialized policy.
     */
    private function contentSecurityPolicy(Request $request, string $nonce): string
    {
        $policy = $this->isAdminArea($request) || $request->is('livewire*', 'up')
            ? self::BASELINE_POLICY
            : $this->strictPolicy($nonce);

        if ($request->isSecure()) {
            $policy['upgrade-insecure-requests'] = [];
        }

        return implode('; ', array_map(
            static fn (string $directive, array $sources): string => trim($directive.' '.implode(' ', $sources)),
            array_keys($policy),
            $policy,
        ));
    }

    /**
     * Whether the request targets the Filament admin panel.
     *
     * @param Request $request The incoming HTTP request.
     *
     * @return bool True for admin panel URLs.
     */
    private function isAdminArea(Request $request): bool
    {
        $adminPath = config()->string('portfolio.admin.path');

        return $request->is($adminPath, $adminPath.'/*');
    }

    /**
     * The strict policy for public pages, allowing the Vite dev server while it runs.
     *
     * @param string $nonce This request's CSP nonce.
     *
     * @return array<string, list<string>> Sources keyed by directive.
     */
    private function strictPolicy(string $nonce): array
    {
        $self = ["'self'"];
        $connect = ["'self'"];

        if (Vite::isRunningHot()) {
            $devServer = trim((string) file_get_contents(Vite::hotFile()));
            $self[] = $devServer;
            $connect = [...$self, (string) preg_replace('/^http/', 'ws', $devServer)];
        }

        return [
            'default-src' => ["'self'"],
            'script-src' => [...$self, "'nonce-{$nonce}'"],
            'style-src' => [...$self, "'nonce-{$nonce}'"],
            'img-src' => ["'self'", 'data:', 'https:'],
            'font-src' => $self,
            'connect-src' => $connect,
            ...self::BASELINE_POLICY,
        ];
    }
}
