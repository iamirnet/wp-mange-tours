<?php

function mrjahani_entries(){
    $entries = GFAPI::get_entries(0);
    $mrjahanicode = get_metadata( 'user', get_current_user_id(), "mrjahanicode", true );
    $table = '<table class="table">';
    $table .= '<thead><tr><th>عنوان</th><th>وضعیت پرداخت</th><th>ش.تراکنش</th><th>وضیعت سفارش</th><th>عملیات</th></tr></thead>';
    $table .= '<tbody>';
    foreach ($entries as $key => $value) {
        if(in_array($mrjahanicode, $value) && $value["payment_status"] == "Paid" && $mrjahanicode != null){
            $form = GFAPI::get_form($value["form_id"]);
            $value['form_title'] =  $form["title"];
            $status = isset($form['fields'][0]['id'])?$form['fields'][0]['id']:0;
            $status = $value[$status];
            $date = explode(' ',datetimetojalali($value['payment_date']));
            $table .= '<tr><td>'.$value['form_title'].'</td><td>موفق</td><td>'.$value['transaction_id'].'</td><td>'.mrStatusOrder($status).'</td><td><a class="button" target="_blank" href="entry?order='.$value['id'].'">نمایش</a></td></tr>';
            $me[] =  $value;
        }
    }
    if (!is_array($me)) {
        $table .= '<tr><td colspan="6" class="center">هیچ داده ای در این قسمت متاسفانه ثبت نشده است.</td></tr>';
    }
    $table .= '</tbody>';
    $table .= '</table>';
    return $table;
}
add_shortcode( 'mrjahani-entries', 'mrjahani_entries' );

function mrjahani_entriy(){
    if ($_GET['order']) {
        $entriy = GFAPI::get_entry($_GET['order']);
        $mrjahanicode = get_metadata( 'user', get_current_user_id(), "mrjahanicode", true );

        if(in_array($mrjahanicode, $entriy) && $entriy["payment_status"] == "Paid" && $mrjahanicode != null){
            $form = GFAPI::get_form($entriy["form_id"]);
            $me['عنوان برنامه'] =  $form["title"];
            foreach ($form["fields"] as $field) {
                if ($field->type == 'product') {
                    $me["تعداد افراد"] = $entriy[$field->inputs[2]['id']];
                }elseif ($field->type == 'list'){
                    $me[$field->label] = unserialize($entriy[$field->id]);
                } elseif ($field->type == 'checkbox' && $field->label != 'هزینه ها'){
                    foreach ($field['choices'] as $kchoice => $vchoice) {
                        $inputs = $field['inputs'];
                        $vchoice['value'] = explode(':',$vchoice['value']);
                        $level = $kchoice+1;
                        if (isset($_POST['factorNumber']) && $_POST['factorNumber'] == $level && !isset($entriy['paylevel'.$level])) {
                            $status       = $_POST['status'];
                            $transId      = $_POST['transaction'];
                            $factorNumber = $_POST['factorNumber'];
                            $message      = $_POST['message'];
                            if ($status == 1) {
                                $verify = verifyPayCup($transId);
                                if ($verify->message == "OK"){
                                    if (GFAPI::update_entry_field( $_GET['order'], 'paylevel'.$level, $transId.':'.date('Y-m-d H:i:s' ))){
                                        $message = "پرداخت {$vchoice['text']} شما به شماره تراکنش $transId با موفقیت انجام شد.";
                                        $entriy['paylevel'.$level] = $transId;
                                    }
                                }else{
                                    $message = "پرداخت شما موفقیت آمیز نبود.";
                                    //$message = '<div class="bs-shortcode-alert alert alert-success"><strong>'.$message.'</strong></div>';
                                }
                            }else{
                                $message = "پرداخت شما موفقیت آمیز نبود.";
                                //$message = '<div class="bs-shortcode-alert alert alert-danger"><strong>'.$message.'</strong></div>';
                            }
                        }
                        if ($entriy[$inputs[$kchoice]['id']] != null){
                            $choices[] = array('name' =>$vchoice['text'], 'date' => $vchoice['value'][0], 'price' => $vchoice['value'][1], 'isPay' => 'پرداخت شده', 'tran' => $entriy["transaction_id"]);
                        }else {
                            $day = MRShowAge($vchoice['value'][1],true,'dd');
                            if ($day[1] == 'روز' && $day[0] > 0){
                                foreach ($field['choices'] as $lprice) {
                                    $total += explode(':',$lprice['value'])[1];
                                }
                                $total = $me["counts"]?$total*$me["counts"]:$total;
                                $delay = $total*0.02*$day[0];
                                unset($total);
                            }
                            $paylevel = MRSPEntry($entriy["form_id"],$_GET['order'],'paylevel'.$level)[0]->meta_value;
                            $paylevel = explode(":",$paylevel)[0];
                            $choices[] = array('name' =>$vchoice['text'], 'date' => $vchoice['value'][0], 'price' => $vchoice['value'][1], 'isPay' => $paylevel?'پرداخت شده':'پرداخت نشده' , 'tran' => $paylevel?$paylevel:null,'btn' => $paylevel?false:true);
                            if (isset($delay)) {
                                $choices[]['delay'] = $delay;
                            }
                        }
                    }
                    $me[$field->label] = $choices;
                } else {
                    $me[$field->label] = $entriy[$field->id];
                }
            }
            if (isset($message)) {
                $table = json_encode($verify);
            }
            //$table .= json_encode($_POST,JSON_UNESCAPED_UNICODE);
            $table .= '<table class="table">';
            $table .= '<tbody>';
            $table .= '<tr><th>عنوان برنامه</th><td colspan="5">'.$me['عنوان برنامه'].'</td></tr>';
            $table .= '<tr><th>تعداد ستاره برنامه</th><td colspan="5">'.$me['تعداد ستاره'].'</td></tr>';
            $table .= '<tr><th>نام کامل شما</th><td colspan="5">'.$me['نام کامل'].'</td></tr>';
            $table .= '<tr><th>کد عضویت شما</th><td colspan="5">'.$me['کد عضویت'].'</td></tr>';
            $table .= '<tr><th>نوع ثبت نام</th><td colspan="5">'.$me['نوع ثبت نام'].'</td></tr>';
            if (strpos($me['نوع ثبت نام'], 'گروهی') !== false) {
                $table .= '<tr><th>لیست افراد</th><td colspan="5">';
                foreach ($me['لیست مشخصات سایر افراد'] as $member) {
                    foreach ($member as $kmember => $vmember) {
                        $table .= $kmember.':'.$vmember.' | ';
                    }
                    $table = trim($table," | ");
                    $table .= '<br>';
                }
                $table .= '</td></tr>';
            }
            $table .= '<tr><th>نوع پرداخت</th><td colspan="5">'.$me['نوع پرداخت'].'</td></tr>';
            if (strpos($me['نوع پرداخت'], 'یک') !== false) {
                $table .= '<tr><th>شماره تراکنش</th><td colspan="5">'.$entriy["transaction_id"].'</td></tr>';
            }elseif (strpos($me['نوع پرداخت'], 'چند') !== false) {
                $table .= '<tr><th>مراحل پرداخت</th><th>هزینه(تومان)</th><th>دیرکرد(تومان)</th><th>وضعیت</th><th>ش.تراکنش</th><th>عملیات</th><tr>';
                foreach ($me['مراحل پرداخت'] as $kpay => $vpay){
                    $table .= '<tr><th>'.$vpay['name'].'</th>';
                    $table .= '<td>'.$vpay['price'].'</td>';
                    $delay = $vpay['delay']?$vpay['delay']:0;
                    $table .= '<td>'.$delay.'</td>';
                    $table .= '<td>'.$vpay['isPay'].'</td>';
                    $table .= '<td>'.$vpay['tran'].'</td>';
                    $level = $kpay+1;
                    $table .= $vpay['btn']?"<td><a href=\"/payment?o={$_GET['order']}&l=$level\">پرداخت</a></td>":'<td>پرداخت</td>';
                    $table .= '</tr>';
                    if (isset($vpay['tran'])){
                        $totalprice += $vpay['price'];
                    }
                }

            }else {
                $table .= '<tr><th>شماره تراکنش</th><td colspan="5">'.$entriy["transaction_id"].'</td></tr>';
            }
            $table .= '<tr><th>وضعیت سفارش</th><td colspan="5">'.$me['وضعیت سفارش'].'</td></tr>';
            $table .= '<tr><th>وضعیت پرداخت</th><td colspan="5">موفق</td></tr>';
            $table .= '<tr><th>میزان پرداختی شما</th><td colspan="5">'.floor($totalprice).' تومان</td></tr>';
        }else {
            $table .= '<tr><td colspan="6" class="center">هیچ داده ای در این قسمت متاسفانه ثبت نشده است.</td></tr>';
        }
        $table .= '</tbody>';
        $table .= '</table>';
        return $table;
    }
}
add_shortcode( 'mrjahani-entry', 'mrjahani_entriy' );