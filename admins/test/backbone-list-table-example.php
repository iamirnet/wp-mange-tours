<?php

/**
 * Plugin Name: Backbone List Table
 * Description: Backbone.js API for WP_List_Table
 * Author: John Ciacia
 */


if ( ! class_exists( 'WP_Backbone_List_Table' ) ) {
    require_once( __DIR__ . '/backbone-list-table.php' );
}


class Backbone_List_Table_Example {

    public function init() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        add_action( 'wp_ajax__fetch_toplevel_page_movies', array( $this, '_ajax_fetch_callback' ) );
    }

    public function admin_menu() {
        add_object_page( 'Movies', 'Movies', 'edit_posts', 'movies', array( $this, 'render_table' ), 'dashicons-format-video' );
    }

    public function admin_enqueue_scripts() {
        wp_enqueue_script( 'backbone-list-table-example', plugins_url( 'backbone-list-table-example.js', __FILE__ ), array( 'backbone-list-table' ) );
    }

    public function render_table() {

        $movies = new WP_Backbone_List_Table_Movies();

        $movies->prepare_items();

        ?>
        <div class="wrap">

            <div id="icon-users" class="icon32"><br/></div>
            <h2>Movies</h2>

            <form id="movies-filter" method="get">
                <?php $movies->display() ?>
            </form>

            <button id="more-movies">More</button>

        </div>
        <?php
    }

    public function _ajax_fetch_callback() {
        $table = new WP_Backbone_List_Table_Movies();
        $table->ajax_response();
    }

}

add_action( 'init', array( new Backbone_List_Table_Example(), 'init' ) );


/**
 *
 */
class WP_Backbone_List_Table_Movies extends WP_Backbone_List_Table {

    /**
     * @param $item
     * @param $column_name
     * @return string
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'title':
                return $item['title'];
            default:
                return print_r( $item, true );
        }
    }

    /**
     * @return array
     */
    public function get_columns() {
        $columns = array(
            'title' => 'Title',
        );

        return $columns;
    }

    /**
     *
     */
    public function prepare_items() {

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array( $columns, $hidden, $sortable );

        $results = array();
        for( $i = 0; $i < 30; $i++ ) {
            $results[] = array(
                'title' => 'Title ' . $i
            );
        }


        $this->items = $results;
    }
}