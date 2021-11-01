<?php

namespace App\Http\Middleware;

use Closure;
use Spatie\Permission\Exceptions\UnauthorizedException;

class mw_role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param $role 格式 [1,2,3]
     * @return mixed
     */
    public function handle($request, Closure $next, ...$role)
    {
        //api接口使用,暫不指定守衛api
        if (user_has_role($role)) {
            return $next($request);
        }
        throw UnauthorizedException::forRoles([$role]);
    }
}
