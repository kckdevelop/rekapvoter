<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isAdmin()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Fitur ini hanya dapat diakses oleh Administrator.',
                ], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Fitur ini hanya untuk Administrator.');
        }

        return $next($request);
    }
}
