<?php


namespace App\Http\Controllers\api;


use App\models\mod_common;
use App\models\mod_faq;
use App\models\mod_faq_cat;
use Illuminate\Http\Request;

class ctl_faq extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取常见问题列表
     *
     * Author   Alan
     * Created  2021-04-01 10:45
     * Modified By Alan
     * Modified 2020-04-01 10:45
     *
     * @apiSampleRequest off
     * @api {get} faq 获取常见问题列表
     * @apiGroup faq
     * @apiName faq
     * @apiVersion 1.0.0
     * @apiDescription 获取常见问题列表
     * @apiParam {int} page_size  每页显示几条
     * @apiParam {int} page_no 第几页
     * @apiParam {int} cat_id 分類id
     * @apiSuccessExample {json} 返回示例:
    {
    "code": 0,
    "msg": "success",
    "timestamp": 1635728725,
    "data": {
    "data": [
    {
    "id": "c3a5a3b5cef355132fddf8096b2d45cd",
    "cat_id": 2,
    "question": "在屋頂架設太陽光電系統，是否會對人體健康造成影響？",
    "answer": "目前所知對人體健康有疑慮的是交流電所產生之電磁波，而太陽光電產生的是直流電，完全不會對人體健康有任何危害性。",
    "status": 1,
    "sort": 0,
    "create_user": "1",
    "create_time": 1635725102,
    "status_dis": "啟用",
    "create_time_dis": "2021/11/01 00:05",
    "create_user_dis": "1"
    },
    {
    "id": "9c5a5350f05d38fd25876784af5cb3bd",
    "cat_id": 2,
    "question": "申請架設太陽光電系統，所提供的服務範圍有哪些？",
    "answer": "本公司包辦所有行政作業，包括與台電簽約、能源局驗收，直到業主順利取得躉售台電之匯款。",
    "status": 1,
    "sort": 0,
    "create_user": "1",
    "create_time": 1635725088,
    "status_dis": "啟用",
    "create_time_dis": "2021/11/01 00:04",
    "create_user_dis": "1"
    }
    ],
    "total_page": 1,
    "total": 10
    }
    }
     */
    public function index(Request $request)
    {
        $page_size = $request->input('page_size', 10);
        $page_no = $request->input('page_no', 1);
        $page_no = !empty($page_no) ? $page_no : 1;

        $cat_id = $request->input('cat_id');
        $title    = $request->input('title') ?? '';

        //獲取數據
        $rows = mod_faq::list_data([
            'cat_id'    => $cat_id,
            'title'     =>  $title,
            'page'      => $page_no,
            'page_size' => $page_size,
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

    /**
     * 获取常见问题分类
     *
     * Author   Alan
     * Created  2021-04-01 10:45
     * Modified By Alan
     * Modified 2020-04-01 10:45
     *
     * @apiSampleRequest off
     * @api {get} faq_cats 获取常见问题分类
     * @apiGroup faq
     * @apiName faq_cats
     * @apiVersion 1.0.0
     * @apiDescription 获取常见问题分类
     * @apiSuccessExample {json} 返回示例:
    {
    "code": 0,
    "msg": "success",
    "timestamp": 1635730830,
    "data": [
    {
    "id": 2,
    "name": "常見問題",
    "desc": null,
    "sort": 0,
    "status": 1,
    "create_user": "1",
    "create_time": 1635724813,
    "status_dis": "啟用",
    "create_time_dis": "2021/11/01 00:00",
    "create_user_dis": "1"
    }
    ]
    }
     */
    public function cats(Request $request)
    {
        $rows = mod_faq_cat::list_data([
            'status'    => mod_faq_cat::ENABLE,
            'order_by'  => ['create_time', 'asc'],
        ]);

        return mod_common::success($rows);
    }
}
