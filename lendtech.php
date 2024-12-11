<?php 
// Ensure the plugin is not accessed directly
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

include_once 'includes/LendtechAjaxHandler.php';
include_once 'includes/LendTechCrudHandler.php';
include_once 'includes/SmsFunctionHandler.php';
include_once 'includes/DatabaseTables.php';
include_once 'includes/LendTechHelperFunctions.php';
include_once 'includes/admin-page/LendtechAdminPage.php';

//do_action('public_script');
//$helper=new HelperFunctions();
$crud = new LendTechCrudHandler();
$sendNotif = new SmsFunctionHandler();
$helper = new LendTechHelperFunctions();

new LendtechAjaxHandler($crud,$sendNotif,$helper);
new LendtechAdminPage($crud,$helper );
// Add script and style in plugin
// add_action( 'wp_footer','o2pay_transaction_enqueue_style');


// function o2pay_transaction_enqueue_style(): void{
//         //wp_enqueue_style('jalalidatepicker2', plugin_dir_url(__FILE__) . 'assets/css/jalalidatepicker.min.css',null, null);
// }

// Add script and style in plugin
// add_action( 'wp_footer','o2pay_transaction_script');


// function o2pay_transaction_script(){

//        // wp_enqueue_script('jalalidatepicker2', plugin_dir_url(__FILE__) . 'assets/js/jalalidatepicker.min.js', array('jquery'), null, null);
        

// }



function add_admin_script($page){
       
       if($page == 'lendtech-merchant_page_show-merchant'){
               // wp_enqueue_script('jalalidatepicker2', plugin_dir_url(__FILE__) . 'assets/js/jalalidatepicker.min.js', array('jquery'), null, null);
               // wp_enqueue_style('jalalidatepicker2', plugin_dir_url(__FILE__) . 'assets/css/jalalidatepicker.min.css',null, null);

                wp_enqueue_script('datatable-js','https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js',array( 'jquery' ), 1.1,true);
                wp_enqueue_style( 'datatable-css', 'https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css' );
                
                wp_enqueue_script('datatable-search-js','https://cdn.datatables.net/searchbuilder/1.3.4/js/dataTables.searchBuilder.min.js',array( 'jquery' ), 1.1,true);
                wp_enqueue_style( 'datatable-search-css', 'https://cdn.datatables.net/searchbuilder/1.3.4/css/searchBuilder.dataTables.min.css' );

                wp_enqueue_script('datatable-search-js','https://cdn.datatables.net/plug-ins/1.12.1/pagination/jPaginator/dataTables.jPaginator.js',array( 'jquery' ), 1.1,true);

                wp_enqueue_script('datatable-btn-js','https://cdn.datatables.net/buttons/2.2.3/js/buttons.dataTables.min.js',array( 'jquery' ), 1.1,true);
                wp_enqueue_style( 'datatable-btn-css', 'https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css' );

                wp_enqueue_script('datatable-buttons-js','https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js',array( 'jquery' ), 1.1,true);
                wp_enqueue_script('datatable-jszip-js','https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js',array( 'jquery' ), 1.1,true);
                wp_enqueue_script('datatable-pdfmake-js','https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js',array( 'jquery' ), 1.1,true);
                wp_enqueue_script('datatable-pdfmakelib-js','https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js',array( 'jquery' ), 1.1,true);
                wp_enqueue_script('datatable-html5-js','https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js',array( 'jquery' ), 1.1,true);
                wp_enqueue_script('datatable-print-js','https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js',array( 'jquery' ), 1.1,true);
                
       }    

}
    
    add_action( 'admin_enqueue_scripts', 'add_admin_script');
    
