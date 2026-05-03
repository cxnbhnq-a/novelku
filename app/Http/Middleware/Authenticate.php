<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            
            // JANGAN PERNAH EXPOSE TOKEN DI SINI!
            // Kalau ada siapapun (hacker/user biasa) nyoba nembus rute admin, 
            // JANGAN di-redirect! Langsung putus lehernya pakai 404!
            if ($request->is('dashboard/admin*') || $request->is('portal/*')) {
                abort(404);
            }
            
            // Kalau selain admin, lempar ke form login normal
            return route('login');
        }

        return null;
    }
}