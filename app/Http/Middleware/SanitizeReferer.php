<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeReferer
{
    /**
     * Prevent redirect()->back() and url()->previous() from trusting an
     * attacker-controlled external Referer header.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $referer = $request->headers->get('referer');

        if ($referer !== null && ! $this->isSameOrigin($request, $referer)) {
            $request->headers->remove('referer');
        }

        return $next($request);
    }

    private function isSameOrigin(Request $request, string $referer): bool
    {
        $parts = parse_url($referer);

        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            return false;
        }

        $scheme = strtolower($parts['scheme']);
        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);

        return in_array($scheme, ['http', 'https'], true)
            && $scheme === strtolower($request->getScheme())
            && strtolower($parts['host']) === strtolower($request->getHost())
            && $port === $request->getPort();
    }
}
