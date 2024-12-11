<?php
class SmsFunctionHandler {
    public static function kavenegar_sms_sender($phone_number, $template, $token, $token2, $token3) {
        // پارامترهایی که می‌خواهید در قالب پیامک جایگزین شوند
        $api_key = get_option('kavenegar_api_key', '');
        // تنظیمات API کاوه نگار
        $url = "https://api.kavenegar.com/v1/$api_key/verify/lookup.json";

        // داده‌های ارسال
        $data = array(
            'receptor' => $phone_number,
            'template' => $template,
            'token' => $token,
            'token2' => $token2,
            'token3' => $token3,
        );

        // ارسال درخواست به کاوه نگار
        $args = array(
            'body' => $data,
            'timeout' => '10',
            'blocking' => true,
        );

        $response = wp_remote_post($url, $args);
    }
}