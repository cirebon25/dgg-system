<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConfirmPasswordForModule
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah mengonfirmasi password dalam 30 menit terakhir
        if (
            ! session()->has('auth.password_confirmed_at') ||
            (time() - session('auth.password_confirmed_at') > 1800)
        ) {

            // Simpan URL asal agar setelah konfirmasi dikembalikan ke sini
            session(['url.intended' => $request->url()]);

            // Arahkan ke halaman konfirmasi password bawaan Laravel
            return redirect()->route('password.confirm');
        }

        return $next($request);
    }
}