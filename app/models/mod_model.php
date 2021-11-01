<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class mod_model extends Model
{
    //主键
    public $primaryKey = '';
    //主键是否支持自增,默认支持
    public $incrementing = true;
    //表名称
    public $table = '';
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
    //AES加解密算法使用key
    public static $crypt_key = 'NfGiFzgqjWPaz';

    //获取列表
    protected function get_list(array $conds)
    {
        $pagesize = $conds['page_size'] ?? self::$page_size;
        $fields    = self::get_fields($conds);
        $table   = self::table($conds);

        //调试时打开
        //DB::enableQueryLog();
        $query = static::select($fields)->from($table);

        if(!empty($conds['id']))
        {
            $query->where($this->get_pk($conds), '=', $conds['id']);
        }
        elseif(!empty($conds['where']))
        {
            self::_where($query, $conds['where']);
        }

        //是否加锁
        if(!empty($conds['lock']) || !empty($conds['share']))
        {
            if(!empty($conds['lock']))
            {
                $query->lockForUpdate(); //排他鎖(寫鎮),框架默认走主库,事務中使用才生效
            }
            else if(!empty($conds['share']))
            {
                $query->sharedLock(); //共享鎖(讀鎖),框架默认走从库,事務中使用才生效
            }
            //锁表一律只走主库
            $query->useWritePdo();
        }

        if(!empty($conds['order_by']))
        {
            $order_by = $conds['order_by'];
            $query->orderby($order_by[0], $order_by[1]);
        }

        //对于一些大表，因为innodb统计总数不像myisam那样本来已经统计好了，
        //所以会非常慢，一般不做分页，只显示是否有下一页
        //对于app,不需要计算总条数，只需返回是否需要下一页
        if ( !empty($conds['next_page']) )
        {
            $_pagesize = $pagesize;
            ++$pagesize;
        }

        //分页显示数据
        if(isset($conds['page']) || isset($conds['offset']))
        {
            $page = max(1, (isset($conds['page']) ? $conds['page'] : 1));
            $offset = !empty($conds['offset']) ?
                intval($conds['offset']) :
                ($page - 1) * (!empty($conds['next_page']) ? $_pagesize : $pagesize);

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
        $rows = [];
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
            $rows[$index] = $row;
        }

        //是否返回总条数
        if(!empty($conds['count']))
        {
            $data = [
                'total' => static::get_count($conds),
                'data' => $rows
            ];
        }
        elseif ( !empty($conds['next_page']) ) //是否有下一页
        {
            $has_next_page = 0;
            if ( count($rows) > $_pagesize )
            {
                $has_next_page = 1;
                array_pop($rows); //丢掉最后一条数据
            }

            $data = [
                'next_page' => $has_next_page,
                'data' => $rows
            ];
        }
        else
        {
            $data = $rows;
        }

        //调试时打开
//        echo '<pre>';
//        print_r(DB::getQueryLog());
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

    /**
     * 获取sql一条数据，测试用
     *
     * @param $sql
     * @param bool $is_master
     * @return array|mixed
     */
    protected function get_one_query($sql, $is_master = false)
    {
        //调试时打开
        //DB::enableQueryLog();
        $sql = empty($sql) ? '':"{$sql} limit 0,:n";
        $data = DB::select($sql, ['n' => 1]);
        //调试时打开
//        echo '<pre>';
//        print_r(DB::getQueryLog());
//        exit;

        $data = collect($data)->map(function ($item, $key) {
            return (array) $item;
        })->toArray();
        //返回一组数组
        return $data ? current($data) : $data;
    }

    /**
     * 获取sql数据
     *
     * @param $sql
     * @param bool $is_master
     * @return array|mixed
     */
    protected function get_list_query($sql, $is_master = false)
    {
        //调试时打开
        //DB::enableQueryLog();
        $data = DB::select($sql);
        //调试时打开
//        echo '<pre>';
//        print_r(DB::getQueryLog());
//        exit;

        $data = collect($data)->map(function ($item, $key) {
            return (array) $item;
        })->toArray();
        return $data;
    }

    //添加数据
    protected function insert_data(array $data, $table = '')
    {
        if(empty($data))
        {
            return false;
        }
        $table = empty($table) ? $this->table:$table;
        if(empty($table)) return false;

        //调试时打开
        //DB::enableQueryLog();
        $mutipule = is_array(reset($data)) ? true : false;
        if( !empty($mutipule) ) //批量插入
        {
            foreach ($data as $k => $v)
            {
                ksort($v);
                $data[$k] = $v;
            }

            //框架insert支持批量插入
            $result = static::from($table)->insert($data);
        }
        else //单条插入
        {
            //统一插入格式
            //$data = [$data];

            $result = static::from($table)->insertGetId($data);
        }

        //调试时打开
//        echo '<pre>';
//        print_r(DB::getQueryLog());
//        exit;

        return $result;
    }

    //批量插入或更新
    protected function insertOrUpdate(array $data, $table = '')
    {
        if(empty($data))
        {
            return false;
        }
        $table = empty($table) ? $this->table:$table;
        if(empty($table)) return false;

        $first = reset($data);

        $columns = implode( ',',
            array_map(function($value) {
                return "$value";
            } , array_keys($first))
        );

        $values = implode(',', array_map(function($data) {
                return '('.implode(',',
                        array_map(function($value) { return '"'.str_replace('"', '""', $value).'"'; }, $data)
                    ).')';
            }, $data)
        );

        $updates = implode( ',',
            array_map(function($value) { return "$value = VALUES($value)"; } , array_keys($first))
        );
        //更新数据，如果不存在则创建
        $table = DB::getTablePrefix().$table;
        $sql = "INSERT INTO {$table}({$columns}) VALUES {$values} ON DUPLICATE KEY UPDATE {$updates}";

        return DB::statement($sql);
    }

    //修改数据
    protected function update_data(array $data, array $where, $table = '')
    {
        if(empty($data) || empty($where))
        {
            return false;
        }
        $table = empty($table) ? $this->table:$table;
        if(empty($table)) return false;

        //调试时打开
        //DB::enableQueryLog();
        //框架目前不支持批量更新
        //$result = static::from($table)->where($where)->update($data);
        $query = static::from($table);
        self::_where($query, $where);
        $result = $query->update($data);

        //调试时打开
//        echo '<pre>';
//        print_r(DB::getQueryLog());
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

        $table = empty($table) ? $this->table:$table;
        if(empty($table)) return false;

        //调试时打开
        //DB::enableQueryLog();
        $query = static::from($table);
        self::_where($query, $where);

        $result = $query->delete();
        //调试时打开
//        echo '<pre>';
//        print_r(DB::getQueryLog());
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
        if( empty($conds['field']) )
        {
            $conds['field'] = !empty($this->get_pk($conds)) ? $this->get_pk($conds) : '*';
        }

        //调试时打开
        //DB::enableQueryLog();

        $query = static::select()->from(self::table($conds));
        //是否加锁
        if(!empty($conds['lock']) || !empty($conds['share']))
        {
            if(!empty($conds['lock']))
            {
                $query->lockForUpdate(); //排他鎖(寫鎮),框架默认走主库,事務中使用才生效
            }
            else if(!empty($conds['share']))
            {
                $query->sharedLock(); //共享鎖(讀鎖),框架默认走从库,事務中使用才生效
            }
            //锁表一律只走主库
            $query->useWritePdo();
        }

        if(!empty($conds['where']))
        {
            self::_where($query, $conds['where']);
        }

        $data = $query->count($conds['field']);

        //调试时打开
//        echo '<pre>';
//        print_r(DB::getQueryLog());
//        exit;

        return $data;
    }

    //获取字段求和
    protected function get_sum(array $conds)
    {
        //调试时打开
        //DB::enableQueryLog();
        $query = static::select()->from(self::table($conds));

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
        //调试时打开
        //DB::enableQueryLog();
        $query = static::select()->from(self::table($conds));

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
        //调试时打开
        //DB::enableQueryLog();
        $query = static::select()->from(self::table($conds));

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
        //调试时打开
        //DB::enableQueryLog();
        $query = static::select()->from(self::table($conds));

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

    /**
     * 加解密字段，mysql要定义该字段为blob
     *
     * @param $field 字段
     * @param bool $encode
     * @return string
     */
    protected function crypt_field($field, $encode = false)
    {
        $crypt_key = self::$crypt_key;
        $func = !empty($encode) ? 'AES_ENCRYPT' : 'AES_DECRYPT';
        return  "CONVERT({$func}({$field}, '{$crypt_key}') USING utf8)";
    }

    /**
     * 字段值加密
     *
     * @param $value
     * @return string
     */
    public function crypt_value($value)
    {
        $crypt_key = self::$crypt_key;
        $func = 'AES_ENCRYPT';
        return "{$func}('{$value}', '{$crypt_key}')";
    }

    /**
     * 自定义字段
     *
     * @param $string
     * @return mixed
     */
    protected function expr($string)
    {
        return DB::raw($string);
    }
}
