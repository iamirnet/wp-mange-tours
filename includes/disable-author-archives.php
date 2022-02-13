<?php
/*
Plugin Name: dis auth
Plugin URI: http://ahja.ir
Description: Disables author archives and makes the web server return status code 404 ('Not Found') instead.
*/

if ( ! defined( 'ABSPATH' ) ) exit;

/* Return status code 404 for existing and non-existing author archives. */
add_action( 'template_redirect',
    function() {
        if ( isset( $_GET['author'] ) || is_author() ) {
            global $wp_query;
            $wp_query->set_404();
            status_header( 404 );
            nocache_headers();
        }
    }, 1 );
/* Remove author links. */
add_filter( 'author_link', function() { return '#'; }, 99 );
add_filter( 'the_author_posts_link', '__return_empty_string', 99 );
