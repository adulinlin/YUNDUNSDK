<?php
/**
 * Desc:
 * Created by PhpStorm.
 * User: jason-gao
 * Date: 2018/3/12 17:55.
 */

namespace Tests;

use YunDunSdk\YunDunSdk;

/**
 * @coversDefaultClass \YunDunSdk\YunDunSdk
 */
class YunDunSdkTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructCorrectInstance()
    {
        $app_id           = 'xx';
        $app_secret       = 'xx';
        $client_ip        = 'xx';
        $client_userAgent = '';
        $user_id          = 0;
        $handler          = 'guzzle'; //curl/guzzle默认guzzle,guzzle支持异步调用，curl驱动目前未实现异步

        $yunDunSdk = new YunDunSdk(['app_id'                      => $app_id,
                                    'app_secret'                  => $app_secret,
                                    'client_ip'                   => $client_ip,
                                    'client_userAgent'            => $client_userAgent,
                                    'user_id'                     => $user_id,
                                    'handler'                     => $handler,
                                    'syncExceptionOutputCode'     => 0,
                                    'syncExceptionOutputMessage'  => '同步请求异常信息提示',
                                    'asyncExceptionOutputCode'    => 0,
                                    'asyncExceptionOutputMessage' => '异步请求异常信息提示',
                                    'log'                         => true,  //是否记录sdk相关日志
                                    'logfileWin'                  => 'E:/sdkV4.log', //windows日志路径
                                    'logfileLinux'                => '/tmp/sdkV4.log', //linux日志路径
        ]);

        $this->assertInstanceOf(YunDunSdk::class, $yunDunSdk);
    }

    public function testGet()
    {
    }
}
