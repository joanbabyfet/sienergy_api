<?php

namespace App\models;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class mod_role extends mod_model
{
    //主键
    public $primaryKey = 'id';
    //主键是否支持自增,默认支持
    public $incrementing = true;
    //表名称
    public $table = 'roles';
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
        $name       = !empty($conds['name']) ? $conds['name']:'';
        $guard_name = !empty($conds['guard_name']) ? $conds['guard_name']:'';
        $id         = $conds['id'] ?? null;

        $where = [];
        $name and $where[] = ['name', 'like', "%{$name}%"];
        $guard_name and $where[] = ['guard_name', '=', $guard_name];
        $id and $where[] = ['id', 'in', is_array($id) ? $id : [$id]];

        $order_by = !empty($order_by) ? $order_by : ['created_at', 'desc'];
        $group_by = !empty($group_by) ? $group_by : [];

        $rows = self::get_list([
            'fields'    => ['id', 'name', 'guard_name', 'created_at'
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
        //获取守卫表
        $guards = config('global.guard_names');

        foreach ($list as $k => $v)
        {
            $row_plus = [
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
            'do'        => 'required',
            'id'        => $do == 'edit' ? 'required' : '',
            'name'     => 'required',
            'guard_name'   => 'required',
            'permissions'   => '',
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

            $do     = $data_filter['do'];
            $id     = $data_filter['id'];
            $guard  = $data_filter['guard_name'];
            $permissions = empty($data_filter['permissions']) ? [] : $data_filter['permissions'];
            unset($data_filter['do'], $data_filter['id'], $data_filter['permissions']);

            if($do == 'add')
            {
                $data_filter['created_at'] = date('Y-m-d H:i:s');
                $id = self::insert_data($data_filter);

                $role = Role::findById($id, $guard); //添加组权限
                $role->givePermissionTo($permissions);
            }
            elseif($do == 'edit')
            {
                $data_filter['updated_at'] = date('Y-m-d H:i:s');
                self::update_data($data_filter, ['id'=>$id]);

                if($id != config('global.super_role_id')) //超级管理员不做同步
                {
                    $role = Role::findById($id, $guard); //同步组权限
                    $role->syncPermissions($permissions);
                }
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

            if(in_array($id, [config('global.super_role_id'), config('global.gen_mem_role_id')]))
            {
                self::exception('超级管理员无法删除', -2);
            }

//            $where = [];
//            $where[] = ['role_id', '=', $id];
//            if(mod_model_has_roles::get_count(['where' => $where]))
//            {
//                self::exception('该用户组已被使用，无法删除', -3);
//            }

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

    //根据用户id获取用户组列表
    protected function get_roles_by_uid(array $data)
    {
        //参数过滤
        $data_filter = mod_common::data_filter([
            'model_id'    => 'required',
            'model_type'  => '',
        ], $data);

        $ids = empty($data_filter['model_id']) ? [-1] : $data_filter['model_id'];

        $roles = mod_model_has_roles::list_data([
            'model_id'      => $ids,
            'model_type'    => $data_filter['model_type']
        ]);

        $ret = mod_role::list_data([
            'id'    => mod_array::sql_in($roles, 'role_id')
        ]);

        return $ret;
    }
}
