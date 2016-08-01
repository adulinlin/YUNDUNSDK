package com.yundun.sdk.java;

import java.util.HashMap;
import java.util.LinkedHashMap;
import java.util.Map;

public class Demo {
    public static void main(String[] args) throws Exception {
        Map<String, Object> params = new HashMap<>();
        params.put("domain_type", "cname");
        String res = new YundunSDK("app_id", "app_secret")
//                .apiCall("http://api.yundun.cn/V1/Domain.Record.Type", params);
                .apiCall("Domain.Record.Type", params);
        System.out.println(res);
    }
}
