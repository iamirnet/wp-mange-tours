<?php
function MRRTO($url){
    if (!headers_sent()){
        header('Location: '.$url); exit;
    }else{
        echo '<script type="text/javascript">';
        echo 'window.location.href="'.$url.'";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>'; exit;
    }
}
function MRRTOPOST($url,array $data) {
    $html = "<form id='form' action='$url' method='post'>";
    foreach ($data as $key => $value) {
        $html .= "<input type='hidden' name='$key' value='$value'>";
    }
    $html .= "</form><script>document.getElementById('form').submit();</script>";
    $html .= "";
    print($html);
}
function MRRTOlogin($log,$pwd,$rd = "https://jahanitours.com") {
    $url = get_option('home')."/wp-login.php";
    $html = "<form id='form' action='$url' method='post'>";
    $html .= "<input type=\"text\" name=\"log\" id=\"log\" value=\"$log\" size=\"20\" />";
    $html .= "<input type=\"password\" name=\"pwd\" id=\"pwd\" value=\"$pwd\" size=\"20\" />";
    //$html .= "<input type=\"submit\" name=\"submit\" value=\"ورود\" class=\"button\" />";
    $html .= "<label for=\"rememberme\"><input name=\"rememberme\" id=\"rememberme\" type=\"checkbox\" checked=\"checked\" value=\"forever\" /> مرا به یاد داشته باش</label>";
    $html .= "<input type=\"hidden\" name=\"redirect_to\" value=\"$rd\" />";
    $html .= "</form><script>document.getElementById('form').submit();</script>";
    $html .= "";
    print($html);
}
function MRRTOPOST2($url, array $data,$method = 'POST', array $headers = null) {
    $params = array(
        'http' => array(
            'method' => $method,
            'content' => http_build_query($data)
        )
    );
    if (!is_null($headers)) {
        $params['http']['header'] = '';
        foreach ($headers as $k => $v) {
            $params['http']['header'] .= "$k: $v\n";
        }
    }
    $ctx = stream_context_create($params);
    $fp = @fopen($url, 'rb', false, $ctx);
    if ($fp) {
        echo @stream_get_contents($fp);
        die();
    } else {
        // Error
        throw new Exception("Error loading '$url', $php_errormsg");
    }
}

function MrArraySearch(array $data,$haystack) {
    foreach ($data as $key => $value) {
        if (in_array($haystack,$value)){
            $Selected[] = $key;
        }
    }
    return $Selected;
}
