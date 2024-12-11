<?php
class LendtechDatabaseTables {
    public static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Define table names
        $tables = [
            'transaction_request' => $wpdb->prefix . 'lendtech_transaction_request', 
            'merchants' => $wpdb->prefix . 'lendtech_merchants',
            'merchant_request' => $wpdb->prefix . 'lendtech_merchant_request ',

        ];
    
        // Table creation SQL statements
        $sql_statements = [];


        if ($wpdb->get_var("SHOW TABLES LIKE '{$tables['transaction_request']}'") != $tables['transaction_request']) {
            $sql_statements[] = "CREATE TABLE {$tables['transaction_request']} (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                tracking_code VARCHAR(50) NOT NULL,
                national_id varchar(20) NOT NULL,
                merchant_id varchar(20) NOT NULL,
                order_id bigint(20) NOT NULL,
                pay_amount int NOT NULL,
                status varchar(20) NOT NULL,
                description text,
                otp_code varchar(10),
                otp_expiration datetime,
                otp_wrong tinyint(1) DEFAULT 0,
                transaction_expiration datetime,
                created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";
        }

        if ($wpdb->get_var("SHOW TABLES LIKE '{$tables['merchants']}'") != $tables['merchants']) {
            $sql_statements[] = "CREATE TABLE {$tables['merchants']} (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                merchant_id varchar(45) NOT NULL,
                merchant_key varchar(45) NOT NULL,
                merchant_cat varchar(20) NOT NULL,
                merchant_commission varchar(20) NOT NULL,
                merchant_credit bigint(20) NOT NULL,
                merchant_status varchar(20) NOT NULL,
                merchant_name varchar(40) NOT NULL,
                merchant_national_id varchar(20) NOT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";
        }

        if ($wpdb->get_var("SHOW TABLES LIKE '{$tables['merchant_request']}'") != $tables['merchant_request']) {
            $sql_statements[] = "CREATE TABLE {$tables['merchant_request']} (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                mr_id varchar(45) NOT NULL,
                mr_amount bigint(20) NOT NULL,
                mr_status varchar(20),
                mr_verify_date datetime,
                mr_description text,
                mr_receipt_img bigint(20),
                mr_acc_id bigint(20),
                created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";
        }

        // Execute the table creation if they do not exist
        if (!empty($sql_statements)) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            foreach ($sql_statements as $sql) {
                dbDelta($sql);
            }
        }
    }
}
?>
