<?php
class Lendtech_Rules {

    public static function init() {
        //add_action('wp_enqueue_scripts', [__CLASS__, 'load_assets']);
        add_action('init', [__CLASS__, 'add_my_account_rewrite_rule']); // Add rewrite rule for custom link
        add_filter('query_vars', [__CLASS__, 'add_custom_query_vars']); // Register query variable
        add_action('template_redirect', [__CLASS__, 'my_account_template_redirect']); // Load custom template
    }

    public static function activate() {
        self::add_my_account_rewrite_rule();
        flush_rewrite_rules(); // Flush rewrite rules on activation
    }

    public static function deactivate() {
        flush_rewrite_rules(); // Flush rewrite rules on deactivation
    }


    public static function add_my_account_rewrite_rule() {
        add_rewrite_rule('^my-account/?$', 'index.php?o2pay_account=1', 'top');
    }

    public static function add_custom_query_vars($vars) {
        $vars[] = 'o2pay_account';
        return $vars;
    }

    public static function my_account_template_redirect() {
        if (get_query_var('o2pay_account') == 1) {
            include plugin_dir_path(__FILE__) . '../templates/my-account.php';
            exit;
        }
    }

    // ثبت rewrite rule برای /my-account/multistep-form
    public function add_rewrite_multistep_form_rule() {
        add_rewrite_rule(
            '^my-account/multistep-form/?$',  // الگوی مسیر
            'index.php?multistep_form=1',     // پارامتر قابل استفاده در callback
            'top'                             // قرار دادن آن در اول لیست
        );
    }

    public function custom_register_query_var($vars) {
        $vars[] = 'multistep_form'; // اضافه کردن پارامتر جدید
        return $vars;
    }

    public function custom_handle_multistep_form() {
        if (get_query_var('multistep_form')) {
            $plugin_dir_path = plugin_dir_path(__FILE__);
            // نمایش محتوای صفحه فرم چند مرحله‌ای یا بارگذاری قالب اختصاصی
            include($plugin_dir_path . 'pages/multistep-form.php');
            exit; // جلوگیری از ادامه پردازش
        }
    }
}

// استفاده از شیء برای فراخوانی متدهای غیر استاتیک
$lendtech_rules = new Lendtech_Rules();
add_action('init', [$lendtech_rules, 'add_rewrite_multistep_form_rule']);  // ثبت rewrite rule برای /my-account/multistep-form
add_filter('query_vars', [$lendtech_rules, 'custom_register_query_var']);    // ثبت پارامترهای query
add_action('template_redirect', [$lendtech_rules, 'custom_handle_multistep_form']); // پردازش درخواست‌های فرم چند مرحله‌ای

?>
