<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class mod_country extends mod_model
{
    //主键
    public $primaryKey = 'id';
    //主键是否支持自增,默认支持
    public $incrementing = true;
    //表名称
    public $table = 'country';
    //使用其他数据库连接
    //protected $connection = '';
    //字段
    public static $field = [
    ];
    //每页展示几笔
    public static $page_size = 10;
    //狀態
    const DISABLE = 0;
    const ENABLE = 1;
    public static $status_map = [
        self::DISABLE   => '禁用',
        self::ENABLE    => '啟用'
    ];
    //固定頻道
    public static $is_fix_map = [
        '0' => '否',
        '1' => '是',
    ];
    //默認展示
    public static $is_default_map = [
        '0' => '否',
        '1' => '是',
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
        $name       = !empty($conds['name']) ? $conds['name']:'';
        $status     = $conds['status'] ?? null;

        $where = [];
        $where[] = ['delete_time', '=', 0];
        $name and $where[] = ['name', 'like', "%{$name}%"];
        is_numeric($status) and $where[] = ['status', '=', $status];

        $order_by = !empty($order_by) ? $order_by : ['create_time', 'desc'];
        $group_by = !empty($group_by) ? $group_by : [];

        $rows = self::get_list([
            'fields'    => ['id', 'is_fix', 'is_default', 'name', 'en_short_name',
                'mobile_prefix', 'sort', 'status', 'create_user', 'create_time'
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
                //状态
                'status_dis'       => array_key_exists($v['status'], self::$status_map) ? self::$status_map[$v['status']]:'',
                //固定頻道
                'is_fix_dis'  => array_key_exists($v['is_fix'], self::$is_fix_map) ? self::$is_fix_map[$v['is_fix']]:'',
                //默認展示
                'is_default_dis'  => array_key_exists($v['is_default'], self::$is_default_map) ? self::$is_default_map[$v['is_default']]:'',
                //添加日期
                'create_time_dis'  => mod_display::datetime($v['create_time']),
                //添加人
                'create_user_dis'  => $v['create_user'],
            ];

            $list[$k] = array_merge($v, $row_plus);
        }

        return is_array(reset($data)) ? $list : reset($list);
    }

    //保存
    protected function save_data(array $data)
    {
        $do             = isset($data['do']) ? $data['do'] : '';
        //参数过滤
        $data_filter = mod_common::data_filter([
            'do'        => 'required',
            'id'        => $do == 'edit' ? 'required' : '',
            'name'     => 'required',
            'is_default'   => 'required',
            'is_fix'   => 'required',
            'status'    => 'required',
            'create_user'       => '',
            'update_user'       => '',
        ], $data);

        //开启事务
        DB::beginTransaction();
        $status = 1;
        try
        {
            if(!is_array($data_filter))
            {
                self::exception(trans('api.api_param_error'), -1);
            }

            $do = $data_filter['do'];
            $id = $data_filter['id'];
            $create_user  = $data_filter['create_user'];
            $update_user  = $data_filter['update_user'];
            //狀態
            $data_filter['status'] = ($data_filter['status'] === 'on') ? 1:0;
            //默認展示
            $data_filter['is_default'] = ($data_filter['is_default'] === 'on') ? 1:0;
            //固定頻道
            $data_filter['is_fix'] = ($data_filter['is_fix'] === 'on') ? 1:0;
            unset($data_filter['do'], $data_filter['id'], $data_filter['create_user'], $data_filter['update_user']);

            if($do == 'add')
            {
                $data_filter['create_time'] = time();
                $data_filter['create_user'] = $create_user;
                self::insert_data($data_filter);
            }
            elseif($do == 'edit')
            {
                $data_filter['update_time'] = time();
                $data_filter['update_user'] = $update_user;
                self::update_data($data_filter, ['id'=>$id]);
            }
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

        if ($status > 0)
        {
            DB::commit();   //手動提交事务
        }
        else
        {
            DB::rollback(); //手動回滚事务
        }

        return $status;
    }

    //刪除
    protected function del_data(array $data)
    {
        //参数过滤
        $data_filter = mod_common::data_filter([
            'id'                => 'required',
            'delete_user'       => '',
        ], $data);

        //开启事务
        DB::beginTransaction();
        $status = 1;
        try
        {
            $id = $data_filter['id'];
            unset($data_filter['id']);

            if(!is_array($data_filter))
            {
                self::exception(trans('api.api_param_error'), -1);
            }

            $data_filter['delete_time'] = time();
            self::update_data($data_filter, ['id'=>$id]);
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

        if ($status > 0)
        {
            DB::commit();   //手動提交事务
        }
        else
        {
            DB::rollback(); //手動回滚事务
        }

        return $status;
    }

    //啟用或禁用
    protected function change_status(array $data)
    {
        //参数过滤
        $data_filter = mod_common::data_filter([
            'id'                => 'required',
            'update_user'       => '',
        ], $data);

        //开启事务
        DB::beginTransaction();
        $status = 1;
        try
        {
            $id     = $data_filter['id'];
            unset($data_filter['id']);

            if(!is_array($data_filter) || !is_numeric($data_filter['status']))
            {
                self::exception(trans('api.api_param_error'), -1);
            }

            $data_filter['update_time'] = time();
            self::update_data($data_filter, ['id'=>$id]);
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

        if ($status > 0)
        {
            DB::commit();   //手動提交事务
        }
        else
        {
            DB::rollback(); //手動回滚事务
        }

        return $status;
    }

    //获取手机国码
    protected function get_mobile_prefix()
    {
        $rows = self::get_list([
            'where'     =>  [
                ['status', '=', self::ENABLE],
                ['mobile_prefix', '!=', ''],
            ],
            'order_by'  => ['id', 'asc']
        ]);
        $ret = mod_array::one_array($rows, ['mobile_prefix', 'mobile_prefix']);
        return $ret;
    }
}
