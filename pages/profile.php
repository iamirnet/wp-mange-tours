<?php

/*if (preg_match("/^[ ؀-ۿa-zA-Z]*$/","Amir Hossein")){
    echo 'yes';
}*/

//echo faverifyen("علی!@#$%^&*");


/*echo mobile_exists("09146941147",1);*/
/*$status = get_metadata( 'user', get_current_user_id(), "status", true );
echo wp_get_current_user()->user_status;*/
//$user = MRVKCode("980150002");
/*$user = get_user_by( 'login', '961250058' );
$user_id = get_current_user_id();
$user = get_user_meta( $user_id );
var_dump($user);*/
/*if ("جهانی آلانق" != get_metadata( 'user', $user_id, "first_name", true )) {
    echo 'yes';
}*/

//echo '2012-03-02'-'2012-02-20';


/*$time2 = date('Y-m-d H:i:s');
$time1 = '1999-12-29 00:00:00';*/

/*function timeDiff($time2,$time1,$year =  false){
    $diff = strtotime($time2) - strtotime($time1);
    if($year){
        return round($diff / 31536000,0,1).' سال قبل';
    }
    elseif($diff < 60){
        return $diff.' ثانیه قبل';
    }
    elseif($year){
        return round($diff / 60,0,1).' دقیقه قبل';
    }
    elseif($diff < 3600){
        return round($diff / 60,0,1).' دقیقه قبل';
    }
    elseif($diff >= 3660 && $diff < 86400){
        return round($diff / 3600,0,1).' ساعت قبل';
    }
    elseif($diff > 86400){
        return round($diff / 86400,0,1).' روز قبل';
    }
}*/

/*echo MRShowAge("1379/10/09");
*/
/*$user_id = get_current_user_id();
$user = get_user_meta( 'user', $user_id );
foreach ($user as $kmeta => $vmeta) {
    $user[$kmeta] = $vmeta[0];
}
$username = wp_get_current_user()->user_login;
$status = wp_get_current_user()->user_status;

echo json_encode(get_metadata( 'user', get_current_user_id(), "verified", true ));*/

//echo createmrjahanicode(7);
//echo MRUInfo("980150006");
/*$gender = ;
$date = gregorian_to_jalali_jahani(date("Y"),date("m"),date("d"));
$date[0] = str_split($date[0], 2);
if ($date[1] < 10) {
    $date[1] = '0'.$date[1];
}
$code = (int) $date[0][1].$date[1].$gender."0000";

echo $code;*/

/*echo send_sms_cup('09195889045',"سلام، با عرض پوزش کد عضویت شما به 980150007 تغییر کرد. با تشکر تاج کیهان");
echo send_sms_cup('09100588731',"سلام، با عرض پوزش کد عضویت شما به 980150006 تغییر کرد. با تشکر تاج کیهان");
echo send_sms_cup('09192517600',"سلام، با عرض پوزش کد عضویت شما به 980150008 تغییر کرد. با تشکر تاج کیهان");
echo send_sms_cup('09302551723',"سلام، با عرض پوزش کد عضویت شما به 980170005 تغییر کرد. با تشکر تاج کیهان");*/

header("Content-type: application/json; charset=utf-8");

//echo json_encode(GFAPI::update_entry(array("jahani" => 205545),$_GET['e']));
//echo json_encode(GFAPI::update_entry_field( $_GET['e'], "jahani", $value ));
/*if (isset($_GET['e'])) {
    $entriy = GFAPI::get_entry($_GET['e']);
    echo json_encode($entriy,JSON_UNESCAPED_UNICODE);
}elseif (isset($_GET['f'])){
    $form = GFAPI::get_form($_GET['f']);
    echo json_encode($form,JSON_UNESCAPED_UNICODE);
}
*/

//echo json_encode(publisher_get_view( 'loop', 'listing-modern-grid-5-item-small', '', false ), JSON_UNESCAPED_UNICODE);
// echo MRVKCode('961070005');
/*$verifySmsDate = get_the_author_meta( 'verifySmsDate', 8 )?get_the_author_meta( 'verifySmsDate', 8 ):date('Y-m-d H:i:s');
$verifySmsDateDiff = MRTimeDiff(date('Y-m-d H:i:s'),$verifySmsDate,"ss");*/
//echo json_encode(MRGetEntries(12),JSON_UNESCAPED_UNICODE);
//echo MRCNum("۰۹۱۹۶۶۵۵۲۲۰");
//MRRTOPOST(get_option('home')."/wp-login.php",array("log" => "961250058","pwd" => "manonakon1379","submit" => true, "rememberme" => "forever", "redirect_to" => get_option('home')));
//MRRTOlogin("961250058","manonakon1379");

/*function MRGPass($length = 5) {
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    return substr(str_shuffle($chars), 0, $length);
}*/


/*$user = MRUInfo("0372074685");
$forgotSmsDate = get_the_author_meta( 'forgotSmsDate', $user["user_id"] )?get_the_author_meta( 'forgotSmsDate', $user["user_id"] ):null;
$forgotSmsDateDiff = MRTimeDiff(date('Y-m-d H:i:s'),$forgotSmsDate,"ss");
echo json_encode($forgotSmsDateDiff);*/
?>

