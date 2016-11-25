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
use GuzzleHttp\Psr7\ResponseInterface;
use GuzzleHttp\Ring\Exception\RingException;
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
        $options = [
            'headers' => $headers,
            'body' => $body,
            'timeout' => $timeOut,
            'connect_timeout' => 10,
        ];
        $request = $this->guzzleClient->createRequest($method, $url, $options);
        try {
            $rawResponse = $this->guzzleClient->send($request);
        } catch (RequestException $e) {
            $rawResponse = $e->getResponse();
            if ($e->getPrevious() instanceof RingException || !$rawResponse instanceof ResponseInterface) {
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