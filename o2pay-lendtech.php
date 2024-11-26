<?php
/**
 * Plugin Name: O2Pay LendTech
 * Description: Custom plugin to handle O2Pay LendTech API services.
 * Version: 1.0.0
 * Author: Your Team
 */

// Prevent direct access
if (!defined('ABSPATH')) exit;

// Autoload classes
require_once plugin_dir_path(__FILE__) . 'includes/class-api-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-otp-service.php';
require_once plugin_dir_path(__FILE__) . 'includes/helpers.php';

// Initialize API Handler
add_action('rest_api_init', ['O2Pay_API_Handler', 'register_routes']);

