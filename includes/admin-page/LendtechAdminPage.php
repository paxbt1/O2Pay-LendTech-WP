<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class LendtechAdminPage{

    private $crud;
    private $helper;
    public function __construct(LendTechCrudHandler $crud, LendTechHelperFunctions $helper){

        $this->crud = $crud;
        $this->helper = $helper;
        add_action( 'admin_menu', [$this,'add_admin_page'] );


    }

   

    public function add_admin_page(){
        add_menu_page (
            'LendTech',
            'پذیرنده ها',
            'activate_plugins',
            'lend-tech-merchant',
            array($this, 'add_admin_menu_page'),
            'dashicons-superhero',
            8

        );

        add_submenu_page(
            'lend-tech-merchant',
            'show merchant',
            'مشاهده پذیرنده ها',
            'activate_plugins',
            'show-merchant',
            array($this,'show_merchant_admin_page')
        );
        

        add_menu_page (
            'financial-panel',
            'پنل مالی',
            'activate_plugins',
            'lend-tech-financial',
            array($this, 'add_admin_financial_panel'),
            'dashicons-money-alt',
            9
        );

        add_submenu_page(
            'lend-tech-financial',
            'show requests',
            'مشاهده تمامی تراکنش ها',
            'activate_plugins',
            'show-requests',
            array($this,'show_merchants_requests')
        );
    }

    public function show_merchants_requests(){
        echo 'gg';
    }

    public function add_admin_financial_panel(){
        global $wpdb;
        $results = $this->crud->get_merchant_request();

        ?>
        <style>
            .d-flex{
                display: flex;
                align-items: center;
                flex-wrap: wrap;
            }
            .col-4{  
                flex: 0 0 auto;
                width: calc(25% - 20px);
                padding: 0 8px
            }
            .mb-20{
                margin-bottom: 20px;
            }
            .mt-40{
                margin-top: 40px;
            }
            .justify-between{
                justify-content: space-between;
            }

            .form-controler__lable{
                display:block;
                margin-bottom: 10px;
            }
            .form-controler{
                margin-bottom: 15px;
            }
            .w-25{
                width: 25%;
            }

            </style>
            
            <div class="container mt-40">

            <?php
            if(isset($_GET['id'])){
                $id = $_GET['id'];
                $results = $this->crud->get_merchant_request('id',$id,'%d');
                $merchant_desc = $results->mr_description; 
                $mr_acc_id = $results->mr_acc_id; 
                $mr_status = $results->mr_status; 
                var_dump(value: $mr_status);

            ?>
                <h1>اطلاعات درخواستی پذیرنده</h1>
                <p>جهت تایید اطلاعات زیر را پر کنید</p>
                <form method="POST">
                    <?php wp_nonce_field('save_merchant_request', 'merchant_request_nonce'); ?>
                    
                    <div class="form-controler">
                        <label class="form-controler__lable" for="merchant_desc">توضیحات پرداخت</label>
                        <textarea class="w-25" name="merchant_desc" id="merchant_desc" rows="6" ><?php if(isset($merchant_desc)) echo $merchant_desc; else echo ''; ?></textarea>
                    </div>
                    <div class="form-controler">
                        <label class="form-controler__lable" for="merchant_acc">شماره سند حسابداری</label>
                        <input class="w-25" name="merchant_acc" id="merchant_acc" type="text" value="<?php if(isset($mr_acc_id)) echo $mr_acc_id; else echo ''; ?>">
                    </div>
                    <div class="form-controler">
                        <label class="form-controler__lable" for="merchant_receipt_img">سند واریز</label>
                        <input class="w-25" type="file" name="merchant_receipt_img" id="merchant_receipt_img">
                    </div>
                    <div class="form-controler">
                        <label class="" for="merchant_status">پرداخت را تایید می کنید؟</label>
                        <input class="w-25" type="checkbox" id="merchant_status" name="merchant_status" <?php if(isset($mr_status) && $mr_status =='pending') echo 'checked'; else echo ''; ?>>
                    </div>
                    <div class="form-controler mt-40"><input type="submit" name="submit" id="submit" class="button button-primary" value="ذخیره تغییرات"></div>
                </form>
            <?php
                return;
            }
            
            ?>
                <h1>اطلاعات مالی</h1>
                <p>درخواست های پذیرنده ها</p>
                <div class="d-flex">
                    
                    <?php
                     foreach($results as $s){
                        $merchant = $this->crud->get_merchant_by('merchant_id',$s->mr_id,'%s');
                        $merchantID = $merchant->merchant_id;
                    ?>

                    <div class="col-4">
                        <div class="card">
                            <div class="d-flex justify-between mb-20">
                                <div class="">نام پذیرنده</div>
                                <div class=""><strong><?php echo $merchant->merchant_name ?></strong></div>
                            </div>
                            <div class="d-flex justify-between mb-20">
                                <div class="">مقدار درخواست</div>
                                <div class=""><strong><?php echo number_format_i18n($s->mr_amount) ?></strong></div>
                            </div>
                            <div class="d-flex justify-between mb-20">
                                <div class="">وضعیت درخواست</div>
                                <div class=""><strong><?php if($s->mr_status =='pending') echo 'فعال'; else echo 'تایید شده'; ?></strong></div>
                            </div>
                            <div class="d-flex justify-between mb-20">
                                <div class="">تاریخ درخواست</div>
                                <div class=""><strong><?php echo $this->helper->convert_to_jalali_date($s->mr_verify_date) ?></strong></div>
                            </div>
                            <div class="d-flex justify-between">
                                <div class=""></div>
                                <div class=""><a href="?page=lend-tech-financial&id=<?php echo $s->id ?>" class="button button-primary"><strong>مشاهده</strong></a></div>
                            </div>
                        </div>
                    </div>

                    <?php  } ?>
                </div>

                
            </div>



        <?php
    }

    

    public function show_merchant_admin_page(){?>
        <style>
            .form-controler{
                margin-bottom: 15px;
            }
            .form-controler__lable{
                display:block;
                margin-bottom: 10px;
            }
            .w-25{
                width: 25%;
            }
            .text-danger{
                color: #C6011F;
                font-size: 15px;
            }
            
            .text-success{
                color: #006400;
                font-size: 15px;
            }
            .dataTables_wrapper .dataTables_filter{
                margin-bottom:10px;
            }
       
            table.dataTable thead th{
                text-align:right !important
            }
            .discount-card_wrapper{
                padding: 2rem 1rem;
            }
        </style>   

        <div class="container mt-40">
            <?php 

                if(isset($_GET['id'])){
                    $id = $_GET['id'];
                    $merchant = $this->crud->get_merchant_by('id',$id , placeholders: '%d');
                    $merchantID = $merchant->merchant_id;
                    $merchanKey = $merchant->merchant_key;
                    $merchantCat = $merchant->merchant_cat;
                    $merchantCommission = $merchant->merchant_commission;
                    $merchantCredit = $merchant->merchant_credit;
                    $merchantName = $merchant->merchant_name;
                    $merchantNationalID = $merchant->merchant_national_id;


                    if(isset($_POST['submit']) && isset($_POST['lendetch_merchant_update_nonce']) && wp_verify_nonce($_POST['lendetch_merchant_update_nonce'], 'update_lendetch_merchant')){
                       
                        $update_fields =[
                            'merchant_id'=> $_POST['merchant_id'],
                            'merchant_key'=> $_POST['merchant_key'],
                            'merchant_cat'=> $_POST['merchant_cat'],
                            'merchant_commission'=> $_POST['merchant_commission'],
                            'merchant_credit'=> $_POST['merchant_credit'],
                            'merchant_name'=> $_POST['merchant_name'],
                            'merchant_national_id'=> $_POST['merchant_national_id'],
                            
                        ];
                        $conditions = ['id'=> $id];
                        global $wpdb;
                        $update_marchant = $this->crud->update_merchant($update_fields, $conditions);
                        
                        //if($update_marchant == 0){
                            $merchant = $this->crud->get_merchant_by('id',$id , placeholders: '%d');
                            $merchantID= $merchant->merchant_id;
                            $merchanKey = $merchant->merchant_key;
                            $merchantCat = $merchant->merchant_cat;
                            $merchantCommission = $merchant->merchant_commission;
                            $merchantCredit = $merchant->merchant_credit;
                            $merchantName = $merchant->merchant_name;
                            $merchantNationalID = $merchant->merchant_national_id;

                            echo '<div class="text-success"><strong>پذیرنده با موفقیت بروزرسانی شد!</strong></div>';
                            

                            
                        //}

                    
                    }
                    ?>

                    <h1>ویرایش اطلاعات پذیرنده ها</h1>
                    <p>در قسمت زیر اطلاعات پذیرنده را ویرایش کنید</p>

                        <form method="POST">
                            <?php wp_nonce_field('update_lendetch_merchant', 'lendetch_merchant_update_nonce'); ?>
                            <div class="form-controler">
                                <label class="form-controler__lable" for="merchant_id">آیدی پذیرنده</label>
                                <input class="w-25" id="merchant_id" name="merchant_id" type="text" value="<?php if(isset($merchantID)) echo $merchantID; else echo ''; ?>">
                            </div>
                            <div class="form-controler">
                                <label class="form-controler__lable" for="merchant_key">کلید پذیرنده</label>
                                <input class="w-25" id="merchant_key" name="merchant_key" type="text" value="<?php if(isset($merchanKey)) echo $merchanKey; else echo ''; ?>">
                            </div>
                            <div class="form-controler">
                                <label class="form-controler__lable" for="merchant_cat">دسته بندی پذیرنده</label>
                                <input class="w-25" name="merchant_cat" name="merchant_cat" type="text" value="<?php if(isset($merchantCat)) echo $merchantCat; else echo ''; ?>">
                            </div>
                            <div class="form-controler">
                                <label class="form-controler__lable" for="merchant_commission">کمیسیون پذیرنده</label>
                                <input class="w-25" name="merchant_commission" id="merchant_commission" type="text" value="<?php if(isset($merchantCommission)) echo $merchantCommission; else echo ''; ?>">
                            </div>
                            <div class="form-controler">
                                <label class="form-controler__lable" for="merchant_credit">اعتبار پذیرند (به تومان)</label>
                                <input class="w-25" name="merchant_credit" id="merchant_credit" type="text" value="<?php if(isset($merchantCredit)) echo $merchantCredit; else echo ''; ?>">
                            </div>
                            <div class="form-controler">
                                <label class="form-controler__lable" for="merchant_name">نام پذیرنده</label>
                                <input class="w-25" name="merchant_name" id="merchant_name" type="text" value="<?php if(isset($merchantName)) echo $merchantName; else echo ''; ?>">
                            </div>
                            <div class="form-controler">
                                <label class="form-controler__lable" for="merchant_national_id">کد ملی پذیرنده</label>
                                <input class="w-25" name="merchant_national_id" id="merchant_national_id" type="text" value="<?php if(isset($merchantNationalID)) echo $merchantNationalID; else echo ''; ?>">
                            </div>
                            <div class="form-controler"><input type="submit" name="submit" id="submit" class="button button-primary" value="ذخیره تغییرات"></div>
                        </form>
                    <?php

                        




                    return;
                }
            
            ?>

            <h1>اطلاعات پذیرنده ها</h1>
            <p>در صورت نیاز به اصلاح اطلاعات پذیرنده، برای هر پذیرنده روی دکمه مشاهده کلیک نمایید!</p>

            <?php
       
        
        $get_merchants = $this->crud->get_merchant_by();
        
    ?>  

            <div class="discount-card_wrapper">
                <table id="enq-price" class="wp-list-table widefat fixed striped table-view-list pages">
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>آیدی پذیرنده</th>
                            <th>کلید پذیرنده</th>
                            <th>نام پذیرنده</th>
                            <th>دسته بندی پذیرنده</th>
                            <th>کمسیون پذیرنده</th>
                            <th>اعتبار پذیرنده</th>
                            <th>وضعیت پذیرنده</th>
                            <th>تاریخ ایجاد</th>
                            <th>تاریخ آپدیت</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                        
                        foreach($get_merchants as $index => $m): 
                        
                        ?>
                        <tr>
                           <td><?php echo $index + 1 ?></td>
                           <td><?php echo $m->merchant_id ?></td>
                           <td><?php echo $m->merchant_key ?></td>
                           <td><?php echo $m->merchant_name ?></td>
                           <td><?php echo $m->merchant_cat ?></td>
                           <td><?php echo $m->merchant_commission ?></td>
                           <td><?php echo $m->merchant_credit ?></td>
                           <td><?php echo $m->merchant_status ?></td>
                           <td><?php echo $this->helper->convert_to_jalali_date($m->created_at) ?></td>
                           <td><?php echo $this->helper->convert_to_jalali_date( $m->updated_at) ?></td>
                           <td><a href="?page=show-merchant&id=<?php echo $m->id ?>">مشاهده</a></td>

                        </tr>
                       
                    <?php 
                   
                    endforeach; ?>

                    </tbody>
                </table>
            </div>

        </div>


        <script>
            jQuery(document).ready(function($) {
                $('#enq-price').DataTable( {
                    ordering:false,
                    dom: 'Bfrtip',
                    buttons: [
                    {
                        extend: 'excel',
                        text: 'دریافت لیست',
                        title: 'لیست استعلام قیمت',
                    }
                ]
                } );
            } );
        </script>
       
    <?php }



    public function add_admin_menu_page(){ ?>
    <style>
        .form-controler{
            margin-bottom: 15px;
        }
        .form-controler__lable{
            display:block;
            margin-bottom: 10px;
        }
        .w-25{
            width: 25%;
        }
        .text-danger{
            color: #C6011F;
            font-size: 15px;
        }
        
        .text-success{
            color: #006400;
            font-size: 15px;
        }

    </style>    


    <div class="container mt-40">

    <h1>ثبت اطلاعات پذیرنده ها</h1>
    
    <p>لطفا اطلاعات زیر را برای اضافه کردن پذیرنده جدید پر کنید.</p>

    <form method="POST">
    <?php wp_nonce_field('save_lendetch_merchant', 'lendetch_merchant_nonce'); ?>
        <div class="form-controler">
            <label class="form-controler__lable" for="merchant_id">آیدی پذیرنده</label>
            <input class="w-25" id="merchant_id" name="merchant_id" type="text">
        </div>
        <div class="form-controler">
            <label class="form-controler__lable" for="merchant_key">کلید پذیرنده</label>
            <input class="w-25" id="merchant_key" name="merchant_key" type="text">
        </div>
        <div class="form-controler">
            <label class="form-controler__lable" for="merchant_cat">دسته بندی پذیرنده</label>
            <input class="w-25" name="merchant_cat" name="merchant_cat" type="text">
        </div>
        <div class="form-controler">
            <label class="form-controler__lable" for="merchant_commission">کمیسیون پذیرنده</label>
            <input class="w-25" name="merchant_commission" id="merchant_commission" type="text">
        </div>
        <div class="form-controler">
            <label class="form-controler__lable" for="merchant_credit">اعتبار پذیرند (به تومان)</label>
            <input class="w-25" name="merchant_credit" id="merchant_credit" type="text">
        </div>
        <div class="form-controler">
            <label class="form-controler__lable" for="merchant_name">نام پذیرنده</label>
            <input class="w-25" name="merchant_name" id="merchant_name" type="text">
        </div>
        <div class="form-controler">
            <label class="form-controler__lable" for="merchant_national_id">کد ملی پذیرنده</label>
            <input class="w-25" name="merchant_national_id" id="merchant_national_id" type="text">
        </div>
        <div class="form-controler"><input type="submit" name="submit" id="submit" class="button button-primary" value="ذخیره تغییرات"></div>
        

        
    </form>


    <?php

        if(isset($_POST['submit']) && isset($_POST['lendetch_merchant_nonce']) && wp_verify_nonce($_POST['lendetch_merchant_nonce'], 'save_lendetch_merchant')){
            

            $merchant_id = $_POST['merchant_id'];
            $merchant_key = $_POST['merchant_key'];
            $merchant_cat = $_POST['merchant_cat'];
            $merchant_commission = $_POST['merchant_commission'];
            $merchant_credit = $_POST['merchant_credit'];
            $merchant_name = $_POST['merchant_name'];
            $merchant_national_id = $_POST['merchant_national_id'];

            if(empty($merchant_id)){
                echo '<div class="text-danger"><strong>آیدی پذیرنده خالی است!</strong></div>';
                return;
            }elseif(empty($merchant_cat)){
                echo '<div class="text-danger"><strong>دسته بندی پذیرنده خالی است!</strong></div>';
                return;
            }elseif(empty($merchant_commission)){
                echo '<div class="text-danger"><strong>دسته بندی پذیرنده خالی است!</strong></div>';
                return;
            }elseif(empty($merchant_credit)){
                echo '<div class="text-danger"><strong>اعتبار پذیرنده خالی است!</strong></div>';
                return;
            }elseif(empty($merchant_name)){
                echo '<div class="text-danger"><strong>دسته بندی پذیرنده خالی است!</strong></div>';
                return;
            }elseif(empty($merchant_national_id)){
                echo '<div class="text-danger"><strong>کد ملی پذیرنده خالی است!</strong></div>';
                return;
            }elseif(empty($merchant_key)){
                echo '<div class="text-danger"><strong>کلید پذیرنده خالی است!</strong></div>';
                return;
            }elseif(!is_int($merchant_credit)){
                echo '<div class="text-danger"><strong>مقدار اعتبار پذیرنده قابل قبول نیست!</strong></div>';

            }

            $get_merchant = $this->crud->get_merchant_by('merchant_id',$merchant_id , '%s');;
            if($get_merchant){
                echo '<div class="text-danger"><strong>پذیرنده فوق قبلا ثبت نام شده است!</strong></div>';
                return;
            }

            $merchant_data = [
                'merchant_id' =>$merchant_id,
                'merchant_key' =>$merchant_id,
                'merchant_cat'=>$merchant_cat,
                'merchant_commission'=>$merchant_commission,
                'merchant_credit'=>$merchant_credit,
                'merchant_name'=>$merchant_name,
                'merchant_national_id'=>$merchant_national_id,
                'merchant_status' => 'active',

            ];
            global $wpdb;
            
            $insert_merchant = $this->crud->add_merchant($merchant_data);
            if($insert_merchant){
                echo '<div class="text-success"><strong>ثبت نام پذیرنده با موفقیت انجام شد!</strong></div>';
                return;
            }else{
                echo '<div class="text-danger"><strong>خطا! هنگام ثبت پذیرنده خطایی رخ داده است!</strong></div>';
                return;
            }



            
        }
    
    
    
    ?>

    </div>    

        
    




<?php   

    }
    






}








		
// $FILES = $_FILES['Avatar'];
// if($FILES['name'] != ''){
//     require_once(ABSPATH . 'wp-admin/includes/image.php');
//     require_once(ABSPATH . 'wp-admin/includes/file.php');
//     require_once(ABSPATH . 'wp-admin/includes/media.php');
    
//     $type = $FILES['type'];
//     $size = $FILES['size'];
//     $maxsize = 100000;
//     if($size > $maxsize){
//         $error_array[0] = 'سایز تصویر باید کمتر از 100 کیلوبایت باشد.';
//     }
//     if($type != 'image/jpeg' && $type != 'image/jpg' && $type != 'image/png'){
//         $error_array[1] = 'این پسوند معتبر نیست.';
//     }
//     if(count($error_array) == 0){
//         $attach_id = media_handle_upload('Avatar',$userID);
//         $post = get_post($attach_id);
//         $guid = $post->guid;
//         update_user_meta($userID,'avatar',$guid);

//     }
// }
