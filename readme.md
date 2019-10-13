
[![Build Status](https://travis-ci.org/jason-gao/YUNDUNSDK.svg?branch=master)](https://travis-ci.org/jason-gao/YUNDUNSDK)
[![Latest Stable Version](https://poser.pugx.org/yundun/yundunsdk/v/stable)](https://packagist.org/packages/yundun/yundunsdk)
[![Total Downloads](https://poser.pugx.org/yundun/yundunsdk/downloads)](https://packagist.org/packages/yundun/yundunsdk)
[![Latest Unstable Version](https://poser.pugx.org/yundun/yundunsdk/v/unstable)](https://packagist.org/packages/yundun/yundunsdk)
[![License](https://poser.pugx.org/yundun/yundunsdk/license)](https://packagist.org/packages/yundun/yundunsdk)
[![Monthly Downloads](https://poser.pugx.org/yundun/yundunsdk/d/monthly)](https://packagist.org/packages/yundun/yundunsdk)
[![Daily Downloads](https://poser.pugx.org/yundun/yundunsdk/d/daily)](https://packagist.org/packages/yundun/yundunsdk)


# YUNDUN API PHP SDK legend

+	接口基地址： 'http://apiv4.yundun.cn/V4/';
+	接口遵循RESTful,默认请求体json,接口默认返回json
+	app_id, app_secret 联系技术客服，先注册一个云盾的账号，用于申请绑定api身份

* 签名
    * 每次请求都签名，保证传输过程数据不被篡改
    * 客户端：sha256签名算法，将参数base64编码+app_secret用sha256签名，每次请求带上签名
    * 服务端：拿到参数用相同的算法签名，对比签名是否正确
    
+   环境要求：php >=5.5
+   依赖composer
         
##使用步骤
1.	composer require yundun/yundunsdk

2.	实例化

```
    //sdk
    require 'xx/vendor/autoload.php';
    $app_id = 'xx';
    $app_secret = 'xx';
    $client_ip = 'xx';
    $client_userAgent = '';
    $base_api_url = 'http://apiv4.yundun.cn/V4/';
    ;base_api_url = 'http://127.0.0.1/V4/'
    $host = 'apiv4.yundun.cn';
    $handler = 'guzzle'; //curl/guzzle默认guzzle,guzzle支持异步调用，curl驱动目前未实现异步
    $sdk = new YundunSDK (
        [
        'app_id'=>$app_id, 
        'app_secret'=>$app_secret, 
        'base_api_url' = 'http://127.0.0.1/V4/',
        'host' => 'apiv4.xx.cn',
        'client_ip'=>$client_ip, 
        'client_userAgent'=>$client_userAgent, 
        'handler'=> $handler,
        'syncExceptionOutputCode' => 0,
        'syncExceptionOutputMessage' =>'同步请求异常信息提示',
        'asyncExceptionOutputCode' => 0,
        'asyncExceptionOutputMessage' => '异步请求异常信息提示'
        'log' => true,  //是否记录sdk相关日志
        'logfileWin' => 'E:/sdkV4.log', //windows日志路径
        'logfileLinux' => '/tmp/sdkV4.log' //linux日志路径
        ]
    );

```

3. 调用

>   format json返回json，xml返回xml

>   body 支持传递json和数组

>   urlParams会拼接在url后面

>   支持get/post/patch/put/delete方法


## sync request

+ get

```

$request = array(
    'url' => 'api/version',
    'body' => '',
    'headers' => [
        'format' => 'json',
    ],
    'timeout' => 10,
    'query' => [
        'params1' => 1,
        'params2' => 2
    ],
);
try{
    $res = $sdk->get($request);
}catch (\Exception $e){
    var_dump($e->getCode());
    var_dump($e->getMessage());
}
exit($res);

```

+ post/put/patch/delete

```

$request = array(
    'url' => 'api/version',
    'body' => json_encode([
        'body1' => 1,
        'body2' => 2,
    ]),
    'headers' => [
        'format' => 'json',
    ],
    'timeout' => 10,
    'query' => [
        'params1' => 1,
        'params2' => 2
    ],
);
try{
    $res = $sdk->post($request);
}catch (\Exception $e){
    var_dump($e->getCode());
    var_dump($e->getMessage());
}
exit($res);

```


## async request

+ get

```

$request = array(
    'url' => 'api/version',
    'body' => '',
    'headers' => [
        'format' => 'json',
    ],
    'timeout' => 10,
    'query' => [
        'params1' => 1,
        'params2' => 2
    ],
    'options' => [
        'async' => true,
        'callback' => function($response){
            $body = $response->getBody->getContents();
            echo $body;
            exit;
        },
        'exception' => function($exception){}
    ]
);
try{
    $sdk->getAsync($request);
}catch (\Exception $e){
    var_dump($e->getCode());
    var_dump($e->getMessage());
}


```

+ post/put/patch/delete

```

$request = array(
    'url' => 'api/version',
    'body' => json_encode([
        'body1' => 1,
        'body2' => 2,
    ]),
    'headers' => [
        'format' => 'json',
    ],
    'timeout' => 10,
    'query' => [
        'params1' => 1,
        'params2' => 2
    ],
    'options' => [
        'async' => true,
        'callback' => function($response){
            $body = $response->getBody->getContents();
            echo $body;
            exit;
        },
        'exception' => function($exception){}
    ]
);
try{
    $sdk->postAsync($request);
}catch (\Exception $e){
    var_dump($e->getCode());
    var_dump($e->getMessage());
}

```
