<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

/**
 * Redis操作類
 * Class mod_redis
 * @package App\models
 */
class mod_redis extends Model
{
    //默认缓存时间2小时，单位秒
    private static $cache_time  = 120 * 60;
    // key默认前缀
    private static $df_prefix = 'mc_df_';
    //开启redis自动序列化存储，默认true
    private static $serialize = true;
    //類型
    public static $type_map = [
        0    => 'none', //key didn't exist
        1    => 'string',
        2    => 'set',
        3    => 'list',
        4    => 'zset',
        5    => 'hash',
    ];

    //获取服务器信息
    public static function info()
    {
        return Redis::info();
    }

    //获取所有键名
    public static function keys($key)
    {
        $key = empty($key) ? '*' : $key;
        return Redis::keys($key);
    }

    //获取键名类型
    public static function _type($key)
    {
        $type = Redis::type($key); //返回int
        return array_key_exists($type, self::$type_map) ? self::$type_map[$type] : '';
    }

    //获取键名剩余超时时间
    public static function ttl($key)
    {
        return Redis::ttl($key);
    }

    //获取键名队列(列表)长度
    public static function lLen($key)
    {
        return Redis::lLen($key);
    }

    //获取字符串
    public static function get($key, $serialize = null)
    {
        //调试模式时不获取
        //if (env('APP_DEBUG')) return false;

        if($serialize === null) $serialize = self::$serialize;

        $value = Redis::get($key);

        //return @unserialize($value);//要加@若不是序列化数据,則返回false防止報錯
        return $serialize ? self::decode($value) : $value;
    }

    /**
     * 设置字符串
     * @param $key
     * @param $value
     * @param int $expire 有效时间，单位是秒(0=不限, -1=使用系统默认)
     * @param null $serialize
     * @return mixed
     */
    public static function set($key, $value, $expire = -1, $serialize = null)
    {
        if($expire == -1) $expire = self::$cache_time;  //获取默认时间
        if($serialize === null) $serialize = self::$serialize;

        //序列化数组并返回字符串，不要用php的serialize()，别的语言无法读
        //$value = serialize($value);
        $value = $serialize ? self::encode($value) : $value;

        if ($expire == 0)
        {
            return Redis::set($key, $value);
        }
        else
        {
            return Redis::setex($key, $expire, $value);
        }
    }


    /**
     * 刪除緩存
     * @param $key
     * @return mixed
     */
    public static function del($key)
    {
        return Redis::del($key);
    }

    /**
     * 获取加密过key
     * @param $key
     * @return string
     */
    public static function get_key($key)
    {
        $key = md5(self::$df_prefix.'_'.$key);
        return $key;
    }

    /**
     * 检查当前redis是否存在且能连接上
     * @return bool
     */
    public static function ping()
    {
        return Redis::ping() == '+PONG';
    }

    /**
     * 检查值的類型 字符串=string,列表=list,集合=set/zset,hash=hash
     * @param $key
     * @return mixed
     */
    public static function type($key)
    {
        return Redis::type($key);
    }

    /**
     * 数组转json
     * @param $value
     * @return string
     */
    public static function encode($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    /**
     * json转数组
     * @param $value
     * @return mixed
     */
    public static function decode($value)
    {
        return json_decode($value, true);
    }

    /**
     * 上锁，redis分布式锁，同时只能有一个人可以操作某个行为
     * @param $token
     * @param int $timeout 循环获取锁的等待超时时间，在此时间内会一直尝试获取锁直到超时，为0表示失败后直接返回不等待
     * @param int $expire 当前锁的最大生存时间(秒)，必须大于0，如果超过生存时间锁仍未被释放，则系统会自动强制释放
     * @param int $wait_interval_us 获取锁失败后挂起再试的时间间隔(微秒)
     * @return mixed
     */
    public static function redis_lock($token, $timeout = 0, $expire = 10, $wait_interval_us = 100000)
    {
        //取得当前时间
        $now = time();
        //获取锁失败时的等待超时时刻
        $timeout_at = $now + $timeout;

        while (true)
        {
            $result = Redis::set("my:lock", $token, "ex", $expire, "nx");//该锁有效时间默认10秒
            if ($result)
            {
                return true;
            }

            //循环请求锁，如果没设置锁失败的等待时间 或者 已超过最大等待时间了，那就退出
            if ($timeout <= 0 || $timeout_at < microtime(true))
            {
                break;
            }

            //隔 $wait_interval_us 后继续 请求
            usleep($wait_interval_us);
        }

        return false;
    }

    /**
     * 解锁
     * @param $value
     * @return mixed
     */
    public static function redis_unlock($token)
    {
        $script = "if redis.call('get', KEYS[1]) == ARGV[1]
        then return redis.call('del', KEYS[1])
        else return 0
        end";

        return Redis::eval($script,1,'my:lock',$token); //eval方法用lua解释器执行脚本
    }

    /**
     * 获取哈西表
     * @param $key
     * @param $hash 表名
     * @param null $serialize
     * @return mixed
     */
    public static function hget($hash, $key, $serialize = null)
    {
        if($serialize === null) $serialize = self::$serialize;

        return $serialize ? self::decode(Redis::hget($hash, $key)) :
            Redis::hget($hash, $key);
    }

    /**
     * 设置哈西表
     * @param $key
     * @param $hash 表名
     * @param $value
     * @param null $serialize
     * @return mixed
     */
    public static function hset($hash, $key, $value, $serialize = null)
    {
        if($serialize === null) $serialize = self::$serialize;

        $value = $serialize ? self::encode($value) : $value;
        return Redis::hset($hash, $key, $value);
    }

    /**
     * list列表插入数据在头部
     * @param $key 表名
     * @param $value
     * @param null $serialize
     * @return mixed
     */
    public static function lpush($key, $value, $serialize = null)
    {
        if($serialize === null) $serialize = self::$serialize;

        $value = $serialize ? self::encode($value) : $value;
        return Redis::lpush($key, $value);
    }

    /**
     * list列表插入数据在尾部
     * @param $key 表名
     * @param $value
     * @param null $serialize
     * @return mixed
     */
    public static function rpush($key, $value, $serialize = null)
    {
        if($serialize === null) $serialize = self::$serialize;

        $value = $serialize ? self::encode($value) : $value;
        return Redis::rpush($key, $value);
    }

    /**
     * list列表干掉头部1个数据
     * @param $key 表名
     * @return mixed
     */
    public static function rpop($key)
    {
        return Redis::rpop($key);
    }

    /**
     * list列表干掉尾部1个数据
     * @param $key 表名
     * @return mixed
     */
    public static function lpop($key)
    {
        return Redis::lpop($key);
    }

    /**
     * 返回指定顺序位置的list值
     * @param $key 表名
     * @param $index 第几个
     * @param null $serialize
     * @return mixed
     */
    public static function lindex($key, $index, $serialize = null)
    {
        if($serialize === null) $serialize = self::$serialize;

        return $serialize ? self::decode(Redis::lindex($key, $index)) : Redis::lindex($key, $index);
    }

    /**
     * 集合添加數據, 成功返回true， 重复返回false
     * @param $key
     * @param $value
     * @param null $serialize
     * @return mixed
     */
    public static function sadd($key, $value, $serialize = null)
    {
        if($serialize === null) $serialize = self::$serialize;

        return $serialize ? self::decode(Redis::sadd($key, $value)) :
            Redis::sadd($key, $value);
    }

    /**
     * 集合干掉數據
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function srem($key, $value)
    {
        return Redis::srem($key, $value);
    }

    /**
     * 集合干掉第一項數據
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function spop($key)
    {
        return Redis::spop($key);
    }

    /**
     * 获取集合个数
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function scard($key)
    {
        return Redis::scard($key);
    }

    /**
     * 是否属于当前集合
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function sismember($key, $value)
    {
        return Redis::sismember($key, $value);
    }

    /**
     * 获取集合全部元素
     * @param $key
     * @param $value
     * @return mixed 数组
     */
    public static function smembers($key)
    {
        return Redis::smembers($key);
    }

    /**
     * 获取集合随机元素
     * @param $key
     * @param $value
     * @return mixed 数组
     */
    public static function srandmember($key)
    {
        return Redis::srandmember($key);
    }

    /**
     * 有序集合添加數據, 成功返回true， 重复返回false
     * @param $key
     * @param $value
     * @param null $serialize
     * @return mixed
     */
    public static function zadd($key, $score, $value, $serialize = null)
    {
        if($serialize === null) $serialize = self::$serialize;

        return $serialize ? self::decode(Redis::zadd($key, $score, $value)) :
            Redis::zadd($key, $score, $value);
    }

    /**
     * 获取有序集合个数
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function zcard($key)
    {
        return Redis::zcard($key);
    }

    /**
     * 获取有序集合个数依分數區間
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function zcount($key, $min, $max)
    {
        return Redis::zcount($key, $min, $max);
    }

    /**
     * 有序集合干掉數據
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function zrem($key, $value)
    {
        return Redis::zrem($key, $value);
    }

    /**
     * 返回有序集合成員分數
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function zscore($key, $value)
    {
        return Redis::zscore($key, $value);
    }

    /**
     * 返回有序集合區間成員
     * @param $key
     * @param $start
     * @param $stop
     * @return mixed 數組
     */
    public static function zrange($key, $start, $stop)
    {
        return Redis::zrange($key, $start, $stop);
    }
}
