<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class mod_req extends Model
{
    /**
     * 获取token认证令牌
     * 优先级：请求头部,客户端token
     * @return array|string|null
     */
    public static function get_token()
    {
        $token = request()->server('_token', '');
        $token = empty($token) ? request()->header('HTTP_AUTHORIZATION', '') : $token;

        return $token;
    }

    /**
 * 获取客户端语言
 * 优先级：请求头部,客户端浏览器语言,配置文件zh-tw
 * @return array|string|null
 */
    public static function get_language()
    {
        $lang = request()->header('language', '');
        $lang = empty($lang) ? request()->server('HTTP_LANGUAGE', '') : $lang;
        $lang = empty($lang) ? config('app.locale') : strtolower($lang);

        return $lang;
    }

    /**
     * 获取当前版本号
     * 优先级：客户端版本号,请求头部
     * @return array|string|null
     */
    public static function get_version()
    {
        $version = request()->server('HTTP_VERSION', '');
        $version = empty($version) ? request()->header('version', '') : $version;
        $version = empty($version) ? '' : $version;

        return $version;
    }

    /**
     * 获取客户端系统信息
     * 优先级：客户端系统信息,请求头部
     * @return array|string|null
     */
    public static function get_os_info()
    {
        $os = request()->server('HTTP_OS', '');
        $os = empty($os) ? request()->header('os', '') : $os;
        $os = empty($os) ? '' : $os;

        return $os;
    }

    /**
     * 获取客户端时区
     * 优先级：客户端系统信息,请求头部,配置文件
     * @return array|\Illuminate\Config\Repository|mixed|string|null
     */
    public static function get_timezone()
    {
        $timezone = request()->server('HTTP_TIMEZONE', '');
        $timezone = empty($timezone) ? request()->header('timezone', '') : $timezone;
        $timezone = empty($timezone) ? config('app.timezone') : $timezone;

        return $timezone;
    }

    /**
     * 获取后台时区
     * @return \Illuminate\Config\Repository|mixed
     */
    public static function get_admin_timezone()
    {
        return config('global.to_timezone');
    }

    /**
     * 获取系统類型
     * 优先级：客户端系统類型,请求头部
     * @return array|string|null
     */
    public static function get_os_type()
    {
        $os = request()->server('HTTP_OS', '');
        $os = empty($os) ? request()->header('os', '') : $os;

        if (strpos(strtolower($os), 'android') !== false)
        {
            $os = 'android';
        }
        elseif (strpos(strtolower($os), 'ios') !== false)
        {
            $os = 'ios';
        }
        elseif ($os === 'web') //例 h5官网
        {
            $os = 'web';
        }

        return $os;
    }
}
