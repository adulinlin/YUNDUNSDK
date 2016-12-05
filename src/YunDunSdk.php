<?php
namespace YunDunSdk;

/**
 * 1. resetful
 * 2. 请求体支持
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
        $this->http_client_handler = HttpClientsFactory::createHttpClient($param['http_client_handler'] ?: '');
    }

    //sign request
    public function signedRequest(RawRequest $request)
    {
        if($request->getBodyType() == 'json'){
            $payload['body'] = $request->getBody();
            $body = $request->getBody();
            $this->request->setHeader('request-data-type', 'json');
        }else if($request->getBodyType() == 'array'){
            $payload['body'] = $request->getBody();
            $body = RawRequest::build_query($payload);
            $this->request->setHeader('request-data-type', 'array');
        }
        if (strtoupper($request->getMethod()) == 'GET') {
            $payload['body'] = $request->getUrlParams();
            $body = RawRequest::build_query($payload);
        }

        //签名
        $sign = SignedRequest::make($payload, $this->app_secret);
        $this->request->setHeader('sign', $sign);
        $this->request->setHeader('app_id', $this->app_id);
        $url = $request->getUrl();
        $method = $request->getMethod();
        $headers = $request->getHeaders();
        $timeOut = $request->getTimeOut();

        $RawResponse = $this->http_client_handler->send($url, $method, $body, $headers, $timeOut);
        return $RawResponse;
    }

    private function build_request($request)
    {
        $defaultRequest = [
            'url' => '',
            'body' => [],
            'method' => 'GET',
            'headers' => [
                'format' => 'json'
            ],
            'timeOut' => 10,
            'urlParams' => [],
            'returnArr' => false
        ];
        $request = array_merge($defaultRequest, $request);

        $defaultData = [
            'user_id' => $this->user_id,
            'client_ip' => $this->client_ip,
            'client_userAgent' => $this->client_userAgent,
            'fromadmin' => $_SESSION['fromadmin'],
        ];


        if(is_string($request['body']) && HttpLib::isCorrectJson($request['body'])){
            $request['body'] = array_merge($defaultData, json_decode($request['body'], true));
            $request['body'] = json_encode($request['body']);
            if (strtoupper($request['method']) == 'GET') {
                $request['body'] = '';
            }
            $this->request->setBodyType('json');
        }else if(is_array($request['body'])){
            $request['body'] = array_merge($defaultData, $request['body']);
            if (strtoupper($request['method']) == 'GET') {
                $request['body'] = array();
            }
            $this->request->setBodyType('array');
        }else{
            throw new YunDunSdkException(ExceptionCodeMsg::MSG_YUNDUNSDK_BUILD_REQUEST_1, ExceptionCodeMsg::CODE_YUNDUNSDK_BUILD_REQUEST_1);
        }

        if (strtoupper($request['method']) == 'GET') {
            $request['urlParams'] = array_merge($defaultData, $request['urlParams']);
        }

        $this->request->setBody($request['body']);
        $this->request->setUrl($request['url']);
        $this->request->setMethod($request['method']);
        $this->request->setTimeOut($request['timeOut']);
        $this->request->setHeaders($request['headers']);
        isset($_SERVER['HTTP_SOCKETLOG']) && $this->request->setHeader('Socketlog', $_SERVER['HTTP_SOCKETLOG']);
        isset($_SERVER['HTTP_USER_AGENT']) && $this->request->setHeader('User-Agent', $_SERVER['HTTP_USER_AGENT']);
        $this->request->setUrlParams($request['urlParams']);

        return $this->request;
    }


    private function api_call($request)
    {
//        $request = array(
//            'url' => '',
//            'body' => []/json,
//            'method' => '',
//            'headers' => [],
//            'timeOut' => 10,
//            'urlParams' => [],
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


}