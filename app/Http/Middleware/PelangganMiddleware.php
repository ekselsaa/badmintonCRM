<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class PelangganMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'pelanggan') {
            return $next($request);
        }
        
        abort(403, 'Akses Ditolak. Halaman ini hanya untuk Pelanggan.');
    }
}
