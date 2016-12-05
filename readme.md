# YUNDUN API PHP SDK legend

+	接口基地址： 'http://api.yundun.cn/V1/';
+	接口遵循REST请求,接口默认返回json
+	app_id, app_secret 向YUNDUN技术客服索取，先注册一个云盾的账号，用于申请绑定 app_id,app_secret,用于调用接口_	

+   环境要求：php >=5.5
         
##使用步骤
1.	composer require yundun/yundunsdk dev-master
 
2.	实例化
```
    //sdk
    require 'xx/vendor/autoload.php';
    $app_id = 'xx';
    $app_secret = 'xx';
    $client_ip = 'xx';
    $client_userAgent = '';
    $base_api_url = 'http://api.yundun.cn/V1/';
    $sdk = new YundunSDK (array('app_id'=>$app_id, 'app_secret'=>$app_secret, 'client_ip'=>$client_ip, 'client_userAgent'=>$client_userAgent);

```

3. 调用

> format json返回json，xml返回xml
> body 支持传递json和数组
> urlParams会拼接在url后面
> 支持get/post/patch/put/delete方法

```
$request = array(
    'url' => 'test.version',
    'body' => json_encode([
        'body1' => 1,
        'body2' => 3,
    ]),
    'headers' => [
        'format' => 'json',
    ],
    'timeOut' => 10,
    'urlParams' => [
        'params1' => 1,
        'params2' => 2
    ],
);
try{
    $res = $sdk->put($request);
}catch (\Exception $e){
    var_dump($e->getCode());
    var_dump($e->getMessage());
}
exit($res);

```

      
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