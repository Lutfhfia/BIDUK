<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Memeriksa apakah user memiliki permission tertentu.
     */
    public function handle(
        Request $request,
        Closure $next,
        string $permission
    ): Response {
        $user = $request->user();

        /*
         * User harus sudah login.
         */
        if (!$user) {
            abort(401, 'Anda harus login terlebih dahulu.');
        }

        /*
         * Super Admin selalu memiliki akses.
         */
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        /*
         * Cek permission berdasarkan role user.
         */
        if (!$user->hasPermission($permission)) {
            abort(
                403,
                'Anda tidak memiliki izin untuk mengakses fitur ini.'
            );
        }

        return $next($request);
    }
}