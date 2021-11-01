<?php

namespace App\models;

use App\Jobs\job_send_mail;
use Illuminate\Support\Facades\DB;

class mod_feedback extends mod_model
{
    //主键
    public $primaryKey = 'id';
    //主键是否支持自增,默认支持
    public $incrementing = true;
    //表名称
    public $table = 'feedback';
    //使用其他数据库连接
    //protected $connection = '';
    //字段
    public static $field = [
    ];
    //每页展示几笔
    public static $page_size = 10;
    //性別
    public static $sex_map = [
        0 => '女',
        1 => '男',
    ];

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
            'id'            => $do == 'edit' ? 'required' : '',
            'captcha'       => 'required',
            'name'          => 'required',
            'company_name'  => '',
            'sex'           => 'required',
            'email'         => 'required',
            'phone'         => '',
            'content'       => '',
            'create_user'   => '',
            'update_user'   => '',
        ], $data);

        //开启事务
        DB::beginTransaction();
        $status = 1;
        try
        {
            if(empty($data_filter['captcha']) || !captcha_check($data_filter['captcha']))
            {
                self::exception('验证码输入有误，请重新输入', -1);
            }

            if(empty($data_filter['name']))
            {
                self::exception('姓名不可空白', -2);
            }

            if(empty($data_filter['email']))
            {
                self::exception('電子郵件不可空白', -3);
            }

            if(empty($data_filter['content']))
            {
                self::exception('您的意見不可空白', -4);
            }

            $id = $data_filter['id'];
            $data_filter['content']  = mod_common::htmlentities($data_filter['content']);
            unset($data_filter['do'], $data_filter['id'], $data_filter['captcha']);

            if($do == 'add')
            {
                $data_filter['create_time'] = time();
                self::insert_data($data_filter);
                //发送邮件
                $mail_data = [
                    'company_name'  =>  $data_filter['company_name'],
                    'name'          =>  $data_filter['name'],
                    'sex'           =>  self::$sex_map[$data_filter['sex']],
                    'email'         =>  $data_filter['email'],
                    'phone'         =>  $data_filter['phone'],
                    'content'       =>  htmlspecialchars_decode($data_filter['content']),
                ];
                $to = [
                    'email' =>'crwu0206@gmail.com',
                    'name'  =>''
                ];
                //發送郵件走異步任務,減少用戶等待時間
                $job = new job_send_mail([
                    'to'        => $to,
                    'subject'   => '問題諮詢',
                    'view'      => 'mail.contact',
                    'view_data' => $mail_data,
                ]);
                dispatch($job);
            }
            elseif($do == 'edit')
            {
                $data_filter['update_time'] = time();
                self::update_data($data_filter, [['id'=>$id]]);
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
