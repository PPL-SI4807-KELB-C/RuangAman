<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePelapor
{
    /**
     * Memastikan hanya user dengan role 'Pelapor' yang dapat mengakses rute pendaftaran pelapor.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isPelapor()) {
            abort(403, 'Akses ditolak. Fitur ini hanya tersedia untuk Pelapor.');
        }

        return $next($request);
    }
}
