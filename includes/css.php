<?php

/*function mrjahani_style(){
    wp_enqueue_style('mrjahani-style', plugin_dir_url(__DIR__) . 'assets/css/styles.css',array('theme-style'));
}

add_action('wp_enqueue_scripts', 'mrjahani_style');*/

function mrjahani_scripts() {
    wp_enqueue_style( 'style', get_stylesheet_uri() );
    wp_enqueue_style('mrjahani-style', plugin_dir_url(__DIR__) . 'assets/css/styles.css',array(), '1.1', 'all');
    wp_enqueue_script( 'mrjahani-script', plugin_dir_url(__DIR__) . 'assets/js/script.js', array ( 'jquery' ), 1.1, true);
    ;

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'mrjahani_scripts' );

/*
add_action('init','_remove_style');
function _remove_style(){
    wp_dequeue_style('ilightbox-css');
}

// Compiles the CSS to a file instead of adding inline.
add_filter( 'kirki/dynamic_css/method', function() {
    return 'file';
});
// Embed googlefonts in styles instead of loading separate link.
// REPLACE MY_CONFIG WITH YOUR ACTUAL CONFIG-ID.
add_filter( 'kirki/MY_CONFIG/googlefonts_load_method', function() {
    return 'embed';
});
// Add the theme's stylesheet to the compiled CSS.
add_filter( "kirki/{$config_id}/dynamic_css", function( $css ) {
    return file_get_contents( plugin_dir_url(__DIR__) . 'assets/css/styles.css' ) . $css;
});*/
add_action('admin_enqueue_scripts', 'mrjahani_admin_style');
function mrjahani_admin_style() {
    //global $ztjalali_option;
    wp_register_style('mrjahani_reg_admin_style', plugin_dir_url(__DIR__) . 'assets/css/admin.css');
    wp_enqueue_style('mrjahani_reg_admin_style');

    /*if (isset($ztjalali_option['ztjalali_admin_style']) && $ztjalali_option['ztjalali_admin_style']){
        wp_register_style('ztjalali_reg_custom_admin_style', plugins_url('assets/css/admin_style.css', __FILE__));
        wp_enqueue_style('ztjalali_reg_custom_admin_style');
    }

    add_editor_style(plugins_url('assets/css/wysiwyg.css', __FILE__));
    wp_enqueue_script('ztjalali_reg_date_js', plugins_url('assets/js/date.js', __FILE__));
    if (isset($ztjalali_option['afghan_month_name']) && $ztjalali_option['afghan_month_name'])
        wp_enqueue_script('ztjalali_reg_admin_js', plugins_url('assets/js/admin-af.js', __FILE__), array('jquery'));
    else
        wp_enqueue_script('ztjalali_reg_admin_js', plugins_url('assets/js/admin-ir.js', __FILE__), array('jquery'));*/
}

