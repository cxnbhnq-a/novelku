<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role  <-- Kita tambahin ini buat nerima parameter role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        // 1. Cek dulu apakah dia udah login?
        if (!Auth::check()) {
            return redirect('login');
        }

        // ... (kode di atasnya)
        if (Auth::user()->role !== $role) {
            abort(404); // Ini yang bikin server nge-throw status 404 beneran
        }
        // ...

        // Kalau aman, silakan lewat
        return $next($request);
    }
}