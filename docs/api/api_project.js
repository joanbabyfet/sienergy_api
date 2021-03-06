define({
  "name": "鑫盈能源api接口文檔",
  "version": "1.0.0",
  "description": "鑫盈能源api接口文檔",
  "title": "鑫盈能源api接口文檔",
  "url": "/",
  "sampleUrl": null,
  "header": {
    "title": "接口公共说明",
    "content": "<p>[TOC]</p>\n<h2>域名</h2>\n<table>\n<thead>\n<tr>\n<th>线上测试</th>\n<th>线上正式</th>\n</tr>\n</thead>\n<tbody>\n<tr>\n<td>http://testapi.example.com/</td>\n<td>http://api.example.com/</td>\n</tr>\n</tbody>\n</table>\n<h2>http头部参数(仅限 app)</h2>\n<table>\n<thead>\n<tr>\n<th>参数名</th>\n<th>参数類型</th>\n<th>参数说明</th>\n</tr>\n</thead>\n<tbody>\n<tr>\n<td>Authorization</td>\n<td>String</td>\n<td>认证token</td>\n</tr>\n<tr>\n<td>os</td>\n<td>String</td>\n<td>客户端系统信息，ios 12/android 7.0/h5</td>\n</tr>\n<tr>\n<td>timezone</td>\n<td>String</td>\n<td>客户端时区，格式 GMT-8/UTC-8</td>\n</tr>\n<tr>\n<td>language</td>\n<td>String</td>\n<td>language 客户端语言，格式 zh-cn/en/km</td>\n</tr>\n<tr>\n<td>version</td>\n<td>String</td>\n<td>客户端当前版本号，格式 x.x.x</td>\n</tr>\n<tr>\n<td>device</td>\n<td>String</td>\n<td>客户端设备信息，如：mei=设备IMEI值|pixel=12*32（设备分辨率）</td>\n</tr>\n</tbody>\n</table>\n<h2>h5客户端请求接口说明</h2>\n<h4>注意事项</h4>\n<ol>\n<li>所有请求使用 <code>POST</code> 提交</li>\n<li>由于h5客户端有些接口不需要登录就能访问，为了增加安全性不需要登录的接口需要进行参数签名，签名字段：sign</li>\n<li>sign 参数不参与签名</li>\n</ol>\n<h4>签名密钥</h4>\n<table>\n<thead>\n<tr>\n<th>-</th>\n<th>线上测试</th>\n<th>线上正式</th>\n</tr>\n</thead>\n<tbody>\n<tr>\n<td>app_key</td>\n<td>-</td>\n<td>-</td>\n</tr>\n</tbody>\n</table>\n<h4>签名算法</h4>\n<pre class=\"prettyprint\">1、参数正排序\n2、使用&连接参数生成签名字符串\n3、签名字符串后面加上密钥参数&key=[app_key]\n4、把签名字符串md5加密再转大写生成签名\n</code></pre>\n<h2>api版本更新说明</h2>\n<h3>1.0.0版本</h3>\n<h4>H5接口</h4>\n<ul>\n<li>增加 <code>/register</code> 会员注册</li>\n<li>增加 <code>/login</code> 会员登录</li>\n<li>增加 <code>/logout</code> 会员登出</li>\n<li>增加 <code>/get_userinfo</code> 获取会员信息</li>\n<li>增加 <code>/refresh_token</code> 刷新认证token</li>\n<li>增加 <code>/index</code> 获取首页数据</li>\n<li>增加 <code>/ip</code> 获取客户端ip地址</li>\n<li>增加 <code>/news</code> 获取新闻列表</li>\n<li>增加 <code>/news/detail</code> 获取新闻详情</li>\n<li>增加 <code>/news_cats</code> 获取新闻分类</li>\n<li>增加 <code>/faq</code> 获取常见问题列表</li>\n<li>增加 <code>/faq_cats</code> 获取常见问题分类</li>\n<li>增加 <code>/link</code> 获取友情链接列表</li>\n<li>增加 <code>/feedback</code> 提交联络表单</li>\n</ul>\n"
  },
  "template": {
    "withCompare": true,
    "withGenerator": true,
    "aloneDisplay": false
  },
  "order": [],
  "defaultVersion": "0.0.0",
  "apidoc": "0.3.0",
  "generator": {
    "name": "apidoc",
    "time": "2021-11-01T23:17:53.724Z",
    "url": "https://apidocjs.com",
    "version": "0.29.0"
  }
});
