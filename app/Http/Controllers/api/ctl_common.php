<?php

namespace App\Http\Controllers\api;

use App\models\mod_common;
use Illuminate\Http\Request;
use Mews\Captcha\Facades\Captcha;

class ctl_common extends Controller
{
    /**
     * ping
     * 检测用,可查看是否返回信息及时间戳
     * @return \Illuminate\Http\JsonResponse
     */
    public function ping()
    {
        return mod_common::success();
    }

    /**
     * 获取客户端ip地址
     *
     * Author   Alan
     * Created  2021-04-01 10:45
     * Modified By Alan
     * Modified 2020-04-01 10:45
     *
     * @apiSampleRequest off
     * @api {get} ip 获取客户端ip地址
     * @apiGroup common
     * @apiName ip
     * @apiVersion 1.0.0
     * @apiDescription 获取客户端ip地址
     * @apiSuccessExample {json} 返回示例:
    {
    "code": 0,
    "msg": "success",
    "timestamp": 1635743698,
    "data": {
    "ip": "127.0.0.1"
    }
    }
     */
    public function ip(Request $request)
    {
        return mod_common::success(['ip' => $request->ip()]);
    }
}
