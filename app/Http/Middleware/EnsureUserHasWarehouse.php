<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasWarehouse
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user === null) {
            return redirect()->route('login');
        }

        if ($user->hasAnyRole(['super-admin', 'viewer']) || $user->roles->isEmpty()) {
            return $next($request);
        }

        if ($user->hasRole('admin') && $user->warehouses()->count() === 0) {
            return redirect()->route('unassigned');
        }

        return $next($request);
    }
}
