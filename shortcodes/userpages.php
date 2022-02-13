<?php

function mrjahani_register() {
    if (!is_user_logged_in()) {
        if (isset($_POST['signup'])) {
            $error = array();
            global $wpdb;
            //usage call it somewhere in beginning of your script
            MRClean($_POST);MRClean($_GET);MRClean($_REQUEST);// and so on..
            $pwd1 = $wpdb->escape(trim(MRCNum($_POST['pwd1'])));
            $pwd2 = $wpdb->escape(trim(MRCNum($_POST['pwd2'])));
            $first_name = $wpdb->escape(trim($_POST['first_name']));
            $last_name = $wpdb->escape(trim($_POST['last_name']));
            $birthdate = $wpdb->escape(trim(MRCNum($_POST['birthdate'])));
            $nationalcode = $wpdb->escape(trim(MRCNum($_POST['nationalcode'])));
            $gender = $wpdb->escape(trim(MRCNum($_POST['gender'])));
            $mobile = $wpdb->escape(trim(MRCNum($_POST['mobile'])));
            $reagent = $wpdb->escape(trim(MRCNum($_POST['reagent'])));
            if (!isset($_POST["laws"])) {
                $error[] = "برای ثبت نام باید قوانین را قبول کنید.";
            }
            if (!MRVString($first_name)) {
                $error[] = "نام وارد شده معتبر نمی باشد.";
            }
            if(!MRVString($last_name)){
                $error[] = "نام خانوادگی وارد شده معتبر نمی باشد.";
            }
            if (!MRVDate($birthdate)){
                $error[] = "تاریخ تولد وارد شده معتبر نمی باشد.";
            }
            if (!MRVNCode($nationalcode)){
                $error[] = "کد ملی وارد شده معتبر نمی باشد.";
            }elseif (!national_exists($nationalcode)){
                $error[] = "کد ملی وارد شده قبلا ثبت شده است.";
            }
            if (!MRVGender($gender)){
                $error[] = "جنسیت انتخاب شده معتبر نمی باشد.";
            }
            if (!MRVMobile($mobile)){
                $error[] = "شماره موبایل وارد شده معتبر نمی باشد.";
            }elseif (!mobile_exists($mobile)){
                $error[] = "حد مجاز وارد کردن این شماره تمام شده است، لطفا شماره دیگری را وارد کنید.";
            }
            if (!MRVKCode($reagent)){
                $error[] = "کد معرف وارد شده معتبر نمی باشد.";
            }
            if (!MRVPassword($pwd1)){
                $error[] = "رمز عبور باید شامل حروف انگلیسی و اعداد انگلیسی باشد، کمتر از 4 کارکتر و بیشتر از 15 کارکتر نباشد.";
            }
            if (!MRVPassword($pwd2)){
                $error[] = "تکرار رمز عبور باید شامل حروف انگلیسی و اعداد انگلیسی باشد، کمتر از 4 کارکتر و بیشتر از 15 کارکتر نباشد.";
            } if($pwd1 <> $pwd2 ){
                $error[] = 'رمز عبور با تکرار رمز عبور مطابقت ندارد.';
            }
            if(count($error) == 0) {
                $mrjahanicode = createmrjahanicode($gender);
                $user_id = wp_insert_user(
                    array (
                        'first_name' => apply_filters('pre_user_first_name', $first_name),
                        'last_name' => apply_filters('pre_user_last_name', $last_name),
                        'user_pass' => apply_filters('pre_user_user_pass', $pwd1),
                        'user_login' => apply_filters('pre_user_user_login', $mrjahanicode),
                        'role' => 'subscriber'
                    )
                );
                if( is_wp_error($user_id) ) :
                    $error[] = 'خطایی در ارسال درخواست نام نویسی شما رخ داده است!';
                else :
                    do_action('user_register', $user_id);
                    update_user_meta( $user_id, 'nationalcode', $nationalcode );
                    $birthdate = str_replace("-","/",$birthdate);
                    $birthdate = str_replace(".","/",$birthdate);
                    update_user_meta( $user_id, 'birthdate', $birthdate );
                    update_user_meta( $user_id, 'gender', $gender );
                    update_user_meta( $user_id, 'reagent', $reagent );
                    update_user_meta( $user_id, 'usereagent', false );
                    update_user_meta( $user_id, 'mrjahanicode', $mrjahanicode );
                    update_user_meta( $user_id, 'mobile', $mobile );
                    update_user_meta( $user_id, 'verified', false );
                    $success = true;
                    send_userinfo($mrjahanicode,MRCNum($_POST['pwd1']),$mobile);
                    //MRRTOPOST(site_url( '/wp-login.php' ),array('user_login' => $mrjahanicode,'user_pass' => $pwd1));
                    //MRRTOlogin($mrjahanicode,$pwd1);
                    MRRTO( '/login?mrmsg=truereg' );
                endif;

            }
            if (count($error) != 0) {
                $messages = '<div class="bs-shortcode-alert alert alert-danger"><strong>لطفا ابتدا خطا های زیر را بر طرف کنید.</strong><br/>';
                foreach ($error as $verror) {
                    $messages .= $verror . "<br>";
                }
                $messages .= '</div>';
            }elseif ($success == true){
                $messages = '<div class="bs-shortcode-alert alert alert-success"><strong>ثبت نام شما با موفقیت انجام شد و اطلاعات ورود به موبایل پیامک شد</strong></div>';
            }
        }
        ?>
        <div role="form" dir="rtl" lang="fa-IR">
            <div class="screen-reader-response">
                <div class="bs-shortcode-alert alert alert-warning">
                    <strong>توجه! لطفا نکات زیر را رعایت کنید.</strong><br>
                    <ul>
                        <li>لطفا از اعداد انگلیسی استفاده کنید.</li>
                        <li>نام و نام خانوادگی تنها می تواند شامل حروف فارسی باشد.</li>
                        <li>تاریخ تولد باید در قالب 1370/01/01 باشد.</li>
                        <li>کد ملی باید کاملا دقیق وارد شود و از اعداد انگلیسی استفاده کنید.</li>
                        <li>شما با هر شماره ای می توانید 5 بار ثبت نام کنید.</li>
                        <li>رمز عبور و تکرار آن باید شامل حروف انگلیسی و اعداد انگلیسی باشد، کمتر از 4 کارکتر و بیشتر از 15 کارکتر نباشد.</li>
                        <li><strong>اگر کد معرف ندارید لازم نیست وارد کنید.</strong></li>
                    </ul>
                </div>
                <?php echo isset($_POST['signup'])?$messages:null; ?>
            </div>
            <form method="post">
                <p>
                    <label> نام (الزامی)
                        <br>
                        <span class="first_name">
                            <input type="text" name="first_name" size="40" required placeholder="نمونه: علی">
                        </span>
                    </label>
                    <br>
                    <label> نام خانوادگی (الزامی)
                        <br>
                        <span class="last_name"><input type="text" name="last_name" size="40" required placeholder="نمونه: محمدی">
                        </span>
                    </label>
                    <br>
                    <label> تاریخ تولد (الزامی)
                        <br>
                        <span class="birthdate"><input type="text" name="birthdate" size="40" required placeholder="نمونه: 1379/10/09">
                        </span>
                    </label>
                    <br>
                    <label> کد ملی (الزامی)
                        <br>
                        <span class="nationalcode"><input type="text" name="nationalcode" size="40" required placeholder="123456789">
                        </span>
                    </label>
                    <br>
                    <label>جنسیت  (الزامی)
                        <br>
                        <span class="gender">
                            <select name="gender" style="width: 100%;" required>
                                <option value="">انتخاب کنید.</option>
                                <option value="5">مرد</option>
                                <option value="7">زن</option>
                            </select>
                        </span>
                    </label>
                    <br>
                    <label>موبایل  (الزامی)
                        <br>
                        <span class="mobile">
                            <input type="text" name="mobile" size="40" required placeholder="09121234567">
                        </span>
                    </label>
                    <br>
                    <label>کد معرف (اختیاری)
                        <br>
                        <span class="reagent">
                            <input type="text" name="reagent" size="40">
                        </span>
                    </label>
                    <br>
                    <label>رمز عبور  (الزامی)
                        <br>
                        <span class="pwd1">
                            <input type="password" name="pwd1" size="40" required>
                        </span>
                    </label>
                    <br>
                    <label>تکرار رمز عبور  (الزامی)
                        <br>
                        <span class="pwd2">
                            <input type="password" name="pwd2" size="40" required>
                        </span>
                    </label>
                    <br>
                    <label>
                        <span class="laws">
                            <input type="radio" class="form-check-input" id="laws" required="required" name="laws" checked value="1">
                            <a href="/laws" target="_blank">قبول شرایط و قوانین تاج کیهان</a>
                        </span>
                    </label>
                    <br>
                    <input type="submit" value="ثبت نام" name="signup">
                </p>
            </form>
        </div>
        <div class="login-field login-signup">
            <span><a href="/login">می خواهم وارد سایت شوم</a></span>
        </div>
        <div class="login-field login-forgot">
            <a href="/forgot" class="go-reset-panel">پسوردم را فراموش کرده‌ام</a>
        </div>
        <?php
    }else {
        MRRTO( '/my-account' );
    }

}
add_shortcode( 'mrjahani-register', 'mrjahani_register' );

function mrjahani_userinfo() {
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $user = get_user_meta( $user_id );
        foreach ($user as $kmeta => $vmeta) {
            $user[$kmeta] = $vmeta[0];
        }
        $username = wp_get_current_user()->user_login;
        $status = wp_get_current_user()->user_status;
        $month = array("ماه","فروردین","خرداد","اردیبهشت","تیر","مرداد","شهریور","مهر","آبان","آذر","دی","بهمن","اسفند");
        $gender = array(5 => "مرد",7 => "زن");
        $gvalue = get_the_author_meta('gender', $user_id);
        $gvalue = isset($gvalue)?$gvalue:0;
        if (isset($_POST['changeinfo'])) {
            $error = array();
            global $wpdb;
            //usage call it somewhere in beginning of your script
            MRClean($_POST);MRClean($_GET);MRClean($_REQUEST);// and so on..
            $pwd1 = $wpdb->escape(trim(MRCNum($_POST['pwd1'])));
            $pwd2 = $wpdb->escape(trim(MRCNum($_POST['pwd2'])));
            $first_name = $wpdb->escape(trim($_POST['first_name']));
            $last_name = $wpdb->escape(trim($_POST['last_name']));
            $birthdate = $wpdb->escape(trim(MRCNum($_POST['birthdate'])));
            /*$_POST['birthyear'] = $wpdb->escape(trim(MRCNum($_POST['birthyear'])));
            $_POST['birthmonth'] = $wpdb->escape(trim(MRCNum($_POST['birthmonth'])));
            $_POST['birthday'] = $wpdb->escape(trim(MRCNum($_POST['birthday'])));*/
            if ($status == 3) {
                $nationalcode = $wpdb->escape(trim(MRCNum($_POST['nationalcode'])));
                $gender = $wpdb->escape(trim(MRCNum($_POST['gender'])));
                $mobile = $wpdb->escape(trim(MRCNum($_POST['mobile'])));
                $reagent = $wpdb->escape(trim(MRCNum($_POST['reagent'])));
            }
            if (!MRVString($first_name)){
                $error[] = "نام وارد شده معتبر نمی باشد.";
            }
            if(!MRVString($last_name)){
                $error[] = "نام خانوادگی وارد شده معتبر نمی باشد.";
            }
            //$birthdate = $_POST['birthyear']."-".$_POST['birthmonth']."-".$_POST['birthday'];
            $birthdate = str_replace("-","/",$birthdate);
            $birthdate = str_replace(".","/",$birthdate);
            if (!MRVDate($birthdate)){
                $error[] = "تاریخ تولد وارد شده معتبر نمی باشد.";
            }
            if ($status == 3) {
                if (!MRVNCode($nationalcode)){
                    $error[] = "کد ملی وارد شده معتبر نمی باشد.";
                }elseif (!national_exists($nationalcode)){
                    $error[] = "کد ملی وارد شده قبلا ثبت شده است.";
                }
                if (!MRVGender($gender)){
                    $error[] = "جنسیت انتخاب شده معتبر نمی باشد.";
                }
                if (!MRVMobile($mobile)){
                    $error[] = "شماره موبایل وارد شده معتبر نمی باشد.";
                }elseif (!mobile_exists($mobile)){
                    $error[] = "حد مجاز وارد کردن این شماره تمام شده است، لطفا شماره دیگری را وارد کنید.";
                }
                if (!MRVKCode($reagent)){
                    $error[] = "کد معرف وارد شده معتبر نمی باشد.";
                }
            }
            if (($pwd1 != '' && $pwd2 != '')){
                if (!MRVPassword($pwd1)){
                    $error[] = "رمز عبور باید شامل حروف انگلیسی و اعداد انگلیسی باشد، کمتر از 4 کارکتر و بیشتر از 15 کارکتر نباشد.";
                }
                if (!MRVPassword($pwd2)){
                    $error[] = "تکرار رمز عبور باید شامل حروف انگلیسی و اعداد انگلیسی باشد، کمتر از 4 کارکتر و بیشتر از 15 کارکتر نباشد.";
                } if($pwd1 <> $pwd2 ){
                    $error[] = 'رمز عبور با تکرار رمز عبور مطابقت ندارد.';
                }
            }

            if(count($error) == 0) {
                if ($first_name != wp_get_current_user()->first_name) {
                    update_user_meta( $user_id, 'first_name', $first_name );
                }
                if ($last_name !=  wp_get_current_user()->last_name) {
                    update_user_meta( $user_id, 'last_name', $last_name );
                }
                if ($birthdate != get_metadata( 'user', $user_id, "birthdate", true )) {
                    update_user_meta( $user_id, 'birthdate', $birthdate );
                }
                if ($pwd1 != get_metadata( 'user', $user_id, "user_pass", true )) {
                    MRUPassword(md5($pwd1),$user_id);
                    $passchang = true;
                }
                if ($status == 3) {
                    update_user_meta( $user_id, 'mrjahanicode', $username );
                    update_user_meta( $user_id, 'mobile', $mobile );
                    update_user_meta( $user_id, 'nationalcode', $nationalcode );
                    update_user_meta( $user_id, 'gender', $gender );
                    update_user_meta( $user_id, 'reagent', $reagent );
                    update_user_meta( $user_id, 'usereagent', false );
                    update_user_meta( $user_id, 'verified', false );
                    if (MRUStatus(0,$user_id)){
                        send_userinfo($username,MRCNum($_POST['pwd1']),$mobile);
                    }

                }
                $success = true;
                
                if($passchang == true) {
                    MRRTO(home_url()."/login");
                }
            }
            if (count($error) != 0) {
                $messages = '<div class="bs-shortcode-alert alert alert-danger"><strong>لطفا ابتدا خطا های زیر را بر طرف کنید.</strong><br/>';
                foreach ($error as $verror) {
                    $messages .= $verror . "<br>";
                }
                $messages .= '</div>';
            }elseif ($success == true){
                $messages = '<div class="bs-shortcode-alert alert alert-success"><strong>اطلاعات شما با موفقیت ذخیره شد.</strong></div>';
            }
        }
        ?>
        <div role="form" dir="rtl" lang="fa-IR">
            <div class="screen-reader-response">
                <div class="bs-shortcode-alert alert alert-warning">
                    <strong>توجه! لطفا نکات زیر را رعایت کنید.</strong><br>
                    <ul>
                        <li>لطفا از اعداد انگلیسی استفاده کنید.</li>
                        <li>نام و نام خانوادگی تنها می تواند شامل حروف فارسی باشد.</li>
                        <li>تاریخ تولد باید در قالب 1370/01/01 باشد.</li>
                        <li>کد ملی باید کاملا دقیق وارد شود و از اعداد انگلیسی استفاده کنید.</li>
                        <li>شما با هر شماره ای می توانید 5 بار ثبت نام کنید.</li>
                        <li>رمز عبور و تکرار آن باید شامل حروف انگلیسی و اعداد انگلیسی باشد، کمتر از 4 کارکتر و بیشتر از 15 کارکتر نباشد.</li>
                        <li><strong>اگر کد معرف ندارید لازم نیست وارد کنید.</strong></li>
                    </ul>
                </div>
                <?php echo isset($_POST['changeinfo'])?$messages:null; ?>
            </div>
            <form action="#profile" method="post" novalidate="novalidate">
                <p>
                    <label> نام (الزامی)
                        <br>
                        <span class="first_name">
                            <input type="text" name="first_name" size="40" required placeholder="نمونه: علی" value="<?php echo esc_attr( get_the_author_meta( 'first_name', $user_id ) ); ?>">
                        </span>
                    </label>
                    <br>
                    <label> نام خانوادگی (الزامی)
                        <br>
                        <span class="last_name"><input type="text" name="last_name" size="40" required placeholder="نمونه: محمدی" value="<?php echo esc_attr( get_the_author_meta( 'last_name', $user_id ) ); ?>">
                        </span>
                    </label>
                    <br>
                    <label> تاریخ تولد (الزامی)
                        <br>
                        <span class="birthdate"><input type="text" name="birthdate" size="40" required placeholder="نمونه: 1379/10/09" value="<?php echo esc_attr( get_the_author_meta( 'birthdate', $user_id ) ); ?>">
                        </span>
                    </label>
                    <br>
                    <label> کد ملی (الزامی)
                        <br>
                        <span class="nationalcode"><input type="text" name="nationalcode" size="40" required placeholder="123456789" <?php echo isset($status)?null:'disabled'; ?> value="<?php echo esc_attr( get_the_author_meta( 'nationalcode', $user_id ) ); ?>">
                        </span>
                    </label>
                    <br>
                    <label>جنسیت  (الزامی)
                        <br>
                        <span class="gender">
                            <select name="gender" style="width: 100%;" required <?php echo isset($status)?null:'disabled'; ?>>
                                <option value="">انتخاب کنید.</option>
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
                        </span>
                    </label>
                    <br>
                    <label>موبایل  (الزامی)
                        <br>
                        <span class="mobile">
                            <input type="text" name="mobile" size="40" required placeholder="09121234567" <?php echo isset($status)?null:'disabled'; ?> value="<?php echo esc_attr( get_the_author_meta( 'mobile', $user_id ) ); ?>">
                        </span>
                    </label>
                    <br>
                    <label>کد معرف (اختیاری)
                        <br>
                        <span class="reagent">
                            <input type="text" name="reagent" size="40" <?php echo isset($status)?null:'disabled'; ?> value="<?php echo esc_attr( get_the_author_meta( 'reagent', $user_id ) ); ?>">
                        </span>
                    </label>
                    <br>
                    <label>رمز عبور  (الزامی)
                        <br>
                        <span class="pwd1">
                            <input type="password" name="pwd1" size="40" required>
                            <span>اگر نمی خواهید تغییر بدید، خالی بزارید.</span>
                        </span>
                    </label>
                    <br>
                    <label>تکرار رمز عبور  (الزامی)
                        <br>
                        <span class="pwd2">
                            <input type="password" name="pwd2" size="40" required>
                            <span>اگر نمی خواهید تغییر بدید، خالی بزارید.</span>
                        </span>
                    </label>
                    <br>
                    <input type="submit" value="ذخیره اطلاعات" name="changeinfo">
                </p>
            </form>
        </div>
        <?php
    }else {
        MRRTO( '/login' );
    }

}
add_shortcode( 'mrjahani-userinfo', 'mrjahani_userinfo' );

function mrjahani_login() {
    if (isset($_GET['mrmsg']) && $_GET['mrmsg'] == 'truereg') {
        $messages = '<div class="bs-shortcode-alert alert alert-success"><strong>ثبت نام شما با موفقیت انجام شد و اطلاعات ورود به موبایل شما پیامک شد</strong></div>';
    }
    ?>
    <div class="screen-reader-response">
        <?php echo isset($_GET['mrmsg'])?$messages:null; ?>
    </div>
    <?php
    $args = array(
        'echo'           => true,
        'remember'       => true,
        'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
        'form_id'        => 'loginform',
        'id_username'    => 'user_login',
        'id_password'    => 'user_pass',
        'id_remember'    => 'rememberme',
        'id_submit'      => 'wp-submit',
        'label_username' => 'کد عضویت',
        'label_password' => __( 'Password' ),
        'label_remember' => __( 'Remember Me' ),
        'label_log_in'   => __( 'Log In' ),
        'value_username' => '',
        'value_remember' => false
    );
     wp_login_form( $args );
     ?>
    <div class="login-field login-signup">
        <span><a href="/register">می خواهم عضو سایت شوم</a></span>
    </div>
    <div class="login-field login-forgot">
        <a href="/forgot" class="go-reset-panel">پسوردم را فراموش کرده‌ام</a>
    </div>
    <?php
}
add_shortcode( 'mrjahani-login', 'mrjahani_login' );


function mrjahani_verify() {
    $user_id = get_current_user_id();
    $mobile = isset($_POST['mobile'])?$_POST['mobile']:null;
    $code = isset($_POST['code'])?$_POST['code']:null;
    $verifySmsDate = get_the_author_meta( 'verifySmsDate', $user_id )?get_the_author_meta( 'verifySmsDate', $user_id ):date('Y-m-d H:i:s');
    $verifySmsDateDiff = MRTimeDiff(date('Y-m-d H:i:s'),$verifySmsDate,"ss");
    if (isset($mobile) && $code == null) {
        if ($verifySmsDateDiff[0] > 60) {
            if (preg_match("/^09[0-9]{9}$/", $mobile)){
                if(mobile_exists($mobile)){
                    $code = verify_send_sms($mobile);
                    if (preg_match("/^[0-9]{5}$/", $code)){
                        update_user_meta( $user_id, 'verify_code', $code );
                        update_user_meta( $user_id, 'mobile', $mobile );
                        update_user_meta( $user_id, 'verifySmsDate', date('Y-m-d H:i:s') );
                        $_SESSION["btn"] = "بررسی و ادامه";
                        unset($code);
                    }
                }else {
                    $error[] = 'حد مجاز وارد کردن این شماره تمام شده است، لطفا شماره موبایل دیگری را وارد کنید.';
                }
            }else {
                $error[] = "شماره موبایل وارد شده معتبر نیست";
            }
        }else{
            $error[] = "شما بعد از گذشت ".(60-$verifySmsDate[0])." می توانید کد تایید را مجدد ارسال کنید";
        }

    }
    if ($code == get_metadata( 'user', $user_id, "verify_code", true ) && isset($code)) {
        if (preg_match("/^[0-9]{5}$/", $code)) {
            update_user_meta( $user_id, 'verified', true );
            $messages = '<div class="bs-shortcode-alert alert alert-success"><strong>احراز هویت شما با موفقیت انجام شد.</strong></div>';
            MRRTO('/my-account');
        }else{
            $error[] = "کد تأیید وارد شده ممعتبر نیست";
        }
    }
    if (count($error) != 0) {
        $messages = '<div class="bs-shortcode-alert alert alert-danger"><strong>لطفا ابتدا خطا های زیر را بر طرف کنید.</strong><br/>';
        foreach ($error as $verror) {
            $messages .= $verror . "<br>";
        }
        $messages .= '</div>';
    }

    ?>
    <div role="form" dir="rtl" lang="fa-IR">
        <div class="screen-reader-response">
            <?php
            if ($_POST['sendmobile'] || $_POST['sendcode']) {
                echo $messages;
            }
            ?>
        </div>
        <form action="#" method="post" novalidate="novalidate">
            <p>
                <?php
                if (isset($mobile) == false || $verifySmsDateDiff[0] < 60){ ?>
                    <label>شماره تلفن همراه (الزامی)
                        <br><span class="tel-729"><input type="tel" name="mobile" size="40" required placeholder="09121234567" value="<?php echo get_metadata( 'user', $user_id, "mobile", true ); ?>"></span> </label>
                    <br>
                    <input type="submit" value="ارسال کد تایید" name="sendmobile">
                <?php }else { ?>
                    <label>کد تایید  (الزامی)
                        <br><span class="tel-729"><input type="number" name="code" size="6" required placeholder="12345"></span> </label>
                    <br>
                    <input type="submit" value="بررسی کد تایید" name="sendcode">
                <?php } ?>
            </p>
        </form>
    </div>
<?php
}

add_shortcode( 'mrjahani-verify', 'mrjahani_verify' );

function mrjahani_forgot() {
    $codelog = isset($_POST['codelog'])?$_POST['codelog']:null;

    if (MRVKCode($codelog) && $codelog != null){
        $user = MRUInfo($codelog);
        $forgotSmsDate = get_the_author_meta( 'forgotSmsDate', $user["user_id"] )?get_the_author_meta( 'forgotSmsDate', $user["user_id"] ):null;
        $forgotSmsDateDiff = MRTimeDiff(date('Y-m-d H:i:s'),$forgotSmsDate,"ss");
        if ($forgotSmsDateDiff[0] > 3600 && is_array($user)) {
            $pwd1 = MRGPass();
            MRUPassword(md5($pwd1),$user["user_id"]);
            send_userinfo($user["mrjahanicode"],$pwd1,$user["mobile"]);
            update_user_meta( $user["user_id"], 'forgotSmsDate', date('Y-m-d H:i:s'));
            $messages = '<div class="bs-shortcode-alert alert alert-success"><strong>رمز عبور جدید برای شما ارسال شد.</strong></div>';
        }else{
            $error[] = "شما بعد از گذشت یک ساعت مجددا می توانید درخواست رمز جدید بدید.";
        }

    }else{
        if (MRVNCode($codelog)){
            if (!national_exists($codelog)){
                $user = MRUInfo($codelog);
                $forgotSmsDate = get_the_author_meta( 'forgotSmsDate', $user["user_id"] )?get_the_author_meta( 'forgotSmsDate', $user["user_id"] ):null;
                $forgotSmsDateDiff = MRTimeDiff(date('Y-m-d H:i:s'),$forgotSmsDate,"ss");
                if ($forgotSmsDateDiff[0] > 3600 && is_array($user)) {
                    $pwd1 = MRGPass();
                    MRUPassword(md5($pwd1),$user["user_id"]);
                    send_userinfo($user["mrjahanicode"],$pwd1,$user["mobile"]);
                    update_user_meta( $user["user_id"], 'forgotSmsDate', date('Y-m-d H:i:s'));
                    $messages = '<div class="bs-shortcode-alert alert alert-success"><strong>رمز عبور جدید برای شما ارسال شد.</strong></div>';
                }else{
                    $error[] = "شما بعد از گذشت یک ساعت مجددا می توانید درخواست رمز جدید کنید.";
                }
            }else{
                $error[] = "کد ملی یا کد عضویت در سیستم وجود ندارد.";
            }
        }else{
            $error[] = "کد ملی یا کد عضویت وارد شده معتبر نمی باشد.";
        }
    }
    if (count($error) != 0) {
        $messages = '<div class="bs-shortcode-alert alert alert-danger"><strong>لطفا ابتدا خطا های زیر را بر طرف کنید.</strong><br/>';
        foreach ($error as $verror) {
            $messages .= $verror . "<br>";
        }
        $messages .= '</div>';
    }

    ?>
    <div role="form" dir="rtl" lang="fa-IR">
        <div class="screen-reader-response">
            <?php
            if ($_POST['sendpass'] || $_POST['sendpass']) {
                echo $messages;
            }
            ?>
        </div>
        <form action="#" method="post" novalidate="novalidate">
            <p>
                <?php
                if (isset($mobile) == false){ ?>
                    <label>کد ملی یا کد عضویت
                        <br><span class="log"><input type="text" name="codelog" size="40" required ></span> </label>
                    <br>
                    <input type="submit" value="ارسال رمز جدید" name="sendpass">
                <?php } ?>
            </p>
        </form>
    </div>
    <?php
}

add_shortcode( 'mrjahani-forgot', 'mrjahani_forgot' );

function mrjahani_check() {
    $verified = get_metadata( 'user', get_current_user_id(), "verified", true );
    $status = wp_get_current_user()->user_status;
    if ($status == 3 && strpos($_SERVER[REQUEST_URI], 'my-account') == false && is_user_logged_in()){
        MRRTO( '/my-account/#profile' );
        exit;
    }/* elseif ($status == 0 && $verified == false && strpos($_SERVER[REQUEST_URI], 'verify') == false && is_user_logged_in()) {
        MRRTO( '/verify' );
        exit;
    }elseif ($verified != false && strpos($_SERVER[REQUEST_URI], 'verify')){
        MRRTO( '/my-account' );
        exit;
    }*/
    /*if ((strpos($_SERVER[REQUEST_URI], 'register') || strpos($_SERVER[REQUEST_URI], 'login') || strpos($_SERVER[REQUEST_URI], 'forgot')) && is_user_logged_in()) {
        MRRTO( '/my-account' );
        exit;
    }*/
    /*if (strpos($_SERVER[REQUEST_URI], 'wp-login.php?action=register')  && !is_user_logged_in()) {
        MRRTO( '/register' );
        exit;
    }*/
}
add_action( 'init', 'mrjahani_check' );

/*add_filter( 'edit_profile_url', 'modify_profile_url_wpse_94075', 10, 3 );

/**
 * http://core.trac.wordpress.org/browser/tags/3.5.1/wp-includes/link-template.php#L2284
 *
 * @param string $scheme The scheme to use.
 * Default is 'admin'. 'http' or 'https' can be passed to force those schemes.
 */
/*function modify_profile_url_wpse_94075( $url, $user_id, $scheme )
{
    // Makes the link to http://example.com/custom-profile
    $url = site_url( '/my-account' );
    return $url;
}*/