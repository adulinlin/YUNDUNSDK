# YUNDUN API PHP SDK legend

+	接口基地址： 'http://api.yundun.cn/V1/';
+	接口遵循REST请求,接口默认返回json
+	app_id, app_secret 向YUNDUN技术客服索取，先注册一个云盾的账号，用于申请绑定 app_id,app_secret,用于调用接口_	

+   环境要求：php >=5.5
         
##使用步骤
1.	composer require yundun/yundunsdk dev-master
 
2.	实例化
//sdk
require 'xx/vendor/autoload.php';
$app_id = 'xx';
$app_secret = 'xx';
$client_ip = 'xx';
$client_userAgent = $_SERVER['HTTP_USER_AGENT'];
$sdk = new YundunSDK (array('app_id'=>$app_id, 'app_secret'=>$app_secret, 'client_ip'=>$client_ip, 'client_userAgent'=>$client_userAgent);


3. 调用
$payload = array(); //请求参数
$sdk->api_call('xxxrequrl', $payload, true, $method = 'POST', $headers = array(), $timeOut = '');
第三个参数 true返回数组，false返回json数据


      
###获取单个域名信息
    
请求url: Domain.info
          
请求参数：
```
     {
        "user_id" : "9281", //代理下线用户id,仅操作自己的下线时候需要传递 [可选]
        "domain": "xxx",
     }
```         
返回值：

```
    {
     "status": {
         "code": 1
     },
     "domain": "xx"
    }
```                