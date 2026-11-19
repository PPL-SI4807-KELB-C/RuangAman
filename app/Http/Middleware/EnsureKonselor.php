<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKonselor
{
    /**
     * Memastikan hanya user dengan role 'Konselor' yang dapat mengakses rute ini.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isKonselor()) {
            abort(403, 'Akses ditolak. Fitur ini hanya dapat diakses oleh Konselor/Psikolog.');
        }

        return $next($request);
    }
}
