<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBackOfficeUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, ['admin', 'commercial'], true)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Accès non autorisé'], 403);
            }

            return redirect()->route('admin.login')->with('error', 'Accès réservé aux administrateurs et commerciaux.');
        }

        return $next($request);
    }
}
