<?php
/**
 * Desc: http response
 * Created by PhpStorm.
 * User: jason-gao
 * Date: 2018/3/13 14:40.
 */

namespace Tests\Http;

use Tests\TestCase;
use YunDunSdk\Http\RawResponse;

/**
 * @coversDefaultClass \YunDunSdk\Http\RawResponse
 */
class RawResponseTest extends TestCase
{
    protected $fakeRawProxyHeader = "HTTP/1.0 200 Connection established
Proxy-agent: Kerio Control/7.1.1 build 1971\r\n\r\n";

    protected $fakeRawHeader = <<<HEADER
HTTP/1.1 200 OK
Etag: "9d86b21aa74d74e574bbb35ba13524a52deb96e3"
Content-Type: text/javascript; charset=UTF-8
X-FB-Rev: 9244768
Date: Mon, 19 May 2014 18:37:17 GMT
X-FB-Debug: 02QQiffE7JG2rV6i/Agzd0gI2/OOQ2lk5UW0=
Access-Control-Allow-Origin: *\r\n\r\n
HEADER;

    protected $fakeHeadersAsArray = [
        'Etag'                        => '"9d86b21aa74d74e574bbb35ba13524a52deb96e3"',
        'Content-Type'                => 'text/javascript; charset=UTF-8',
        'X-FB-Rev'                    => '9244768',
        'Date'                        => 'Mon, 19 May 2014 18:37:17 GMT',
        'X-FB-Debug'                  => '02QQiffE7JG2rV6i/Agzd0gI2/OOQ2lk5UW0=',
        'Access-Control-Allow-Origin' => '*',
    ];
    protected $jsonFakeHeader        = 'x-xx: {"app_id_util_pct": 0.00,"acc_id_util_pct": 0.00}';
    protected $jsonFakeHeaderAsArray = ['x-xx' => '{"app_id_util_pct": 0.00,"acc_id_util_pct": 0.00}'];

    /**
     * @covers ::__construct
     */
    public function testConstructCorrectInstance()
    {
        $rawResponse = new RawResponse($this->fakeRawHeader, '');

        $this->assertInstanceOf(RawResponse::class, $rawResponse);
    }

    /**
     * @covers ::getHeaders
     */
    public function testCanSetTheHeadersFromAnArray()
    {
        $myHeaders = [
            'foo' => 'bar',
            'baz' => 'faz',
        ];
        $response = new RawResponse($myHeaders, '');
        $headers  = $response->getHeaders();

        $this->assertEquals($myHeaders, $headers);
    }

    /**
     * @covers ::getHeaders
     * @covers ::getHttpResponseCode
     */
    public function testCanSetTheHeadersFromAString()
    {
        $response         = new RawResponse($this->fakeRawHeader, '');
        $headers          = $response->getHeaders();
        $httpResponseCode = $response->getHttpResponseCode();
        $this->assertEquals($this->fakeHeadersAsArray, $headers);
        $this->assertEquals(200, $httpResponseCode);
    }

    /**
     * @covers ::getHeaders
     * @covers ::getHttpResponseCode
     */
    public function testWillIgnoreProxyHeaders()
    {
        $response         = new RawResponse($this->fakeRawProxyHeader.$this->fakeRawHeader, '');
        $headers          = $response->getHeaders();
        $httpResponseCode = $response->getHttpResponseCode();
        $this->assertEquals($this->fakeHeadersAsArray, $headers);
        $this->assertEquals(200, $httpResponseCode);
    }

    /**
     * @covers ::getHeaders
     */
    public function testCanTransformJsonHeaderValues()
    {
        $response = new RawResponse($this->jsonFakeHeader, '');
        $headers  = $response->getHeaders();

        $this->assertEquals($this->jsonFakeHeaderAsArray['x-xx'], $headers['x-xx']);
    }
}
