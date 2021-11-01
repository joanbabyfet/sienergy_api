<?php

namespace App\models;

//use Illuminate\Database\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * mongodb 操作類
 *
 * 保存mondb的数据时需要注意,
 * 对于 int 類型, 必须严格声明, 否则可能在特殊情况下会被当成字符串, 从而无法识别
 * 可以用 MongoInt32/64 , 也可以用php默认的 intval(假如不限定整形长度的话)
 *
 * mongodb 内置的数据類型：
 * MongoId
 * MongoCode
 * MongoDate
 * MongoRegex
 * MongoBinData
 * MongoInt32
 * MongoInt64
 * MongoDBRef
 * MongoMinKey
 * MongoMaxKey
 * MongoTimestamp
 *
 * Class mod_mongodb
 * @package App\models
 */
class mod_mongodb extends Model
{
    //主键
    public $primaryKey = '';
    //文件名称
    protected $table = '';
    //使用其他数据库连接
    protected $connection = '';
    //字段
    public static $field = [];
    //添加时间字段
    const CREATED_AT = 'create_time';
    //修改时间字段
    const UPDATED_AT = 'update_time';
    //false=禁用自动填充时间戳
    public $timestamps = false;
    //时间使用时间戳
    public $dateFormat = 'U';
    //每页展示几笔
    public static $page_size = 10;
    public static $unknown_err_status = -1211; //未知错误,一般都是数据库死锁
    public static $dead_lock_status  = -1213; //死锁全局返回状态
    public static $msg_maps = [];

    protected function get_list(array $conds)
    {
        $connection   = !empty($this->connection) ? $this->connection:'mongodb';
        $pagesize     = $conds['page_size'] ?? self::$page_size;
        $fields    = self::get_fields($conds);
        $table   = self::table($conds);

        //调试时打开
        //DB::connection($connection)->enableQueryLog();
        $query = DB::connection($connection)->collection($table)->select($fields);

        if(!empty($conds['id']))
        {
            $query->where($this->get_pk($conds), '=', $conds['id']);
        }
        elseif(!empty($conds['where']))
        {
            self::_where($query, $conds['where']);
        }

        if(!empty($conds['order_by']))
        {
            $order_by = $conds['order_by'];
            $query->orderby($order_by[0], $order_by[1]);
        }

        //分页显示数据
        if(isset($conds['page']) || isset($conds['offset']))
        {
            $page = max(1, (isset($conds['page']) ? $conds['page'] : 1));
            $offset = !empty($conds['offset']) ?
                intval($conds['offset']) : ($page - 1) * $pagesize;

            $query->offset($offset)->limit($pagesize);
        }
        elseif(isset($conds['limit']))
        {
            $query->limit($conds['limit']);
        }

        if( !empty($conds['group_by']) )
        {
            $query->groupby($conds['group_by']);
        }

        //一律返回二维数组,不返回对象
        $tmp = $query->get()->toArray();

        //以指定字段當鍵名
        $index     = -1;
        $data = [];
        foreach($tmp as $row)
        {
            if(!empty($conds['index']) && isset($row[$conds['index']]))
            {
                $index = $row[$conds['index']];
            }
            else
            {
                ++$index;
            }
            $data[$index] = $row;
        }

        //是否返回总条数
        if(!empty($conds['count']))
        {
            $data = [
                'total' => static::get_count($conds),
                'data' => $data
            ];
        }

        //调试时打开
//        echo '<pre>';
//        print_r(DB::connection($connection)->getQueryLog());
//        exit;

        return $data;
    }

    //获取查询字段
    protected function get_fields(array $conds)
    {
        if(empty($conds['fields']) || $conds['fields'] === '*')
        {
            if(!empty(static::$field))
            {
                $fields = static::$field;
            }
            else
            {
                $fields = ['*'];
            }
        }
        else
        {
            $fields = $conds['fields'];
        }

        return $fields;
    }

    //获取单条数据
    protected function get_one(array $conds)
    {
        $data = self::get_list(array_merge($conds, [
            'limit' => 1,
        ]));
        //返回一组数组
        return $data ? current($data) : $data;
    }

    //添加数据
    protected function insert_data(array $data, $table = '')
    {
        if(empty($data))
        {
            return false;
        }
        $connection   = !empty($this->connection) ? $this->connection:'mongodb';

        $table = empty($table) ? $this->table:$table;
        if(empty($table)) return false;

        //调试时打开
        //DB::connection($connection)->enableQueryLog();
        $mutipule = is_array(reset($data)) ? true : false;
        if( !empty($mutipule) ) //批量插入
        {
            foreach ($data as $k => $v)
            {
                ksort($v);
                $data[$k] = $v;
            }

            //框架insert支持批量插入
            $result = DB::connection($connection)->collection($table)->insert($data);
        }
        else //单条插入
        {
            //统一插入格式
            //$data = [$data];

            $result = DB::connection($connection)->collection($table)->insertGetId($data);
        }

        //调试时打开
//        echo '<pre>';
//        print_r(DB::connection($connection)->getQueryLog());
//        exit;

        return $result;
    }

    //修改数据
    protected function update_data(array $data, array $where, $table = '')
    {
        if(empty($data) || empty($where))
        {
            return false;
        }
        $connection   = !empty($this->connection) ? $this->connection:'mongodb';
        $table = empty($table) ? $this->table:$table;
        if(empty($table)) return false;

        //调试时打开
        //DB::connection($connection)->enableQueryLog();
        //框架目前不支持批量更新
        $query = DB::connection($connection)->collection($table);
        self::_where($query, $where);
        $result = $query->update($data);

        //调试时打开
//        echo '<pre>';
//        print_r(DB::connection($connection)->getQueryLog());
//        exit;

        return $result;
    }

    //处理where条件
    protected function _where($query, $where, $table = '')
    {
        if(empty($where))
        {
            return false;
        }

        foreach($where as $column => $value)
        {
            if(is_numeric($column))
            {
                $field = $value[0];

                if(count($value) == 2)
                {
                    if(is_array($value[1]))
                    {
                        $query->whereIn($field, $value[1]);
                    }
                    else
                    {
                        $query->where($field, $value[1]);
                    }
                }
                else
                {
                    if(is_array($value[2]))
                    {
                        $query->whereIn($field, $value[2]);
                    }
                    else
                    {
                        $query->where($field, $value[1], $value[2]);
                    }
                }
            }
            else
            {
                if(is_array($value))
                {
                    $query->whereIn($column, $value);
                }
                else
                {
                    $query->where($column, $value);
                }
            }
        }

        return $query;
    }

    //删除数据
    protected function del(array $where, $table = '')
    {
        if(empty($where))
        {
            return false;
        }

        $connection   = !empty($this->connection) ? $this->connection:'mongodb';
        $table = empty($table) ? $this->table:$table;
        if(empty($table)) return false;

        //调试时打开
        //DB::connection($connection)->enableQueryLog();
        $query = DB::connection($connection)->collection($table);
        self::_where($query, $where);

        $result = $query->delete();
        //调试时打开
//        echo '<pre>';
//        print_r(DB::connection($connection)->getQueryLog());
//        exit;

        return $result;
    }

    //获取字段的值
    protected function get_field_value(array $conds)
    {
        $data = self::get_one($conds);
        return $data ? reset($data) : $data;
    }

    //获取条数
    protected function get_count(array $conds)
    {
        $connection   = !empty($this->connection) ? $this->connection:'mongodb';
        if( empty($conds['field']) )
        {
            $conds['field'] = !empty($this->get_pk($conds)) ? $this->get_pk($conds) : '*';
        }

        //调试时打开
        //DB::connection($connection)->enableQueryLog();
        $query = DB::connection($connection)->collection(self::table($conds));

        if(!empty($conds['where']))
        {
            $query->where($conds['where']);
        }

        $data = $query->count($conds['field']);

        //调试时打开
//        echo '<pre>';
//        print_r(DB::connection($connection)->getQueryLog());
//        exit;

        return $data;
    }

    //获取字段求和
    protected function get_sum(array $conds)
    {
        $connection   = !empty($this->connection) ? $this->connection:'mongodb';

        //调试时打开
        //DB::connection($connection)->enableQueryLog();
        $query = DB::connection($connection)->collection(self::table($conds));

        if(!empty($conds['where']))
        {
            self::_where($query, $conds['where']);
        }

        $data = $query->sum($conds['field']);

        //调试时打开
//        echo '<pre>';
//        print_r(DB::getQueryLog());
//        exit;

        return $data;
    }

    //获取字段最大值
    protected function get_max(array $conds)
    {
        $connection   = !empty($this->connection) ? $this->connection:'mongodb';

        //调试时打开
        //DB::connection($connection)->enableQueryLog();
        $query = DB::connection($connection)->collection(self::table($conds));

        if(!empty($conds['where']))
        {
            self::_where($query, $conds['where']);
        }

        $data = $query->max($conds['field']);

        //调试时打开
//        echo '<pre>';
//        print_r(DB::getQueryLog());
//        exit;

        return $data;
    }

    //获取字段最小值
    protected function get_min(array $conds)
    {
        $connection   = !empty($this->connection) ? $this->connection:'mongodb';

        //调试时打开
        //DB::connection($connection)->enableQueryLog();
        $query = DB::connection($connection)->collection(self::table($conds));

        if(!empty($conds['where']))
        {
            self::_where($query, $conds['where']);
        }

        $data = $query->min($conds['field']);

        //调试时打开
//        echo '<pre>';
//        print_r(DB::getQueryLog());
//        exit;

        return $data;
    }

    //获取字段平均
    protected function get_avg(array $conds)
    {
        $connection   = !empty($this->connection) ? $this->connection:'mongodb';

        //调试时打开
        //DB::connection($connection)->enableQueryLog();
        $query = DB::connection($connection)->collection(self::table($conds));

        if(!empty($conds['where']))
        {
            self::_where($query, $conds['where']);
        }

        $data = $query->avg($conds['field']);

        //调试时打开
//        echo '<pre>';
//        print_r(DB::getQueryLog());
//        exit;

        return $data;
    }

    //统一异常处理
    protected function get_exception_status(\Exception $e)
    {
        $err_code = $e->getCode();
        $status = $err_code >= 0 ? self::$unknown_err_status : $err_code;
        self::$msg_maps[$status] = $e->getMessage();

        return $status;
    }

    //获取异常信息
    protected function get_err_msg($status)
    {
        return isset(static::$msg_maps[$status]) ? static::$msg_maps[$status] : 'Unknown error!';
    }

    //抛异常封装
    protected function exception($msg = '', $code = null)
    {
        $code = $code ? $code : self::$unknown_err_status;
        throw new \Exception($msg, $code);
    }

    /**
     * @param  上层方法提交的数组
     * @return string
     * 返回查询字段
     */
    protected function get_pk(array $conds)
    {
        $pk = '';
        if( !empty($conds['pk']) )
        {
            $pk = $conds['pk'];
        }
        else
        {
            $pk = $this->primaryKey;
        }

        return $pk;
    }

    /**
     * 上层方法提交的数组
     *
     * @param array $conds
     * @return mixed|string
     */
    protected function table(array $conds)
    {
        $table = '';
        if(!empty($conds['table']))
        {
            $table = $conds['table'];
        }
        elseif(!empty($this->table))
        {
            $table = $this->table;
        }
        return $table;
    }
}
