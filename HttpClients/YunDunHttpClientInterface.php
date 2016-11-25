<?php
/**
 * Desc:
 * Created by PhpStorm.
 * User: <gaolu@yundun.com>
 * Date: 2016/11/25 14:07
 */

namespace YunDunSdk\HttpClients;

interface YunDunHttpClientInterface{
    /**
     * Sends a request to the server and returns the raw response.
     *
     * @param string $url     The endpoint to send the request to.
     * @param string $method  The request method.
     * @param string $body    The body of the request.
     * @param array  $headers The request headers.
     * @param int    $timeOut The timeout in seconds for the request.
     *
     * @return \YunDunSdk\Http\RawResponse Raw response from the server.
     *
     * @throws \YunDunSdk\Exceptions\HttpClientException
     */
    public function send($url, $method, $body, array $headers, $timeOut);
}