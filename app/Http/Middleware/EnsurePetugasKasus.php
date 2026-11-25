<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePetugasKasus
{
    /**
     * Memastikan hanya Admin Kasus atau Konselor yang dapat mengakses modul log penanganan kasus.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->isPelapor() || (! $user->isAdminKasus() && ! $user->isKonselor())) {
            abort(403, 'Akses ditolak. Fitur log penanganan kasus hanya dapat diakses oleh Admin Kasus dan Konselor.');
        }

        return $next($request);
    }
}
