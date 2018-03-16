<?php

namespace YunDunSdk\HttpClients;

use Exception;
use GuzzleHttp\Client;
use InvalidArgumentException;

class HttpClientsFactory
{
    private function __construct()
    {
        // a factory constructor should never be invoked
    }

    /**
     * HTTP client generation.
     *
     * @param YunDunHttpClientInterface|Client|string|null $handler
     *
     * @throws Exception                If the cURL extension or the Guzzle client aren't available (if required).
     * @throws InvalidArgumentException If the http client handler isn't "curl", "stream", "guzzle", or an instance of YunDunSdk\HttpClients\YunDunHttpClientInterface.
     *
     * @return YunDunHttpClientInterface
     */
    public static function createHttpClient($handler)
    {
        if (!$handler) {
            return self::detectDefaultClient();
        }

        if ($handler instanceof YunDunHttpClientInterface) {
            return $handler;
        }

        if ('curl' === $handler) {
            if (!extension_loaded('curl')) {
                throw new Exception('The cURL extension must be loaded in order to use the "curl" handler.');
            }

            return new YunDunCurlHttpClient();
        }

        if ('guzzle' === $handler && !class_exists('GuzzleHttp\Client')) {
            throw new Exception('The Guzzle HTTP client must be included in order to use the "guzzle" handler.');
        }

        if ($handler instanceof Client) {
            return new YunDunGuzzleHttpClient($handler);
        }
        if ('guzzle' === $handler) {
            return new YunDunGuzzleHttpClient();
        }

        throw new InvalidArgumentException('The http client handler must be set to "curl", "stream", "guzzle", be an instance of GuzzleHttp\Client or an instance of Facebook\HttpClients\FacebookHttpClientInterface');
    }

    /**
     * Detect default HTTP client.
     *
     * @return YunDunHttpClientInterface
     */
    private static function detectDefaultClient()
    {
        if (class_exists('GuzzleHttp\Client')) {
            return new YunDunGuzzleHttpClient();
        }

        if (extension_loaded('curl')) {
            return new YunDunCurlHttpClient();
        }
    }
}
