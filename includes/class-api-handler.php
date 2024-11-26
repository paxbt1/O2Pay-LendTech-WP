<?php

if (!defined('ABSPATH')) exit;

class O2Pay_API_Handler {
    
    /**
     * Register API endpoints.
     */
    public static function register_routes() {
        register_rest_route('o2pay-lendtech/v1', '/get-token', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'handle_get_token'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('o2pay-lendtech/v1', '/get-credit', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'handle_get_credit'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('o2pay-lendtech/v1', '/pay-request', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'handle_pay_request'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('o2pay-lendtech/v1', '/pay-otp-resend', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'handle_pay_otp_resend'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('o2pay-lendtech/v1', '/pay-otp-verify', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'handle_pay_otp_verify'],
            'permission_callback' => '__return_true',
        ]);
    }

    /**
     * Validate Token from request headers.
     */
    private static function validate_token($request) {
        $token = $request->get_header('Authorization');

        if (!$token || $token !== 'your-token-value') {
            return new WP_Error('invalid_token', 'Token is missing or invalid', ['status' => 401]);
        }
        return true;
    }

    /**
     * Endpoint: /get-token
     */
    public static function handle_get_token($request) {
        $validation = self::validate_token($request);
        if (is_wp_error($validation)) return $validation;

        return rest_ensure_response(['message' => 'Token fetched successfully']);
    }

    /**
     * Endpoint: /get-credit
     */
    public static function handle_get_credit($request) {
        $validation = self::validate_token($request);
        if (is_wp_error($validation)) return $validation;

        return rest_ensure_response(['credit' => 1000]);
    }

    /**
     * Endpoint: /pay-request
     */
    public static function handle_pay_request($request) {
        $validation = self::validate_token($request);
        if (is_wp_error($validation)) return $validation;

        // Implement pay-request logic
        return rest_ensure_response(['message' => 'Pay request initiated']);
    }

    /**
     * Endpoint: /pay-otp-resend
     */
    public static function handle_pay_otp_resend($request) {
        $validation = self::validate_token($request);
        if (is_wp_error($validation)) return $validation;

        // Implement OTP resend logic
        return rest_ensure_response(['message' => 'OTP resent successfully']);
    }

    /**
     * Endpoint: /pay-otp-verify
     */
    public static function handle_pay_otp_verify($request) {
        $validation = self::validate_token($request);
        if (is_wp_error($validation)) return $validation;

        // Implement OTP verification logic
        return rest_ensure_response(['message' => 'OTP verified successfully']);
    }
}

