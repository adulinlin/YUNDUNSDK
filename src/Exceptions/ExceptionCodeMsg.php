<?php

namespace YunDunSdk\Exceptions;

class ExceptionCodeMsg
{
    //HttpLib/isCorrectJson
    const CODE_HTTP_LIB_CODE_IS_CORRECT_JSON_1 = 7001;
    const MSG_HTTP_LIB_CODE_IS_CORRECT_JSON_1  = 'body must be string';

    //YunDunSdk
    const CODE_YUNDUNSDK_BUILD_REQUEST_1 = 8001;
    const MSG_YUNDUNSDK_BUILD_REQUEST_1  = 'body must be string or array';
    const CODE_YUNDUNSDK_BUILD_REQUEST_2 = 8002;
    const MSG_YUNDUNSDK_BUILD_REQUEST_2  = 'invalid json format';

    const CODE_YUNDUNGUZZLEHTTPCLIENT_SEND_1 = 9001;
    const MSG_YUNDUNGUZZLEHTTPCLIENT_SEND_1  = 'Content-Type must be application/json or application/x-www-form-urlencoded';
}
