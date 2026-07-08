<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequerirCambioPassword
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->debe_cambiar_password && (bool) $request->session()->get('forzar_cambio_password') && ! $request->routeIs('admin.password.*', 'admin.logout')) {
            return redirect()->route('admin.password.edit');
        }

        return $next($request);
    }
}
