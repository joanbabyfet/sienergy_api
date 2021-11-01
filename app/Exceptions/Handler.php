<?php

namespace App\Exceptions;

use App\models\mod_common;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($this->isHttpException($exception))  //自定义404与500页面
        {
            //api接口异常处理
            if ($exception instanceof TokenInvalidException)
            {
                return mod_common::error('获取token失败', -4001); //token不合法
            }
            else if ($exception instanceof TokenExpiredException)
            {
                return mod_common::error('会话已过期, 请尝试重新登录', -4002);
            }
            else if ($exception instanceof UnauthorizedHttpException || $exception instanceof TokenBlacklistedException)
            {
                return mod_common::error('未登录或登录超时', -4003);
            }
            //无权限异常处理
            else if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) //权限不足，无法访问
            {
                if (! $request->expectsJson()){
                    return msgbox([
                        'icon' => 5,
                        'msg' => '权限不足, 对不起，你没权限执行本操作！',
                        'gourl' => '',
                    ]);
                }
                else{
                    return mod_common::error('无权限', -1);
                }
            }
            //web异常处理
            else if ($exception->getStatusCode() == 404) //您访问的页面不存在
            {
                return page_error(['code' => 404]);
            }
            else if ($exception->getStatusCode() == 500) //网站有一个异常，请稍候再试
            {
                return page_error(['code' => 500]);
            }
        }
        return parent::render($request, $exception);
    }
}
