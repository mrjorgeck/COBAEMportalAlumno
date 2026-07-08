<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForzarHttpsProduccion
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->isProduction() && ! $this->requestIsHttps($request)) {
            return redirect()->secure($request->getRequestUri());
        }

        $response = $next($request);

        if (app()->isProduction()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        return $response;
    }

    private function requestIsHttps(Request $request): bool
    {
        return $request->secure() || $request->headers->get('x-forwarded-proto') === 'https';
    }
}
