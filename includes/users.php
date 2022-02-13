<?php
add_filter( 'user_contactmethods' , 'update_contact_methods' , 10 , 1 );

function update_contact_methods( $contactmethods ) {

// Add new fields
    $contactmethods['fathername'] = 'نام پدر';
    $contactmethods['mobile'] = 'موبایل';
    $contactmethods['booked_phone'] = 'تلفن ثابت';
    $contactmethods['telegramid'] = 'تلگرام (بدون @)';
    $contactmethods['instagramid'] = 'اینستاگرام (بدون @)';
    $contactmethods['verified'] = 'احراز هویت';
    return $contactmethods;
}
add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );
//  This action for 'Add New User' screen
//add_action( 'user_new_form', 'extra_user_profile_fields' );

function extra_user_profile_fields( $user ) {
    $birthdate = explode("/",get_the_author_meta( 'birthdate', $user->ID ));
    $month = array("ماه","فروردین","خرداد","اردیبهشت","تیر","مرداد","شهریور","مهر","آبان","آذر","دی","بهمن","اسفند");
    $gender = array(5 => "مرد",7 => "زن");
    $gvalue = get_the_author_meta('gender', $user->ID);
    $gvalue = isset($gvalue)?$gvalue:0;

    ?>
    <h3><?php _e("اطلاعات لازم عضو در سایت", "blank"); ?></h3>

    <table class="form-table">
        <tr>
            <th><label for="regtime"><?php _e("زمان عضویت"); ?></label></th>
            <td>
                <input type="text" id="regtime" disabled="disabled" value="<?php echo datetimetojalali(get_the_author_meta( 'user_registered', $user->ID ));; ?>" class="regular-text" /><br />
                <span class="description"><?php _e("غیر قابل تغییر می باشد."); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="mrjahanicode"><?php _e("کد عضویت"); ?></label></th>
            <td>
                <input type="number" id="mrjahanicode" name="mrjahanicode" value="<?php echo esc_attr( get_the_author_meta( 'mrjahanicode', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description"><?php _e("با احتیاط ویرایش شود."); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="nationalcode"><?php _e("کد ملی / پاسپورت"); ?></label></th>
            <td>
                <input type="text" id="nationalcode" name="nationalcode"  value="<?php echo esc_attr( get_the_author_meta( 'nationalcode', $user->ID ) ); ?>" class="regular-text" /><br />
            </td>
        </tr>
        <tr>
            <th><label><?php _e("تاریخ تولد"); ?></label></th>
            <td>
                <input type="number" id="birthday" name="birthday" value="<?php echo $birthdate[2]; ?>" min="1" max="31" />
                <select id="birthmonth" name="birthmonth" >
                    <?php
                    foreach ($month as $key => $value){
                        if ($key == $birthdate[1]) {
                            echo "<option value=\"$key\" selected>$value</option>";
                        }else{
                            echo "<option value=\"$key\">$value</option>";
                        }
                    }
                    ?>
                </select>
                <input type="number" id="birthyear" name="birthyear"  value="<?php echo $birthdate[0]; ?>" min="1330" />
            </td>
        </tr>
        <tr>
            <th><label for="gender"><?php _e("جنسیت"); ?></label></th>
            <td>
                <select id="gender" name="gender" >
                    <?php
                    foreach ($gender as $key => $value){
                        if ($key == $gvalue) {
                            echo "<option value=\"$key\" selected>$value</option>";
                        }else{
                            echo "<option value=\"$key\">$value</option>";
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="address"><?php _e("آدرس منزل"); ?></label></th>
            <td>
                <input type="text" id="address" name="address"  value="<?php echo esc_attr( get_the_author_meta( 'address', $user->ID ) ); ?>" class="regular-text" /><br />
            </td>
        </tr>
        <tr>
            <th><label for="mobile"><?php _e("موبایل"); ?></label></th>
            <td>
                <input type="text" id="mobile" name="mobile"  value="<?php echo esc_attr( get_the_author_meta( 'mobile', $user->ID ) ); ?>" class="regular-text" /><br />
            </td>
        </tr>
    </table>
<?php }
add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );
//add_action( 'user_register', 'save_extra_user_profile_fields' );


function save_extra_user_profile_fields( $user_id ) {
    global $wpdb;
    if ( !current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }
    $birthdate = $_POST['birthyear']."/".$_POST['birthmonth']."/".$_POST['birthday'];
    update_user_meta( $user_id, 'nationalcode', $_POST['nationalcode'] );
    if (get_the_author_meta( 'mrjahanicode', $user_id ) != $_POST['mrjahanicode']) {
        $wpdb->update($wpdb->users, array('user_login' => $_POST['mrjahanicode']), array('ID' => $user_id));
    }
    update_user_meta( $user_id, 'mrjahanicode', $_POST['mrjahanicode'] );
    update_user_meta( $user_id, 'birthdate', $birthdate );
    update_user_meta( $user_id, 'mobile', $_POST['mobile'] );
    update_user_meta( $user_id, 'gender', $_POST['gender'] );
    update_user_meta( $user_id, 'address', $_POST['address'] );

}


function createmrjahanicode($gender) {
    global $wpdb;
    $date = gregorian_to_jalali_jahani(date("Y"),date("m"),date("d"));
    $date[0] = str_split($date[0], 2);
    if ($date[1] < 10) {
        $date[1] = '0'.$date[1];
    }
    $code = (int) $date[0][1].$date[1].$gender."0000";
    $code2 = (int) $date[0][1].$date[1].$gender;

    $query = $wpdb->get_results("SELECT * FROM `ahja_usermeta` WHERE `meta_key` LIKE 'mrjahanicode' AND `meta_value` LIKE '%$code2%' AND `meta_value`>$code ORDER BY `meta_value` DESC LIMIT 1");
    if ($query != null)
        $lastcode = (int) $query[0]->meta_value;
    else {
        $lastcode = $code;
    }
    return $lastcode+1;
    //return json_encode($code);
}

function MRGPass($length = 5) {
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    return substr(str_shuffle($chars), 0, $length);
}

function mobile_exists($mobile,$limit = 5,$id = false) {
    global $wpdb;
    $query = $wpdb->get_results("SELECT * FROM `ahja_usermeta` WHERE `meta_key` LIKE 'mobile' AND `meta_value` LIKE '$mobile' ORDER BY `meta_value` DESC");
    if (count($query) <= $limit){
        if ($id) {
            return array("status" =>  true, 'user_id' => $query[0]->user_id);
        }else
            return true;
    }
    else
        return false;
}

function national_exists($nationalcode) {
    global $wpdb;
    $query = $wpdb->get_results("SELECT * FROM `ahja_usermeta` WHERE `meta_key` LIKE 'nationalcode' AND `meta_value` LIKE '$nationalcode' ORDER BY `meta_value` DESC LIMIT 1");
    if ($query[0]->meta_value == $nationalcode)
        return false;
    else
        return true;
}
function MRGetEntries($form) {
    global $wpdb;
    $query = $wpdb->get_results("SELECT * FROM `ahja_gf_entry` WHERE `form_id` = $form AND `status` LIKE 'active' ORDER BY `ahja_gf_entry`.`id` DESC");
    if (is_array($query))
        return $query;
    else
        return false;
}
function MRUInfo($log) {
    global $wpdb;
    if (MRVKCode($log)){
        $key = "mrjahanicode";
    }else{
        if (MRVNCode($log)){
            $key = "nationalcode";
        }else{
            $key = "mrjahanicode";
        }
    }
    $query = $wpdb->get_results("SELECT * FROM `ahja_usermeta` WHERE `meta_key` LIKE '$key' AND `meta_value` LIKE '$log' ORDER BY `meta_value` DESC LIMIT 1");

    $user = get_user_meta( $query[0]->user_id );
    foreach ($user as $kmeta => $vmeta) {
        $user[$kmeta] = $vmeta[0];
    }
    $user['user_id'] = $query[0]->user_id;
    return $user;
}

function MRUStatus($status,$id) {
    global $wpdb;
    $query = $wpdb->get_results("UPDATE `ahja_users` SET `user_status`=$status WHERE `ID`=$id");
    if ($query)
        return false;
    else
        return true;
}

function MRUPEntry($value,$form,$entry,$field) {
    global $wpdb;
    $query = $wpdb->get_results("UPDATE ahja_gf_entry_meta SET meta_value='$value' WHERE `form_id` = $form AND `entry_id` = $entry AND `meta_key` LIKE '$field'");
    if ($query)
        return true;
    else
        return false;
}
function MRSPEntry($form,$entry,$field) {
    global $wpdb;
    $query = $wpdb->get_results("SELECT * FROM `ahja_gf_entry_meta` WHERE `form_id` = $form AND `entry_id` = $entry AND `meta_key` LIKE '$field' LIMIT 1");
    if ($query)
        return $query;
    else
        return false;
}

function MRUPassword($status,$id) {
    global $wpdb;
    $query = $wpdb->get_results("UPDATE `ahja_users` SET `user_pass`='$status' WHERE `ID`=$id");
    if ($query)
        return false;
    else
        return true;
}

function MRVKCode($code) {
    global $wpdb;
    $query = $wpdb->get_results("SELECT * FROM `ahja_usermeta` WHERE `meta_key` LIKE 'mrjahanicode' AND `meta_value` LIKE '$code' ORDER BY `meta_value` DESC LIMIT 1");
    if ($query[0]->meta_value == $code || $code == '')
        return true;
    else
        return false;
}
function MRVString($string) {
    if (preg_match("/^[ ؀-ۿa-zA-Z]*$/",$string)){
        return true;
    }else{
        return false;
    }
}
function MRVPassword($password){
    #must contain 8 characters, 1 uppercase, 1 lowercase and 1 number
    //return preg_match('/^(?=^.{8,}$)((?=.*[A-Za-z0-9])(?=.*[A-Z])(?=.*[a-z]))^.*$/', $password);
    return preg_match('/^[a-zA-Z1-9]{3,14}$/', $password);
}

function MRVDate($date){
    #2009/12/11
    #2009-12-11
    #2009.12.11
    #09.12.11
    if (preg_match("/^[1-9][0-9]{3}\/(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)){
        return true;
    }elseif (preg_match("/^[1-9][0-9]{3}\/([1-9]|1[0-2])\/([1-9]|[1-2][0-9]|3[0-1])$/", $date)){
        return true;
    }else{
        return false;
    }

}

function MRVNCode( $code ) {
    $code = (string) preg_replace( '/[^0-9]/', '', $code );

    if ( strlen( $code ) != 10 ) {
        return false;
    }

    $list_code = str_split( $code );
    $last      = (int) $list_code[9];
    unset( $list_code[9] );
    $i   = 10;
    $sum = 0;

    foreach ( $list_code as $key => $_ ) {
        $sum += intval( $_ ) * $i --;
    }

    $mod = (int) $sum % 11;

    if ( $mod >= 2 ) {
        $mod = 11 - $mod;
    }

    if ($mod == $last){
        return true;
    }else{
        return false;
    }
}

function MRVMobile($mobile) {
    if(preg_match("/^09[0-9]{9}$/", $mobile)) {
        return true;
    }else{
        return false;
    }
}

function MRVVCode($code) {
    if (preg_match("/^[0-9]{5}$/", $code)){
        return true;
    }else{
        return false;
    }
}
function MRVGender($gender) {
    if ($gender == 5){
        return true;
    }elseif($gender == 7){
        return true;
    }else{
        return false;
    }
}

function MRClean($str){
    return is_array($str) ? array_map('MRClean', $str) : str_replace('\\', '\\\\', strip_tags(trim(htmlspecialchars((get_magic_quotes_gpc() ? stripslashes($str) : $str), ENT_QUOTES))));
}

function vgender($n) {
    switch ($n) {
        case 5:
            return "آقای";
            break;
        case 7:
            return "خانم";
            break;
    }
}

function MRCNum($text,$lang = 'en') {
    $p_num = array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹');
    $e_num = array('0','1','2','3','4','5','6','7','8','9');
    if ($lang == 'fa')
        return str_replace($e_num, $p_num, $text);
    elseif ($lang == 'en')
        return str_replace($p_num, $e_num, $text);
}

function MRTOLogin($username,$password) {
    $username = "admin";
    $password = "blog";
    $url = site_url();
    $cookie = "cookie.txt";

    $postdata = "log=" . $username . "&pwd=" . $password . "&wp-submit=Log%20In&redirect_to=" . $url . "/my-account";
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL, $url . "/wp-login.php");

    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt ($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 0);
    curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt ($ch, CURLOPT_REFERER, $url . "/wp-login.php");
    curl_setopt ($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt ($ch, CURLOPT_POST, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    header('location: my-account/');
    return $result;
}
/*add_action( 'wp_authenticate' , 'mrjahani_update_username' );
function mrjahani_update_username(&$username){


    if(isset($_POST['log']) && !isset($_POST['mobile'])){
        $_POST['mobile'] = $_POST['log'];
    }
    if(isset($_POST['mobile'])){
        $mobile = sanitize_text_field($_POST['mobile']);

        if(!empty($countrycode) && !empty($mobile)){

            $user = $mobile;

            if(!empty($user)){
                $username = $user->user_login;
            }else{
                $username = $mobile;
            }
        }
    }

}*/

add_filter( 'woocommerce_default_address_fields' , 'optional_default_address_fields' );
function optional_default_address_fields( $address_fields ) {
    $address_fields['company']['required'] = false;
    $address_fields['postcode']['required'] = false;
    return $address_fields;
}
add_filter( 'woocommerce_checkout_fields' , 'custom_checkout_fields' );
function custom_checkout_fields( $fields ) {
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_state']);
    unset($fields['billing']['billing_city']);
    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_phone']);
    unset($fields['billing']['billing_address_1']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_email']);
    return $fields;
}
add_filter('user_contactmethods','hide_profile_fields',10,1);

/*function hide_profile_fields( $contactmethods ) {
    unset($contactmethods['email']);
    return $contactmethods;
}*/
add_action( 'user_profile_update_errors', 'gowp_remove_new_user_email_error', 10, 3 );
//add_action( 'user_profile_insert_errors', 'gowp_remove_new_user_email_error', 10, 3 );
function gowp_remove_new_user_email_error( $errors, $update, $user ) {
    unset( $errors->errors['empty_email'] );
}
/*remove_filter( 'authenticate', 'wp_authenticate_email_password', 20 );
remove_filter( 'authenticate', 'user_registration_email');
remove_filter( 'authenticate', 'pre_user_email');*/

