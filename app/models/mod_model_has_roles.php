<?php

namespace App\models;

class mod_model_has_roles extends mod_model
{
    //主鍵
    public $primaryKey = 'role_id';
    //主键是否支持自增,默认支持
    public $incrementing = false;
    //表名称
    public $table = 'model_has_roles';
    //使用其他数据库连接
    //protected $connection = '';
    //字段
    public static $field = [
    ];
    //每页展示几笔
    public static $page_size = 10;

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
        $model_id   = $conds['model_id'] ?? null;
        $model_type = $conds['model_type'] ?? null;
        $role_id   = $conds['role_id'] ?? null;

        $where = [];
        $model_id and $where[] = ['model_id', 'in',
            is_array($model_id) ? $model_id : [$model_id]];
        $model_type and $where[] = ['model_type', '=', $model_type];
        $role_id and $where[] = ['role_id', 'in', is_array($role_id) ? $role_id : [$role_id]];

        $order_by = !empty($order_by) ? $order_by : [];
        $group_by = !empty($group_by) ? $group_by : [];

        $rows = self::get_list([
            'fields'    => ['role_id', 'model_type', 'model_id'
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

    //詳情
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
            ];

            $list[$k] = array_merge($v, $row_plus);
        }
        return is_array(reset($data)) ? $list : reset($list);
    }
}
