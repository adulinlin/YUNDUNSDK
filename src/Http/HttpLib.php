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


}