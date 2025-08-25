<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsApproved
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->is_approved) {
            return redirect()->route('not.approved')->with('error', 'Your account is pending approval.');
        }

        return $next($request);
    }
}

