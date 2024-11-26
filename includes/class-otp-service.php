<?php

if (!defined('ABSPATH')) exit;

class O2Pay_OTP_Service {
    
    /**
     * Send an SMS with OTP.
     */
    public static function send_otp($phone, $otp) {
        // Example integration with a third-party SMS service
        $response = wp_remote_post('https://api.sms-service.com/send', [
            'body' => [
                'phone' => $phone,
                'message' => "Your OTP code is: $otp",
            ],
        ]);

        if (is_wp_error($response)) {
            return new WP_Error('sms_error', 'Failed to send SMS', ['status' => 500]);
        }

        return true;
    }
}

