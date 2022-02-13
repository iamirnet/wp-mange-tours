<?php
function mrjahani_payment() {
    MRClean($_POST);MRClean($_GET);MRClean($_REQUEST);// and so on..
    if (isset($_GET['o']) && isset($_GET['l']) ) {
        $entriy = GFAPI::get_entry($_GET['o']);
        $mrjahanicode = get_metadata( 'user', get_current_user_id(), "mrjahanicode", true );
        if (!is_array($entriy['errors'])  && in_array($mrjahanicode, $entriy) && $mrjahanicode != null && $entriy["payment_status"] == "Paid") {
            $form = GFAPI::get_form($entriy["form_id"]);
            foreach ($form["fields"] as $field) {
                if ($field->type == 'checkbox' && $field->label != 'هزینه ها'){
                    foreach ($field['choices'] as $kchoice => $vchoice) {
                        $inputs = $field['inputs'];
                        if ($entriy[$inputs[$kchoice]['id']] != null){
                            $choices[] = array('name' =>$vchoice['text'], 'value' => $vchoice['value'], 'isPay' => 'پرداخت شده', 'tran' => $entriy["transaction_id"]);
                        }else {
                            $choices[] = array('name' =>$vchoice['text'], 'value' => $vchoice['value'], 'isPay' => 'پرداخت نشده' , 'tran' => null);
                        }
                    }
                    $me[$field->label] = $choices;
                }elseif ($field->label == 'نوع پرداخت' || $field->label == 'نوع ثبت نام' || $field->label == 'نوع ثبت نام'){
                    $me[$field->label] = $entriy[$field->id];
                }elseif ($field->type == 'product' && strpos($me['نوع ثبت نام'], 'گروهی') !== false) {
                    $me["counts"] = $entriy[$field->inputs[2]['id']];
                }
            }
            if (strpos($me['نوع پرداخت'], 'چند') !== false){
                if (isset($me['مراحل پرداخت'][$_GET['l']-1]) && strpos($me['مراحل پرداخت'][$_GET['l']-1]['isPay'], 'نشده') !== false) {
                    $level = $me['مراحل پرداخت'][$_GET['l']-1];
                    $level = explode(':',$level['value']);
                    $day = MRShowAge($level[0],true,'dd');
                    if ($day[1] == 'روز' && $day[0] > 0){
                        foreach ($me['مراحل پرداخت'] as $price) {
                            $total += explode(':',$price['value'])[1];
                        }
                        $total = $me["counts"]?$total*$me["counts"]:$total;
                        $delay = $total*0.02*$day[0];
                    }
                    $price = $delay?ceil(($me["counts"]?$level[1]*$me["counts"]:$level[1])+$delay):ceil($me["counts"]?$level[1]*$me["counts"]:$level[1]);
                    $pay = sendPayCup($price,"https://jahanitours.com/my-account/entry/?order={$_GET['o']}",$_GET['l']);
                    MRRTO("https://payment.cuphost.net/payment/send.php?iid={$pay->token}");
                }else{
                    MRRTO("https://jahanitours.com/my-account/entry/?order={$_GET['o']}");
                }
            }else{
                MRRTO("https://jahanitours.com/my-account/entry/?order={$_GET['o']}");
            }

        }else{
            MRRTO("https://jahanitours.com");
        }

    }else{
        MRRTO("https://jahanitours.com");
    }
}
add_shortcode( 'mrjahani-payment', 'mrjahani_payment' );
