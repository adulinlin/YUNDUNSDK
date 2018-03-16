<?php
/**
 * Desc: 签名
 * Created by PhpStorm.
 * User: jason-gao
 * Date: 2018/3/13 10:30.
 */

namespace Tests\SignRequest;

use Tests\TestCase;
use YunDunSdk\SignRequest\SignedRequest;

/**
 * @coversDefaultClass \YunDunSdk\SignRequest\SignedRequest
 */
class SignedRequestTest extends TestCase
{
    protected $appSecret;
    protected $payload          = [];
    protected $rawSignedRequest = '';
    protected $rawSignature;
    protected $rawPayload;

    protected function setUp()
    {
        $this->appSecret = 'test-app-secret';
        $this->payload   = [
            'a'           => 1,
            'b'           => 2,
            'oauth_token' => 'test_token',
            'user_id'     => 123,
            'issued_at'   => 1520911781,
            'algorithm'   => 'HMAC-SHA256',
        ];
        $this->rawSignedRequest          = 'InS2x-EJ5H-449w4QhuXxrX5rgQTu_e9rXGxjKL7OmY=.eyJhIjoxLCJiIjoyLCJvYXV0aF90b2tlbiI6InRlc3RfdG9rZW4iLCJ1c2VyX2lkIjoxMjMsImlzc3VlZF9hdCI6MTUyMDkxMTc4MSwiYWxnb3JpdGhtIjoiSE1BQy1TSEEyNTYifQ==';
        list($rawSignature, $rawPayload) = explode('.', $this->rawSignedRequest, 2);
        $this->rawSignature              = $rawSignature;
        $this->rawPayload                = $rawPayload;
    }

    protected function tearDown()
    {
        $this->appSecret        = '';
        $this->payload          = [];
        $this->rawSignedRequest = '';
        $this->rawSignature     = '';
        $this->rawPayload       = '';
    }

    /**
     * @covers ::__construct()
     */
    public function testConstructCorrectInstance()
    {
        $signRequest = new SignedRequest();

        $this->assertInstanceOf(SignedRequest::class, $signRequest);
    }

    /**
     * @covers ::make
     */
    public function testMake()
    {
        $rawSignedRequest = SignedRequest::make($this->payload, $this->appSecret);

        $sr      = new SignedRequest($rawSignedRequest, null, $this->appSecret);
        $payload = $sr->getPayload();

        $this->assertEquals($payload, $this->payload);
        $this->assertEquals($rawSignedRequest, $this->rawSignedRequest);
    }

    /**
     * @expectedException \YunDunSdk\Exceptions\SignedRequestException
     * @expectedExceptionCode 606
     * @expectedExceptionMessage Malformed signed request
     */
    public function testInvalidSignedRequests606()
    {
        SignedRequest::parse('test raw sign', null, $this->appSecret);
    }

    /**
     * @covers ::base64UrlEncode
     */
    public function testBase64EncodingIsUrlSafe()
    {
        $encodedData = SignedRequest::base64UrlEncode('aijkoprstADIJKLOPQTUVX1256!)]-:;"<>?.|~');

        $this->assertEquals('YWlqa29wcnN0QURJSktMT1BRVFVWWDEyNTYhKV0tOjsiPD4_Lnx-', $encodedData);
    }

    /**
     * @covers ::base64UrlDecode
     */
    public function testAUrlSafeBase64EncodedStringCanBeDecoded()
    {
        $decodedData = SignedRequest::base64UrlDecode('YWlqa29wcnN0QURJSktMT1BRVFVWWDEyNTYhKV0tOjsiPD4_Lnx-');

        $this->assertEquals('aijkoprstADIJKLOPQTUVX1256!)]-:;"<>?.|~', $decodedData);
    }

    /**
     * @expectedException \YunDunSdk\Exceptions\SignedRequestException
     * @expectedExceptionCode 602
     * @expectedExceptionMessage  Signed request has an invalid signature
     */
    public function testAnImproperlyEncodedSignatureWillThrowAnException()
    {
        new SignedRequest('test_sig'.'.'.$this->rawPayload, null, $this->appSecret);
    }

    /**
     * @expectedException \YunDunSdk\Exceptions\SignedRequestException
     * @expectedExceptionCode 602
     * @expectedExceptionMessage  Signed request has an invalid signature
     */
    public function testAnImproperlyEncodedPayloadWillThrowAnException()
    {
        new SignedRequest($this->rawSignature.'.'.'test_payload', null, $this->appSecret);
    }

    /**
     * @expectedException \YunDunSdk\Exceptions\SignedRequestException
     * @expectedExceptionCode 605
     * @expectedExceptionMessage Signed request is using the wrong algorithm
     */
    public function testNonApprovedAlgorithmsWillThrowAnException()
    {
        $signedRequestData              = $this->payload;
        $signedRequestData['algorithm'] = 'TEST-ALGORITHM';

        $rawSignedRequest = SignedRequest::make($signedRequestData, $this->appSecret);

        new SignedRequest($rawSignedRequest, null, $this->appSecret);
    }

    /**
     * @covers ::getPayload
     */
    public function testAsRawSignedRequestCanBeValidatedAndDecoded()
    {
        $sr = new SignedRequest($this->rawSignedRequest, null, $this->appSecret);

        $this->assertEquals($this->payload, $sr->getPayload());
    }

    /**
     * @covers ::getPayload
     * @covers ::getUserId
     * @covers ::getRawSignedRequest
     * @covers ::hasOAuthData
     */
    public function testARawSignedRequestCanBeValidatedAndDecoded()
    {
        $sr = new SignedRequest($this->rawSignedRequest, null, $this->appSecret);

        $this->assertEquals($this->payload, $sr->getPayload());
        $this->assertEquals(123, $sr->getUserId());
        $this->assertEquals($this->rawSignedRequest, $sr->getRawSignedRequest());
        $this->assertTrue($sr->hasOAuthData());
    }
}
