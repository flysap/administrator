<?php

namespace Flysap\Application\Middlewares;

use Closure;

class PermissionsMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param $permissions
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions) {
        $user = $request->user();

        if(! $user)
            return redirect(
                route('login')
            );

        $permissions = !is_array($permissions) ? [$permissions] : $permissions;

        if ( ! $user->can([$permissions]))
            return redirect(
                route('login')
            );

        return $next($request);
    }
}