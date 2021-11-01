<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Exceptions\UnauthorizedException;

class mw_permission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        //当前路由
        $permission = Route::currentRouteName();

        //获取该用户权限
        $purviews = get_purviews([
            'guard' => $guard,
            'field' => 'name'
        ]);

        //有超级管理员权限*可访问所有地址
        if (user_can($permission, $guard) || in_array('*', $purviews)) {
            return $next($request);
        }

        throw UnauthorizedException::forPermissions([$permission]);
    }
}
