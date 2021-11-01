<?php

use App\models\mod_common;
use Illuminate\Support\Facades\Auth;

/**
 * 全局函数
 */
if (!function_exists('pr')) {
    /**
     * 打印
     * @param array $data
     */
    function pr($data = [])
    {
        echo '<pre>';
        print_r($data);
        exit;
    }
}

if (!function_exists('request_filter')) {
    /**
     * 获取过滤后请求参数
     * @param $keys
     * @return array
     */
    function request_filter($fields)
    {
        if(empty($fields)) return [];
        $ret = array_filter(request()->only(is_array($fields) ? $fields : func_get_args()));
        return $ret;
    }
}

if (!function_exists("user_can"))
{
    /**
     * 检测用户是否有权限
     * @param $permission 权限
     * @param null $guard 守卫
     * @return mixed
     */
    function user_can($permission, $guard = null)
    {
        return auth($guard)->user()->can($permission);
    }
}

if (!function_exists("user_has_role"))
{
    /**
     * 检测用户是否有该角色
     * @param $role 角色 格式 [1,2,3]
     * @param null $guard 守卫
     * @return mixed
     */
    function user_has_role(array $role, $guard = null)
    {
        //必须为int不然会报错
        $role = array_map('intval', $role);
        return auth($guard)->user()->hasAnyRole($role);
    }
}

if (!function_exists('make_options')) {
    /**生成选择框选项
     * @param array $data
     * @param null $default
     * @return string
     */
    function make_options(array $data, $default = '')
    {
        $options = '';
        if (empty($data)) return $options;
        $default = is_numeric($default) ? (int) $default : $default;

        foreach($data as $k => $v)
        {
            $selected = ($k === $default) ? ' selected' : '';
            $options .= sprintf('<option value="%s"%s>%s</option>', $k, $selected,  $v);
        }
        return $options;
    }
}

if (!function_exists("make_tree")) {
    /**
     * 生成树
     * @param array $data 数据
     * @param int $pid 上级id
     * @return array
     */
    function make_tree(array $data, $field_id = 'id', $field_pid = 'pid', $pid = 0)
    {
        $tree = [];
        if (empty($data)) return $tree;

        $field_id = empty($field_id) ? 'id' : $field_id;
        $field_pid = empty($field_pid) ? 'pid' : $field_pid;

        $rows = [];
        foreach ($data as $k => $v) {
            $rows[$v[$field_id]] = $v; //获取字段id当键名
        }

        foreach ($data as $item)
        {
            if ($pid == $item[$field_pid])
            {
                $tree[] = &$rows[$item[$field_id]];
            }
            elseif (isset($rows[$item[$field_pid]]))
            {
                $rows[$item[$field_pid]]['children'][] = &$rows[$item[$field_id]];
            }
        }
        return $tree;
    }
}

if (!function_exists('get_default_guard')) {
    /**
     * 获取当前守卫
     * @return mixed
     */
    function get_default_guard()
    {
        return Auth::getDefaultDriver();
    }
}

if (!function_exists('get_purviews')) {
    /**
     * 获取已认证用户权限
     * 用户权限 = 用户权限 + 组权限
     * @param array $data
     * @return array
     */
    function get_purviews(array $data)
    {
        //参数过滤
        $data_filter = mod_common::data_filter([
            'guard'        => '',
            'field'        => '',
        ], $data);

        $default_guard = get_default_guard();//默认守卫
        $guard = empty($data_filter['guard']) ? $default_guard : $data_filter['guard'];
        $field = empty($data_filter['field']) ? 'id' : $data_filter['field']; //默认返回id字段

        //获取该用户全部权限
        $purviews = auth($guard)->user()->getAllPermissions()
            ->pluck($field)->toArray(); //一律返回id,不要用name路由名称
        //检测是否有超级管理员权限
        if ( auth($guard)->user()->hasRole(config('global.super_role_id')) ) //1=超级管理员
        {
            $purviews = ['*'];
        }

        return $purviews;
    }
}

if(!function_exists('msgbox'))
{
    /**
     * 显示一个简单的对话框
     * @param array $data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function msgbox(array $data)
    {
        //参数过滤
        $data_filter = mod_common::data_filter([
            'icon'      => '',  //6=SUCCESS 5=ERROR
            'title'     => '',
            'msg'       => '',
            'gourl'     => '',
            'limit_sec' => '', //3000=3秒
        ], $data);

        $icon       = empty($data_filter['icon']) ? 6 : $data_filter['icon'];
        $title      = $data_filter['title'] ?? '系统提示信息';
        $msg        = $data_filter['msg'] ?? '';
        $gourl      = $data_filter['gourl'] ?? '';
        $gourl      = ($gourl=='javascript:;') ? '' : $gourl;
        $limit_sec  = $data_filter['limit_sec'] ?? 3000; //N秒后调用函数
        $jump_msg   = '';
        $js_tmp     = '';

        if( $gourl == -1 ) //返回上一页
        {
            $gourl = "javascript:history.go(-1);";
        }

        if( $gourl == -2 ) //重新登录
        {
            $jump_msg = "<a href='/logout'>重新登录</a>";
        }
        elseif( $gourl != '' )
        {
            $jump_msg = "<a href='{$gourl}'>如果你的浏览器没有自动跳转，请点击这里...</a>";
            $js_tmp = "setTimeout(\"JumpUrl('{$gourl}')\", {$limit_sec});";
        }

        print view('system.msgbox', [
            'icon'      => $icon,
            'title'     => $title,
            'msg'       => $msg,
            'jump_msg'  => $jump_msg,
            'js_tmp'    => $js_tmp,
        ]);
        exit();
    }
}

if(!function_exists('page_error'))
{
    /**
     * 显示自定义错误页面
     * @param array $data
     * @return \Illuminate\Http\Response
     */
    function page_error(array $data)
    {
        //参数过滤
        $data_filter = mod_common::data_filter([
            'code'        => 'required',
        ], $data);

        $code = empty($data_filter) ? '404' : $data_filter['code']; //http_code

        return response()->view("system.{$code}", [], $code);
    }
}

if ( ! function_exists('config_path'))
{
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}
