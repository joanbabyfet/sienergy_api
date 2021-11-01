<?php

namespace App\models;

use App\Jobs\job_send_mail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

/**
 * 系统邮件
 * Class mod_sys_mail
 * @package App\models
 */
class mod_sys_mail extends Model
{
    //邮件发送对象
    const OBJECT_TYPE_ALL      = 1;
    const OBJECT_TYPE_PERSONAL = 2;
    const OBJECT_TYPE_LEVEL    = 3;
    const OBJECT_TYPE_REG_TIME = 4;
    public static $object_type = [
        1=>'所有用户',
        2=>'个人',
        3=>'会员等级',
        4=>'注册时间'
    ];

    /**
     * 發送郵件 (外部调用这里)
     * @param array $data
     * @return int|mixed
     */
    public static function send(array $data)
    {
        //参数过滤
        $data_filter = mod_common::data_filter([
            'object_type'   => 'required',
            'object_ids'    => '',
            'subject'       => 'required',
            'view'          => 'required',
            'view_data'     => '',
        ], $data);

        $status = 1;
        try
        {
            if(!is_array($data_filter))
            {
                mod_model::exception(trans('api.api_param_error'), -1);
            }

            $view       = $data_filter['view'];
            $subject    = $data_filter['subject'];
            $view_data  = empty($data_filter['view_data']) ? [] : $data_filter['view_data'];

            $where = ['status' => 1]; //激活
            switch ($data_filter['object_type'])
            {
                case self::OBJECT_TYPE_ALL:
                    break;
                case self::OBJECT_TYPE_PERSONAL:
                    $uids = explode(',', $data_filter['object_ids']);
                    $uids = empty($uids) || empty($data_filter['object_ids']) ?
                        [-1] : $uids;
                    //用户id
                    $where[] = ['id', 'in', $uids];
                    break;
                case self::OBJECT_TYPE_LEVEL:
                    $level_ids = explode(',', $data_filter['object_ids']);
                    $level_ids = empty($level_ids) || empty($data_filter['object_ids']) ?
                        [] : $data_filter['object_ids'];

                    //获取该用户组有哪些用户
                    $uids = [];
                    if(!empty($level_ids))
                    {
                        $users = mod_model_has_roles::list_data([
                            'role_id'       => $level_ids,
                            'model_type'    => get_class(new mod_user())
                        ]);
                        $uids = mod_array::sql_in($users, 'model_id');
                    }
                    $uids = empty($uids) ? [-1] : $uids;
                    //会员等级id
                    $where[] = ['id', 'in', $uids];
                    break;
                case self::OBJECT_TYPE_REG_TIME:
                    if (!empty($data_filter['object_ids']) && strpos($data_filter['object_ids'], ',') !== false)
                    {
                        list($start_date, $end_date) = explode(',', $data_filter['object_ids']);
                        $start_time = empty($start_date) ? '' : mod_common::date_convert_timestamp("{$start_date} 00:00:00", mod_common::get_admin_timezone());
                        $end_time   = empty($end_date) ? '' : mod_common::date_convert_timestamp("{$end_date} 23:59:59", mod_common::get_admin_timezone());
                    }
                    if (!empty($start_time) && !empty($end_time) && $start_time < $end_time)
                    {
                        $where[] = ['create_time', '>=', $start_time];
                        $where[] = ['create_time', '<=', $end_time];
                    }
                    break;
                default:
                    mod_model::exception('发送对象類型错误', -2);
            }

            $page_no = 1;
            do
            {
                //获取收件人信箱,姓名
                $rows = mod_model::get_list([
                    'fields'    => [
                        'id', 'username', 'email',
                        mod_model::expr('`realname` As name'),
                    ],
                    'table'     => 'users',
                    'page'      =>  $page_no,
                    'page_size' =>  500,
                    'where'     =>  $where,
                    'order_by'  => ['create_time', 'asc'],
                ]);

                if (empty($rows))
                {
                    break;
                }

                $to = [];
                foreach($rows as $k => $v)
                {
                    //收件人
                    $to_plus = [
                        'name'      => $v['name'],
                        'email'     => $v['email'],
                    ];
                    $to[$v['id']] = $to_plus;
                }

                //執行腳本,将任务放入异步队列中
                $params = [
                    'to'        => $to, //多收件人
                    'subject'   => $subject,
                    'view'      => $view,
                    'view_data' => $view_data,
                ];
                $job = new job_send_mail($params);
                dispatch($job);

                $page_no++;
            }
            while (!empty($rows));

            if (empty($rows) && $page_no === 1)
            {
                mod_model::exception('发送对象不存在', -3);
            }
        }
        catch (\Exception $e)
        {
            $status = $e->getCode();
            //記錄日誌
            mod_common::logger(__METHOD__, [
                'status'  => $status,
                'errcode' => $e->getCode(),
                'errmsg'  => $e->getMessage(),
                'data'    => $data,
            ]);
        }

        return $status;
    }

    /**
     * 發送郵件
     * @param array $data
     * @return bool
     */
    public static function _send_mail(array $data)
    {
        //参数过滤
        $data_filter = mod_common::data_filter([
            'to'            => 'required',
            'subject'       => 'required',
            'view'          => 'required',
            'view_data'     => '',
        ], $data);

        $status = 1;
        try
        {
            $to             = $data_filter['to'];
            $to             = is_array(reset($to)) ? $to : [$to]; //兼容一维数组
            $subject        = $data_filter['subject'];
            $view           = $data_filter['view'];
            $view_data      = $data_filter['view_data'];
            $view_data      = is_array(reset($view_data)) ? $view_data : [$view_data];
            $from = [ //寄送人
                'email' => config('mail.from.address'),
                'name'  => config('mail.from.name'),
            ];

            foreach($to as $k => $item)
            {
                $mail_data = empty($view_data) || empty($data_filter['view_data']) ?
                    [] : $view_data[$k];
                //发送
                Mail::send($view, $mail_data, function($mail) use ($from, $item, $subject)
                {
                    $mail->from($from['email'], $from['name']);
                    $mail->to($item['email'], $item['name'])->subject($subject);
                });
                //避免太過頻繁的查詢
                usleep(100000); //让进程挂起一段时间,避免cpu跑100%(单位微秒 1秒=1000000)
            }
        }
        catch (\Exception $e)
        {
            $status = $e->getCode();
            //記錄日誌
            mod_common::logger(__METHOD__, [
                'status'  => $status,
                'errcode' => $e->getCode(),
                'errmsg'  => $e->getMessage(),
                'args'    => func_get_args()
            ]);
        }

        return $status;
    }
}
