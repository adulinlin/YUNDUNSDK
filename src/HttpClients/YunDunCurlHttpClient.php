<?php
/**
 * Desc: YunDunCurlHttpClient
 * Created by PhpStorm.
 * User: <gaolu@yundun.com>
 * Date: 2016/11/25 16:14.
 */

namespace YunDunSdk\HttpClients;

use YunDunSdk\Http\RawResponse;
use YunDunSdk\Exceptions\HttpClientException;

class YunDunCurlHttpClient implements YunDunHttpClientInterface
{
    /**
     * @var string The client error message
     */
    protected $curlErrorMessage = '';
    /**
     * @var int The curl client error code
     */
    protected $curlErrorCode = 0;
    /**
     * @var string|bool The raw response from the server
     */
    protected $rawResponse;
    /**
     * @var YunDunCurl Procedural curl as object
     */
    protected $yunDunCurl;

    /**
     * @param YunDunCurl|null Procedural curl as object
     * @param null|YunDunCurl $yunDunCurl
     */
    public function __construct(YunDunCurl $yunDunCurl = null)
    {
        $this->yunDunCurl = $yunDunCurl ?: new YunDunCurl();
    }

    /**
     * {@inheritdoc}
     */
    public function send($url, $method, $body, array $headers, $timeOut, $otherOptions = [])
    {
        if ($body && !is_string($body)) {
            throw new HttpClientException('curl body must be string');
        }
        $this->openConnection($url, $method, $body, $headers, $timeOut);
        $this->sendRequest();
        //todo async request
        if ($curlErrorCode = $this->yunDunCurl->errno()) {
            throw new HttpClientException($this->yunDunCurl->error(), $curlErrorCode);
        }
        // Separate the raw headers from the raw body
        list($rawHeaders, $rawBody) = $this->extractResponseHeadersAndBody();
        $httpStatusCode             = $this->yunDunCurl->getinfo(CURLINFO_HTTP_CODE);
        $this->closeConnection();

        return new RawResponse($rawHeaders, $rawBody, $httpStatusCode);
    }

    /**
     * Opens a new curl connection.
     *
     * @param string $url     The endpoint to send the request to.
     * @param string $method  The request method.
     * @param string $body    The body of the request.
     * @param array  $headers The request headers.
     * @param int    $timeOut The timeout in seconds for the request.
     */
    public function openConnection($url, $method, $body, array $headers, $timeOut)
    {
        $options = [
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => $this->compileRequestHeaders($headers),
            CURLOPT_URL            => $url,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => $timeOut,
            CURLOPT_RETURNTRANSFER => true, // Follow 301 redirects
            CURLOPT_HEADER         => true, // Enable header processing
        ];
        if ('GET' !== $method) {
            $options[CURLOPT_POSTFIELDS] = $body;
        }
        $this->yunDunCurl->init();
        $this->yunDunCurl->setoptArray($options);
    }

    /**
     * Closes an existing curl connection.
     */
    public function closeConnection()
    {
        $this->yunDunCurl->close();
    }

    /**
     * Send the request and get the raw response from curl.
     */
    public function sendRequest()
    {
        $this->rawResponse = $this->yunDunCurl->exec();
    }

    /**
     * Compiles the request headers into a curl-friendly format.
     *
     * @param array $headers The request headers.
     *
     * @return array
     */
    public function compileRequestHeaders(array $headers)
    {
        $return = [];
        foreach ($headers as $key => $value) {
            $return[] = $key.': '.$value;
        }

        return $return;
    }

    /**
     * Extracts the headers and the body into a two-part array.
     *
     * @return array
     */
    public function extractResponseHeadersAndBody()
    {
        $parts      = explode("\r\n\r\n", $this->rawResponse);
        $rawBody    = array_pop($parts);
        $rawHeaders = implode("\r\n\r\n", $parts);

        return [trim($rawHeaders), trim($rawBody)];
    }
}
