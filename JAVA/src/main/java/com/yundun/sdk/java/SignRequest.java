package com.yundun.sdk.java;

import com.google.gson.Gson;
import org.apache.commons.codec.binary.Base64;

import javax.crypto.Mac;
import javax.crypto.spec.SecretKeySpec;
import java.util.Map;

public class SignRequest {

    private static Gson gson = new Gson();

    //生成签名
    public static String make(Map<String, Object> params, String appSecret) {
        params.put("algorithm", "HMAC-SHA256");
        params.put("issued_at", System.currentTimeMillis() / 1000);
        String encodedPayload = base64UrlEncode(gson.toJson(params).getBytes());
        byte[] hashedSig = null;
        try {
            hashedSig = hashSignature(encodedPayload, appSecret);
        } catch (Exception e) {
            e.printStackTrace();
        }
        String encodedSig = base64UrlEncode(hashedSig);
        return encodedSig + "." + encodedPayload;
    }

    public static String base64UrlEncode(byte[] bs) {
        return new String(Base64.encodeBase64(bs)).replace("+", "-").replace("/", "_");
    }

    public static byte[] hashSignature(String encodedData, String appSecret) throws Exception {
        if (appSecret == null) {
            throw new IllegalArgumentException("Secret key is null");
        }
        try {
            Mac mac = Mac.getInstance("HmacSHA256");
            SecretKeySpec secret = new SecretKeySpec(appSecret.getBytes(), "HmacSHA256");
            mac.init(secret);
            byte[] bytes = mac.doFinal(encodedData.getBytes());
            return bytes;
        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }
    }
}
