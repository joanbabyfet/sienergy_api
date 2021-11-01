<?php


namespace App\Http\Controllers\api;


use App\models\mod_common;
use App\models\mod_news;
use Illuminate\Http\Request;

class ctl_index extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取首页数据
     *
     * Author   Alan
     * Created  2021-04-01 10:45
     * Modified By Alan
     * Modified 2020-04-01 10:45
     *
     * @apiSampleRequest off
     * @api {get} index 获取首页数据
     * @apiGroup common
     * @apiName index
     * @apiVersion 1.0.0
     * @apiDescription 获取首页数据
     * @apiSuccessExample {json} 返回示例:
    {
        "code": 0,
        "msg": "success",
        "timestamp": 1635743526,
        "data": {
            "news": [   //前3条新闻
                {
                "id": "88e8150eac9198f1072657068a53358c",
                "cat_id": 1,
                "title": "【能源局新聞】經濟部101年度第6期太陽光電競標開標　共92件得標，總得標容量為23,630.849瓩",
                "content": "<h2>【能源局新聞】經濟部101年度第6期太陽光電競標開標　共92件得標，總得標容量為23,630.849瓩</h2>\n<p>發布日期：2012-08-09 下午 07:00</p>\n<p>經濟部101年第6期太陽光電發電設備競標作業，於8月8日進行開標，計有屋頂型89件得標，地面型3件得標，總計容量為23,630.849瓩。</p>\n<p> 經濟部能源局表示，<span style=\"color:#FF0000;\">本期太陽光電競標經審查符合競標資格者計162件，其中屋頂型計159件，合計容量為36,233.814瓩；地面型計3件，容量為525.12瓩，總容量共36,758.934瓩，超過基本容量10,000瓩部分為26,758.934瓩。依101年太陽光電競標作業要點規定，得標容量上限為基本容量加計超過部分容量50％，爰本期得標容量上限為23,379.467瓩。</span>\n                            </p>\n<p>\n                                開標作業開放參加競標者親臨現場觀看，決標方式按折扣率由高至低順序排列依次選取，加計最末件得標者容量後倘超過競標容量上限，仍得將其超過容量計入，但屋頂型以1,000瓩為限，地面型以500瓩為限。按本次最末件得標者可再計入容量251.382瓩，<span style=\"color:#FF0000;\">總計得標容量為23,630.849瓩，平均折扣率為4.37％</span>。未來得標業者適用之太陽光電躉購費率按其完工時公告費率扣除其折扣額度計之，即公告費率X(1-業者投標之折扣率)。\n                            </p>\n<p> 另外，<span style=\"color:#FF0000;\">經濟部101年8月1日公告修正「經濟部101年太陽光電發電設備競標作業要點」，101年度競標容量上限由70,000瓩提高為83,000瓩，累計第1期至第6期得標容量，及考量得標未簽約及撤案等加計容量後，第7期僅剩容量766.927瓩，因此101年9月將為本年度最後1期競標。</span>\n                            </p>\n<p>\n                                經濟部能源局進一步說明及提醒，101年第7期太陽光電競標作業收件截止日為8月20日，開標日為9月12日，前6期未得標、未補正或欲參與第7期競標作業者，請於8月20日下午5時30分前，將應備文件與第7期標單寄達或送達經濟部能源局，並請留意標單內容期別應填寫為第7期，且標單封套應予彌封。</p>\n<p>能源局發言人：王副局長運銘 <br>\n                                電話：02-2773-4729 ；行動電話：0910-216-359<br>\n                                電子郵件：<a href=\"mialto:ymwang@moeaboe.gov.tw\">ymwang@moeaboe.gov.tw</a><br>\n                                技術諮詢聯絡人：藍科長文宗<br>\n                                電話：02-2775-7641；行動電話：0988-396-386<br>\n                                電子郵件：<a href=\"mailto:wtlan@moeaboe.gov.tw\">wtlan@moeaboe.gov.tw </a></p>\n<p></p>",
                "is_hot": 0,
                "status": 1,
                "sort": 0,
                "create_user": "1",
                "create_time": 1635731650,
                "status_dis": "啟用",
                "create_time_dis": "2021/11/01 01:54",
                "create_user_dis": "1",
                "img_dis": [],
                "img_url_dis": [],
                "file_dis": [],
                "file_url_dis": []
                }
            ]
        }
    }
     */
    public function index(Request $request)
    {
        //获取前3条新闻
        $rows = mod_news::list_data([
            'status'    => mod_news::ENABLE,
            'count'     => false,
            'limit'     => 3,
            'order_by'  => ['create_time', 'desc'],
        ]);

        return mod_common::success([
            'news' => $rows,
        ]);
    }
}
