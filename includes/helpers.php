<?php

if (!defined('ABSPATH')) exit;

/**
 * Helper to generate OTP.
 */
function o2pay_generate_otp() {
    return rand(100000, 999999);
}

