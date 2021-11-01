<?php
/**
 * 配置启用文件
 */
require_once __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades(); //默认注释, 注册使用 Facades
$app->withEloquent(); //默认注释, 注册使用 Eloquent

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

//session 單例模式, 綁定一個類別或介面至容器中, 之後調用都會從容器中返回相同的實例化對象
$app->singleton(Illuminate\Session\SessionManager::class, function () use ($app) {
    return $app->loadComponent('session', Illuminate\Session\SessionServiceProvider::class, 'session');
});
$app->singleton('session.store', function () use ($app) {
    return $app->loadComponent('session', Illuminate\Session\SessionServiceProvider::class, 'session.store');
});
//cookie 單例模式, 綁定一個類別或介面至容器中, 之後調用都會從容器中返回相同的實例化對象
$app->singleton('cookie', function () use ($app) {
    return $app->loadComponent('session', Illuminate\Cookie\CookieServiceProvider::class, 'cookie');
});
$app->bind('Illuminate\Contracts\Cookie\QueueingFactory', 'cookie');

/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
|
| Now we will register the "app" configuration file. If the file exists in
| your configuration directory it will be loaded; otherwise, we'll load
| the default version. You may register other files below as needed.
|
*/

//注册配置文件
$app->configure('app');
$app->configure('global');
$app->configure('auth');
$app->configure('jwt');
$app->configure('permission');
$app->configure('session');

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([ //全局中间件,每次请求,每个中间件都会执行
    //App\Http\Middleware\ExampleMiddleware::class
    //Illuminate\Session\Middleware\StartSession::class,
]);

 $app->routeMiddleware([ //路由中间件，定义路由时引用
     'auth' => App\Http\Middleware\Authenticate::class,
     'jwt.auth' => Tymon\JWTAuth\Http\Middleware\Authenticate::class, //api认证中间件,
     'permission' => App\Http\Middleware\mw_permission::class,
     'role' => App\Http\Middleware\mw_role::class,
 ]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\AppServiceProvider::class); //默认注释
$app->register(App\Providers\AuthServiceProvider::class); //默认注释
$app->register(App\Providers\EventServiceProvider::class); //默认注释
//$app->register(Intervention\Image\ImageServiceProvider::class);
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);
$app->alias('cache', Illuminate\Cache\CacheManager::class); //记得加,不然会报错
$app->register(Spatie\Permission\PermissionServiceProvider::class);
//$app->register(Mews\Captcha\CaptchaServiceProvider::class);
/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace'     => 'App\Http\Controllers\api',
    'domain'        => config('global.api.domain'),
], function ($router) {
    require __DIR__.'/../routes/api.php';
});

//$app->router->group([
//    'namespace'     => 'App\Http\Controllers\web',
//    'domain'        => config('global.web.domain'),
//], function ($router) {
//    require __DIR__.'/../routes/web.php';
//});

return $app;
