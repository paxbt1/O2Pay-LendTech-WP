<?php


add_filter('cron_schedules', 'lendtech_wallet_cron_schedules');
function lendtech_wallet_cron_schedules($schedules) {
    $schedules['every_Four_hours'] = array(
        'interval' => 14400, // 4 hours in seconds
        'display'  => __('Every 4 Hours')
    );
    return $schedules;
}


// Schedule the cron event if it is not already scheduled
add_action('wp', 'schedule_lendtech_wallet_cron_event');
function schedule_lendtech_wallet_cron_event() {
    if (!wp_next_scheduled('lendtech_wallet_cron_event_hook')) {
        wp_schedule_event(time(), 'every_eight_hours', 'lendtech_wallet_cron_event_hook');
    }
}

// Hook the function to the moneysa_wallet cron event
add_action('lendtech_wallet_cron_event_hook', 'lendtech_check_customer_wallet');



function lendtech_check_customer_wallet(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'o2pay_wallets';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE status = %s",'suspend'));
    
    foreach($results as $r){
        $id = $r->id;
        $current_time= current_time('Y-m-d h:i:s');
        $startTime = $r->updated_at;
        $plusTime = strtotime("+1 day", strtotime($startTime));
        $endTime = date('Y-m-d h:i:s', $plusTime);
        $from_time = strtotime($endTime); 
        $to_time = strtotime($current_time); 
        $deff_time = round(($from_time - $to_time) / 60,2);
    
        if($deff_time <=0){
            $update = $wpdb->update(
                $table_name, 
                ['status'=> 'active'], 
                ['id' => $id]
            );
        
        }
    }    

    

}


// Clear the scheduled event on deactivation
register_deactivation_hook(__FILE__, 'deactivate_lendtech_wallet_cron_event');
function deactivate_moneysa_wallet_cron_event() {
    $timestamp = wp_next_scheduled('schedule_lendtech_wallet_cron_event');
    wp_unschedule_event($timestamp, 'schedule_lendtech_wallet_cron_event');
}
