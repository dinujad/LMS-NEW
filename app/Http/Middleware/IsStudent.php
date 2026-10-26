<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsStudent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && (auth()->user()->isStudent() || auth()->user()->isAdmin())) {
             // Admins can also access student views if needed, or strictly restrict.
             // Usually admins want to see what students see, but for now let's allow both or just student.
             // If stricter: if (auth()->user()->isStudent())
             return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
