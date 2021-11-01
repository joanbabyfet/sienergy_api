<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class mod_model_has_permissions extends mod_model
{
    //主鍵
    public $primaryKey = 'permission_id';
    //主键是否支持自增,默认支持
    public $incrementing = false;
    //表名称
    public $table = 'model_has_permissions';
    //使用其他数据库连接
    //protected $connection = '';
    //字段
    public static $field = [
    ];
    //每页展示几笔
    public static $page_size = 10;

    protected function list_data(array $conds)
    {
        $data = parent::get_list($conds);
        return $data;
    }

    //保存
    protected function save_data(array $data)
    {
        $do             = isset($data['do']) ? $data['do'] : '';
        //参数过滤
        $data_filter = mod_common::data_filter([
            'do'            => 'required',
            'model_id'      => 'required', //用户id
            'permission_id' => '', //权限id
            'model_type'    => '', //用户類型,例 App\models\mod_user
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

            $do    = $data_filter['do'];
            $id    = $data_filter['model_id'];
            unset($data_filter['do']);

            if($do == 'add')
            {
                //会员
                if(class_basename($data_filter['model_type']) == 'mod_user') {
                    $user = mod_user::find($id);
                    $user->syncPermissions($data_filter['permission_id']);
                }
                //管理员
                elseif(class_basename($data_filter['model_type']) == 'mod_admin_user') {
                    $user = mod_admin_user::find($id);
                    $user->syncPermissions($data_filter['permission_id']);
                }
            }
            elseif($do == 'edit')
            {

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
}
