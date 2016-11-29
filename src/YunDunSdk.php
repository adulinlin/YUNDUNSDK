<?php
namespace YunDunSdk;

use YunDunSdk\Exceptions\YunDunSdkException;
use YunDunSdk\Exceptions\HttpClientException;
use GuzzleHttp\Exception\RequestException;
use YunDunSdk\SignRequest\SignedRequest;
use YunDunSdk\HttpClients\HttpClientsFactory;

class YunDunSdk
{
    const __BASE_API_URL__ = 'http://api.yundun.cn/V1/';
    private $app_id; //必需
    private $app_secret; //必需
    private $api_url; //api request url
    private $user_id; // user id
    private $client_ip; //客户端ip
    private $client_userAgent; //客户端userAgent
    private $base_api_url; //api base url
    private $http_client_handler; //http client handler
    private $timeOut = 10; //接口请求超时时间

    public function __construct($param)
    {
        if (!is_array($param)) {
            throw new YunDunSdkException('param must be array');
        }
        $this->app_id = $param['app_id'];
        $this->app_secret = $param['app_secret'];
        if (isset($param['user_id'])) {
            $this->user_id = (int)$param['user_id'];
        }
        if (isset($param['client_ip'])) {
            $this->client_ip = isset($param['client_ip']) ? trim($param['client_ip']) : '';
        }
        if (isset($param['client_userAgent'])) {
            $this->client_userAgent = isset($param['client_userAgent']) ? trim($param['client_userAgent']) : '';
        }
        $this->base_api_url = isset($param['base_api_url']) && !empty($param['base_api_url']) ? $param['base_api_url'] : self::__BASE_API_URL__;
        if (substr($this->base_api_url, -1) != '/') {
            $this->base_api_url .= '/';
        }
        $this->http_client_handler = HttpClientsFactory::createHttpClient($param['http_client_handler'] ?: null);
    }

    public function setApiUrl($url)
    {
        $api_url = $this->base_api_url . $url;
        //http https开头的url
        if (stripos($url, 'http://') !== false || stripos($url, 'https://') !== false) {
            $api_url = $url;
        }
        $this->api_url = $api_url;
    }

    public function getApiUrl()
    {
        return $this->api_url;
    }

    public function getTimeOut()
    {
        return $this->timeOut;
    }

    public function setTimeOut($timeOut)
    {
        $this->timeOut = (int)$timeOut;
    }

    //sign request
    public function signedRequest(array $payload)
    {
        $api_url = $this->getApiUrl();
        if (!$api_url) {
            throw new YunDunSdkException('you need to call setApiUrl function first to set api call url');
        }
        //签名
        $sign = SignedRequest::make($payload, $this->app_secret);
        $param = array(
            'sign' => $sign,
            'app_id' => isset($payload['app_id']) ? $payload['app_id'] : $this->app_id,
        );
        $headers = $payload['request_headers'] ?: array();
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $headers['User-Agent'] = $_SERVER['HTTP_USER_AGENT'];
        }
        if (isset($_SERVER['HTTP_SOCKETLOG'])) {
            $headers['Socketlog'] = $_SERVER['HTTP_SOCKETLOG'];
        }
        $request_method = $payload['request_method'] ?: 'POST';
        $RawResponse = $this->http_client_handler->send($api_url, $request_method, $param, $headers, $this->getTimeOut());
        return $RawResponse;
    }

    public function api_call($api_url, $payload, $return_arr = false, $method = 'POST', $headers = array(), $timeOut = '')
    {
        $payload = array_merge([
            'request_method' => $method,
            'request_timeOut' => $timeOut ?: $this->timeOut,
            'request_headers' => $headers,
            'user_id' => $this->user_id,
            'client_ip' => $this->client_ip,
            'client_userAgent' => $this->client_userAgent,
            'fromadmin' => $_SESSION['fromadmin'],
        ], $payload);
        $this->setApiUrl($api_url);
        $this->setTimeOut($payload['request_timeOut']);
        try {
            $rawResponse = $this->signedRequest($payload);
        } catch (HttpClientException $e) {
        } catch (RequestException $e) {
        }
        if ($return_arr) {
            //todo...根据返回值类型返回数组
        } else {
            return $rawResponse->getBody();
        }
    }
}