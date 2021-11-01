<?php

namespace App\Http\Controllers\api;

use App\models\mod_common;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class ctl_login extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 登录
     *
     * Author   Alan
     * Created  2021-04-01 10:45
     * Modified By Alan
     * Modified 2020-04-01 10:45
     *
     * @apiSampleRequest off
     * @api {post} login 登录
     * @apiGroup member
     * @apiName login
     * @apiVersion 1.0.0
     * @apiDescription 登录
     * @apiParam {String} username  用戶名，必填
     * @apiParam {String} password 密碼，必填
     * @apiParam {String} captcha 驗證碼，必填
     * @apiSuccessExample {json} 返回示例:
    {
    "code": 0,
    "msg": "登入成功",
    "timestamp": 1635779396,
    "data": {
    "id": "a28b00b8772138bf9cb7a824bdcbbd9a",
    "origin": 2,
    "username": "sccot",
    "realname": "陳聰明",
    "email": "test@example.com",
    "phone_code": "86",
    "phone": "0912345678",
    "status": 1,
    "is_first_login": 1,
    "is_audit": 0,
    "session_expire": 1440,
    "session_id": "",
    "reg_ip": "127.0.0.1",
    "login_time": 0,
    "login_ip": "",
    "language": "zh-tw",
    "create_time": 1635777232,
    "create_user": "0",
    "update_time": 0,
    "update_user": "0",
    "delete_time": 0,
    "delete_user": "0",
    "api_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9hcGkubG1fc2llbmVyZ3kubG9jYWxcL2xvZ2luIiwiaWF0IjoxNjM1Nzc5Mzk2LCJleHAiOjE2MzU3ODI5OTYsIm5iZiI6MTYzNTc3OTM5NiwianRpIjoiMlRsMkVIOHcwOEZFc3lQSSIsInN1YiI6ImEyOGIwMGI4NzcyMTM4YmY5Y2I3YTgyNGJkY2JiZDlhIiwicHJ2IjoiOTIyNDBmZmI4YTExMTRjODAzZWNiOTMyZmI3MjlhY2UwOGVkZmMzNSJ9.QRDTzVHlpKigT9mAKKVUh48xA5h6XvZ5uWnypfptxkQ",
    "api_token_expire": 1635782996
    }
    }
     */
    public function login(Request $request)
    {
        $credentials = $request->only(["username", "password"]);
        $credentials['status'] = 1; //已激活
        $guard = $this->guard;

        if (!$token = auth($guard)->attempt($credentials))
        {
            return mod_common::error(trans('api.api_login_pass_incorrect'));
        }

        //根据token获取用户信息,jwt后台不需要保存Token
        $user_info = auth($guard)->authenticate($token)->toArray();
        $user_info['api_token'] = $token;
        $jwt_ttl = auth($guard)->factory()->getTTL(); //單位:分鐘
        $user_info['api_token_expire'] = strtotime("+{$jwt_ttl} minutes", time());

        return mod_common::success($user_info, trans('api.api_login_success'));
    }

    /**
     * 登出
     *
     * Author   Alan
     * Created  2021-04-01 10:45
     * Modified By Alan
     * Modified 2020-04-01 10:45
     *
     * @apiSampleRequest off
     * @api {post} logout 登出
     * @apiGroup member
     * @apiName logout
     * @apiVersion 1.0.0
     * @apiDescription 登出
     * @apiSuccessExample {json} 返回示例:
    {
    "code": 0,
    "msg": "登出成功",
    "timestamp": 1635779714,
    "data": []
    }
     */
    public function logout()
    {
        $guard = $this->guard;
        auth($guard)->logout();
        return mod_common::success([], trans('api.api_logout_success'));
    }

    /**
     * 刷新认证token
     *
     * Author   Alan
     * Created  2021-04-01 10:45
     * Modified By Alan
     * Modified 2020-04-01 10:45
     *
     * @apiSampleRequest off
     * @api {post} refresh_token 刷新认证token
     * @apiGroup member
     * @apiName refresh_token
     * @apiVersion 1.0.0
     * @apiDescription 刷新认证token
     * @apiSuccessExample {json} 返回示例:
    {
    "code": 0,
    "msg": "success",
    "timestamp": 1635779990,
    "data": {
    "uid": "a28b00b8772138bf9cb7a824bdcbbd9a",
    "api_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9hcGkubG1fc2llbmVyZ3kubG9jYWxcL3JlZnJlc2hfdG9rZW4iLCJpYXQiOjE2MzU3Nzk5NzgsImV4cCI6MTYzNTc4MzU5MCwibmJmIjoxNjM1Nzc5OTkwLCJqdGkiOiJSajNxZjBPVnlnTm8yU1VJIiwic3ViIjoiYTI4YjAwYjg3NzIxMzhiZjljYjdhODI0YmRjYmJkOWEiLCJwcnYiOiI5MjI0MGZmYjhhMTExNGM4MDNlY2I5MzJmYjcyOWFjZTA4ZWRmYzM1In0.UZUToV2Ktu9YROclqr_6_VO4kQgEKg6JBAd0vbRWOKc",
    "api_token_expire": 1635783590
    }
    }
     */
    public function refresh_token()
    {
        try
        {
            $guard = $this->guard;
            $token = auth($guard)->refresh();

            //根据token获取用户信息,jwt后台不需要保存Token
            $user_info = auth($guard)->authenticate($token)->toArray();
            $jwt_ttl = auth($guard)->factory()->getTTL(); //單位:分鐘
        }
        catch(TokenInvalidException $e)
        {
            return mod_common::error('获取token失败', -4004); //token不合法
        }

        return mod_common::success([
            'uid'               =>  $user_info['id'],
            'api_token'         =>  $token,
            'api_token_expire'  =>  strtotime("+{$jwt_ttl} minutes", time()),
        ]);
    }
}
