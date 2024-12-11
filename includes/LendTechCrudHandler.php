<?php

class LendTechCrudHandler{

    private $wpdb;
    private $opay_wallet_table;
    private $opay_loan_requests_table;
    private $lendtech_merchants;
    private $transaction_request;
    private $merchant_request;
    public function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->opay_wallet_table = 'o2pay_wallets';
        $this->opay_loan_requests_table = 'o2pay_loan_requests';
        $this->lendtech_merchants = 'lendtech_merchants';
        $this->transaction_request = 'lendtech_transaction_request';
        $this->merchant_request = 'lendtech_merchant_request';

    }
    public function get_customer_wallet($row, $row_value, $placeholders ){

        global $wpdb;
        $table_wallets = $wpdb->prefix . $this->opay_wallet_table;
        $query = $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM $table_wallets WHERE $row = $placeholders ", $row_value));
        return $query;
    }

    public function update_customer_wallet($update_fields, $conditions){
        global $wpdb;
        $table_name = $wpdb->prefix . $this->opay_wallet_table;
        
        $query = $this->wpdb->update(
            $table_name , 
            $update_fields, 
            $conditions
        );

        return $query;
    }

    public function get_loan_request($row_para, $national_id, $placeholders){
        global $wpdb;

        $table_loan = $wpdb->prefix . $this->opay_loan_requests_table;
        $query = $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM $table_loan WHERE $row_para = $placeholders", $national_id));
        return $query;

    }


    public function get_merchant_by($row = '',$row_value='', $placeholders=''){
        global $wpdb;
        $table_name = $wpdb->prefix . $this->lendtech_merchants;
        if($row == ''){
            $query = $this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM $table_name"));

        }else{

            $query = $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM $table_name WHERE $row = $placeholders", $row_value));

        }
        return $query;
    }

    public function add_merchant($data){
        global $wpdb;

        $merchant_data = [
            'merchant_id' => $data['merchant_id'],
            'merchant_key' => $data['merchant_key'],
            'merchant_cat'=> $data['merchant_cat'],
            'merchant_commission' => $data['merchant_commission'],
            'merchant_credit' => $data['merchant_credit'],
            'merchant_name' => $data['merchant_name'],
            'merchant_national_id' => $data['merchant_national_id'],
            'merchant_status' => 'active'
        ];
        
        $table_name = $wpdb->prefix . $this->lendtech_merchants;
        $query = $this->wpdb->insert(
            $table_name, $merchant_data
        );
        return $query;
        
    }

    public function update_merchant($update_fields, $conditions){
        global $wpdb;
        $table_name = $wpdb->prefix . $this->lendtech_merchants;
        
        $query = $this->wpdb->update(
            $table_name , 
            $update_fields, 
            $conditions
        );

        return $query;
    }


    public function get_transaction_request($row='', $row_value='', $placeholder=''){

        global $wpdb;
        $table_name = $wpdb->prefix . $this->transaction_request;
        if($row){
            $query = $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM $table_name WHERE $row = $placeholder",$row_value));

        }else{
            $query = $this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM $table_name"));
        }
        return $query;


    }

    public function update_transaction_request($update_fields, $conditions){
        global $wpdb;
        $table_name = $wpdb->prefix . $this->transaction_request;
        
        $query = $this->wpdb->update(
            $table_name , 
            $update_fields, 
            $conditions
        );

        return $query;
    }

    public function create_transaction_request($data){
        global $wpdb;
        $table_name = $wpdb->prefix . $this->transaction_request;
        $result = $wpdb->insert(
            $table_name,
            $data    
        );
        return $result;


    }

    public function create_merchant_request($data){
        global $wpdb;
        $table_name = $wpdb->prefix . $this->merchant_request;
        $result = $wpdb->insert(
            $table_name,
            $data    
        );
        return $result;
    }



    
    public function get_merchant_request($row='', $row_value='', $placeholder=''){

        global $wpdb;
        $table_name = $wpdb->prefix . $this->merchant_request;
        
        if($row){
            $query = $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM $table_name WHERE $row = $placeholder",$row_value));

        }else{
            $query = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name"));
        }
        return $query;


    }

    


}







