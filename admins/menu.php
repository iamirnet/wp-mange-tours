<?php

add_action('admin_menu', 'mr_reports_page');
function mr_reports_page(){
    add_menu_page('گزارشات', 'گزارشات', 'manage_mr_reports', 'mr-reports', 'my_menu_output','dashicons-portfolio', 12 );
    /*$hook = add_submenu_page('mr-reports', 'برنامه ها', 'ثبت نام کننده گان', 'manage_options', 'mr-reports' , 'my_render_list_page');
    add_action( "load-$hook", 'add_options' );*/
    //add_submenu_page('mr-reports', 'برنامه ها', 'ثبت نام کننده گان', 'manage_options', 'mr-reports' , 'mr_register_true_list');
    add_submenu_page('mr-reports', 'برنامه ها', 'ثبت نام کننده گان', 'manage_mr_reports', 'mr-reports' , 'mr_register_true_list');
    //add_submenu_page('mr-reports', '2برنامه ها', 'ثبت نام کننده گان2', 'manage_mr_reports', 'mr-reports2' , 'mr_register_true_list2');

}
function wporg_simple_role_caps()
{
    // gets the simple_role role object
    $role = get_role('administrator');

    // add a new capability
    $role->add_cap('manage_mr_reports', true);
}

// add simple_role capabilities, priority must be after the initial role definition
add_action('init', 'wporg_simple_role_caps', 11);
function mr_register_true_list2() {
    include 'test.php';
}
/*function add_options() {
    global $myListTable;
    $option = 'per_page';
    $args = array(
        'label' => 'Books',
        'default' => 10,
        'option' => 'books_per_page'
    );
    add_screen_option( $option, $args );
    $myListTable = new My_Example_List_Table();
}*/

function mr_register_true_list() {
    global $wpdb; //This is used only if making any database queries
    if (isset($_GET['report'])) {
        $form = GFAPI::get_form($_GET['report']);
        if (isset($_POST['doaction']) && isset($_POST['items']) && $_POST['action'] != '-1' && $_POST['action'] != 'excel') {
            foreach ($_POST['items'] as $item) {
                $item = explode("-",$item);
                MRUPEntry($_POST['action'],$_GET['report'],$item[0],$item[1]);
                //send_status_order($_POST['action'],$item[3],$item[2]);
            }
        }

        $sqlentries = MRGetEntries($_GET['report']);
        foreach ($sqlentries as $item) {
            $entries[] = GFAPI::get_entry($item->id);
        }
        $totalPrice = 0;
        $counts = 0;
        $totalcosts = 0;
        $body = '';
        $i = 0;
        $p = 0;
        foreach ($entries as $key => $value){
            if($value["payment_status"] == "Paid" && $value["status"] == 'active'){
                foreach ($form["fields"] as $field) {
                    if ($field->type == 'product') {
                        if ($value[$field->inputs[2]['id']]) {
                            $me["counts"] = $value[$field->inputs[2]['id']];
                        }
                    }elseif ($field->type == 'list'){
                        $me[$field->label] = unserialize($value[$field->id]);
                    } elseif ($field->type == 'checkbox' && $field->label != 'هزینه ها'){
                        foreach ($field['choices'] as $kchoice => $vchoice) {
                            $inputs = $field['inputs'];
                            if ($value[$inputs[$kchoice]['id']] != null){
                                $choices[] = array('name' =>$vchoice['text'], 'value' => $vchoice['value'], 'isPay' => 'پرداخت شده', 'tran' => $value["transaction_id"]);
                            }else {
                                $choices[] = array('name' =>$vchoice['text'], 'value' => $vchoice['value'], 'isPay' => 'پرداخت نشده' , 'tran' => null);
                            }
                        }
                        $me[$field->label] = $choices;
                    }elseif ($field->label == 'هزینه ها' && !is_array($costs)) {
                        foreach ($field['choices'] as $kchoice => $vchoice) {
                            $costs[] = array('title' => $vchoice['text'], 'amount' => $vchoice['value']?$vchoice['value']:0);
                            $totalcosts += $vchoice['value'];
                        }
                    } else {
                        $me[$field->label] = $value[$field->id];
                        if (strpos($field->label, 'وضعیت') !== false){
                            $data[$i]['status'] = $field->id;
                            $personal[$p]['status'] = $field->id;
                        }
                    }
                }
                $user = MRUInfo($me['کد عضویت']);

                if (strpos($me['نوع ثبت نام'], 'شخصی') !== false) {
                    $personal[$p]['entry_id'] = $value["id"];
                    $personal[$p]['mrjahanicode'] = $user['mrjahanicode'];
                    $personal[$p]['first_name'] = $user['first_name'];
                    $personal[$p]['last_name'] = $user['last_name'];
                    $personal[$p]['nationalcode'] = $user['nationalcode'];
                    $personal[$p]['birthdate'] = $user['birthdate'];
                    $personal[$p]['age'] = MRShowAge($user['birthdate'])[0].MRShowAge($user['birthdate'])[1];
                    $personal[$p]['mobile'] = $user['mobile'];
                    $pcounts++;
                    //$personal[$p]['rider'] = $me["نام و نام خانوادگی راننده"];
                    $personal[$p]['carcode'] = $me["شماره پلاک دقیق خودرو"];
                    $personal[$p]['transaction'] =  $value["transaction_id"];
                    $personal[$p]['orderstatus'] = $me['وضعیت سفارش'];
                    $personal[$p]['amounts'] = floor($value["payment_amount"]);
                    $ptotalPrice += (int) $value["payment_amount"];
                    foreach ($me['مشخصات سرنشینان'] as $member) {
                        $p++;
                        $personal[$p]['checkbox'] = 'no';
                        $personal[$p]['mrjahanicode'] = $user['mrjahanicode'];
                        foreach ($member as $kmember => $vmember) {
                            if ($kmember == "نام"){
                                $personal[$p]['first_name'] = $vmember;
                            }elseif(strpos($kmember, 'خانوادگی')){
                                $personal[$p]['last_name'] = $vmember;
                            }elseif (strpos($kmember, 'ملی')) {
                                $personal[$p]['nationalcode'] = $vmember;
                            }elseif(strpos($kmember, 'تولد')){
                                $personal[$p]['birthdate'] = $vmember;
                                $personal[$p]['age'] = MRShowAge($vmember)[0].MRShowAge($vmember)[1];
                            }else{
                                $personal[$p][$kmember] = $vmember;
                            }
                        }
                        $personal[$p]['mobile'] =  '';
                        $personal[$p]['typereg'] = '';
                        $personal[$p]['counts'] = '';
                        $personal[$p]['transaction'] =  '';
                        $personal[$p]['orderstatus'] = '';
                        $personal[$p]['paystatus'] = '';
                        $personal[$p]['amounts'] = '';

                    }
                    $p++;
                }else{
                    //if (!is_array(MrArraySearch($data,$user['nationalcode']))) {
                        $data[$i]['entry_id'] = $value["id"];
                        $data[$i]['mrjahanicode'] = $user['mrjahanicode'];
                        $data[$i]['first_name'] = $user['first_name'];
                        $data[$i]['last_name'] = $user['last_name'];
                        $data[$i]['nationalcode'] = $user['nationalcode'];
                        $data[$i]['birthdate'] = $user['birthdate'];
                        $data[$i]['age'] = MRShowAge($user['birthdate'])[0].MRShowAge($user['birthdate'])[1];
                        $data[$i]['mobile'] = $user['mobile'];
                        $data[$i]['typereg'] = $me['نوع ثبت نام'];
                        $data[$i]['counts'] = $me["counts"];
                        if ($me['مشخصات سرنشینان']) {
                            $data[$i]['counts'] += count($me['مشخصات سرنشینان'])+1;
                        }
                        $counts += (int) $data[$i]['counts'];
                        $data[$i]['transaction'] =  $value["transaction_id"];
                        $data[$i]['orderstatus'] = $me['وضعیت سفارش'];
                        $data[$i]['paystatus'] = "موفق";
                        $data[$i]['amounts'] = floor($value["payment_amount"]);
                        $totalPrice += (int) $value["payment_amount"];
                        if (strpos($me['نوع ثبت نام'], 'گروهی') !== false) {
                            foreach ($me['لیست مشخصات سایر افراد'] as $member) {
                                $i++;
                                $data[$i]['checkbox'] = 'no';
                                $data[$i]['mrjahanicode'] = $user['mrjahanicode'];
                                foreach ($member as $kmember => $vmember) {
                                    if ($kmember == "نام"){
                                        $data[$i]['first_name'] = $vmember;
                                    }elseif(strpos($kmember, 'خانوادگی')){
                                        $data[$i]['last_name'] = $vmember;
                                    }elseif (strpos($kmember, 'ملی')) {
                                        $data[$i]['nationalcode'] = $vmember;
                                    }elseif(strpos($kmember, 'تولد')){
                                        $data[$i]['birthdate'] = $vmember;
                                        $data[$i]['age'] = MRShowAge($vmember)[0].MRShowAge($vmember)[1];
                                    }elseif (strpos($kmember, 'موبایل')) {
                                        $data[$i]['mobile'] = $vmember;
                                    }else{
                                        $data[$i][$kmember] = $vmember;
                                    }
                                }
                                $data[$i]['typereg'] = '';
                                $data[$i]['counts'] = '';
                                $data[$i]['transaction'] =  '';
                                $data[$i]['orderstatus'] = '';
                                $data[$i]['paystatus'] = '';
                                $data[$i]['amounts'] = '';
                            }
                        }
                        $i++;
                   // }
                }

            }

        }
        if($_POST['action'] == 'excel' && isset($_POST['items']))
        {
            foreach ($_POST['items'] as $kitem => $vitem) {
                $item = explode("-",$vitem);
                $items[$kitem] = $item[0];
            }
            MRRTOPOST("https://jahanitours.com/wp-admin/admin-ajax.php?action=csv_pull&report={$_GET['report']}",array('status' =>  true,'items' => implode(',',$items),'ahja' => '2f>B&E))8W8M6J5JDkRM+([,p#rR<vFu[gTd%:kj53ub~<jc4;aRT;]dB%}*L)4_Nbk/$h8y+L,&=nr=./2P*2qkUbA3{E:Y]mTmn==['));
        }
        ?>
        <div class="wrap">
            <h2>گزارش <?php echo $form['title']?></h2>
            <br>
            <?php
            //echo print_r($_POST);
            ?>
            <form method="post">
                <h3>لیست هزینه های برنامه</h3>
                <table class="widefat fixed" cellspacing="0">
                    <tbody>
                    <?php
                    $bcost = '';
                    $costs[] = array('title' => 'مجموع هزینه ها  ( تومان )', 'amount' => number_format($totalcosts));
                    $costs[] = array('title' => 'کل مبالغ پرداختی ( تومان )', 'amount' => number_format($totalPrice));
                    $costs[] = array('title' => 'کل مبالغ پرداختی ماشین های شخصی ( تومان )', 'amount' => number_format($ptotalPrice));
                    $costs[] = array('title' => 'باقی مانده  ( تومان )', 'amount' => number_format(($ptotalPrice+$totalPrice)-$totalcosts));
                    $costs[] = array('title' => 'کل افراد شرکت کننده', 'amount' => $counts);
                    $costs[] = array('title' => 'تعداد ماشین های شخصی', 'amount' => $pcounts);
                    foreach ($costs as $kcost => $vcost) {
                        $class = $kcost % 2?"":"class=\"alternate\"";
                        $bcost .= "<tr $class valign=\"top\">";
                        $bcost .= '<th class="column-columnname">'.$vcost['title'].'</th>';
                        $bcost .= '<td class="column-columnname">'.$vcost['amount'].'</td>';
                        $bcost .= '</tr>';
                    }

                        echo $bcost;
                    ?>
                    </tbody>
                </table>
                <br>
                <h3>لیست ثبت نامی های برنامه</h3>
                <div class="tablenav bottom">
                    <div class="alignleft actions bulkactions">
                        <label for="bulk-action-selector-top" class="screen-reader-text">انتخاب کار دسته‌جمعی</label><select name="action" id="bulk-action-selector-top">
                            <option value="-1">کارهای دسته‌جمعی</option>
                            <option value="درحال بررسی">علامت گذاری به عنوان در حال بررسی</option>
                            <option value="تایید شده">علامت گذاری به عنوان تایید شده</option>
                            <option value="تایید نشده">علامت گذاری به عنوان تایید نشده</option>
                            <option value="excel">دانلود فایل اکسل</option>
                        </select>
                        <input type="submit" name="doaction" class="button action" value="اجرا">
                    </div>
                </div>
                <table class="wp-list-table widefat fixed striped posts">
                    <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-1">انتخاب همه</label>
                            <input id="cb-select-all-1" type="checkbox">
                        </td>
                        <th scope="col" id="mrjahanicode" class="manage-column column-mrjahanicode column-primary sortable desc" style="padding:0 !important;">
                            کد عضویت
                        </th>
                        <th scope="col" id="first_name" class="manage-column column-first-name">
                            نام
                        </th>
                        <th scope="col" id="last_name" class="manage-column column-last-name">
                            نام خانوادگی
                        </th>
                        <th scope="col" id="nationalcode" class="manage-column column-nationalcode">
                            کد ملی
                        </th>
                        <th scope="col" id="birthdate" class="manage-column column-date column-birthdate">
                            تاریخ تولد
                        </th>
                        <th scope="col" id="age" class="manage-column column-age">
                            سن
                        </th>
                        <th scope="col" id="mobile" class="manage-column column-mobile">
                            موبایل
                        </th>
                        <th scope="col" id="typereg" class="manage-column column-typereg">
                            نوع ثبت نام
                        </th>
                        <th scope="col" id="counts" class="manage-column column-counts">
                            تعداد افراد
                        </th>
                        <th scope="col" id="transaction" class="manage-column column-transaction">
                            تراکنش
                        </th>
                        <th scope="col" id="orderstatus" class="manage-column column-orderstatus">
                            وضعیت سفارش
                        </th>
                        <th scope="col" id="paystatus" class="manage-column column-paystatus">
                            وضعیت پرداخت
                        </th>
                        <th scope="col" id="amounts" class="manage-column column-amounts">
                            پرداختی
                        </th>
                    </tr>
                    </thead>

                    <tbody id="the-list">
                    <?php
                        foreach ($data as $kdata => $vdata) {
                            if (isset($vdata['first_name'])) {
                                $body .= "<tr id=\"report-$kdata\" class=\"iedit author-other level-0 report-$kdata type-post format-standard hentry\">";
                                if ($vdata['checkbox'] != 'no') {
                                    $box = "<input id=\"cb-select-301\" type=\"checkbox\" name=\"items[]\" value=\"{$vdata['entry_id']}-{$vdata['status']}-{$vdata['mobile']}-{$vdata['transaction']}\">";
                                }else
                                    $box = '';
                                $body .= "<th scope=\"row\" class=\"check-column\">$box</th>";
                                $body .= "<td class=\"title column-title has-row-actions column-primary report-mrjahanicode\" data-colname=\"کد عضویت\"><span>{$vdata['mrjahanicode']}</span><button type=\"button\" class=\"toggle-row\"><span class=\"screen-reader-text\">نمایش جزئیات بیشتر</span></button></td>";
                                $body .= "<td class=\"first-name column-first-name\" data-colname=\"نام\">{$vdata['first_name']}</td>";
                                $body .= "<td class=\"last-name column-last-name\" data-colname=\"نام خانوادگی\">{$vdata['last_name']}</td>";
                                $body .= "<td class=\"nationalcode column-nationalcode\" data-colname=\"کد ملی\">{$vdata['nationalcode']}</td>";
                                $body .= "<td class=\"birthdate column-birthdate\" data-colname=\"تاریخ تولد\">{$vdata['birthdate']}</td>";
                                $body .= "<td class=\"age column-age\" data-colname=\"سن\">{$vdata['age']}</td>";
                                $body .= "<td class=\"mobile column-mobile\" data-colname=\"موبایل\">{$vdata['mobile']}</td>";
                                $body .= "<td class=\"typereg column-typereg\" data-colname=\"نوع ثبت نام\">{$vdata['typereg']}</td>";
                                $body .= "<td class=\"counts column-counts\" data-colname=\"تعداد افراد\">{$vdata['counts']}</td>";
                                $body .= "<td class=\"transaction column-transaction\" data-colname=\"تراکنش\">{$vdata['transaction']}</td>";
                                $body .= "<td class=\"orderstatus column-orderstatus\" data-colname=\"وضعیت سفارش\">{$vdata['orderstatus']}</td>";
                                $body .= "<td class=\"paystatus column-paystatus\" data-colname=\"وضعیت پرداخت\">{$vdata['paystatus']}</td>";
                                $body .= "<td class=\"amounts column-amounts\" data-colname=\"پرداختی\">{$vdata['amounts']}</td>";
                                $body .= "</tr>";
                            }
                        }
                        echo $body;
                    ?>
                    </tbody>

                    <tfoot>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-2">
                                انتخاب همه
                            </label>
                            <input id="cb-select-all-2" type="checkbox">
                        </td>
                        <th scope="col" class="manage-column column-mrjahanicode column-primary sortable desc" style="padding:0 !important;">
                            <span>کد عضویت</span>
                        </th>
                        <th scope="col" id="first_name" class="manage-column column-first-name">
                            نام
                        </th>
                        <th scope="col" id="last_name" class="manage-column column-last-name">
                            نام خانوادگی
                        </th>
                        <th scope="col" id="nationalcode" class="manage-column column-nationalcode">
                            کد ملی
                        </th>
                        <th scope="col" id="birthdate" class="manage-column column-date column-birthdate">
                            تاریخ تولد
                        </th>
                        <th scope="col" id="age" class="manage-column column-age">
                            سن
                        </th>
                        <th scope="col" id="mobile" class="manage-column column-mobile">
                            موبایل
                        </th>
                        <th scope="col" id="typereg" class="manage-column column-typereg">
                            نوع ثبت نام
                        </th>
                        <th scope="col" id="counts" class="manage-column column-counts">
                            تعداد افراد
                        </th>
                        <th scope="col" id="transaction" class="manage-column column-transaction">
                            تراکنش
                        </th>
                        <th scope="col" id="orderstatus" class="manage-column column-orderstatus">
                            وضعیت سفارش
                        </th>
                        <th scope="col" id="paystatus" class="manage-column column-paystatus">
                            وضعیت پرداخت
                        </th>
                        <th scope="col" id="amounts" class="manage-column column-amounts">
                            پرداختی
                        </th>
                    </tr>
                    </tfoot>

                </table>
                <h4>لیست ماشین های شخصی</h4>
                <table class="wp-list-table widefat fixed striped posts">
                    <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-1">انتخاب همه</label>
                            <input id="cb-select-all-1" type="checkbox">
                        </td>
                        <th scope="col" id="mrjahanicode" class="manage-column column-mrjahanicode column-primary sortable desc" style="padding:0 !important;">
                            کد عضویت
                        </th>
                        <th scope="col" id="first_name" class="manage-column column-first-name">
                            نام
                        </th>
                        <th scope="col" id="last_name" class="manage-column column-last-name">
                            نام خانوادگی
                        </th>
                        <th scope="col" id="nationalcode" class="manage-column column-nationalcode">
                            کد ملی
                        </th>
                        <th scope="col" id="birthdate" class="manage-column column-date column-birthdate">
                            تاریخ تولد
                        </th>
                        <th scope="col" id="age" class="manage-column column-age">
                            سن
                        </th>
                        <th scope="col" id="mobile" class="manage-column column-mobile">
                            موبایل
                        </th>
                        <th scope="col" id="carcode" class="carcode-column column-counts">
                            پلاک
                        </th>
                        <th scope="col" id="transaction" class="manage-column column-transaction">
                            تراکنش
                        </th>
                        <th scope="col" id="orderstatus" class="manage-column column-orderstatus">
                            وضعیت سفارش
                        </th>
                        <th scope="col" id="amounts" class="manage-column column-amounts">
                            پرداختی
                        </th>
                    </tr>
                    </thead>

                    <tbody id="the-list">
                    <?php
                    foreach ($personal as $kpersonal => $vpersonal) {
                        if (isset($vpersonal['first_name'])) {
                            $bpersonal .= "<tr id=\"report-$vpersonal\" class=\"iedit author-other level-0 report-$vpersonal type-post format-standard hentry\">";
                            if ($vpersonal['checkbox'] != 'no') {
                                $box = "<input id=\"cb-select-301\" type=\"checkbox\" name=\"items[]\" value=\"{$vpersonal['entry_id']}-{$vpersonal['status']}-{$vpersonal['mobile']}-{$vpersonal['transaction']}\">";
                            }else
                                $box = '';
                            $bpersonal .= "<th scope=\"row\" class=\"check-column\">$box</th>";
                            $bpersonal .= "<td class=\"title column-title has-row-actions column-primary report-mrjahanicode\" data-colname=\"کد عضویت\"><span>{$vpersonal['mrjahanicode']}</span><button type=\"button\" class=\"toggle-row\"><span class=\"screen-reader-text\">نمایش جزئیات بیشتر</span></button></td>";
                            $bpersonal .= "<td class=\"first-name column-first-name\" data-colname=\"نام\">{$vpersonal['first_name']}</td>";
                            $bpersonal .= "<td class=\"last-name column-last-name\" data-colname=\"نام خانوادگی\">{$vpersonal['last_name']}</td>";
                            $bpersonal .= "<td class=\"nationalcode column-nationalcode\" data-colname=\"کد ملی\">{$vpersonal['nationalcode']}</td>";
                            $bpersonal .= "<td class=\"birthdate column-birthdate\" data-colname=\"تاریخ تولد\">{$vpersonal['birthdate']}</td>";
                            $bpersonal .= "<td class=\"age column-age\" data-colname=\"سن\">{$vpersonal['age']}</td>";
                            $bpersonal .= "<td class=\"mobile column-mobile\" data-colname=\"موبایل\">{$vpersonal['mobile']}</td>";
                            $bpersonal .= "<td class=\"carcode column-counts\" data-colname=\"پلاک\">{$vpersonal['carcode']}</td>";
                            $bpersonal .= "<td class=\"transaction column-transaction\" data-colname=\"تراکنش\">{$vpersonal['transaction']}</td>";
                            $bpersonal .= "<td class=\"orderstatus column-orderstatus\" data-colname=\"وضعیت سفارش\">{$vpersonal['orderstatus']}</td>";
                            $bpersonal .= "<td class=\"amounts column-amounts\" data-colname=\"پرداختی\">{$vpersonal['amounts']}</td>";
                            $bpersonal .= "</tr>";
                        }

                    }
                    echo $bpersonal;
                    ?>
                    </tbody>

                    <tfoot>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-2">
                                انتخاب همه
                            </label>
                            <input id="cb-select-all-2" type="checkbox">
                        </td>
                        <th scope="col" class="manage-column column-mrjahanicode column-primary sortable desc" style="padding:0 !important;">
                            <span>کد عضویت</span>
                        </th>
                        <th scope="col" id="first_name" class="manage-column column-first-name">
                            نام
                        </th>
                        <th scope="col" id="last_name" class="manage-column column-last-name">
                            نام خانوادگی
                        </th>
                        <th scope="col" id="nationalcode" class="manage-column column-nationalcode">
                            کد ملی
                        </th>
                        <th scope="col" id="birthdate" class="manage-column column-date column-birthdate">
                            تاریخ تولد
                        </th>
                        <th scope="col" id="age" class="manage-column column-age">
                            سن
                        </th>
                        <th scope="col" id="mobile" class="manage-column column-mobile">
                            موبایل
                        </th>
                        <th scope="col" id="carcode" class="carcode-column column-counts">
                            پلاک
                        </th>
                        <th scope="col" id="transaction" class="manage-column column-transaction">
                            تراکنش
                        </th>
                        <th scope="col" id="orderstatus" class="manage-column column-orderstatus">
                            وضعیت سفارش
                        </th>
                        <th scope="col" id="amounts" class="manage-column column-amounts">
                            پرداختی
                        </th>
                    </tr>
                    </tfoot>

                </table>
            </form>

        </div>


        <?php
    }else{
        $query = "SELECT * FROM `ahja_gf_form` WHERE `title` LIKE '%برنامه%' ORDER BY id DESC ";
        $totalitems = $wpdb->get_results($query);
        $body = '';
        foreach ($totalitems as $key => $value){
            $value->is_active = isset($value->is_active)?'فعال':'غیرفعال';
            if($key % 2){
                $body .= "<tr valign=\"top\">
                <th class=\"check-column\" scope=\"row\"></th>
                <td class=\"column-columnname\"><a href=\"https://jahanitours.com/wp-admin/admin.php?page=mr-reports&report=$value->id\" target='_blank'>$value->title</a></td>
            </tr>";
            }else{
                $body .= "<tr class=\"alternate\" valign=\"top\">
                <th class=\"check-column\" scope=\"row\"></th>
                <td class=\"column-columnname\"><a href=\"https://jahanitours.com/wp-admin/admin.php?page=mr-reports&report=$value->id\" target='_blank'>$value->title</a></td>
            </tr>";
            }
        }
        ?>
        <div class="wrap">
            <h2>گزارش  برنامه ها</h2>
            <br>
            <?php
            //echo json_encode($totalitems);
            ?>
            <table class="widefat fixed" cellspacing="0">
                <thead>
                <tr>
                    <th id="cb" class="manage-column column-cb check-column" scope="col"></th>
                    <th id="columnname" class="manage-column column-columnname" scope="col">عنوان برنامه</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th class="manage-column column-cb check-column" scope="col"></th>
                    <th class="manage-column column-columnname" scope="col">عنوان برنامه</th>
                </tr>
                </tfoot>

                <tbody>

                <?php
                echo $body;
                ?>
                </tbody>
            </table>

        </div>


        <?php
    }
}

function ns_contact_form_csv_pull() {
    $items = $_POST['items'];
    $items = explode(',',$items);
    //print_r($items);
    if ($_POST['ahja'] == '2f>B&E))8W8M6J5JDkRM+([,p#rR<vFu[gTd%:kj53ub~<jc4;aRT;]dB%}*L)4_Nbk/$h8y+L,&=nr=./2P*2qkUbA3{E:Y]mTmn==[' && isset($_POST['items'])){
        $form = GFAPI::get_form($_GET['report']);
       // $entries = GFAPI::get_entries($items);
        foreach ($items as $item) {
            $entries[] = GFAPI::get_entry($item);
        }
        $totalPrice = 0;
        $counts = 0;
        $totalcosts = 0;
        $data[0] = array('title' => 'گزارش '.$form['title']);
        $personal[0] = array('title' => 'گزارش ماشین های شخصی');
        $data[1] = array('کد عضویت','نام','نام خانوادگی','کد ملی','تاریخ تولد','سن','موبایل','نوع ثبت نام','تعداد افراد','ش.تراکنش','وضعیت سفارش','وضعیت پرداخت','پرداختی (تومان)');
        $personal[1] = array('کد عضویت','نام','نام خانوادگی','کد ملی','تاریخ تولد','سن','موبایل','پلاک','ش.تراکنش','وضعیت سفارش','پرداختی (تومان)');
        $i = 2;
        $p = 2;
        foreach ($entries as $key => $value){
            if($value["payment_status"] == "Paid" && $value["status"] == 'active'){
                foreach ($form["fields"] as $field) {
                    if ($field->type == 'product') {
                        if ($value[$field->inputs[2]['id']]) {
                            $me["counts"] = $value[$field->inputs[2]['id']];
                        }
                    }elseif ($field->type == 'list'){
                        $me[$field->label] = unserialize($value[$field->id]);
                    }elseif ($field->label == 'هزینه ها' && !is_array($costs)) {
                        foreach ($field['choices'] as $kchoice => $vchoice) {
                            $costs[] = array('title' => $vchoice['text'], 'amount' => $vchoice['value']?$vchoice['value']:0);
                            $totalcosts += $vchoice['value'];
                        }
                    } elseif ($field->type == 'checkbox' && $field->label != 'هزینه ها'){
                        foreach ($field['choices'] as $kchoice => $vchoice) {
                            $inputs = $field['inputs'];
                            if ($value[$inputs[$kchoice]['id']] != null){
                                $choices[] = array('name' =>$vchoice['text'], 'value' => $vchoice['value'], 'isPay' => 'پرداخت شده', 'tran' => $value["transaction_id"]);
                            }else {
                                $choices[] = array('name' =>$vchoice['text'], 'value' => $vchoice['value'], 'isPay' => 'پرداخت نشده' , 'tran' => null);
                            }
                        }
                        $me[$field->label] = $choices;
                    } else {
                        $me[$field->label] = $value[$field->id];
                        if (strpos($field->label, 'وضعیت') !== false){
                            $status = $field->id;
                        }
                    }
                }
                $user = MRUInfo($me['کد عضویت']);
                if (strpos($me['نوع ثبت نام'], 'شخصی') !== false) {
                    $personal[$p]['mrjahanicode'] = $user['mrjahanicode'];
                    $personal[$p]['first_name'] = $user['first_name'];
                    $personal[$p]['last_name'] = $user['last_name'];
                    $personal[$p]['nationalcode'] = $user['nationalcode'];
                    $personal[$p]['birthdate'] = $user['birthdate'];
                    $personal[$p]['age'] = MRShowAge($user['birthdate'])[0].MRShowAge($user['birthdate'])[1];
                    $personal[$p]['mobile'] = $user['mobile'];
                    $personal[$p]['counts'] = $me["تعداد افراد حاظر"];
                    /*if ($me['مشخصات سرنشینان']) {
                        $personal[$p]['counts'] += count($me['مشخصات سرنشینان']);
                    }*/
                    $pcounts++;
                    //$personal[$p]['rider'] = $me["نام و نام خانوادگی راننده"];
                    $personal[$p]['carcode'] = $me["شماره پلاک دقیق خودرو"];
                    $personal[$p]['transaction'] =  $value["transaction_id"];
                    $personal[$p]['orderstatus'] = $me['وضعیت سفارش'];
                    $personal[$p]['amounts'] = floor($value["payment_amount"]);
                    $ptotalPrice += (int) $value["payment_amount"];
                    foreach ($me['مشخصات سرنشینان'] as $member) {
                        $p++;
                        $personal[$p]['mrjahanicode'] = $user['mrjahanicode'];
                        foreach ($member as $kmember => $vmember) {
                            if ($kmember == "نام"){
                                $personal[$p]['first_name'] = $vmember;
                            }elseif(strpos($kmember, 'خانوادگی')){
                                $personal[$p]['last_name'] = $vmember;
                            }elseif (strpos($kmember, 'ملی')) {
                                $personal[$p]['nationalcode'] = $vmember;
                            }elseif(strpos($kmember, 'تولد')){
                                $personal[$p]['birthdate'] = $vmember;
                                $personal[$p]['age'] = MRShowAge($vmember)[0].MRShowAge($vmember)[1];
                            }else{
                                $personal[$p][$kmember] = $vmember;
                            }
                        }
                        $personal[$p]['mobile'] =  '';
                        $personal[$p]['typereg'] = '';
                        $personal[$p]['counts'] = '';
                        $personal[$p]['transaction'] =  '';
                        $personal[$p]['orderstatus'] = '';
                        $personal[$p]['paystatus'] = '';
                        $personal[$p]['amounts'] = '';

                    }
                    $p++;
                }else{
                    $data[$i]['mrjahanicode'] = $user['mrjahanicode'];
                    $data[$i]['first_name'] = $user['first_name'];
                    $data[$i]['last_name'] = $user['last_name'];
                    $data[$i]['nationalcode'] = $user['nationalcode'];
                    $data[$i]['birthdate'] = $user['birthdate'];
                    $data[$i]['age'] = MRShowAge($user['birthdate'])[0].MRShowAge($user['birthdate'])[1];
                    $data[$i]['mobile'] = $user['mobile'];
                    $data[$i]['typereg'] = $me['نوع ثبت نام'];
                    $data[$i]['counts'] = $me["counts"];
                    if ($me['مشخصات سرنشینان']) {
                        $data[$i]['counts'] += count($me['مشخصات سرنشینان'])+1;
                    }
                    $counts += (int) $data[$i]['counts'];
                    $data[$i]['transaction'] =  $value["transaction_id"];
                    $data[$i]['orderstatus'] = $me['وضعیت سفارش'];
                    $data[$i]['paystatus'] = "موفق";
                    $data[$i]['amounts'] = floor($value["payment_amount"]);
                    $totalPrice += (int) $value["payment_amount"];
                    if (strpos($me['نوع ثبت نام'], 'گروهی') !== false) {
                        foreach ($me['لیست مشخصات سایر افراد'] as $member) {
                            $i++;
                            $data[$i]['mrjahanicode'] = $user['mrjahanicode'];
                            foreach ($member as $kmember => $vmember) {
                                if ($kmember == "نام"){
                                    $data[$i]['first_name'] = $vmember;
                                }elseif(strpos($kmember, 'خانوادگی')){
                                    $data[$i]['last_name'] = $vmember;
                                }elseif (strpos($kmember, 'ملی')) {
                                    $data[$i]['nationalcode'] = $vmember;
                                }elseif(strpos($kmember, 'تولد')){
                                    $data[$i]['birthdate'] = $vmember;
                                    $data[$i]['age'] = MRShowAge($vmember)[0].MRShowAge($vmember)[1];
                                }elseif (strpos($kmember, 'موبایل')) {
                                    $data[$i]['mobile'] = $vmember;
                                }else{
                                    $data[$i][$kmember] = $vmember;
                                }
                            }
                            $data[$i]['typereg'] = '';
                            $data[$i]['counts'] = '';
                            $data[$i]['transaction'] =  '';
                            $data[$i]['orderstatus'] = '';
                            $data[$i]['paystatus'] = '';
                            $data[$i]['amounts'] = '';
                        }
                    }
                    $i++;
                }
            }

        }
        $data = array_merge($data, $personal);
        $i = count($data);
        //$data = array_merge_recursive($data, $personal);
        $i++;
        $data[$i] = array('title' => '');
        $i++;
        $data[$i] = array('title' => 'جمع بندی برنامه');
        $i++;
        $data[$i] = array('title' => '-----------------------');
        foreach ($costs as $kcost => $vcost) {
            $i++;
            $data[$i] = $vcost;
        }
        $i++;
        $data[$i] = array('title' => 'مجموع هزینه ها  ( تومان )', 'amount' => $totalcosts);
        $i++;
        $data[$i] = array('title' => 'کل مبالغ پرداختی ( تومان )', 'amount' => $totalPrice);
        $i++;
        $data[$i] = array('title' => 'کل مبالغ پرداختی ماشین های شخصی ( تومان )', 'amount' => $ptotalPrice);
        $i++;
        $data[$i] = array('title' => 'باقی مانده  ( تومان )', 'amount' => ($ptotalPrice+$totalPrice)-$totalcosts);
        $i++;
        $data[$i] = array('title' => 'کل افراد شرکت کننده', 'amount' => $counts);
        $i++;
        $data[$i] = array('title' => 'تعداد ماشین های شخصی', 'amount' => $pcounts);


        //echo json_encode($data,JSON_UNESCAPED_UNICODE);
        $file = 'mr_reports'; // csv file name
        //$results = $wpdb->get_results("SELECT * FROM $wpdb->prefix$table",ARRAY_A );
        foreach($data as $result){
            $result = array_values($result);
            $result = implode(", ", $result);
            $csv_output .= $result."\n";
        }


        $filename = $file."_".date("Y-m-d_H-i",time());

        header('Content-Description: File Transfer');
        header("Content-type: application/vnd.ms-excel");
        header("Content-disposition: csv" . date("Y-m-d") . ".csv");
        header("Content-disposition: filename=".$filename.".csv");
        header('Content-Transfer-Encoding: binary');
        header('Pragma: public');
        print "\xEF\xBB\xBF";
        print $csv_output;
        exit();
   }
}
add_action('wp_ajax_csv_pull','ns_contact_form_csv_pull');