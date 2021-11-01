<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class mod_example extends mod_model
{
    //主键
    public $primaryKey = 'id';
    //主键是否支持自增,默认支持
    public $incrementing = false;
    //表名称
    public $table = 'example';
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
    //封面图压缩宽度
    public static $img_thumb_with = 100;

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
        $next_page  = $conds['next_page'] ?? null;
        //分類id
        $cat_id     = !empty($conds['cat_id']) ? $conds['cat_id']:'';
        //標題
        $title      = !empty($conds['title']) ? $conds['title']:'';
        $status     = $conds['status'] ?? null;

        $where = [];
        $where[] = ['delete_time', '=', 0];
        $cat_id and $where[] = ['cat_id', '=', $cat_id];
        //搜加密字段
        $title and $where[] = ['title', 'like', "%{$title}%"];
        is_numeric($status) and $where[] = ['status', '=', $status];

        $order_by = !empty($order_by) ? $order_by : ['create_time', 'desc'];
        $group_by = !empty($group_by) ? $group_by : [];

        $rows = self::get_list([
            //返回加密字段 self::expr(self::crypt_field('title').' AS title')
            'fields'    => ['id', 'cat_id', 'title', 'content', 'is_hot',
                'status', 'sort', 'create_user', 'create_time'
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
            //圖片
            $imgs = empty($v['img']) ? []:explode(',', $v['img']);
            $img_dis = [];
            $img_url_dis = [];
            foreach ($imgs as $img)
            {
                $img_dis[] = $img;
                $img_url_dis[] = mod_display::img($img);
            }
            //附件
            $files = empty($v['file']) ? []:explode(',', $v['file']);
            $file_dis = [];
            $file_url_dis = [];
            foreach ($files as $file){
                $file_dis[] = $file;
                $file_url_dis[] = mod_display::img($file, 'doc');
            }

            if(isset($v['content']))
            {
                $v['content'] = htmlspecialchars_decode($v['content']);
            }

            $row_plus = [
                //状态
                'status_dis'       => array_key_exists($v['status'], self::$status_map) ? self::$status_map[$v['status']]:'',
                //添加日期
                'create_time_dis'  => mod_display::datetime($v['create_time']),
                //添加人
                'create_user_dis'  => $v['create_user'],
                //圖片
                'img_dis'  => $img_dis,
                'img_url_dis'  => $img_url_dis,
                //附件
                'file_dis'  => $file_dis,
                'file_url_dis'  => $file_url_dis,
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
            'title'     => 'required',
            'content'   => '',
            'status'    => 'required',
            'file'      => '',
            'img'       => '',
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
            $data_filter['content']  = mod_common::htmlentities($data_filter['content']);
            //圖片
            $img = empty($data_filter['img']) ? []: array_filter($data_filter['img']); //干掉空值
            $data_filter['img'] = implode(',', $img);
            //附件
            $file = empty($data_filter['file']) ? []: array_filter($data_filter['file']); //干掉空值
            $data_filter['file'] = implode(',', $file);
            //狀態
            $data_filter['status'] = ($data_filter['status'] === 'on') ? 1:0;
            unset($data_filter['do'], $data_filter['id'], $data_filter['create_user'], $data_filter['update_user']);

            if($do == 'add')
            {
                $data_filter['id'] = mod_common::random('web');
                $data_filter['create_time'] = time();
                $data_filter['create_user'] = $create_user;
                //$data_filter['title'] = self::expr(self::crypt_value('xxx')); //加密字段blob
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
}
