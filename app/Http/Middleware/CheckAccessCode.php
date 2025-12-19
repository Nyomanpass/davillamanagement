<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAccessCode
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Cek apakah user sudah login
        if (Auth::check()) {
            
            // 2. Jika user belum verifikasi PIN dan tidak sedang di halaman verifikasi
            if (!session('access_code_verified') && !$request->routeIs('verify.pin')) {
                return redirect()->route('verify.pin');
            }
        }

        return $next($request);
    }
}