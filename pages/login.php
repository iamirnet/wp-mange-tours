<?php
/* Template Name: mrjahani-login */
session_start();
get_header();
$btn = isset($_SESSION["btn"])?$_SESSION["btn"]:"ارسال کد تایید";
$mobile = isset($_POST['mobile'])?$_POST['mobile']:null;
$code = isset($_POST['code'])?$_POST['code']:null;

if ($code == $_SESSION['verify_code'] && $code != null) {
    $_SESSION['verified'] = true;
    $_SESSION["btn"] = "ثبت نام";
} else {
    if (isset($mobile) && $code == null) {
        $code = verify_send_sms($mobile);
        if ($code != false){
            $_SESSION['verify_code'] = $code;
            $_SESSION['mobile'] = $mobile;
            $_SESSION["btn"] = "بررسی و ادامه";
        }
    }
}

?>

<?php get_footer(); ?>