<?php
/*
Plugin Name: LendTech
Plugin URI: https://resland.ir.
Description: اپلیکیشن LendTech
Version: 1.0
Author: alireza rashgi
*/

// Ensure the plugin is not accessed directly
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include the main plugin class
require_once plugin_dir_path(__FILE__) . 'includes/class-o2pay-wallet.php';

// Register activation and deactivation hooks
register_activation_hook(__FILE__, ['lendtech_rules', 'activate']);
register_deactivation_hook(__FILE__, ['lendtech_rules', 'deactivate']);

// Initialize the plugin
add_action('plugins_loaded', ['lendtech_rules', 'init']);

include 'lendtech.php';

// Inside your main plugin file
register_activation_hook(__FILE__, ['LendtechDatabaseTables', 'create_tables']);
