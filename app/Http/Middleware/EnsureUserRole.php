<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        if (! in_array($request->user()->role, $roles, true)) {
            return to_route(
                $request->user()->role === 'hr' ? 'hr.dashboard' : 'employee.portal',
            )->with('status', 'Vous avez été redirigé vers votre espace personnel.');
        }

        return $next($request);
    }
}
