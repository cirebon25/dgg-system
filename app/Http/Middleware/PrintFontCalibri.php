<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PrintFontCalibri
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya untuk respons HTML biasa (bukan file, PDF, redirect, JSON)
        $type = $response->headers->get('Content-Type', '');
        if (! str_contains($type, 'text/html') || $response->isRedirection()) {
            return $response;
        }

        // Lewati halaman Filament & Livewire (panel admin tetap pakai font bawaan)
        if ($request->headers->has('X-Livewire')) {
            return $response;
        }

        $html = $response->getContent();

        if (! is_string($html) || str_contains($html, 'fi-body') || str_contains($html, 'wire:id')) {
            return $response;
        }

        $style = '<style id="font-calibri">'
            . '*{font-family:Calibri,Carlito,"Segoe UI",Arial,sans-serif !important;}'
            . '</style>';

        $html = stripos($html, '</head>') !== false
            ? preg_replace('/<\/head>/i', $style . '</head>', $html, 1)
            : $style . $html;

        $response->setContent($html);

        return $response;
    }
}
