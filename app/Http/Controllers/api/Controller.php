<?php

namespace App\Http\Controllers\api;

use App\models\mod_req;
use Illuminate\Support\Facades\App;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected $user = []; //用户信息
    protected $uid = ''; //用户id
    protected $token = ''; //用户认证令牌
    protected $lang = 'zh-tw'; //用戶語系
    protected $timezone = ''; //时区
    protected $guard = ''; //当前使用守卫

    public function __construct()
    {
        define('IN_API', 1);
        $guard = config('global.api.guard'); //api守卫
        $this->guard = $guard;

        //优先使用登录用户当前语言环境
        if (!empty(auth($guard)->user()) && !empty(auth($guard)->user()->language))
        {
            $this->lang = auth($guard)->user()->language;
        }
        else
        {
            $this->lang = mod_req::get_language();
        }
        app()->setLocale($this->lang); //设置语言

        $this->timezone = mod_req::get_timezone();

        if(auth($guard)->check()) //确认当前用户是否通过认证
        {
            $this->uid = auth($guard)->user()->getAuthIdentifier();
            $this->user = auth($guard)->user()->toArray(); //获取token中user信息
            $this->token = auth($guard)->getToken()->get(); //从 request 中获取 token

            //当前认证uid常量,在model里也可使用
            if (!defined('AUTH_UID')) define('AUTH_UID', $this->uid);
        }
    }
}
