<?php
/**
 * 全局变量
 */

return [
    'is_maintenance'    => env('IS_MAINTENANCE', ''), //系统维护中
    'super_role_id'     => 1, //后台超级管理员
    'gen_mem_role_id'   => 2, //普通会员
    //守卫類型
    'guard_names' => [
        'admin' => '后台守卫',
        'web'   => '前台守卫',
    ],
    //h5官网设置
    'web' => [
        'app_title' => '鑫盈能源',
        'app_name'  => 'web',
        'domain'    => env('WEB_DOMAIN', ''),
        'guard'     => env('WEB_GUARD', ''),
    ],
    //api接口设置
    'api' => [
        'app_title' => '',
        'app_name'  => 'api',
        'domain'    => env('API_DOMAIN', ''),
        'guard'     => env('API_GUARD', ''),
    ],
    'to_timezone' => 'ETC/GMT-7', //默认需要转化的时区，东七区是柬埔寨时间
    //h5地址
    'h5_url' => 'http://api.example.local/h5?id=%s',
    //socket配置
    'socket' => [
        'hosts' => [
            'ws_client' => [
                'name'       => 'ClientWsSocketGateway',
                'listen'     => 'websocket://0.0.0.0:2346',
                'start_port' => 2300
            ],
            'admin' => [
                'name'       => 'AdminSocketGateway',
                'listen'     => 'websocket://0.0.0.0:2347',
                'start_port' => 2400
            ],
        ],
        'process_count'           => 4,
        'lan_ip'                  => '127.0.0.1',
        'ping_interval'           => 0,
        'ping_not_response_limit' => 3,
        'ping_data'               => '',
        'register_address'        => '127.0.0.1:1236'
    ],
    //google地图
    'map' => [
        'web_key' => '', //网页key
        'key'     => '', //验证地址是否正确
        'app_key' => '', //app key
    ],
    //翻译 key
    'translate_key' => '',
];
