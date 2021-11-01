<?php


namespace App\Http\Controllers\api;


use App\models\mod_common;
use App\models\mod_link;
use Illuminate\Http\Request;

class ctl_link extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取友情链接列表
     *
     * Author   Alan
     * Created  2021-04-01 10:45
     * Modified By Alan
     * Modified 2020-04-01 10:45
     *
     * @apiSampleRequest off
     * @api {get} link 获取友情链接列表
     * @apiGroup link
     * @apiName link
     * @apiVersion 1.0.0
     * @apiDescription 获取友情链接列表
     * @apiSuccessExample {json} 返回示例:
    {
    "code": 0,
    "msg": "success",
    "timestamp": 1635734508,
    "data": {
    "data": [
    {
    "id": "fe08836248e8589e5041c51c7ce30c5b",
    "name": "經濟部能源局",
    "name_en": null,
    "url": "http://www.moeaboe.gov.tw/",
    "img": "031/aef74f64b56376a05a2cb636e88fed8f.jpg",
    "status": 1,
    "create_time": 1635733032,
    "img_dis": [
    "031/aef74f64b56376a05a2cb636e88fed8f.jpg"
    ],
    "img_url_dis": [
    "http://example.local/storage/image/031/aef74f64b56376a05a2cb636e88fed8f.jpg"
    ],
    "status_dis": "啟用",
    "create_time_dis": "2021/11/01 02:17"
    },
    {
    "id": "094cc8d1080c6db1651ecfe43246d7d2",
    "name": "綠色能源產業資訊網",
    "name_en": null,
    "url": "http://www.taiwangreenenergy.org.tw/Domain/",
    "img": "008/89e061fbc2044625bac65fbc06506e1f.jpg",
    "status": 1,
    "create_time": 1635733001,
    "img_dis": [
    "008/89e061fbc2044625bac65fbc06506e1f.jpg"
    ],
    "img_url_dis": [
    "http://example.local/storage/image/008/89e061fbc2044625bac65fbc06506e1f.jpg"
    ],
    "status_dis": "啟用",
    "create_time_dis": "2021/11/01 02:16"
    }
    ],
    "total_page": 1,
    "total": 4
    }
    }
     */
    public function index(Request $request)
    {
        $page_size = $request->input('page_size', 10);
        $page_no = $request->input('page_no', 1);
        $page_no = !empty($page_no) ? $page_no : 1;

        //獲取數據
        $rows = mod_link::list_data([
            //'page'      => $page_no,
            //'page_size' => $page_size,
            'status'    => mod_link::ENABLE,
            'count'     => 1,
            'order_by'  => ['create_time', 'desc'],
        ]);

        //分頁顯示
        $pages = mod_common::pages($rows['total'], $page_size);

        if (mod_common::get_action() == 'export_list') //獲取調用方法名
        {

        }

        return mod_common::success([
            'data' => $rows['data'],
            'total_page' => $pages->lastPage(),
            'total' => $pages->total()
        ]);
    }
}
