<?php
namespace YunDunSdk;

/**
 * 1. resetful
 * 2. 请求体支持array[application/x-www-form-urlencoded],json['application/json]
 * 3. 支持异步请求
 */

use YunDunSdk\Exceptions\YunDunSdkException;
use YunDunSdk\Exceptions\HttpClientException;
use InvalidArgumentException;
use Exception;
use GuzzleHttp\Exception\RequestException;
use YunDunSdk\SignRequest\SignedRequest;
use YunDunSdk\HttpClients\HttpClientsFactory;
use YunDunSdk\Http\RawRequest;
use YunDunSdk\Http\HttpLib;
use YunDunSdk\Exceptions\ExceptionCodeMsg;

class YunDunSdk
{
    const __BASE_API_URL__ = 'http://api.yundun.cn/V1/';

    private $app_id; //必需
    private $app_secret; //必需
    private $user_id; // 用户id, 仅代理需要
    private $client_ip; //客户端ip
    private $client_userAgent; //客户端userAgent
    private $base_api_url; //api base url
    private $http_client_handler; //http client handler
    private $request; //request 对象

    public function __construct($param)
    {
        if (!is_array($param)) {
            throw new YunDunSdkException('param must be array');
        }
        $this->app_id = $param['app_id'];
        $this->app_secret = $param['app_secret'];
        $this->user_id = (int)$param['user_id'];
        $this->client_ip = isset($param['client_ip']) ? trim($param['client_ip']) : '';
        $this->client_userAgent = isset($param['client_userAgent']) ? trim($param['client_userAgent']) : '';
        $this->base_api_url = isset($param['base_api_url']) && !empty($param['base_api_url']) ? $param['base_api_url'] : self::__BASE_API_URL__;
        $this->request = new RawRequest('', '', array(), null, 10, array());
        $this->request->setBaseApiUrl($this->base_api_url);
        $handler = isset($param['handler'])?$param['handler']:'';
        $this->http_client_handler = HttpClientsFactory::createHttpClient($handler);
    }

    /**
     * @param RawRequest $request
     * @return Http\RawResponse
     * @node_name 签名请求
     * @link
     * @desc
     */
    public function signedRequest(RawRequest $request)
    {
        if($request->getBodyType() == 'json'){
            $payload['body'] = $request->getBody();
            $body = $request->getBody();
        }else if($request->getBodyType() == 'array'){
            $payload['body'] = $request->getBody();
            $body = RawRequest::build_query($payload);
        }
        if (strtoupper($request->getMethod()) == 'GET') {
            $payload['body'] = $request->getUrlParams();
            $body = RawRequest::build_query($payload);
        }

        //签名
        $sign = SignedRequest::make($payload, $this->app_secret);
        $this->request->setHeader('X-Auth-Sign', $sign);
        $this->request->setHeader('X-Auth-App-Id', $this->app_id);
        $url = $request->getUrl();
        $method = $request->getMethod();
        $headers = $request->getHeaders();
        $timeOut = $request->getTimeOut();
        $options = $request->getOptions();

        $RawResponse = $this->http_client_handler->send($url, $method, $body, $headers, $timeOut, $options);
        return $RawResponse;
    }

    /**
     * @param $request
     * @return RawRequest
     * @throws YunDunSdkException
     * @node_name build request
     * @link
     * @desc
     */
    private function build_request($request)
    {
        $defaultRequest = [
            'url' => '',
            'body' => [],
            'method' => 'GET',
            'headers' => [
                'format' => 'json',
            ],
            'timeout' => 10,
            'query' => [],
            'options' => []
        ];
        $request = array_merge($defaultRequest, $request);

        $defaultData = [
            'user_id' => $this->user_id,
            'client_ip' => $this->client_ip,
            'client_userAgent' => $this->client_userAgent,
            'fromadmin' => $_SESSION['fromadmin'],
        ];

        foreach ($request["headers"] as $h => $v) {
            $hs = strtolower($h);
            $vs = strtolower($v);

            if ($hs == "content-type") {
                unset($request['headers'][$h]);
                $request["headers"]['Content-Type'] = $vs;
            }
        }

        if(is_string($request['body'])){
            if(!($json_decode_content = HttpLib::isCorrectJson($request['body']))){
                throw new YunDunSdkException(ExceptionCodeMsg::MSG_YUNDUNSDK_BUILD_REQUEST_2, ExceptionCodeMsg::CODE_YUNDUNSDK_BUILD_REQUEST_2);
            }
            $request['body'] = array_merge($defaultData, $json_decode_content);
            $request['body'] = json_encode($request['body']);
            if (strtoupper($request['method']) == 'GET') {
                $request['body'] = '';
            }
            $this->request->setBodyType('json');
            $request['headers']['Content-Type'] = 'application/json';
        }else if(is_array($request['body'])){
            $request['body'] = array_merge($defaultData, $request['body']);
            if (strtoupper($request['method']) == 'GET') {
                $request['body'] = array();
            }
            $this->request->setBodyType('array');
            $request['headers']['Content-Type'] = 'application/x-www-form-urlencoded';
        }else{
            throw new YunDunSdkException(ExceptionCodeMsg::MSG_YUNDUNSDK_BUILD_REQUEST_1, ExceptionCodeMsg::CODE_YUNDUNSDK_BUILD_REQUEST_1);
        }

        if (strtoupper($request['method']) == 'GET') {
            $request['query'] = array_merge($defaultData, $request['query']);
        }

        if(isset($request['options']['async']) && $request['options']['async']){
            $request['options']['callback'] = isset($request['options']['callback'])?$request['options']['callback']:array($this, 'async_callback');
            $request['options']['exception'] = isset($request['options']['exception'])?$request['options']['exception']:array($this, 'async_callback_exception');
        }

        $this->request->setBody($request['body']);
        $this->request->setUrl($request['url']);
        $this->request->setMethod($request['method']);
        $this->request->setTimeOut($request['timeout']);
        $this->request->setHeaders($request['headers']);
        isset($_SERVER['HTTP_SOCKETLOG']) && $this->request->setHeader('Socketlog', $_SERVER['HTTP_SOCKETLOG']);
        isset($_SERVER['HTTP_USER_AGENT']) && $this->request->setHeader('User-Agent', $_SERVER['HTTP_USER_AGENT']);
        $this->request->setUrlParams($request['query']);
        $this->request->setOptions($request['options']);

        return $this->request;
    }

    /**
     * @param $request
     * @return string
     * @throws YunDunSdkException
     * @node_name
     * @link
     * @desc
     */
    private function api_call($request)
    {
//        $request = array(
//            'url' => '',
//            'body' => []/json,
//            'method' => '',
//            'headers' => [],
//            'timeout' => 10,
//            'query' => [],
//            'options' => [
//                'async' => true, //异步请求
//                'callback' => function(){},
//                'exception' => function(){}
//            ]
//        );

        $httpRequest = $this->build_request($request);
        try {
            $rawResponse = $this->signedRequest($httpRequest);
        } catch (HttpClientException $e) {
            echo $e->getMessage();
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $rawResponse->getBody();
    }


    public  function get($request) {
        $request["method"] = "GET";

        return $this->api_call($request);
    }

    public  function post($request) {
        $request["method"] = "POST";

        return $this->api_call($request);
    }

    public  function put($request) {
        $request["method"] = "PUT";

        return $this->api_call($request);
    }

    public  function patch($request) {
        $request["method"] = "PATCH";

        return $this->api_call($request);
    }

    public  function delete($request) {
        $request["method"] = "DELETE";

        return $this->api_call($request);
    }

    public  function getAsync($request) {
        $request["method"] = "GET";
        $request['options']['async'] = true;

        return $this->api_call($request);
    }

    public  function postAsync($request) {
        $request["method"] = "POST";
        $request['options']['async'] = true;

        return $this->api_call($request);
    }

    public  function putAsync($request) {
        $request["method"] = "PUT";
        $request['options']['async'] = true;

        return $this->api_call($request);
    }

    public  function patchAsync($request) {
        $request["method"] = "PATCH";
        $request['options']['async'] = true;

        return $this->api_call($request);
    }

    public  function deleteAsync($request) {
        $request["method"] = "DELETE";
        $request['options']['async'] = true;

        return $this->api_call($request);
    }

    public function async_callback($response){
        $body = $response->getBody()->getContents();
        echo $body;
        exit;
    }

    public function async_callback_exception($e){
        $message = $e->getMessage();
        $method = $e->getRequest()->getMethod();
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN'){
            error_log('async_callbck_exception,message:'.$message.',method:'.$method, 3, '/var/tmp/api_async_call_errors.log');
        }else{
            error_log('async_callbck_exception,message:'.$message.',method:'.$method, 3, 'E:/api_async_call_errors.log');
        }
    }


}