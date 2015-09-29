<?php

namespace Flysap\Application\Middlewares;

use Closure;

class RoleMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role) {
        $user = $request->user();

        if(! $user)
            return redirect(
                route('login')
            );

        if ( $user->role != $role)
            return redirect(
                route('login')
            );

        return $next($request);
    }
}