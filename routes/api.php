<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

//$router->get('/', function () use ($router) {
//    return $router->app->version();
//});

$router->addRoute(['POST'], 'login', 'ctl_login@login');
$router->addRoute(['POST'], 'refresh_token', 'ctl_login@refresh_token');
$router->addRoute(['POST'], 'register', 'ctl_member@register');
$router->addRoute(['GET'], 'index', 'ctl_index@index');
$router->addRoute(['GET'], 'news', 'ctl_news@index');
$router->addRoute(['GET'], 'news/detail', 'ctl_news@detail');
$router->addRoute(['GET'], 'news_cats', 'ctl_news@cats');
$router->addRoute(['GET'], 'faq', 'ctl_faq@index');
$router->addRoute(['GET'], 'faq_cats', 'ctl_faq@cats');
$router->addRoute(['GET'], 'link', 'ctl_link@index');
$router->addRoute(['POST'], 'feedback', 'ctl_contact@feedback');
$router->addRoute(['GET'], 'ip', 'ctl_common@ip');
$router->addRoute(['GET'], 'ping', 'ctl_common@ping');

//目前改用jwt.auth,通过auth中间件并指定api守卫
$router->group(['middleware'=>'jwt.auth'],function () use ($router){
    $router->addRoute(['POST'], 'logout', 'ctl_login@logout'); //退出
    $router->addRoute(['POST'], 'get_userinfo', 'ctl_member@detail'); //用户信息
    $router->addRoute(['POST'], 'change_pwd', 'ctl_member@edit'); //用户信息

    $router->group(['middleware'=>'role:2'],function () use ($router){
    });
});
