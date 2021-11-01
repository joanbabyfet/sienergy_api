<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class mod_permission extends mod_model
{
    //主键
    public $primaryKey = 'id';
    //主键是否支持自增,默认支持
    public $incrementing = true;
    //表名称
    public $table = 'permissions';
    //使用其他数据库连接
    //protected $connection = '';
    //字段
    public static $field = [
    ];
    //每页展示几笔
    public static $page_size = 10;
    //狀態
//    const DISABLE = 0;
//    const ENABLE = 1;
//    public static $status_map = [
//        self::DISABLE   => '禁用',
//        self::ENABLE    => '啟用'
//    ];

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
        $display_name   = !empty($conds['display_name']) ? $conds['display_name']:'';
        $guard_name     = !empty($conds['guard_name']) ? $conds['guard_name']:'';
        $pg_id          = isset($conds['pg_id']) ? $conds['pg_id']:'';

        $where = [];
        $display_name and $where[] = ['display_name', 'like', "%{$display_name}%"];
        $guard_name and $where[] = ['guard_name', '=', $guard_name];
        is_numeric($pg_id) and $where[] = ['pg_id', '=', $pg_id];

        $order_by = !empty($order_by) ? $order_by : ['created_at', 'desc'];
        $group_by = !empty($group_by) ? $group_by : [];

        $rows = self::get_list([
            'fields'    => ['id', 'name', 'guard_name', 'display_name', 'pg_id', 'created_at'
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

        //获取权限组别列表
        $permission_group = mod_permission_group::list_data([]);
        $permission_group = mod_array::one_array($permission_group, ['id', 'name']);
        //获取守卫表
        $guards = config('global.guard_names');

        foreach ($list as $k => $v)
        {
            $row_plus = [
                'permission_group_name' => array_key_exists($v['pg_id'], $permission_group) ?
                    $permission_group[$v['pg_id']] : '',
                //守卫
                'guard_dis'        => array_key_exists($v['guard_name'], $guards)
                    ? $guards[$v['guard_name']] : '',
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
            'do'            => 'required',
            'id'            => $do == 'edit' ? 'required' : '',
            'name'          => 'required',
            'display_name'  => 'required',
            'pg_id'         => '',
            'guard_name'    => 'required',
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
            unset($data_filter['do'], $data_filter['id']);

            if($do == 'add')
            {
                $data_filter['created_at'] = date('Y-m-d H:i:s');
                self::insert_data($data_filter);
            }
            elseif($do == 'edit')
            {
                $data_filter['updated_at'] = date('Y-m-d H:i:s');
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

            self::del(['id'=>$id]);
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

    //获取树形
    protected function get_tree(array $data)
    {
        //参数过滤
        $data_filter = mod_common::data_filter([
            'guard'             => '',
            'order_by'          => '',
            'is_auth'           => '', //是否验证权限,默认0=不验证
        ], $data);

        $default_guard = get_default_guard(); //默认守卫
        $guard = empty($data_filter['guard']) ? $default_guard : $data_filter['guard'];
        $order_by = empty($data_filter['order_by']) ? ['created_at', 'asc'] : $data_filter['order_by'];
        $is_auth = empty($data_filter['is_auth']) ? 0 : $data_filter['is_auth'];

        //获取权限组
        $first_item[0] = ['id' => 0, 'name' => '未分類'];
        $tree = mod_permission_group::list_data([
            'index'     => 'id',
            'order_by'  =>  $order_by,
        ]);
        //插入到数组开头
        $tree = $first_item + $tree;
        //获取权限列表
        $rows = self::list_data([
            'guard_name'    =>  $guard,
            'order_by'      =>  $order_by,
        ]);
        //设置人自己拥有的权限，如自己都没有的权限当然不能给别人设置
        $purviews = get_purviews([
            'guard' => config('global.admin.guard'), //固定为admin
            'field' => 'id'
        ]);
        foreach ($rows as $item)
        {
            if (isset($tree[$item['pg_id']]))
            {
                //匹配当前用户权限,超级管理员全部展示
                if(in_array('*', $purviews) ||
                    (!$is_auth || in_array($item['id'], $purviews)))
                {
                    $tree[$item['pg_id']]['children'][] = $item;
                }
            }
        }
        //遍历,子项为空,则干掉整个节点
        $tree = array_filter($tree, function($item) {
            return !empty($item['children']);
        });

        return $tree;
    }
}
