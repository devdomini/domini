<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== 'admin') {
            return redirect()
                ->route('commercial.dashboard')
                ->with('error', 'Cette section est réservée aux administrateurs.');
        }

        return $next($request);
    }
}
