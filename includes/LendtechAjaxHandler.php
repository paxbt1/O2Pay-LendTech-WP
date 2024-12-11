<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class LendtechAjaxHandler
{ 
   
    private $api_key;
    private $sms_sender;
    private $template;
    private $template_status;
    private $merchant_id;
    private $merchant_key;
    private $crud;
    private $notif_sender;
    private $helper;
    
    public function __construct(LendTechCrudHandler $crud, SmsFunctionHandler $notif_sender, LendTechHelperFunctions $helper) {
        $this->crud = $crud;
        $this->notif_sender = $notif_sender;
        $this->helper = $helper;
        $this->api_key = get_option('kavenegar_api_key', '');
        $this->template= get_option('kavenegar_template', '');
        $this->template_status= get_option('kavenegar_template_status', '');
        $this->merchant_id = '52t698t225d5d7'; 
        $this->merchant_key = '2r8g4rgsd';
        // رجیستر کردن route
        add_action('rest_api_init', array($this, 'register_new_endpoint'));
           
    }

    public function register_new_endpoint() {
        // register_rest_route( 'webservice/v1', '/get-token', array(
        //     'methods' => 'POST',
        //     'callback' => array($this,'get_token'),
        //     'permission_callback' => '__return_true'
        // ) );

        register_rest_route( 'webservice/v1', '/pay-request', array(
            'methods' => 'POST',
            'callback' => array($this,'pay_request'),
            'permission_callback' => '__return_true'
        ) );

        register_rest_route( 'webservice/v1', '/pay-otp-resender', array(
            'methods' => 'POST',
            'callback' => array($this,'pay_otp_resender'),
            'permission_callback' => '__return_true'
        ) );


        register_rest_route( 'webservice/v1', '/pay-otp-verify', array(
            'methods' => 'POST',
            'callback' => array($this,'pay_otp_verify'),
            'permission_callback' => '__return_true'
        ) );

        register_rest_route( 'webservice/v1', '/merchant-request', array(
            'methods' => 'POST',
            'callback' => array($this,'merchant_request'),
            'permission_callback' => '__return_true'
        ) );
    }
   

    // public function get_token($request) {

    //     $params = $request->get_params();
    //     $merchant_id = sanitize_text_field($params['merchant_id']);
    //     $merchant_key = sanitize_text_field($params['merchant_key']);
    //     $order_id = sanitize_text_field($params['order_id']);
    //     $credit_amount = sanitize_text_field($params['credit_amount']);
    //     $national_id = sanitize_text_field($params['national_id']);
    //     $date = sanitize_text_field($params['date']);
    //     $sended_token = sanitize_text_field($params['token']);

    //     if ($merchant_id != $this->merchant_id ) {
    //         wp_send_json_error(['message' => 'آیدی پذیرنده اشتباه است!'],403);
    //         return;
    //     }
        
    //     if ($merchant_id == $this->merchant_id) {
    //         wp_send_json_error(['message' => 'کلید پذیرنده اشتباه است'],403);
    //         return;
    //     }


    //     $algo = 'sha256';
    //     $key_data= $order_id.$merchant_id.$credit_amount.$national_id.$date;
    //     $secret_key = 'my_secret_key';
    //     $token = hash_hmac($algo,$key_data,$secret_key);

    //     if($token ){
    //         wp_send_json_success([
    //             'success' => true,
    //             'message' => 'توکن معتبر است',
    //             'token' => $token
    //         ],200);

    //     }else{
    //         wp_send_json_error(['message' => 'توکن ساخته نشده است'],403);
    //     }

    //     return;

        
    // }

    /////////////////////pay-request and pay-otp-send functions combine together ///////////////////////


    public function pay_request($request){
        
        $params = $request->get_params();
        $sended_token = sanitize_text_field($params['token']);
        $national_id = sanitize_text_field($params['national_id']);
        $merchant_id = sanitize_text_field($params['merchant_id']);
        $credit_amount = $this->helper->rial_to_toman(sanitize_text_field($params['credit_amount']));
        $order_id = sanitize_text_field($params['order_id']);
        $date = sanitize_text_field($params['date']);

        $token_data = [
            'order_id' => $order_id,
            'merchant_id' => $merchant_id,
            'credit_amount' => $credit_amount,
            'national_id' => $national_id,
            'date' => $date,
        ];

        $token = $this->helper->get_token($token_data);
        
        if($sended_token != $token){
            wp_send_json_error(['message' => 'توکن معتبر نیست!'],403);
            return;
        }       

        
        global $wpdb;
        $request_wallet = $this->crud->get_customer_wallet('national_id',$national_id, '%s');

        if(!$request_wallet->national_id){
            wp_send_json_error(['message' => 'کد ملی معتبر نیست'],403);
            return;
        }


        if($request_wallet->status != 'active'){
            wp_send_json_error(['message' => 'کیف پول فعالی وجود ندارد'],403);
            return;
        }
        
        if($request_wallet->amount_remains < $credit_amount){
            wp_send_json_error(['message' => 'موجودی کافی نیست!'],403);
            return;
        }
        // status merchant status active: در غیر اینصورت پذیرنده طرح فعالی ندارد
        $merchant = $this->crud->get_merchant_by('merchant_id',$merchant_id,'%s');

        if($merchant->merchant_status != 'active'){
            wp_send_json_error(['message' => 'کیف پول پذیرنده فعال نیست'],403);
            return;
        } 


        if($merchant->merchant_cat == 'gold'){
            wp_send_json_error(['message' => 'پذیرنده مجاز به تراکنش در دسته بندی مشتری نیست'],403);
            return;
        } 


        /**********otp send*********/
        $otp_code = wp_rand(100000, 999999);  
        $current_time = current_time('Y-m-d h:i:s'); 

        $time_2min = strtotime("+2 minutes", strtotime($current_time));
        $otp_expiration = date('Y-m-d h:i:s', $time_2min);

        /*************kavenegar************/

        $phoneNumber = $request_wallet->phone_number;
        // $api_key = $this->api_key;
        $template = $this->template;
        // $url = "https://api.kavenegar.com/v1/$api_key/verify/lookup.json?receptor=$phoneNumber&token=$otp_code&template=$template";
        // $response = wp_remote_get($url);
        $token1 = '';
        $token2 = '';
        $token3 = '';
        $response = $this->notif_sender->kavenegar_sms_sender($phoneNumber, $template, $token1,$token2,$token3);
        $response_body = wp_remote_retrieve_body($response);

        

        /******************create transaction*****************/
        $tracking_code = wp_hash(microtime());
        $pay_amount = $request_wallet->amount_remains - $credit_amount;
        $time_15min = strtotime("+15 minutes", baseTimestamp: strtotime($current_time));
        $transaction_expiration = date('Y-m-d h:i:s', $time_15min);

        /***********************check transaction expiration**************************/

        $request_transaction_req = $this->crud->get_transaction_request('national_id', $national_id,'%s');
        if($request_transaction_req){
            $time_req = strtotime($request_transaction_req[0]->transaction_expiration); 
            //$to_time = strtotime($datetime_1); 
            $deff_time = round(abs($time_req - $current_time) / 60,2);
            if($deff_time >= 15){

                $update_fields = ['status' => 'expired',];
                $conditions = ['merchant_id' => $merchant_id];
                $update_row = $this->crud->update_transaction_request($update_fields, $conditions);
               
                if($update_row){
                    wp_send_json_error(['message' => 'تراکنش منقضی شده است'],403);
    
                }
                return;
            }
        }


        /***********************insert data in database**************************/
        $data = [
                'tracking_code' => $tracking_code,
                'national_id' => $national_id, 
                'merchant_id' => $merchant_id,
                'order_id' =>$order_id,
                'pay_amount' => $pay_amount,
                'status' => 'otp-send',
                'description' => $response_body,
                'otp_code'  =>  $otp_code,
                'otp_expiration' => $otp_expiration,
                'otp_wrong'=> 1,
                'transaction_expiration' => $transaction_expiration,
            ];
        $result = $this->crud->create_transaction_request($data); 

        
        if($result){
            wp_send_json_success([
                'message' => 'کد تایید با موفقیت ارسال شد',
                'success' => true,
                'tracking_code'=>$tracking_code
            ]);

        }else{
            wp_send_json_error(['message' => 'خطا در ارسال کد تایید'],403);

        }

        return;
    }


    public function pay_otp_resender($request){

        $params = $request->get_params();
        $national_id = sanitize_text_field($params['national_id']);
        $token = sanitize_text_field($params['token']);


        global $wpdb;
        $current_time = current_time('Y-m-d h:i:s'); 

        /******************get req pay table********************/
        $transaction_table =$wpdb->prefix.'o2pay_transaction_request';
        $query_transaction_req = "SELECT * FROM $transaction_table WHERE national_id = %s LIMIT 1";
        $request_transaction_req = $wpdb->get_results($wpdb->prepare($query_transaction_req, $national_id));

        /******************update otp_wrong******************* */
        $otp_wrong = $request_transaction_req->otp_wrong; 
        if($otp_wrong <= 3){
            $otp_wrong++;
            $result = $wpdb->update(
                $transaction_table, 
                ['otp_wrong'=>$otp_wrong], 
                ['national_id' => $national_id]
            );

        }

        if($otp_wrong > 3){
            wp_send_json_error(['message' => 'درخواست شما بیش از حد مجاز است، لطفا بعدا اقدام نمایید'],403);
            return;
        }

        /****************get phone number********************/
        $table_wallet = $wpdb->prefix . 'o2pay_wallets';
        $query_wallet = "SELECT * FROM $table_wallet WHERE national_id = %s LIMIT 1";
        $request_wallet = $wpdb->get_results($wpdb->prepare($query_wallet, $national_id));
        $phoneNumber = $request_wallet[0]->phone_number;

        /**********otp code*********/
        $otp_code = wp_rand(100000, 999999);  
        $current_time = current_time('Y-m-d h:i:s'); 

        /**********check otp expiration*********/

        $time_req = strtotime($request_transaction_req[0]->otp_expiration); 
        $to_time = strtotime($current_time); 
        $deff_time = round(abs($time_req - $to_time) / 60,2);
        /*************kavenegar************/
        if($deff_time >= 2){

        
        $phoneNumber = $request_wallet[0]->phone_number;
        $api_key = $this->api_key;
        $template = $this->template;
        $url = "https://api.kavenegar.com/v1/$api_key/verify/lookup.json?receptor=$phoneNumber&token=$otp_code&template=$template";
        $response = wp_remote_get($url);
        $response_body = wp_remote_retrieve_body($response);
        
        /******************update transaction table*******************/
        $time_2min = strtotime("+2 minutes", strtotime($current_time));
        $otp_expiration = date('Y-m-d h:i:s', $time_2min);

        $otp_data = [
            'otp_code' => $otp_code,
            'otp_expiration' => $otp_expiration,
            'otp_wrong'=> $otp_wrong,
            'description'=>$response_body
        ];
        $result = $wpdb->update(
            $transaction_table, 
            $otp_data, 
            ['national_id' => $national_id]
        );

        if($result){
            wp_send_json_success([
                'message' => 'کد تایید با موفقیت ارسال شد',
                'success' => true,
                'tracking_code'=>$request_transaction_req[0]->tracking_code
            ]);

        }else{
            wp_send_json_error(['message' => 'خطا در ارسال کد تایید'],403);
        }

        }else{
            wp_send_json_error(['message' => 'زمان ارسال دوباره کد تایید 2 دقیقه می باشد'],403);
        }

        return;

    }

    public function pay_otp_verify($request){

        $params = $request->get_params();
        $merchant_id = sanitize_text_field($params['merchant_id']);
        $tracking_code = sanitize_text_field($params['tracking_code']);
        $otp_sended_code = sanitize_text_field($params['otp_code']);
        $order_id = sanitize_text_field($params['order_id']);
        $credit_amount = sanitize_text_field($params['credit_amount']);
        $date = sanitize_text_field($params['date']);
        $national_id = sanitize_text_field($params['national_id']);
        $sended_token = sanitize_text_field($params['token']);

        /*********************token check********************/
        $token_data = [
            'order_id' => $order_id,
            'merchant_id' => $merchant_id,
            'credit_amount' => $credit_amount,
            'national_id' => $national_id,
            'date' => $date,
        ];
        $token = $this->helper->get_token($token_data);

        if($sended_token != $token){
            wp_send_json_error(['message' => 'توکن معتبر نیست!'],403);
            return;
        } 

       /**********************check database with tracking code**********************/
        $request_transaction = $this->crud->get_transaction_request('tracking_code',$tracking_code,'%s');


        /******************compare otp code****************/
        $otp_code =  $request_transaction->otp_code;

        if($otp_code === $otp_sended_code){

            $otp_data = [
                'status'=> 'confirmed'
            ];

            $this->crud->update_transaction_request($otp_data, ['national_id' => $national_id]);
            ////// از کیف پول من کم میشود وارد پول مرچنت می شود که خودم ساختم بعد دیتابیس اپدیت می شود
            /***************update customer and merchants wallet*****************/
            $pay_amount= $request_transaction->pay_amount;  

            $customer_wallet = $this->crud->get_customer_wallet('national_id',$national_id,'%s');
            $amount_remains = $customer_wallet->amount_remains;
            $update_amounts = (int)$pay_amount - (int)$amount_remains;
            $update_wallets_fields = ['amount_remains'=>$update_amounts];
            $update_wallets_cond = ['national_id'=>$national_id];
            $update_wallets = $this->crud->update_customer_wallet($update_wallets_fields, $update_wallets_cond);

            $merchant = $this->crud->get_merchant_by('merchant_id',$merchant_id,'%s');
            $merchant_credit = $merchant->merchant_credit;
            $merchant_amount_remains = (int)$merchant_credit + (int)$pay_amount;
            $update_merchant_fields = ['merchant_credit'=>$merchant_amount_remains];
            $update_merchant_cond = ['merchant_id'=>$merchant_id];
            $update_merchant = $this->crud->update_merchant($update_merchant_fields,$update_merchant_cond);



            /*************send data***************/
            if($update_wallets && $update_merchant){
                wp_send_json_success([
                    'message' => 'تراکنش با موفقیت انجام شد.',
                    'success' => true,
                    'tracking_code'=>$request_transaction->tracking_code
                ],200);
            } else{
                wp_send_json_error(['message' => 'خطا در ایجاد تراکنش'],403);

            }
            

        }else{
             ///////////////otp wrong ckecked
            //////////// otp > 4 => otpay_wallent=>status->suspend by wallet_id 
            /////////// کیف پول شما برای 24 ساعت مسدود شد
            //////////کرون جاب هر 4 ساعت اجرا شود و 24 ساعت را چک میکند
            /******************update otp_wrong******************* */
            $otp_wrong = $request_transaction->otp_wrong; 
            if($otp_wrong < 4){
                $otp_wrong++;
                $result = $this->crud->update_transaction_request(['otp_wrong'=>$otp_wrong], ['national_id' => $national_id]);

            } else{
                $otp_data = [
                    'status'=> 'suspend'
                ];

                $this->crud->update_customer_wallet($otp_data, ['national_id' => $national_id]);

                wp_send_json_error(['message' => 'درخواست شما بیش از حد مجاز است، لطفا بعدا اقدام نمایید'],403);
                return;
            }


            
            wp_send_json_error(['message' => 'کد ارسالی اشتباه است!'],403);
        }
        
        return;



    }

    public function merchant_request($request){
        global $wpdb;
        $params = $request->get_params();
        $merchant_id = $params['merchant_id'];
        $mr_amount = $params['mr_amount'];
        $merchant = $this->crud->get_merchant_by('merchant_id',$merchant_id,'%s');
        $data= [
            'mr_id'=>$merchant_id,
            'mr_amount'=>$mr_amount,
            'mr_status'=>'pending'
        ];
        $result= $this->crud->create_merchant_request($data);


        if($result){
        wp_send_json_success([
            'message' => 'درخواست شما با موفقیت ثبت شد!',
            'success' => true,
            'merchant'=>$result,
            'mevrchant'=>$wpdb->last_error,
        ],200);
        } else {
            wp_send_json_error(['message' => 'هنگام ثبت درخواست خطایی رخ داده است.'],403);

        }

    }








    
}

 