<?php

function int_div_jahani($a, $b) {
    return (int) ($a / $b);
}

function jalali_to_gregorian_jahani($j_y, $j_m, $j_d)

{
    static $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    static $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
    $jy = $j_y - 979;
    $jm = $j_m - 1;
    $j_day_no = (365 * $jy + int_div_jahani($jy, 33) * 8 + int_div_jahani($jy % 33 + 3, 4));

    for ($i = 0; $i < $jm; ++$i) {
        $j_day_no += $j_days_in_month[$i];
    }

    $j_day_no += $j_d - 1;
    $g_day_no = $j_day_no + 79;
    $gy = (1600 + 400 * int_div_jahani($g_day_no, 146097)); # 146097 = (365 * 400 + 400 / 4 - 400 / 100 + 400 / 400)
    $g_day_no = $g_day_no % 146097;
    $leap = 1;

    if ($g_day_no >= 36525) { # 36525 = (365 * 100 + 100 / 4)
        $g_day_no --;
        $gy += (100 * int_div_jahani($g_day_no, 36524)); # 36524 = (365 * 100 + 100 / 4 - 100 / 100)
        $g_day_no = $g_day_no % 36524;
        if ($g_day_no >= 365) {
            $g_day_no ++;
        } else {
            $leap = 0;
        }
    }

    $gy += (4 * int_div_jahani($g_day_no, 1461)); # 1461 = (365 * 4 + 4 / 4)
    $g_day_no %= 1461;

    if ($g_day_no >= 366) {
        $leap = 0;
        $g_day_no --;
        $gy += int_div_jahani($g_day_no, 365);
        $g_day_no = ($g_day_no % 365);
    }

    for ($i = 0; $g_day_no >= ($g_days_in_month[$i] + ($i == 1 && $leap)); $i ++) {
        $g_day_no -= ($g_days_in_month[$i] + ($i == 1 && $leap));
    }

    return array($gy, $i + 1, $g_day_no + 1);
}

function gregorian_to_jalali_jahani($g_y, $g_m, $g_d) {
    static $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    static $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
    $gy = $g_y - 1600;
    $gm = $g_m - 1;
    $g_day_no = (365 * $gy + int_div_jahani($gy + 3, 4) - int_div_jahani($gy + 99, 100) + int_div_jahani($gy + 399, 400));

    for ($i = 0; $i < $gm; ++$i) {
        $g_day_no += $g_days_in_month[$i];
    }

    if ($gm > 1 && (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0)))
        # leap and after Feb
        $g_day_no ++;
    $g_day_no += $g_d - 1;
    $j_day_no = $g_day_no - 79;
    $j_np = int_div_jahani($j_day_no, 12053); # 12053 = (365 * 33 + 32 / 4)
    $j_day_no = $j_day_no % 12053;
    $jy = (979 + 33 * $j_np + 4 * int_div_jahani($j_day_no, 1461)); # 1461 = (365 * 4 + 4 / 4)
    $j_day_no %= 1461;

    if ($j_day_no >= 366) {
        $jy += int_div_jahani($j_day_no - 1, 365);
        $j_day_no = ($j_day_no - 1) % 365;
    }

    for ($i = 0; ($i < 11 && $j_day_no >= $j_days_in_month[$i]); ++$i) {
        $j_day_no -= $j_days_in_month[$i];
    }

    return array($jy, $i + 1, $j_day_no + 1);
}


function datetimetojalali($datetime) {
    $datetime = explode(" ",$datetime);
    $regdate = explode("-",$datetime[0]);
    $regdate = gregorian_to_jalali_jahani($regdate[0],$regdate[1],$regdate[2]);
    $datetime = $regdate[0]."-".$regdate[1]."-".$regdate[2]." ".$datetime[1];
    return $datetime;
}
function datetimetogregorian($datetime) {
    $datetime = explode(" ",$datetime);
    $regdate = explode("-",$datetime[0]);
    $regdate = jalali_to_gregorian_jahani($regdate[0],$regdate[1],$regdate[2]);
    $datetime = $regdate[0]."-".$regdate[1]."-".$regdate[2]." ".$datetime[1];
    return $datetime;
}

function MRShowAge($age, $jalali = true,$type = null) {
    $age = str_replace("/","-",$age);
    $age = str_replace(".","-",$age);
    $age = explode("-",$age);
    $age = jalali_to_gregorian_jahani($age[0],$age[1],$age[2]);
    $age = $age[0]."-".$age[1]."-".$age[2]." 00:00:00";
    return MRTimeDiff(date('Y-m-d H:i:s'),$age,$type);
}

function MRTimeDiff($time2,$time1,$type = null){
    $diff = strtotime($time2) - strtotime($time1);
    if($diff < 60 || $type == 'ss'){
        return array($diff," ثانیه");
    }
    elseif($diff < 3600 || $type == 'mm'){
        return array(round($diff / 60,0,1),"دقیقه");
    }
    elseif(($diff >= 3660 && $diff < 86400 ) || $type == 'hh'){
        return array(round($diff / 3600,0,1),"ساعت");
    }
    elseif(($diff > 86400 && $diff < 2592000) || $type == 'dd'){
        return array(round($diff / 86400,0,1),"روز");
    }
    elseif(($diff > 2592000 && $diff < 31536000) || $type == 'mh'){
        return array(round($diff / 2592000,0,1),"ماه");
    }
    elseif($diff > 31536000 || $type == 'yy'){
        return array(round($diff / 31536000,0,1),"سال");
    }
}