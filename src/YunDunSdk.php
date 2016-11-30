<?php
namespace YunDunSdk;

use YunDunSdk\Exceptions\YunDunSdkException;
use YunDunSdk\Exceptions\HttpClientException;
use GuzzleHttp\Exception\RequestException;
use YunDunSdk\SignRequest\SignedRequest;
use YunDunSdk\HttpClients\HttpClientsFactory;
use YunDunSdk\Http\RawRequest;

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
        $this->http_client_handler = HttpClientsFactory::createHttpClient($param['http_client_handler'] ?: null);
    }

    //sign request
    public function signedRequest(RawRequest $request)
    {
        $payload = $request->getBody();
        if(strtoupper($request->getMethod()) == 'GET'){
            $payload = $request->getUrlParams();
        }

        //签名
        $sign = SignedRequest::make($payload, $this->app_secret);
        $this->request->setHeader('sign', $sign);
        $this->request->setHeader('app_id', $this->app_id);
        $url = $request->getUrl();
        $method = $request->getMethod();
        $headers = $request->getHeaders();
        $timeOut = $request->getTimeOut();
        $RawResponse = $this->http_client_handler->send($url, $method, $payload, $headers, $timeOut);
        return $RawResponse;
    }

    public function api_call($api_url, $body, $return_arr = false, $method = 'POST', $headers = array(), $timeOut = 10, $urlParams = array())
    {
        $defaultData = [
            'user_id' => $this->user_id,
            'client_ip' => $this->client_ip,
            'client_userAgent' => $this->client_userAgent,
            'fromadmin' => $_SESSION['fromadmin'],
        ];
        $body = array_merge($defaultData, $body);
        if(strtoupper($method) == 'GET'){
            $urlParams = array_merge($defaultData, $urlParams);
            $body = array();
        }
        $this->request->setBody($body);
        $this->request->setUrl($api_url);
        $this->request->setMethod($method);
        $this->request->setTimeOut($timeOut);
        $this->request->setHeaders($headers);
        isset($_SERVER['HTTP_SOCKETLOG']) && $this->request->setHeader('Socketlog', $_SERVER['HTTP_SOCKETLOG']);
        isset($_SERVER['HTTP_USER_AGENT']) && $this->request->setHeader('User-Agent', $_SERVER['HTTP_USER_AGENT']);
        $this->request->setUrlParams($urlParams);

        try {
            $rawResponse = $this->signedRequest($this->request);
        } catch (HttpClientException $e) {
        } catch (RequestException $e) {
        } catch (InvalidArgumentException $e) {
        } catch (Exception $e) {
        }
        if ($return_arr) {
            return json_decode($rawResponse->getBody(), true);
        } else {
            return $rawResponse->getBody();
        }
    }



}