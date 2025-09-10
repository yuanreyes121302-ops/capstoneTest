<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsApproved
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (Auth::check() && !$user->is_approved && $user->role !== 'tenant' && $user->role !== 'admin') {
            return redirect()->route('not.approved')->with('error', 'Your account is pending approval.');
        }

        return $next($request);
    }
}

