<?php
/**
 * Desc: YunDunGuzzleHttpClient
 * Created by PhpStorm.
 * User: <gaolu@yundun.com>
 * Date: 2016/11/25 16:47
 */

namespace YunDunSdk\HttpClients;

use YunDunSdk\Exceptions\HttpClientException;
use YunDunSdk\Http\RawResponse;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

class YunDunGuzzleHttpClient implements YunDunHttpClientInterface{
    /**
     * @var \GuzzleHttp\Client The Guzzle client.
     */
    protected $guzzleClient;
    /**
     * @param \GuzzleHttp\Client|null The Guzzle client.
     */
    public function __construct(Client $guzzleClient = null)
    {
        $this->guzzleClient = $guzzleClient ?: new Client();
    }
    /**
     * @inheritdoc
     */
    public function send($url, $method, $body, array $headers, $timeOut)
    {
        if($body && !is_string($body)){
            throw new HttpClientException('guzzle body must be string');
        }
        $options = [
            'headers' => $headers,
            'timeout' => $timeOut,
            'connect_timeout' => 10,
        ];
        $json_content = false;
        $form_content = true;

        if (isset($headers) && is_array($headers)) {
            foreach ($headers as $h => $v) {
                $h = strtolower($h);
                $v = strtolower($v);

                if ($h == "content-type") {
                    $json_content = $v == "application/json";
                    $form_content = $v == "application/x-www-form-urlencoded";
                }
            }
        }

        if($json_content){
            $content = json_decode($body, true);
            if(function_exists('json_last_error')) {
                $json_error = json_last_error();
                if ($json_error != JSON_ERROR_NONE) {
                    throw new HttpClientException("JSON Error [{$json_error}] - Data: ".$body);
                }
            }
            $options['json'] = $content;
        }else if ($form_content) {
            parse_str($body, $content);
            $options['form_params'] = $content;
        }

        try {
            $rawResponse = $this->guzzleClient->request($method, $url, $options);
        } catch (RequestException $e) {
            $rawResponse = $e->getResponse();
            if (!$rawResponse instanceof ResponseInterface) {
                throw new HttpClientException($e->getMessage(), $e->getCode());
            }
        }
        $rawHeaders = $this->getHeadersAsString($rawResponse);
        $rawBody = $rawResponse->getBody();
        $httpStatusCode = $rawResponse->getStatusCode();
        return new RawResponse($rawHeaders, $rawBody, $httpStatusCode);
    }
    /**
     * Returns the Guzzle array of headers as a string.
     *
     * @param ResponseInterface $response The Guzzle response.
     *
     * @return string
     */
    public function getHeadersAsString(ResponseInterface $response)
    {
        $headers = $response->getHeaders();
        $rawHeaders = [];
        foreach ($headers as $name => $values) {
            $rawHeaders[] = $name . ": " . implode(", ", $values);
        }
        return implode("\r\n", $rawHeaders);
    }
}