<?php
namespace YunDunSdk\Http;

use YunDunSdk\Exceptions\YunDunSdkException;
use YunDunSdk\Exceptions\ExceptionCodeMsg;
class HttpLib{

    /**
     * @param string $body
     * @return bool
     * @throws YunDunSdkException
     * @node_name 是否是合法的json
     * @link
     * @desc
     */
    public static  function isCorrectJson($body = ''){
        if(!is_string($body)){
            throw new YunDunSdkException(ExceptionCodeMsg::MSG_HTTP_LIB_CODE_IS_CORRECT_JSON_1, ExceptionCodeMsg::CODE_HTTP_LIB_CODE_IS_CORRECT_JSON_1);
        }
        $content = json_decode($body, true);
        $json_error = json_last_error();
        if($json_error == JSON_ERROR_NONE){
            return $content;
        }
        return false;
    }


    // windows系统
    public static function isWin()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
            return true;
        }
        return false;
    }


    //获取当前时间
    public static function getCurDateTime($format = 'Y-m-d H:i:s')
    {
        return date($format);
    }


    /**
     * @param $value
     * @param string $logFile
     * @node_name
     * @link
     * @desc
     */
    public static function logSdk($value, $logFile = '')
    {
        if (self::isWin()) {
            $file = 'E:/sdkV4.log';
        } else {
            $file = '/tmp/sdkV4.log';
        }
        if(!empty($logFile) && file_exists(dirname($logFile))){
            $file = $logFile;
        }
        if (is_array($value)) {
            file_put_contents($file, self::getCurDateTime() . ':' . print_r($value, true) . "\n", FILE_APPEND);
        } else if (is_string($value)) {
            file_put_contents($file, self::getCurDateTime() . ':' . $value . "\n", FILE_APPEND);
        } else if (is_numeric($value)) {
            file_put_contents($file, self::getCurDateTime() . ':' . $value . "\n", FILE_APPEND);
        }else if(is_object($value)){
            file_put_contents($file, self::getCurDateTime() . ':' . print_r($value, true) . "\n", FILE_APPEND);
        }
    }


}