<?php
/* Template Name: mrjahani-register */
session_start();
get_header();
$error = array();
$mobile = isset($_POST['mobile'])?$_POST['mobile']:null;
$code = isset($_POST['code'])?$_POST['code']:null;
if (isset($mobile) && $code == null) {
    if (preg_match("/^09[0-9]{9}$/", $mobile)){
        if(mobile_exists($mobile)){
            $code = verify_send_sms($mobile);
            if (preg_match("/^[0-9]{5}$/", $code)){
                $_SESSION['verify_code'] = $code;
                $_SESSION['mobile'] = $mobile;
                $_SESSION["btn"] = "بررسی و ادامه";
                unset($code);
            }
        }else {
            unset($_SESSION['mobile']);
            $error[] = 'شماره موبایل وارد شده وجود دارد لطفا شماره موبایل دیگری را وارد کنید.';
        }
    }else {
        unset($_SESSION['mobile']);
        $error[] = "شماره موبایل وارد شده معتبر نیست";
    }
}
if ($code == $_SESSION['verify_code'] && isset($code)) {
    if (preg_match("/^[0-9]{5}$/", $code)) {
        $_SESSION['verified'] = true;
        $_SESSION["btn"] = "ثبت نام";
    }else{
        unset($_SESSION['verify_code']);
        $error[] = "کد تأیید وارد شده ممعتبر نیست";
    }
}

if (isset($_POST["signup"]) && $_SESSION['verified'] == true) {
    global $wpdb, $PasswordHash, $current_user, $user_ID;
    $pwd1 = $wpdb->escape(trim($_POST['pwd1']));
    $pwd2 = $wpdb->escape(trim($_POST['pwd2']));
    $first_name = $wpdb->escape(trim($_POST['first_name']));
    $last_name = $wpdb->escape(trim($_POST['last_name']));
    $fathername = $wpdb->escape(trim($_POST['fathername']));
    $birthdate = $wpdb->escape(trim($_POST['birthdate']));
    $nationalcode = $wpdb->escape(trim($_POST['nationalcode']));
    $gender = $wpdb->escape(trim($_POST['gender']));
    $booked_phone = $wpdb->escape(trim($_POST['booked_phone']));
    $mobile = $wpdb->escape(trim($_SESSION['mobile']));
    $address = $wpdb->escape(trim($_POST['address']));
    $reagent = $wpdb->escape(trim($_POST['reagent']));
    $username = $wpdb->escape(trim($_POST['username']));
    if( $pwd1 == "" ||
        $pwd2 == "" ||
        $first_name == "" ||
        $last_name == ""||
        $birthdate == ""||
        $nationalcode == ""||
        $gender == ""||
        $mobile == "") {
        $error[] = 'لطفا تمامی فیلدهای الزامی را تکمیل نمایید.';
    }else if(username_exists($username)){
        $error[] = 'نام کاربری وارد شده وجود دارد و یا معتبر نیست. باید از حروف انگلیسی و اعداد استفاده کنید';
    }else if($pwd1 <> $pwd2 ){
        $error[] = 'کلمات عبور با یکدیگر مطابقت ندارند.';
    } else {
        $mrjahanicode = createmrjahanicode($gender);
        $user_id = wp_insert_user(
            array (
                'first_name' => apply_filters('pre_user_first_name', $first_name),
                'last_name' => apply_filters('pre_user_last_name', $last_name),
                'user_pass' => apply_filters('pre_user_user_pass', $pwd1),
                'user_login' => apply_filters('pre_user_user_login', $mrjahanicode),
                'mobile' => apply_filters('pre_user_mobile', $mobile),
                'role' => 'subscriber'
            )
        );
        if( is_wp_error($user_id) ) :
            $error[] = 'خطایی در ارسال درخواست نام نویسی شما رخ داده است!';
        else :
            do_action('user_register', $user_id);
            update_user_meta( $user_id, 'nationalcode', $nationalcode );
            $birthdate = str_replace("/","-",$birthdate);
            update_user_meta( $user_id, 'birthdate', $birthdate );
            update_user_meta( $user_id, 'gender', $gender );
            update_user_meta( $user_id, 'reagent', $reagent );
            update_user_meta( $user_id, 'mrjahanicode', $mrjahanicode );
            update_user_meta( $user_id, 'verified', true );
            unset($_SESSION['verified']);
            unset($_SESSION['verify_code']);
            unset($_SESSION['mobile']);
            unset($_SESSION['btn']);
            $success = 1;
        endif;

    }
}
?>
    <div class="standard_wrapper">
        <div class="page_content_wrapper">
            <div class="inner">
                <div style="margin:auto;width:60%">
                    <h2 class="ppb_title">ثبت نام در سایت</h2>
                    <?php if ($error != null){
                        ?>
                    <div id="error_register" class="alert_box error"><i class="fa fa-exclamation-circle alert_icon"></i>
                        <div class="alert_box_msg">
                            <?php
                            foreach ($error as $value){
                                echo $value."<br>";
                            }
                            ?>
                        </div>
                        <a href="#" class="close_alert" data-target="error_register">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                    <?php } elseif ($success != null){
                        send_userinfo($mrjahanicode,$_POST['pwd1'],$mobile);?>
                    <div id="success_register" class="alert_box success"><i class="fa fa-exclamation-circle alert_icon"></i>
                        <div class="alert_box_msg">
                            <span>نام نویسی شما با موفقیت به اتمام رسید، از شما ممنویم.</span>
                            <span>جهت ورود به محیط کاربریتان <a href="/my-account/">اینجا</a> را کلیک کنید</span>
                        </div>
                        <a href="#" class="close_alert" data-target="success_register">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                    <?php } ?>
                    <div role="form" dir="rtl" lang="fa-IR">
                        <div class="screen-reader-response"></div>
                        <form action="#" method="post" class="wpcf7-form" novalidate="novalidate">
                            <p>
                                <?php if (isset($_SESSION['mobile']) == false && $success == null){ ?>
                                    <label>شماره تلفن همراه (الزامی)
                                        <br><span class="wpcf7-form-control-wrap tel-729"><input type="tel" name="mobile" size="40" class="wpcf7-form-control wpcf7-text wpcf7-tel wpcf7-validates-as-required wpcf7-validates-as-tel" required aria-required="true" aria-invalid="false" placeholder="09121234567"></span> </label>
                                    <br>
                                <?php }elseif ($_SESSION["btn"] == "بررسی و ادامه" && $success == null){ ?>
                                    <label>کد تایید  (الزامی)
                                        <br><span class="wpcf7-form-control-wrap tel-729"><input type="number" name="code" size="6" class="wpcf7-form-control wpcf7-text wpcf7-tel wpcf7-validates-as-required wpcf7-validates-as-tel" required aria-required="true" aria-invalid="false" placeholder="12345"></span> </label>
                                    <br>
                                <?php }elseif (isset($_SESSION['verified']) && $success == null){ ?>
                                    <label> نام (الزامی)
                                        <br> <span class="wpcf7-form-control-wrap first_name"><input type="text" name="first_name" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" required aria-required="true" aria-invalid="false" placeholder="علی"></span> </label>
                                    <br>
                                    <label> نام خانوادگی (الزامی)
                                        <br> <span class="wpcf7-form-control-wrap last_name"><input type="text" name="last_name" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" required aria-required="true" aria-invalid="false" placeholder="محمدی"></span> </label>
                                    <br>
                                    <label> تاریخ تولد (الزامی)
                                        <br> <span class="wpcf7-form-control-wrap birthdate"><input type="text" name="birthdate" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" required aria-required="true" aria-invalid="false" pattern="[0-9]{4}/[0-9]{2}/[0-9]{2}" placeholder="1379/10/09"></span> </label>
                                    <br>
                                    <label> کد ملی (الزامی)
                                        <br> <span class="wpcf7-form-control-wrap nationalcode"><input type="text" name="nationalcode" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" required aria-required="true" aria-invalid="false" pattern="[0-9]{10}" placeholder="123456789"></span> </label>
                                    <br>
                                    <label>جنسیت  (الزامی)
                                        <br>
                                        <span class="wpcf7-form-control-wrap gender">
                                            <select name="gender" class="wpcf7-form-control wpcf7-select wpcf7-validates-as-required" required aria-required="true" aria-invalid="false">
                                                <option value="">انتخاب کنید.</option>
                                                <option value="5">مرد</option>
                                                <option value="7">زن</option>
                                            </select>
                                        </span>
                                    </label>
                                    <br>
                                    <label>موبایل  (الزامی)
                                        <br> <span class="wpcf7-form-control-wrap mobile"><input type="text" size="40" disabled class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" required aria-required="true" aria-invalid="false" placeholder="09121234567" value="<?php echo $_SESSION['mobile']; ?>"></span> </label>
                                    <br>
                                    <label>کد معرف (اختیاری)
                                        <br> <span class="wpcf7-form-control-wrap mobile"><input type="text" size="40" name="reagent" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false"></span> </label>
                                    <br>
                                    <hr>
                                    <br>
                                    <label>رمز عبور  (الزامی)
                                        <br> <span class="wpcf7-form-control-wrap pwd1"><input type="password" name="pwd1" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" required aria-required="true" aria-invalid="false"></span> </label>
                                    <br>
                                    <label>تکرار رمز عبور  (الزامی)
                                        <br> <span class="wpcf7-form-control-wrap pwd2"><input type="password" name="pwd2" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" required aria-required="true" aria-invalid="false"></span> </label>
                                    <br>
                                <?php } if ($success == null) { ?>
                                <input type="submit" value="<?php echo isset($_SESSION["btn"])?$_SESSION["btn"]:"ارسال کد تایید"; ?>" name="<?php echo isset($_SESSION['verified'])?"signup":null; ?>" class="wpcf7-form-control wpcf7-submit">
                                <?php } ?>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php get_footer(); ?>