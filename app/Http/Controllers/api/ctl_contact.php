<?php


namespace App\Http\Controllers\api;

use App\models\mod_common;
use App\models\mod_feedback;
use Illuminate\Http\Request;

class ctl_contact extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 提交联络表单
     *
     * Author   Alan
     * Created  2021-04-01 10:45
     * Modified By Alan
     * Modified 2020-04-01 10:45
     *
     * @apiSampleRequest off
     * @api {post} feedback 提交联络表单
     * @apiGroup contact
     * @apiName feedback
     * @apiVersion 1.0.0
     * @apiDescription 提交联络表单
     * @apiParam {String} name  姓名，必填
     * @apiParam {String} [company_name] 公司名稱
     * @apiParam {int} sex 性別 0=女 1=男，必填
     * @apiParam {String} email 電子郵件，必填
     * @apiParam {String} [phone] 聯絡電話
     * @apiParam {String} content 您的意見，必填
     * @apiParam {String} captcha 驗證碼，必填
     * @apiSuccessExample {json} 返回示例:
    {
    "code": 0,
    "msg": "提交成功",
    "timestamp": 1635746875,
    "data": []
    }
     */
    public function feedback(Request $request)
    {
        if($request->isMethod('POST'))
        {
            $status = mod_feedback::save_data([
                'do'            => 'add',
                'captcha'       => $request->input('captcha'),
                'id'            => $request->input('id'),
                'name'          => $request->input('name'),
                'company_name'  => $request->input('company_name'),
                'sex'           => $request->input('sex'),
                'email'         => $request->input('email'),
                'phone'         => $request->input('phone'),
                'content'       => $request->input('content'),
            ]);
            if($status < 0)
            {
                return mod_common::error(mod_feedback::get_err_msg($status), $status);
            }
            return mod_common::success([], trans('api.api_submit_success'));
        }
        else
        {
            //返回基礎數據
            if($request->ajax())
            {

            }
        }
    }
}
