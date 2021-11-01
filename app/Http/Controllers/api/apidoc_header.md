[TOC]

## 域名

线上测试|线上正式
--------|--------|
http://testapi.example.com/|http://api.example.com/

## http头部参数(仅限 app)

参数名|参数類型|参数说明
--------|--------|--------|
Authorization|String|认证token
os|String|客户端系统信息，ios 12/android 7.0/h5
timezone|String|客户端时区，格式 GMT-8/UTC-8
language|String|language 客户端语言，格式 zh-cn/en/km
version|String|客户端当前版本号，格式 x.x.x
device|String|客户端设备信息，如：mei=设备IMEI值\|pixel=12*32（设备分辨率）

## h5客户端请求接口说明

#### 注意事项
1. 所有请求使用 `POST` 提交
2. 由于h5客户端有些接口不需要登录就能访问，为了增加安全性不需要登录的接口需要进行参数签名，签名字段：sign
3. sign 参数不参与签名

#### 签名密钥

-|线上测试|线上正式
--------|--------|--------|--------|
app_key|-|-

#### 签名算法
```
1、参数正排序
2、使用&连接参数生成签名字符串
3、签名字符串后面加上密钥参数&key=[app_key]
4、把签名字符串md5加密再转大写生成签名
```

## api版本更新说明

### 1.0.0版本
#### H5接口
- 增加 `/index` 获取首页数据
- 增加 `/ip` 获取客户端ip地址
- 增加 `/news` 获取新闻列表
- 增加 `/news/detail` 获取新闻详情
- 增加 `/news_cats` 获取新闻分类
- 增加 `/faq` 获取常见问题列表
- 增加 `/faq_cats` 获取常见问题分类
- 增加 `/link` 获取友情链接列表
- 增加 `/feedback` 提交联络表单

