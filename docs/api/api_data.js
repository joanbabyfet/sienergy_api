define({ "api": [
  {
    "type": "get",
    "url": "index",
    "title": "获取首页数据",
    "group": "common",
    "name": "index",
    "version": "1.0.0",
    "description": "<p>获取首页数据</p>",
    "success": {
      "examples": [
        {
          "title": "返回示例:",
          "content": "{\n    \"code\": 0,\n    \"msg\": \"success\",\n    \"timestamp\": 1635743526,\n    \"data\": {\n        \"news\": [   //前3条新闻\n            {\n            \"id\": \"88e8150eac9198f1072657068a53358c\",\n            \"cat_id\": 1,\n            \"title\": \"【能源局新聞】經濟部101年度第6期太陽光電競標開標　共92件得標，總得標容量為23,630.849瓩\",\n            \"content\": \"<h2>【能源局新聞】經濟部101年度第6期太陽光電競標開標　共92件得標，總得標容量為23,630.849瓩</h2>\\n<p>發布日期：2012-08-09 下午 07:00</p>\\n<p>經濟部101年第6期太陽光電發電設備競標作業，於8月8日進行開標，計有屋頂型89件得標，地面型3件得標，總計容量為23,630.849瓩。</p>\\n<p> 經濟部能源局表示，<span style=\\\"color:#FF0000;\\\">本期太陽光電競標經審查符合競標資格者計162件，其中屋頂型計159件，合計容量為36,233.814瓩；地面型計3件，容量為525.12瓩，總容量共36,758.934瓩，超過基本容量10,000瓩部分為26,758.934瓩。依101年太陽光電競標作業要點規定，得標容量上限為基本容量加計超過部分容量50％，爰本期得標容量上限為23,379.467瓩。</span>\\n                            </p>\\n<p>\\n                                開標作業開放參加競標者親臨現場觀看，決標方式按折扣率由高至低順序排列依次選取，加計最末件得標者容量後倘超過競標容量上限，仍得將其超過容量計入，但屋頂型以1,000瓩為限，地面型以500瓩為限。按本次最末件得標者可再計入容量251.382瓩，<span style=\\\"color:#FF0000;\\\">總計得標容量為23,630.849瓩，平均折扣率為4.37％</span>。未來得標業者適用之太陽光電躉購費率按其完工時公告費率扣除其折扣額度計之，即公告費率X(1-業者投標之折扣率)。\\n                            </p>\\n<p> 另外，<span style=\\\"color:#FF0000;\\\">經濟部101年8月1日公告修正「經濟部101年太陽光電發電設備競標作業要點」，101年度競標容量上限由70,000瓩提高為83,000瓩，累計第1期至第6期得標容量，及考量得標未簽約及撤案等加計容量後，第7期僅剩容量766.927瓩，因此101年9月將為本年度最後1期競標。</span>\\n                            </p>\\n<p>\\n                                經濟部能源局進一步說明及提醒，101年第7期太陽光電競標作業收件截止日為8月20日，開標日為9月12日，前6期未得標、未補正或欲參與第7期競標作業者，請於8月20日下午5時30分前，將應備文件與第7期標單寄達或送達經濟部能源局，並請留意標單內容期別應填寫為第7期，且標單封套應予彌封。</p>\\n<p>能源局發言人：王副局長運銘 <br>\\n                                電話：02-2773-4729 ；行動電話：0910-216-359<br>\\n                                電子郵件：<a href=\\\"mialto:ymwang@moeaboe.gov.tw\\\">ymwang@moeaboe.gov.tw</a><br>\\n                                技術諮詢聯絡人：藍科長文宗<br>\\n                                電話：02-2775-7641；行動電話：0988-396-386<br>\\n                                電子郵件：<a href=\\\"mailto:wtlan@moeaboe.gov.tw\\\">wtlan@moeaboe.gov.tw </a></p>\\n<p></p>\",\n            \"is_hot\": 0,\n            \"status\": 1,\n            \"sort\": 0,\n            \"create_user\": \"1\",\n            \"create_time\": 1635731650,\n            \"status_dis\": \"啟用\",\n            \"create_time_dis\": \"2021/11/01 01:54\",\n            \"create_user_dis\": \"1\",\n            \"img_dis\": [],\n            \"img_url_dis\": [],\n            \"file_dis\": [],\n            \"file_url_dis\": []\n            }\n        ]\n    }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/api/ctl_index.php",
    "groupTitle": "common"
  },
  {
    "type": "get",
    "url": "ip",
    "title": "获取客户端ip地址",
    "group": "common",
    "name": "ip",
    "version": "1.0.0",
    "description": "<p>获取客户端ip地址</p>",
    "success": {
      "examples": [
        {
          "title": "返回示例:",
          "content": "{\n\"code\": 0,\n\"msg\": \"success\",\n\"timestamp\": 1635743698,\n\"data\": {\n\"ip\": \"127.0.0.1\"\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/api/ctl_common.php",
    "groupTitle": "common"
  },
  {
    "type": "post",
    "url": "feedback",
    "title": "提交联络表单",
    "group": "contact",
    "name": "feedback",
    "version": "1.0.0",
    "description": "<p>提交联络表单</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": "<p>姓名，必填</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "company_name",
            "description": "<p>公司名稱</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "sex",
            "description": "<p>性別 0=女 1=男，必填</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": "<p>電子郵件，必填</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "phone",
            "description": "<p>聯絡電話</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "content",
            "description": "<p>您的意見，必填</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "captcha",
            "description": "<p>驗證碼，必填</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "返回示例:",
          "content": "{\n\"code\": 0,\n\"msg\": \"提交成功\",\n\"timestamp\": 1635746875,\n\"data\": []\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/api/ctl_contact.php",
    "groupTitle": "contact"
  },
  {
    "type": "get",
    "url": "faq",
    "title": "获取常见问题列表",
    "group": "faq",
    "name": "faq",
    "version": "1.0.0",
    "description": "<p>获取常见问题列表</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "page_size",
            "description": "<p>每页显示几条</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "page_no",
            "description": "<p>第几页</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "cat_id",
            "description": "<p>分類id</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "返回示例:",
          "content": "{\n\"code\": 0,\n\"msg\": \"success\",\n\"timestamp\": 1635728725,\n\"data\": {\n\"data\": [\n{\n\"id\": \"c3a5a3b5cef355132fddf8096b2d45cd\",\n\"cat_id\": 2,\n\"question\": \"在屋頂架設太陽光電系統，是否會對人體健康造成影響？\",\n\"answer\": \"目前所知對人體健康有疑慮的是交流電所產生之電磁波，而太陽光電產生的是直流電，完全不會對人體健康有任何危害性。\",\n\"status\": 1,\n\"sort\": 0,\n\"create_user\": \"1\",\n\"create_time\": 1635725102,\n\"status_dis\": \"啟用\",\n\"create_time_dis\": \"2021/11/01 00:05\",\n\"create_user_dis\": \"1\"\n},\n{\n\"id\": \"9c5a5350f05d38fd25876784af5cb3bd\",\n\"cat_id\": 2,\n\"question\": \"申請架設太陽光電系統，所提供的服務範圍有哪些？\",\n\"answer\": \"本公司包辦所有行政作業，包括與台電簽約、能源局驗收，直到業主順利取得躉售台電之匯款。\",\n\"status\": 1,\n\"sort\": 0,\n\"create_user\": \"1\",\n\"create_time\": 1635725088,\n\"status_dis\": \"啟用\",\n\"create_time_dis\": \"2021/11/01 00:04\",\n\"create_user_dis\": \"1\"\n}\n],\n\"total_page\": 1,\n\"total\": 10\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/api/ctl_faq.php",
    "groupTitle": "faq"
  },
  {
    "type": "get",
    "url": "faq_cats",
    "title": "获取常见问题分类",
    "group": "faq",
    "name": "faq_cats",
    "version": "1.0.0",
    "description": "<p>获取常见问题分类</p>",
    "success": {
      "examples": [
        {
          "title": "返回示例:",
          "content": "{\n\"code\": 0,\n\"msg\": \"success\",\n\"timestamp\": 1635730830,\n\"data\": [\n{\n\"id\": 2,\n\"name\": \"常見問題\",\n\"desc\": null,\n\"sort\": 0,\n\"status\": 1,\n\"create_user\": \"1\",\n\"create_time\": 1635724813,\n\"status_dis\": \"啟用\",\n\"create_time_dis\": \"2021/11/01 00:00\",\n\"create_user_dis\": \"1\"\n}\n]\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/api/ctl_faq.php",
    "groupTitle": "faq"
  },
  {
    "type": "get",
    "url": "link",
    "title": "获取友情链接列表",
    "group": "link",
    "name": "link",
    "version": "1.0.0",
    "description": "<p>获取友情链接列表</p>",
    "success": {
      "examples": [
        {
          "title": "返回示例:",
          "content": "{\n\"code\": 0,\n\"msg\": \"success\",\n\"timestamp\": 1635734508,\n\"data\": {\n\"data\": [\n{\n\"id\": \"fe08836248e8589e5041c51c7ce30c5b\",\n\"name\": \"經濟部能源局\",\n\"name_en\": null,\n\"url\": \"http://www.moeaboe.gov.tw/\",\n\"img\": \"031/aef74f64b56376a05a2cb636e88fed8f.jpg\",\n\"status\": 1,\n\"create_time\": 1635733032,\n\"img_dis\": [\n\"031/aef74f64b56376a05a2cb636e88fed8f.jpg\"\n],\n\"img_url_dis\": [\n\"http://example.local/storage/image/031/aef74f64b56376a05a2cb636e88fed8f.jpg\"\n],\n\"status_dis\": \"啟用\",\n\"create_time_dis\": \"2021/11/01 02:17\"\n},\n{\n\"id\": \"094cc8d1080c6db1651ecfe43246d7d2\",\n\"name\": \"綠色能源產業資訊網\",\n\"name_en\": null,\n\"url\": \"http://www.taiwangreenenergy.org.tw/Domain/\",\n\"img\": \"008/89e061fbc2044625bac65fbc06506e1f.jpg\",\n\"status\": 1,\n\"create_time\": 1635733001,\n\"img_dis\": [\n\"008/89e061fbc2044625bac65fbc06506e1f.jpg\"\n],\n\"img_url_dis\": [\n\"http://example.local/storage/image/008/89e061fbc2044625bac65fbc06506e1f.jpg\"\n],\n\"status_dis\": \"啟用\",\n\"create_time_dis\": \"2021/11/01 02:16\"\n}\n],\n\"total_page\": 1,\n\"total\": 4\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/api/ctl_link.php",
    "groupTitle": "link"
  },
  {
    "type": "post",
    "url": "change_pwd",
    "title": "修改密碼",
    "group": "member",
    "name": "change_pwd",
    "version": "1.0.0",
    "description": "<p>修改密碼</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "old_password",
            "description": "<p>原密碼，必填</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>新密碼，必填</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "返回示例:",
          "content": "{\n\"code\": 0,\n\"msg\": \"更新成功\",\n\"timestamp\": 1635808511,\n\"data\": []\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/api/ctl_member.php",
    "groupTitle": "member"
  },
  {
    "type": "post",
    "url": "get_userinfo",
    "title": "获取会员信息",
    "group": "member",
    "name": "get_userinfo",
    "version": "1.0.0",
    "description": "<p>获取会员信息</p>",
    "success": {
      "examples": [
        {
          "title": "返回示例:",
          "content": "{\n\"code\": 0,\n\"msg\": \"success\",\n\"timestamp\": 1635780173,\n\"data\": {\n\"id\": \"a28b00b8772138bf9cb7a824bdcbbd9a\",\n\"origin\": 2,\n\"username\": \"sccot\",\n\"realname\": \"陳聰明\",\n\"email\": \"test@example.com\",\n\"phone_code\": \"86\",\n\"phone\": \"0912345678\",\n\"status\": 1,\n\"is_first_login\": 1,\n\"is_audit\": 0,\n\"session_expire\": 1440,\n\"session_id\": \"\",\n\"reg_ip\": \"127.0.0.1\",\n\"login_time\": 0,\n\"login_ip\": \"\",\n\"language\": \"zh-tw\",\n\"create_time\": 1635777232,\n\"create_user\": \"0\",\n\"update_time\": 0,\n\"update_user\": \"0\",\n\"delete_time\": 0,\n\"delete_user\": \"0\"\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/api/ctl_member.php",
    "groupTitle": "member"
  },
  {
    "type": "post",
    "url": "login",
    "title": "登录",
    "group": "member",
    "name": "login",
    "version": "1.0.0",
    "description": "<p>登录</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "username",
            "description": "<p>用戶名，必填</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>密碼，必填</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "captcha",
            "description": "<p>驗證碼，必填</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "返回示例:",
          "content": "{\n\"code\": 0,\n\"msg\": \"登入成功\",\n\"timestamp\": 1635779396,\n\"data\": {\n\"id\": \"a28b00b8772138bf9cb7a824bdcbbd9a\",\n\"origin\": 2,\n\"username\": \"sccot\",\n\"realname\": \"陳聰明\",\n\"email\": \"test@example.com\",\n\"phone_code\": \"86\",\n\"phone\": \"0912345678\",\n\"status\": 1,\n\"is_first_login\": 1,\n\"is_audit\": 0,\n\"session_expire\": 1440,\n\"session_id\": \"\",\n\"reg_ip\": \"127.0.0.1\",\n\"login_time\": 0,\n\"login_ip\": \"\",\n\"language\": \"zh-tw\",\n\"create_time\": 1635777232,\n\"create_user\": \"0\",\n\"update_time\": 0,\n\"update_user\": \"0\",\n\"delete_time\": 0,\n\"delete_user\": \"0\",\n\"api_token\": \"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9hcGkubG1fc2llbmVyZ3kubG9jYWxcL2xvZ2luIiwiaWF0IjoxNjM1Nzc5Mzk2LCJleHAiOjE2MzU3ODI5OTYsIm5iZiI6MTYzNTc3OTM5NiwianRpIjoiMlRsMkVIOHcwOEZFc3lQSSIsInN1YiI6ImEyOGIwMGI4NzcyMTM4YmY5Y2I3YTgyNGJkY2JiZDlhIiwicHJ2IjoiOTIyNDBmZmI4YTExMTRjODAzZWNiOTMyZmI3MjlhY2UwOGVkZmMzNSJ9.QRDTzVHlpKigT9mAKKVUh48xA5h6XvZ5uWnypfptxkQ\",\n\"api_token_expire\": 1635782996\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/api/ctl_login.php",
    "groupTitle": "member"
  },
  {
    "type": "post",
    "url": "logout",
    "title": "登出",
    "group": "member",
    "name": "logout",
    "version": "1.0.0",
    "description": "<p>登出</p>",
    "success": {
      "examples": [
        {
          "title": "返回示例:",
          "content": "{\n\"code\": 0,\n\"msg\": \"登出成功\",\n\"timestamp\": 1635779714,\n\"data\": []\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/api/ctl_login.php",
    "groupTitle": "member"
  },
  {
    "type": "post",
    "url": "refresh_token",
    "title": "刷新认证token",
    "group": "member",
    "name": "refresh_token",
    "version": "1.0.0",
    "description": "<p>刷新认证token</p>",
    "success": {
      "examples": [
        {
          "title": "返回示例:",
          "content": "{\n\"code\": 0,\n\"msg\": \"success\",\n\"timestamp\": 1635779990,\n\"data\": {\n\"uid\": \"a28b00b8772138bf9cb7a824bdcbbd9a\",\n\"api_token\": \"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9hcGkubG1fc2llbmVyZ3kubG9jYWxcL3JlZnJlc2hfdG9rZW4iLCJpYXQiOjE2MzU3Nzk5NzgsImV4cCI6MTYzNTc4MzU5MCwibmJmIjoxNjM1Nzc5OTkwLCJqdGkiOiJSajNxZjBPVnlnTm8yU1VJIiwic3ViIjoiYTI4YjAwYjg3NzIxMzhiZjljYjdhODI0YmRjYmJkOWEiLCJwcnYiOiI5MjI0MGZmYjhhMTExNGM4MDNlY2I5MzJmYjcyOWFjZTA4ZWRmYzM1In0.UZUToV2Ktu9YROclqr_6_VO4kQgEKg6JBAd0vbRWOKc\",\n\"api_token_expire\": 1635783590\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/api/ctl_login.php",
    "groupTitle": "member"
  },
  {
    "type": "post",
    "url": "register",
    "title": "注册",
    "group": "member",
    "name": "register",
    "version": "1.0.0",
    "description": "<p>注册</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "username",
            "description": "<p>用戶名，必填</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>用戶密碼，必填</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "realname",
            "description": "<p>真實姓名，必填</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": "<p>郵箱，必填</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "phone_code",
            "description": "<p>手機號國碼，必填</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "phone",
            "description": "<p>手機號，必填</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "返回示例:",
          "content": "{\n\"code\": 0,\n\"msg\": \"添加成功\",\n\"timestamp\": 1635777233,\n\"data\": []\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/api/ctl_member.php",
    "groupTitle": "member"
  },
  {
    "type": "get",
    "url": "news",
    "title": "获取新闻列表",
    "group": "news",
    "name": "news",
    "version": "1.0.0",
    "description": "<p>获取新闻列表</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "page_size",
            "description": "<p>每页显示几条</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "page_no",
            "description": "<p>第几页</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "cat_id",
            "description": "<p>分類id</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "返回示例:",
          "content": "{\n\"code\": 0,\n\"msg\": \"success\",\n\"timestamp\": 1635731688,\n\"data\": {\n\"data\": [\n{\n\"id\": \"88e8150eac9198f1072657068a53358c\",\n\"cat_id\": 1,\n\"title\": \"【能源局新聞】經濟部101年度第6期太陽光電競標開標　共92件得標，總得標容量為23,630.849瓩\",\n\"content\": \"<h2>【能源局新聞】經濟部101年度第6期太陽光電競標開標　共92件得標，總得標容量為23,630.849瓩</h2>\\n<p>發布日期：2012-08-09 下午 07:00</p>\\n<p>經濟部101年第6期太陽光電發電設備競標作業，於8月8日進行開標，計有屋頂型89件得標，地面型3件得標，總計容量為23,630.849瓩。</p>\\n<p> 經濟部能源局表示，<span style=\\\"color:#FF0000;\\\">本期太陽光電競標經審查符合競標資格者計162件，其中屋頂型計159件，合計容量為36,233.814瓩；地面型計3件，容量為525.12瓩，總容量共36,758.934瓩，超過基本容量10,000瓩部分為26,758.934瓩。依101年太陽光電競標作業要點規定，得標容量上限為基本容量加計超過部分容量50％，爰本期得標容量上限為23,379.467瓩。</span>\\n                            </p>\\n<p>\\n                                開標作業開放參加競標者親臨現場觀看，決標方式按折扣率由高至低順序排列依次選取，加計最末件得標者容量後倘超過競標容量上限，仍得將其超過容量計入，但屋頂型以1,000瓩為限，地面型以500瓩為限。按本次最末件得標者可再計入容量251.382瓩，<span style=\\\"color:#FF0000;\\\">總計得標容量為23,630.849瓩，平均折扣率為4.37％</span>。未來得標業者適用之太陽光電躉購費率按其完工時公告費率扣除其折扣額度計之，即公告費率X(1-業者投標之折扣率)。\\n                            </p>\\n<p> 另外，<span style=\\\"color:#FF0000;\\\">經濟部101年8月1日公告修正「經濟部101年太陽光電發電設備競標作業要點」，101年度競標容量上限由70,000瓩提高為83,000瓩，累計第1期至第6期得標容量，及考量得標未簽約及撤案等加計容量後，第7期僅剩容量766.927瓩，因此101年9月將為本年度最後1期競標。</span>\\n                            </p>\\n<p>\\n                                經濟部能源局進一步說明及提醒，101年第7期太陽光電競標作業收件截止日為8月20日，開標日為9月12日，前6期未得標、未補正或欲參與第7期競標作業者，請於8月20日下午5時30分前，將應備文件與第7期標單寄達或送達經濟部能源局，並請留意標單內容期別應填寫為第7期，且標單封套應予彌封。</p>\\n<p>能源局發言人：王副局長運銘 <br>\\n                                電話：02-2773-4729 ；行動電話：0910-216-359<br>\\n                                電子郵件：<a href=\\\"mialto:ymwang@moeaboe.gov.tw\\\">ymwang@moeaboe.gov.tw</a><br>\\n                                技術諮詢聯絡人：藍科長文宗<br>\\n                                電話：02-2775-7641；行動電話：0988-396-386<br>\\n                                電子郵件：<a href=\\\"mailto:wtlan@moeaboe.gov.tw\\\">wtlan@moeaboe.gov.tw </a></p>\\n<p></p>\",\n\"is_hot\": 0,\n\"status\": 1,\n\"sort\": 0,\n\"create_user\": \"1\",\n\"create_time\": 1635731650,\n\"status_dis\": \"啟用\",\n\"create_time_dis\": \"2021/11/01 01:54\",\n\"create_user_dis\": \"1\",\n\"img_dis\": [],\n\"img_url_dis\": [],\n\"file_dis\": [],\n\"file_url_dis\": []\n},\n{\n\"id\": \"3da194c267fa380af88b44510b1705e5\",\n\"cat_id\": 1,\n\"title\": \"市場利多帶動、設備換機潮浮現 10月PV Taiwan 強力徵展中 昱晶、新日光、友達等200家廠商參展 力邀30大國際買主來台採購\",\n\"content\": \"<h2>市場利多帶動、設備換機潮浮現 10月PV Taiwan 強力徵展中 昱晶、新日光、友達等200家廠商參展 力邀30大國際買主來台採購</h2>\\n<p>隨著德國6月底安裝潮，以及日本市場需求和美國雙反效應帶動下，近來台灣太陽能廠商利多消息不斷，相關製程設備換機需求也逐漸浮現。即將於今年10月3-5日舉行的台灣規模最大、也是唯一的國際級太陽光電專業展「PV Taiwan 2012 台灣國際太陽光電展覽會」目前已經匯集昱晶、新日光、友達、益通等近200家廠商參展，並持續強力徵展中。主辦單位外貿協會、SEMI和TPVIA目前更積極洽邀前30大國際買主於展期來台採購，預估將在10月帶動新一波太陽能的採購熱潮!<br>\\n              歐洲太陽能產業協會(EPIA)預估未來5年全球太陽能產能將有200~400%的成長幅度，全球太陽能安裝量至2016年時，可望達到207.9~342.8GWp，而亞洲及其它新興市場將奪走歐洲的主導權。 根據Solar Buzz 資料顯示，2011年全球的太陽能電池產量達到29.5GW，其中，單單台灣市場的太陽能電池產量就高達7~8GW，占全球總產量的24%以上。</p>\\n<p> 主辦單位外貿協會指出：「台灣的太陽能電池產品優質且價格合理，在市場和美國雙反效應的加持下，許多國際買主已經轉向台灣採購。SEMI與貿協目前已正積極洽邀全球前30大重量級買主於展期間來台採購，期望協助廠商締結商機。」</p>\\n<p> 另一方面，全球太陽能設備市場也從今年起開始進入換機潮，市場呈現V型反彈復甦。研究機構IMS指出，2012年的設備換機/升級需求約有2.5~4GW，預估2013和2014年的設備投資金額分別有20%的成長，2015年更可望大幅增加40%的設備投資。對設備業者來說，從現在到2016年約有250億美金(20GW)的市場需求。由於製造廠集中在亞洲，設備銷售市場也以亞洲為主。</p>\\n<p> 主辦單位SEMI表示：「根據SEMI最新一期的全球太陽光電製造設備的訂單出貨比(Book-to-Bill Ratio；B/B值)報告， 2011年亞洲的太陽光電相關設備銷售約佔全球總出貨量和訂單量的85%。對於設備和原材料供應商來說，今年下半年是進入市場的時機，而參展PV Taiwan則是提供廠商迅速提高品牌知名度，以及和太陽能製造商面對面洽談的最佳平台。」<br>\\n              台灣唯一的國際太陽光電展— PV Taiwan，是太陽能廠商展出優質的太陽光電產品、技術，以及先進製造設備與材料的最佳平台。目前已吸引近200家廠商參展，包括友達、昱晶、新日光、益通、科風、茂矽、富陽、杜邦、博可、均豪、瑞納科技、有成精密、禧通、永光化學、東京威力科創(TEL)、錸德(RITEK)、英穩達(ISEC)、BIG SUN、SIEMENS、UMICORE等指標性大廠都已參展，隨著市場回溫，預計將有更多參展商參與。</p>\\n<p> 今年PV Taiwan的主題展覽專區包括高聚光型太陽能(HCPV)、染料敏化太陽能(DSSC)、太陽光電發電系統(PV System)等專區。同期舉行的台灣最大「國際太陽光電產業論壇」。同時，為協助台灣太陽能廠商優化製程、開拓新商機，主辦單位目前正積極洽邀Soltech、Aleo Solar GmbH、Azimut、ecoSolargy、First Solar、GA-Solar、Gehrlicher Solar AG、Hanwha、Isofotón、Kyocera HIT、NextLight、Philadelphia Solar、Scatec、Schott Solar GmbH、Sharp、SILFAB、Solarworld AG、Solarnica、Solrwatt AG、Solon AG、SUNGRID、Suniva、SunPower、TERA等全球重量級買主於10月來台採購。</p>\\n<p> PV Taiwan 2012參展報名，請洽:<br>\\n              外貿協會 莊小姐 (TEL: 02.2725.5200分機2644)<br>\\n              SEMI 李小姐 (TEL: 03.560.1777 分機101)<br>\\n              最新展覽與論壇訊息請參考 <a href=\\\"http://www.pvtaiwan.com\\\" target=\\\"_blank\\\">www.pvtaiwan.com</a><br>\\n              參觀者線上報名預計7月開放，預先報名可抽大獎，請隨時鎖定 <a href=\\\"http://www.pvtaiwan.com\\\" target=\\\"_blank\\\">www.pvtaiwan.com</a><br>\\n            </p>\\n<h3>新聞聯絡人：</h3>\\n<p> 羅凱琳<br>\\n              SEMI半導體事業部及行銷部 協理<br>\\n              Email: klo@semi.org<br>\\n              TEL: 03.560.1777 ext.201</p>\\n<p> 張兆蓉<br>\\n              外貿協會 展覽業務處<br>\\n              Email: amychang@taitra.org.tw<br>\\n              TEL: 886.2.2725.5200 ext. 2693</p>\\n<p></p>\",\n\"is_hot\": 0,\n\"status\": 1,\n\"sort\": 0,\n\"create_user\": \"1\",\n\"create_time\": 1635731613,\n\"status_dis\": \"啟用\",\n\"create_time_dis\": \"2021/11/01 01:53\",\n\"create_user_dis\": \"1\",\n\"img_dis\": [],\n\"img_url_dis\": [],\n\"file_dis\": [],\n\"file_url_dis\": []\n}\n],\n\"total_page\": 1,\n\"total\": 3\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/api/ctl_news.php",
    "groupTitle": "news"
  },
  {
    "type": "get",
    "url": "news/detail",
    "title": "获取新闻详情",
    "group": "news",
    "name": "news/detail",
    "version": "1.0.0",
    "description": "<p>获取新闻详情</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "id",
            "description": "<p>新闻id</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "返回示例:",
          "content": "{\n\"code\": 0,\n\"msg\": \"success\",\n\"timestamp\": 1635731969,\n\"data\": {\n\"id\": \"88e8150eac9198f1072657068a53358c\",\n\"cat_id\": 1,\n\"title\": \"【能源局新聞】經濟部101年度第6期太陽光電競標開標　共92件得標，總得標容量為23,630.849瓩\",\n\"content\": \"<h2>【能源局新聞】經濟部101年度第6期太陽光電競標開標　共92件得標，總得標容量為23,630.849瓩</h2>\\n<p>發布日期：2012-08-09 下午 07:00</p>\\n<p>經濟部101年第6期太陽光電發電設備競標作業，於8月8日進行開標，計有屋頂型89件得標，地面型3件得標，總計容量為23,630.849瓩。</p>\\n<p> 經濟部能源局表示，<span style=\\\"color:#FF0000;\\\">本期太陽光電競標經審查符合競標資格者計162件，其中屋頂型計159件，合計容量為36,233.814瓩；地面型計3件，容量為525.12瓩，總容量共36,758.934瓩，超過基本容量10,000瓩部分為26,758.934瓩。依101年太陽光電競標作業要點規定，得標容量上限為基本容量加計超過部分容量50％，爰本期得標容量上限為23,379.467瓩。</span>\\n                            </p>\\n<p>\\n                                開標作業開放參加競標者親臨現場觀看，決標方式按折扣率由高至低順序排列依次選取，加計最末件得標者容量後倘超過競標容量上限，仍得將其超過容量計入，但屋頂型以1,000瓩為限，地面型以500瓩為限。按本次最末件得標者可再計入容量251.382瓩，<span style=\\\"color:#FF0000;\\\">總計得標容量為23,630.849瓩，平均折扣率為4.37％</span>。未來得標業者適用之太陽光電躉購費率按其完工時公告費率扣除其折扣額度計之，即公告費率X(1-業者投標之折扣率)。\\n                            </p>\\n<p> 另外，<span style=\\\"color:#FF0000;\\\">經濟部101年8月1日公告修正「經濟部101年太陽光電發電設備競標作業要點」，101年度競標容量上限由70,000瓩提高為83,000瓩，累計第1期至第6期得標容量，及考量得標未簽約及撤案等加計容量後，第7期僅剩容量766.927瓩，因此101年9月將為本年度最後1期競標。</span>\\n                            </p>\\n<p>\\n                                經濟部能源局進一步說明及提醒，101年第7期太陽光電競標作業收件截止日為8月20日，開標日為9月12日，前6期未得標、未補正或欲參與第7期競標作業者，請於8月20日下午5時30分前，將應備文件與第7期標單寄達或送達經濟部能源局，並請留意標單內容期別應填寫為第7期，且標單封套應予彌封。</p>\\n<p>能源局發言人：王副局長運銘 <br>\\n                                電話：02-2773-4729 ；行動電話：0910-216-359<br>\\n                                電子郵件：<a href=\\\"mialto:ymwang@moeaboe.gov.tw\\\">ymwang@moeaboe.gov.tw</a><br>\\n                                技術諮詢聯絡人：藍科長文宗<br>\\n                                電話：02-2775-7641；行動電話：0988-396-386<br>\\n                                電子郵件：<a href=\\\"mailto:wtlan@moeaboe.gov.tw\\\">wtlan@moeaboe.gov.tw </a></p>\\n<p></p>\",\n\"img\": \"\",\n\"file\": \"\",\n\"is_hot\": 0,\n\"sort\": 0,\n\"status\": 1,\n\"create_time\": 1635731650,\n\"create_user\": \"1\",\n\"update_time\": 0,\n\"update_user\": \"0\",\n\"delete_time\": 0,\n\"delete_user\": \"0\",\n\"status_dis\": \"啟用\",\n\"create_time_dis\": \"2021/11/01 01:54\",\n\"create_user_dis\": \"1\",\n\"img_dis\": [],\n\"img_url_dis\": [],\n\"file_dis\": [],\n\"file_url_dis\": []\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/api/ctl_news.php",
    "groupTitle": "news"
  },
  {
    "type": "get",
    "url": "news_cats",
    "title": "获取新闻分类",
    "group": "news",
    "name": "news_cats",
    "version": "1.0.0",
    "description": "<p>获取新闻分类</p>",
    "success": {
      "examples": [
        {
          "title": "返回示例:",
          "content": "{\n\"code\": 0,\n\"msg\": \"success\",\n\"timestamp\": 1635732241,\n\"data\": [\n{\n\"id\": 1,\n\"name\": \"太陽能產業\",\n\"desc\": null,\n\"sort\": 0,\n\"status\": 1,\n\"create_user\": \"1\",\n\"create_time\": 1635731452,\n\"status_dis\": \"啟用\",\n\"create_time_dis\": \"2021/11/01 01:50\",\n\"create_user_dis\": \"1\"\n}\n]\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/api/ctl_news.php",
    "groupTitle": "news"
  }
] });
