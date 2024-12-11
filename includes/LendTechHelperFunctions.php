<?php


class LendTechHelperFunctions {
    
    public static function convert_to_jalali_date($gregorian_date) {
        $date = new DateTime($gregorian_date, new DateTimeZone('Asia/Tehran'));
    
        $fmt = new IntlDateFormatter(
            'fa_IR@calendar=persian',
            IntlDateFormatter::FULL, //date format
            IntlDateFormatter::NONE, //time format
            'Asia/Tehran',
            IntlDateFormatter::TRADITIONAL,
            "yyyy/MM/dd"
        );
    
        return $fmt->format($date);
    }
     
    public static function convert_to_gregorian_date($jalali_date) {
        // Create an IntlDateFormatter for the Jalali (Persian) calendar
        $fmt = new IntlDateFormatter(
            'fa_IR@calendar=persian',
            IntlDateFormatter::FULL, // date format
            IntlDateFormatter::NONE, // time format
            'Asia/Tehran',
            IntlDateFormatter::TRADITIONAL,
            "yyyy/MM/dd"
        );
    
        // Parse the Jalali date
        $timestamp = $fmt->parse($jalali_date);
    
        // Convert the timestamp to a Gregorian date
        $gregorian_date = (new DateTime('@' . $timestamp))->setTimezone(new DateTimeZone('Asia/Tehran'))->format('Y-m-d');
    
        return $gregorian_date;
    }

    public static function get_token($data){
        $order_id = $data['order_id'];
        $merchant_id = $data['merchant_id'];
        $credit_amount = $data['credit_amount'];
        $national_id = $data['national_id'];
        $date = $data['date'];

        if(isset($order_id) && isset($merchant_id) && isset($credit_amount) && isset($national_id) && isset($date)){
            $algo = 'sha256';
            $key_data= $order_id.$merchant_id.$credit_amount.$national_id.$date;
            $secret_key = 'my_secret_key';
            $token = hash_hmac($algo,$key_data,$secret_key);
            return $token;
        }

        
    }

    public static function rial_to_toman($rial){

        return $rial/10;
        
    }

}
