package com.yundun.sdk.java;

import org.apache.http.client.fluent.Form;
import org.apache.http.client.fluent.Request;

import java.io.IOException;
import java.util.Map;

public class YundunSDK {
    private static final String Base_API_URL = "http://api.yundun.cn/V1/";
    private String app_id; //必需
    private String app_secret; //必需
    private String api_url;
    private Integer user_id;
    private String client_ip;
    private String client_userAgent;

    public YundunSDK(String app_id, String app_secret) {
        this.app_id = app_id;
        this.app_secret = app_secret;
    }

    public YundunSDK(String app_id, String app_secret, Integer user_id, String client_ip, String client_userAgent) {
        this(app_id, app_secret);
        this.user_id = user_id;
        this.client_ip = client_ip;
        this.client_userAgent = client_userAgent;
    }

    public void setApi_url(String api_url) {
        if (api_url.contains("http://") || (api_url.contains("https://"))) {
            this.api_url = api_url;
        } else {
            this.api_url = Base_API_URL.concat(api_url);
        }
    }

    //使用apache httpclient封装一个post请求
    private String signedRequest(Map<String, Object> params) {
        if (params.get("user_id") == null) {
            params.put("user_id", user_id);
        }
        if (params.get("client_ip") == null) {
            params.put("client_ip", client_ip);
        }
        if (params.get("client_userAgent") == null) {
            params.put("client_userAgent", client_userAgent);
        }

        String sign = SignRequest.make(params, app_secret);
        String appid = (String) (params.containsValue("app_id") ? params.get("app_id") : app_id);
        String res = "";
        try {
            res = Request.Post(api_url)
                    .bodyForm(Form.form().add("sign", sign).add("app_id", appid).build())
                    .execute().returnContent().toString();
        } catch (IOException e) {
            e.printStackTrace();
        }
        return res;
    }

    /**
     * @param apiUrl 接口url 可以使用两种形式（1）接口名：Domain.Record.Type （2）完整url：http://api.yundun.cn/V1/Domain.Record.Type
     * @param params 接口参数
     * @return
     * @throws IllegalArgumentException
     */
    public String apiCall(String apiUrl, Map<String, Object> params) throws IllegalArgumentException {
        if (apiUrl == null || "".equals(apiUrl)) {
            throw new IllegalArgumentException("请求URL不能为空");
        }
        this.setApi_url(apiUrl);
        String res = this.signedRequest(params);
        return res;
    }
}
