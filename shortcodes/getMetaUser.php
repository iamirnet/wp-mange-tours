<?php
function mrjahani_Dashboard() {
    $meta = get_metadata( 'user', get_current_user_id());
    foreach ($meta as $kmeta => $vmeta) {
        $meta[$kmeta] = $vmeta[0];
    }
    $mrjahanicode = isset($meta["mrjahanicode"])?$meta["mrjahanicode"]:wp_get_current_user()->user_login;
    $body = '<h4>سلام '.$meta['first_name'].' جان!</h4>';
    $body .= '<h5>خوش اومدی</h5>';
    $body .= '<p>کد عضویت: '.$mrjahanicode.'</p>';
    $body .= '<a href="'.wp_logout_url().'">می خواهید خارج شوید؟</a>';
    return $body;
}
add_shortcode( 'mrjahani-dashboard', 'mrjahani_Dashboard' );

function MRSKCode() {
    return get_metadata( 'user', get_current_user_id(), "mrjahanicode", true );
}
add_shortcode( 'mrjahani_code', 'MRSKCode' );