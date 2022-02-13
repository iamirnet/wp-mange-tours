<?php

function mrStatusOrder($n) {
    switch ($n) {
        case 0:
            $status = "درحال بررسی";
            break;
        case 1:
            $status = "تایید شده";
            break;
        case 2:
            $status = "تایید نشده";
            break;
        default:
            $status = "درحال بررسی";
            break;
    }
    return $status;
}

function sendPayCup($amount, $redirect, $factorNumber=null) {
    $api = '974d0d90b52732f3ac0629ccaab468d41f028128522633';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://payment.cuphost.net/payment/send.php');
    curl_setopt($ch, CURLOPT_POSTFIELDS,"api=$api&amount=$amount&CallBack=$redirect&factorNumber=$factorNumber");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($ch);
    $error    = curl_errno($ch);

    curl_close($ch);

    $output = $error ? FALSE : json_decode($response);

    return $output;
}

function verifyPayCup($transId) {
    $api = '974d0d90b52732f3ac0629ccaab468d41f028128522633';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://payment.cuphost.net/payment/verify.php');
    curl_setopt($ch, CURLOPT_POSTFIELDS, "api=$api&transId=$transId");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($ch);
    $error    = curl_errno($ch);

    curl_close($ch);

    $output = $error ? FALSE : json_decode($response);

    return $output;
}