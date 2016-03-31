2016/03/31
==============

#YUNDUN API PHP SDK legend

+	接口基地址： 'http://api.yundun.cn/V1/';
+	接口只允许POST签名请求,接口默认返回json
+	app_id, app_secret 向YUNDUN技术客服索取，先注册一个云盾的账号，用于申请绑定 app_id,app_secret,用于调用接口_	

+   环境要求：php >=5.3
         
##使用步骤
1.	require YundunSDK.php
 
2.	实例化
//sdk
$app_id = 'xx';
$app_secret = 'xx';
$client_ip = 'xx';
$client_userAgent = $_SERVER['HTTP_USER_AGENT'];
$sdk = new YundunSDK (array('app_id'=>$app_id, 'app_secret'=>$app_secret, 'client_ip'=>$client_ip, 'client_userAgent'=>$client_userAgent);


3. 调用
$payload = array(); //请求参数
$sdk->api_call('xxxrequrl', $payload, true);
第三个参数 true返回数组，false返回json数据


## 清缓存

### 清除全站缓存
请求url:Domain.DashBoard.Speed

请求参数:

     {
            "id" : "21994", //域名控制台id，通过域名信息接口获取 [必填]
            "l" : "setting", [必填]
            "ds" : "clearCache", [必填]
            "act" : "save", [必填]
            "user_id" : "9281", //代理下线用户id,仅操作自己的下线时候需要传递 [可选]
            "cache_clean_type" : "wholeSite", //全站缓存 [必填]
            "client_ip" : "127.0.0.1", [可选]
            "client_userAgent" : "Mozilla/5.0", [可选]
     }
     
 返回：
 
     {
         "status": {
             "code": 1,
             "message": "操作成功",
             "create_at": "2016-03-31 16:15:28"
         },
         "data": {
             "cache_clean_type": "wholeSite"
         }
     }
 
 
### 清除指定url
 
 请求url:Domain.DashBoard.Speed
 
 请求参数:
 
      {
             "id" : "21994", //域名控制台id，通过域名信息接口获取 [必填]
             "l" : "setting", [必填]
             "ds" : "clearCache", [必填]
             "act" : "save", [必填]
             "user_id" : "9281", //代理下线用户id,仅操作自己的下线时候需要传递 [可选]
             "cache_clean_type" : "specUrl", //指定url缓存 [必填]
             "specUrl" : "http://home.yundun.cc/a http://home.yundun.cc/b", [必填]
             "client_ip" : "127.0.0.1", [可选]
             "client_userAgent" : "Mozilla/5.0", [可选]
      }
  
  返回：
    
    {
        "status": {
            "code": 1,
            "message": "操作成功",
            "create_at": "2016-03-31 16:18:15"
        },
        "data": {
            "cache_clean_type": "specUrl",
            "specUrl": "http://yundun.com/a\r\nhttp://yundun.com/b"
        }
    }
 
 
### 获取域名列表
 
 请求地址：Domain.list
 
 请求参数：
 
         {
                 "user_id" : "9281", //代理下线用户id,仅操作自己的下线时候需要传递 [可选]
                 "p" : "1",
                 "pagesize" : 10,
                 "searchword" : "xxx", //搜索关键字，不填所有
                 "domain_type" : "ns",  //{cname|ns} 获取cname域名还是ns域名，不传获取所有域名
          }
  
  返回值：
  
          {
              "status": {
                  "code": 1,
                  "message": "操作成功",
                  "create_at": "2016-03-31 16:32:39"
              },
              "domains": [
                  {
                      "id": "8986",
                      "addtime": "2016-01-14 18:09:47",
                      "domain": "yundun.com",
                      "expiry_time": "2017-01-13 00:00:00",
                      "level_id": "6",
                      "useyundun": "1",
                      "domain_status": "1",
                      "domain_type": "ns",
                      "control_id": "21994",
                      "level_name": "高级版",
                      "group_id": null,
                      "dns_or_web": null,
                      "zzkd_status": false,
                      "zzkd_expiry_time": null,
                      "zzkd_product_id": null,
                      "gfdns_status": false,
                      "gfdns_expiry_time": null,
                      "gfdns_product_id": null,
                      "dlssl_status": false,
                      "dlssl_expiry_time": null,
                      "dlssl_product_id": null,
                      "status_name": "审核通过",
                      "is_expiry": false,
                      "expiry_time_format": "2017-01-13",
                      "control_url": "/dashboard/21994",
                      "control_text": "控制台",
                      "waitActive": false,
                      "zzkd_is_expiry": true,
                      "zzkd_buy": false,
                      "zzkd_name": "增值抗D,100G套餐",
                      "zzkd_expiry_time_format": "1970-01-01",
                      "gfdns_is_expiry": true,
                      "gfdns_buy": false,
                      "gfdns_name": "高防DNS增值,100G+100万QPS",
                      "gfdns_expiry_time_format": "1970-01-01",
                      "dlssl_is_expiry": true,
                      "dlssl_buy": false,
                      "dlssl_name": "独立SSL",
                      "dlssl_expiry_time_format": "1970-01-01",
                      "status_reason": ""
                  }
              ],
              "total": "1"
          }
          
###获取单个域名信息
    
请求url: Domain.info
          
请求参数：

     {
       "user_id" : "9281", //代理下线用户id,仅操作自己的下线时候需要传递 [可选]
        "domain": "xxx",
     }
         
返回值：

        {
         "status": {
             "code": 1
         },
         "domain": "xx"
        }