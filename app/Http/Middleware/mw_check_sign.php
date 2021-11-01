<?php

namespace App\Http\Middleware;

use App\models\mod_common;
use Closure;

class mw_check_sign
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $app_id     = $request->input('app_id');
        $sign       = $request->input('sign');
        $token     = $request->input('_token');
        $app_key    = config('global.tp_api.app_key');
        //测试用
        //empty($sign) and $sign = mod_common::sign($request->all(), $app_key);

        if (!empty($app_id)) //验证第三方访问
        {
            //比对应用id与服务器保留是否一致
            if($app_id != config('global.tp_api.app_id'))
            {
                return mod_common::error(trans('api.api_invalid_request'));
            }
        }

        if(empty($token))
        {
            if (!empty($app_id)) //验证第三方访问
            {
                if(empty($sign))
                {
                    return mod_common::error(trans('api.api_invalid_request'));
                }
                //检查签名
                if (!mod_common::check_sign($request->all(), $app_key, $sign))
                {
                    return mod_common::error(trans('api.api_sign_error'));
                }
            }
        }
        return $next($request);
    }
}
