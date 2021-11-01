<?php

namespace App\Http\Controllers\api;

use App\models\mod_common;
use App\models\mod_model;
use App\models\mod_user;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;

class ctl_member extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取会员信息
     *
     * Author   Alan
     * Created  2021-04-01 10:45
     * Modified By Alan
     * Modified 2020-04-01 10:45
     *
     * @apiSampleRequest off
     * @api {post} get_userinfo 获取会员信息
     * @apiGroup member
     * @apiName get_userinfo
     * @apiVersion 1.0.0
     * @apiDescription 获取会员信息
     * @apiSuccessExample {json} 返回示例:
    {
    "code": 0,
    "msg": "success",
    "timestamp": 1635780173,
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
    "delete_user": "0"
    }
    }
     */
    public function detail()
    {
        $guard = $this->guard;
        $user_info = auth($guard)->user()->toArray();
        return mod_common::success($user_info);
    }

    /**
     * 注册
     *
     * Author   Alan
     * Created  2021-04-01 10:45
     * Modified By Alan
     * Modified 2020-04-01 10:45
     *
     * @apiSampleRequest off
     * @api {post} register 注册
     * @apiGroup member
     * @apiName register
     * @apiVersion 1.0.0
     * @apiDescription 注册
     * @apiParam {String} username  用戶名，必填
     * @apiParam {String} password 用戶密碼，必填
     * @apiParam {String} realname 真實姓名，必填
     * @apiParam {String} email 郵箱，必填
     * @apiParam {String} phone_code 手機號國碼，必填
     * @apiParam {String} phone 手機號，必填
     * @apiSuccessExample {json} 返回示例:
    {
    "code": 0,
    "msg": "添加成功",
    "timestamp": 1635777233,
    "data": []
    }
     */
    public function register(Request $request)
    {
        if($request->isMethod('POST'))
        {
            $status = mod_user::save_data([
                'do'            => 'add',
                'id'            => $request->input('id'),
                'origin'        => 2, //0=其他 1=官网 2=用户端APP
                'username'      => $request->input('username'),
                'password'      => $request->input('password'),
                'realname'      => $request->input('realname'),
                'email'         => $request->input('email', ''),
                'phone_code'    => $request->input('phone_code', ''),
                'phone'         => $request->input('phone', ''),
                'role_id'       => config('global.gen_mem_role_id'),
                'reg_ip'        => $request->ip(),
                'language'        => 'zh-tw',
                'create_user'   => '0',
            ]);

            if($status < 0)
            {
                return mod_common::error(mod_model::get_err_msg($status), $status);
            }
            return mod_common::success([], trans('api.api_add_success'));
        }
    }

    /**
     * 修改密碼
     *
     * Author   Alan
     * Created  2021-04-01 10:45
     * Modified By Alan
     * Modified 2020-04-01 10:45
     *
     * @apiSampleRequest off
     * @api {post} change_pwd 修改密碼
     * @apiGroup member
     * @apiName change_pwd
     * @apiVersion 1.0.0
     * @apiDescription 修改密碼
     * @apiParam {String} old_password  原密碼，必填
     * @apiParam {String} password 新密碼，必填
     * @apiSuccessExample {json} 返回示例:
    {
    "code": 0,
    "msg": "更新成功",
    "timestamp": 1635808511,
    "data": []
    }
     */
    public function edit(Request $request)
    {
        if($request->isMethod('POST'))
        {
            $old_password = $request->input('old_password');
            //检测原密码
            if(!mod_common::check_password($old_password, auth($this->guard)->user()->password))
            {
                return mod_common::error('原密碼不正確', -1);
            }

            $status = mod_user::save_data([
                'do'            => mod_common::get_action(),
                'id'            => $this->uid,
                'password'      => $request->input('password'),
                'update_user'   => $this->uid,
            ]);
            if($status < 0)
            {
                return mod_common::error(mod_model::get_err_msg($status), $status);
            }
            return mod_common::success([], trans('api.api_update_success'));
        }
    }
}
