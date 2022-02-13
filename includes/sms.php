<?php
function send_sms_cup($n,$m){
    if(substr($n, 0, 1)=='+'){
        $n = substr($n,1);
    }

    if(substr($n, 0, 2)=='00'){
        $n = substr($n,2);
    }
    if(substr($n, 0, 1)=='0'){
        $n = substr($n,1);
    }
    $url = "login.niazpardaz.ir/SMSInOutBox/SendSms?username=amir&password=45454545&from=10009611&to=".$n."&text=".urlencode($m);
    $handler = curl_init($url);
    curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($handler);

    if ($response == 'SendWasSuccessful' ) {
        return array('success'=>true);
    } else {
        return array('success'=>false);
    }
   // return true;
}
function verify_send_sms($mobile) {
    $code = rand(12345,65535);
    $text = "كد فعال سازی: ".$code."\nتاج کیهان\njahanitours.com";
    if (send_sms_cup($mobile,$text) == true)
        return $code;
    else
        return false;
}
function send_userinfo($c,$p,$m){
    $text = "باتشکر از شما (تاج کیهان)\n کدعضویت \ نام کاربری: $c\nرمز عبور شما: $p";
    if (send_sms_cup($m,$text) == true)
        return true;
    else
        return false;
}

function send_status_order($s,$t,$m){
    $text = "سفارش $t به وضعیت $s تغییر کرد.";
    if (send_sms_cup($m,$text) == true)
        return true;
    else
        return false;
}