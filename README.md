## About
使用 Lumen 微框架搭建的鑫盈能源api接口，应用于前后站分离

## Feature
* 接口支持会员功能、新闻、QA、友情链接、联络表单
* 公共接口支持
* 接口支持jwt与参数签名，强化安全性
* 一款高性能轻量级 laravel 框架，应用场景为微服务或api接口

## Requires
PHP 7.1.3 or Higher  
OpenSSL PHP Extension  
PDO PHP Extension  
Mbstring PHP Extension

## Install
```
composer install
cp .env.example .env
php artisan app:install
```

## Usage
[鑫盈能源api接口文檔](https://joanbabyfet.github.io/sienergy_api/api/)

## Change Log
v1.0.0 - 2021-11-02
* 增加 /register 会员注册
* 增加 /login 会员登录
* 增加 /logout 会员登出
* 增加 /change_pwd 修改密碼
* 增加 /get_userinfo 获取会员信息
* 增加 /refresh_token 刷新认证token
* 增加 /index 获取首页数据
* 增加 /ip 获取客户端ip地址
* 增加 /news 获取新闻列表
* 增加 /news/detail 获取新闻详情
* 增加 /news_cats 获取新闻分类
* 增加 /faq 获取常见问题列表
* 增加 /faq_cats 获取常见问题分类
* 增加 /link 获取友情链接列表
* 增加 /feedback 提交联络表单

## Maintainers
Alan

## LICENSE
[MIT License](https://github.com/joanbabyfet/sienergy_api/blob/master/LICENSE)
