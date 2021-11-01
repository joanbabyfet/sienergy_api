<?php

namespace App\Http\Controllers\api;

use App\models\mod_common;
use App\models\mod_news;
use App\models\mod_news_cat;
use Illuminate\Http\Request;

class ctl_news extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取新闻列表
     *
     * Author   Alan
     * Created  2021-04-01 10:45
     * Modified By Alan
     * Modified 2020-04-01 10:45
     *
     * @apiSampleRequest off
     * @api {get} news 获取新闻列表
     * @apiGroup news
     * @apiName news
     * @apiVersion 1.0.0
     * @apiDescription 获取新闻列表
     * @apiParam {int} page_size  每页显示几条
     * @apiParam {int} page_no 第几页
     * @apiParam {int} cat_id 分類id
     * @apiSuccessExample {json} 返回示例:
    {
    "code": 0,
    "msg": "success",
    "timestamp": 1635731688,
    "data": {
    "data": [
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
    },
    {
    "id": "3da194c267fa380af88b44510b1705e5",
    "cat_id": 1,
    "title": "市場利多帶動、設備換機潮浮現 10月PV Taiwan 強力徵展中 昱晶、新日光、友達等200家廠商參展 力邀30大國際買主來台採購",
    "content": "<h2>市場利多帶動、設備換機潮浮現 10月PV Taiwan 強力徵展中 昱晶、新日光、友達等200家廠商參展 力邀30大國際買主來台採購</h2>\n<p>隨著德國6月底安裝潮，以及日本市場需求和美國雙反效應帶動下，近來台灣太陽能廠商利多消息不斷，相關製程設備換機需求也逐漸浮現。即將於今年10月3-5日舉行的台灣規模最大、也是唯一的國際級太陽光電專業展「PV Taiwan 2012 台灣國際太陽光電展覽會」目前已經匯集昱晶、新日光、友達、益通等近200家廠商參展，並持續強力徵展中。主辦單位外貿協會、SEMI和TPVIA目前更積極洽邀前30大國際買主於展期來台採購，預估將在10月帶動新一波太陽能的採購熱潮!<br>\n              歐洲太陽能產業協會(EPIA)預估未來5年全球太陽能產能將有200~400%的成長幅度，全球太陽能安裝量至2016年時，可望達到207.9~342.8GWp，而亞洲及其它新興市場將奪走歐洲的主導權。 根據Solar Buzz 資料顯示，2011年全球的太陽能電池產量達到29.5GW，其中，單單台灣市場的太陽能電池產量就高達7~8GW，占全球總產量的24%以上。</p>\n<p> 主辦單位外貿協會指出：「台灣的太陽能電池產品優質且價格合理，在市場和美國雙反效應的加持下，許多國際買主已經轉向台灣採購。SEMI與貿協目前已正積極洽邀全球前30大重量級買主於展期間來台採購，期望協助廠商締結商機。」</p>\n<p> 另一方面，全球太陽能設備市場也從今年起開始進入換機潮，市場呈現V型反彈復甦。研究機構IMS指出，2012年的設備換機/升級需求約有2.5~4GW，預估2013和2014年的設備投資金額分別有20%的成長，2015年更可望大幅增加40%的設備投資。對設備業者來說，從現在到2016年約有250億美金(20GW)的市場需求。由於製造廠集中在亞洲，設備銷售市場也以亞洲為主。</p>\n<p> 主辦單位SEMI表示：「根據SEMI最新一期的全球太陽光電製造設備的訂單出貨比(Book-to-Bill Ratio；B/B值)報告， 2011年亞洲的太陽光電相關設備銷售約佔全球總出貨量和訂單量的85%。對於設備和原材料供應商來說，今年下半年是進入市場的時機，而參展PV Taiwan則是提供廠商迅速提高品牌知名度，以及和太陽能製造商面對面洽談的最佳平台。」<br>\n              台灣唯一的國際太陽光電展— PV Taiwan，是太陽能廠商展出優質的太陽光電產品、技術，以及先進製造設備與材料的最佳平台。目前已吸引近200家廠商參展，包括友達、昱晶、新日光、益通、科風、茂矽、富陽、杜邦、博可、均豪、瑞納科技、有成精密、禧通、永光化學、東京威力科創(TEL)、錸德(RITEK)、英穩達(ISEC)、BIG SUN、SIEMENS、UMICORE等指標性大廠都已參展，隨著市場回溫，預計將有更多參展商參與。</p>\n<p> 今年PV Taiwan的主題展覽專區包括高聚光型太陽能(HCPV)、染料敏化太陽能(DSSC)、太陽光電發電系統(PV System)等專區。同期舉行的台灣最大「國際太陽光電產業論壇」。同時，為協助台灣太陽能廠商優化製程、開拓新商機，主辦單位目前正積極洽邀Soltech、Aleo Solar GmbH、Azimut、ecoSolargy、First Solar、GA-Solar、Gehrlicher Solar AG、Hanwha、Isofotón、Kyocera HIT、NextLight、Philadelphia Solar、Scatec、Schott Solar GmbH、Sharp、SILFAB、Solarworld AG、Solarnica、Solrwatt AG、Solon AG、SUNGRID、Suniva、SunPower、TERA等全球重量級買主於10月來台採購。</p>\n<p> PV Taiwan 2012參展報名，請洽:<br>\n              外貿協會 莊小姐 (TEL: 02.2725.5200分機2644)<br>\n              SEMI 李小姐 (TEL: 03.560.1777 分機101)<br>\n              最新展覽與論壇訊息請參考 <a href=\"http://www.pvtaiwan.com\" target=\"_blank\">www.pvtaiwan.com</a><br>\n              參觀者線上報名預計7月開放，預先報名可抽大獎，請隨時鎖定 <a href=\"http://www.pvtaiwan.com\" target=\"_blank\">www.pvtaiwan.com</a><br>\n            </p>\n<h3>新聞聯絡人：</h3>\n<p> 羅凱琳<br>\n              SEMI半導體事業部及行銷部 協理<br>\n              Email: klo@semi.org<br>\n              TEL: 03.560.1777 ext.201</p>\n<p> 張兆蓉<br>\n              外貿協會 展覽業務處<br>\n              Email: amychang@taitra.org.tw<br>\n              TEL: 886.2.2725.5200 ext. 2693</p>\n<p></p>",
    "is_hot": 0,
    "status": 1,
    "sort": 0,
    "create_user": "1",
    "create_time": 1635731613,
    "status_dis": "啟用",
    "create_time_dis": "2021/11/01 01:53",
    "create_user_dis": "1",
    "img_dis": [],
    "img_url_dis": [],
    "file_dis": [],
    "file_url_dis": []
    }
    ],
    "total_page": 1,
    "total": 3
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
        $rows = mod_news::list_data([
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
     * 获取新闻详情
     *
     * Author   Alan
     * Created  2021-04-01 10:45
     * Modified By Alan
     * Modified 2020-04-01 10:45
     *
     * @apiSampleRequest off
     * @api {get} news/detail 获取新闻详情
     * @apiGroup news
     * @apiName news/detail
     * @apiVersion 1.0.0
     * @apiDescription 获取新闻详情
     * @apiParam {String} id  新闻id
     * @apiSuccessExample {json} 返回示例:
    {
    "code": 0,
    "msg": "success",
    "timestamp": 1635731969,
    "data": {
    "id": "88e8150eac9198f1072657068a53358c",
    "cat_id": 1,
    "title": "【能源局新聞】經濟部101年度第6期太陽光電競標開標　共92件得標，總得標容量為23,630.849瓩",
    "content": "<h2>【能源局新聞】經濟部101年度第6期太陽光電競標開標　共92件得標，總得標容量為23,630.849瓩</h2>\n<p>發布日期：2012-08-09 下午 07:00</p>\n<p>經濟部101年第6期太陽光電發電設備競標作業，於8月8日進行開標，計有屋頂型89件得標，地面型3件得標，總計容量為23,630.849瓩。</p>\n<p> 經濟部能源局表示，<span style=\"color:#FF0000;\">本期太陽光電競標經審查符合競標資格者計162件，其中屋頂型計159件，合計容量為36,233.814瓩；地面型計3件，容量為525.12瓩，總容量共36,758.934瓩，超過基本容量10,000瓩部分為26,758.934瓩。依101年太陽光電競標作業要點規定，得標容量上限為基本容量加計超過部分容量50％，爰本期得標容量上限為23,379.467瓩。</span>\n                            </p>\n<p>\n                                開標作業開放參加競標者親臨現場觀看，決標方式按折扣率由高至低順序排列依次選取，加計最末件得標者容量後倘超過競標容量上限，仍得將其超過容量計入，但屋頂型以1,000瓩為限，地面型以500瓩為限。按本次最末件得標者可再計入容量251.382瓩，<span style=\"color:#FF0000;\">總計得標容量為23,630.849瓩，平均折扣率為4.37％</span>。未來得標業者適用之太陽光電躉購費率按其完工時公告費率扣除其折扣額度計之，即公告費率X(1-業者投標之折扣率)。\n                            </p>\n<p> 另外，<span style=\"color:#FF0000;\">經濟部101年8月1日公告修正「經濟部101年太陽光電發電設備競標作業要點」，101年度競標容量上限由70,000瓩提高為83,000瓩，累計第1期至第6期得標容量，及考量得標未簽約及撤案等加計容量後，第7期僅剩容量766.927瓩，因此101年9月將為本年度最後1期競標。</span>\n                            </p>\n<p>\n                                經濟部能源局進一步說明及提醒，101年第7期太陽光電競標作業收件截止日為8月20日，開標日為9月12日，前6期未得標、未補正或欲參與第7期競標作業者，請於8月20日下午5時30分前，將應備文件與第7期標單寄達或送達經濟部能源局，並請留意標單內容期別應填寫為第7期，且標單封套應予彌封。</p>\n<p>能源局發言人：王副局長運銘 <br>\n                                電話：02-2773-4729 ；行動電話：0910-216-359<br>\n                                電子郵件：<a href=\"mialto:ymwang@moeaboe.gov.tw\">ymwang@moeaboe.gov.tw</a><br>\n                                技術諮詢聯絡人：藍科長文宗<br>\n                                電話：02-2775-7641；行動電話：0988-396-386<br>\n                                電子郵件：<a href=\"mailto:wtlan@moeaboe.gov.tw\">wtlan@moeaboe.gov.tw </a></p>\n<p></p>",
    "img": "",
    "file": "",
    "is_hot": 0,
    "sort": 0,
    "status": 1,
    "create_time": 1635731650,
    "create_user": "1",
    "update_time": 0,
    "update_user": "0",
    "delete_time": 0,
    "delete_user": "0",
    "status_dis": "啟用",
    "create_time_dis": "2021/11/01 01:54",
    "create_user_dis": "1",
    "img_dis": [],
    "img_url_dis": [],
    "file_dis": [],
    "file_url_dis": []
    }
    }
     */
    public function detail(Request $request)
    {
        $id = $request->input('id');
        $row = mod_news::detail(['id' => $id]);

        return mod_common::success($row);
    }

    /**
     * 获取新闻分类
     *
     * Author   Alan
     * Created  2021-04-01 10:45
     * Modified By Alan
     * Modified 2020-04-01 10:45
     *
     * @apiSampleRequest off
     * @api {get} news_cats 获取新闻分类
     * @apiGroup news
     * @apiName news_cats
     * @apiVersion 1.0.0
     * @apiDescription 获取新闻分类
     * @apiSuccessExample {json} 返回示例:
    {
    "code": 0,
    "msg": "success",
    "timestamp": 1635732241,
    "data": [
    {
    "id": 1,
    "name": "太陽能產業",
    "desc": null,
    "sort": 0,
    "status": 1,
    "create_user": "1",
    "create_time": 1635731452,
    "status_dis": "啟用",
    "create_time_dis": "2021/11/01 01:50",
    "create_user_dis": "1"
    }
    ]
    }
     */
    public function cats(Request $request)
    {
        $rows = mod_news_cat::list_data([
            'status'    => mod_news_cat::ENABLE,
            'order_by'  => ['create_time', 'asc'],
        ]);

        return mod_common::success($rows);
    }
}
