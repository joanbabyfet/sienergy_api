<?php

namespace App\models;


class mod_api_req_log extends mod_model
{
    //主键
    public $primaryKey = 'id';
    //表名称
    public $table = 'api_req_log';
    //使用其他数据库连接
    protected $connection = ''; //必填,否则用工厂生成测试数据时会报错
    //字段
    public static $field = [
    ];
    //每页展示几笔
    public static $page_size = 10;
    //類型
    public static $type_map = [
        'api'       => '客户端api',
        'web'       => '官网',
        'admin'     => '后台',
    ];

    protected function list_data(array $conds)
    {
        $page_size  = !empty($conds['page_size']) ? $conds['page_size']:self::$page_size;
        $page       = $conds['page'] ?? null;
        $order_by   = $conds['order_by'] ?? null;
        $count      = $conds['count'] ?? null;
        $limit      = $conds['limit'] ?? null;
        $index      = $conds['index'] ?? null;
        $group_by   = $conds['group_by'] ?? null;
        $field      = $conds['field'] ?? null;
        $next_page  = $conds['next_page'] ?? null;
        //用戶名
        $req_data       = !empty($conds['req_data']) ? $conds['req_data']:'';
        $res_data       = !empty($conds['res_data']) ? $conds['res_data']:'';
        $type       = !empty($conds['type']) ? $conds['type']:'';
        $date1 = empty($conds['date1']) ? '' :
            mod_common::date_convert_timestamp("{$conds['date1']} 00:00:00", mod_common::get_admin_timezone());
        $date2   = empty($conds['date2']) ? '' :
            mod_common::date_convert_timestamp("{$conds['date2']} 23:59:59", mod_common::get_admin_timezone());

        $where = [];
        $date1 and $where[] = ['req_time', '>=', (int)$date1]; //开始时间
        $date2 and $where[] = ['req_time', '<=', (int)$date2]; //结束时间
        $req_data and $where[] = ['req_data', 'like', "%{$req_data}%"];
        $res_data and $where[] = ['res_data', 'like', "%{$res_data}%"];
        $type and $where[] = ['type', '=', $type];

        $order_by = !empty($order_by) ? $order_by : ['req_time', 'desc'];
        $group_by = !empty($group_by) ? $group_by : [];

        $rows = self::get_list([
            'fields'    => ['id', 'uid', 'type', 'req_ip', 'req_time'
                , 'req_data', 'res_data'
            ],
            'where'     => $where,
            'page'      => $page,
            'page_size' => $page_size,
            'order_by'  => $order_by,
            'group_by'  => $group_by,
            'count'     => $count,
            'limit'     => $limit,
            'index'     => $index,
            'field'     => $field,
            'next_page' => $next_page, //对于app,不需要计算总条数，只需返回是否需要下一页
        ]);
        //格式化数据
        if($count) {
            $rows['data'] = self::format_data($rows['data']);
        }
        else {
            $rows = self::format_data($rows);
        }

        return $rows;
    }

    //详情
    protected function detail(array $conds)
    {
        $data = self::get_one(['where' => $conds]);

        if(!empty($data))
        {
            $data = self::format_data($data);
        }

        return $data;
    }

    //格式化数据
    private function format_data($data)
    {
        if(empty($data)) return $data;

        $list = is_array(reset($data)) ? $data : [$data];

        foreach ($list as $k => $v)
        {
            $row_plus = [
                //操作時間
                'req_time_dis'  => mod_display::datetime($v['req_time']),
                //類型
                'type_dis'  => array_key_exists($v['type'], self::$type_map) ?
                    self::$type_map[$v['type']]:'',
            ];

            $list[$k] = array_merge($v, $row_plus);
        }

        return is_array(reset($data)) ? $list : reset($list);
    }

    //保存
    protected function save_data(array $data)
    {
        //参数过滤
        $data_filter = mod_common::data_filter([
            'type'      => 'required',
            'uid'       => '',
            'req_data'  => '',
            'res_data'  => 'required',
            'req_time'  => 'required',
            'req_ip'    => 'required',
        ], $data);

        $status = 1;
        try
        {
            if(!is_array($data_filter))
            {
                self::exception(trans('api.api_param_error'), -1);
            }

            self::insert_data($data_filter);
        }
        catch (\Exception $e)
        {
            $status = self::get_exception_status($e);
            //记录日志
            mod_common::logger(__METHOD__, [
                'status'  => $status,
                'errcode' => $e->getCode(),
                'errmsg'  => $e->getMessage(),
                'data'    => $data,
            ]);
        }

        return $status;
    }

    //刪除
    protected function del_data(array $data)
    {
        //参数过滤
        $data_filter = mod_common::data_filter([
            'id'           => 'required',
        ], $data);

        $status = 1;
        try
        {
            $id = $data_filter['id'];
            unset($data_filter['id']);

            if(!is_array($data_filter))
            {
                self::exception(trans('api.api_param_error'), -1);
            }
            self::del(['id' => $id]);
        }
        catch (\Exception $e)
        {
            $status = self::get_exception_status($e);
            //记录日志
            mod_common::logger(__METHOD__, [
                'status'  => $status,
                'errcode' => $e->getCode(),
                'errmsg'  => $e->getMessage(),
                'data'    => $data,
            ]);
        }

        return $status;
    }

    //保存操作日志
    protected function add_log(array $data)
    {
        $req_data = empty($data['req_data']) ? request()->all() : $data['req_data'];
        $res_data = empty($data['res_data']) ? [] : $data['res_data'];
        $req_data = is_array($req_data) || is_object($req_data) ?
            json_encode($req_data, JSON_UNESCAPED_UNICODE) : $req_data;
        $res_data = is_array($res_data) || is_object($res_data) ?
            json_encode($res_data, JSON_UNESCAPED_UNICODE) : $res_data;

        //记录日志
        mod_common::logger(__METHOD__, [
            'type'              => 'api',
            'uid'               => defined('AUTH_UID') ? AUTH_UID : '',
            'header'            => request()->header(), //头部参数也要保存
            'req_data'          => $req_data,
            'res_data'          => $res_data,
            'req_time'          => time(),
            'req_ip'            => request()->ip(),
        ]);

        return $this->save_data([
            'type'              => 'api',
            'uid'               => defined('AUTH_UID') ? AUTH_UID : '',
            //'header'            => request()->header(), //头部参数也要保存
            'req_data'          => $req_data,
            'res_data'          => $res_data,
            'req_time'          => time(),
            'req_ip'            => request()->ip(),
        ]);
    }
}
