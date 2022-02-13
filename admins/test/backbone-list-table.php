<?php

if ( !class_exists( 'WP_List_Table' ) ) {
    error_reporting( ~E_NOTICE );
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WP_Backbone_List_Table extends WP_List_Table {

    public static function initialize() {
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
    }

    /**
     * @param array $args
     */
    public function __construct( $args = array() ) {

        $args = wp_parse_args( $args, array(
            'plural' => '',
            'singular' => '',
            'ajax' => true,
            'screen' => null,
        ) );

        parent::__construct( $args );

        if( $args['ajax'] ) {
            add_action( 'admin_footer', array( $this, '_js_vars' ) );
        }
    }

    /**
     *
     */
    public static function admin_enqueue_scripts() {
        wp_register_script( 'backbone-list-table', plugins_url( 'backbone-list-table.js', __FILE__ ), array( 'backbone', 'jquery' ) );
    }

    /**
     *
     */
    function display_rows_or_placeholder() {
        echo '<tr class="no-items" ><td style="padding:20px;" colspan="' . $this->get_column_count() . '" class="colspanchange"><span class="spinner" style="display:block; float:none; margin:0 auto;"></span></td></tr>';
    }

    /**
     *
     */
    public function _js_vars() {

        $args = array(
            'columns' => $this->get_columns(),
            'class'  => get_class( $this ),
            'screen' => array(
                'id'   => $this->screen->id,
                'base' => $this->screen->base,
            )
        );

        printf( "<script type='text/javascript'>table_args = %s;</script>\n", json_encode( $args ) );
    }

    /**
     *
     */
    public function ajax_response() {
        // @todo: validate nonce
        // @todo: validate current_user_can
        // @todo: other validation?

        $this->prepare_items();

        $response = array();

        foreach ( $this->items as $item ) {
            list( $columns, $hidden ) = $this->get_column_info();

            $row = array();
            foreach ( $columns as $column_name => $column_display_name ) {
                $row[ $column_name ] = $this->column_default( $item, $column_name );
            }
            array_push( $response, $row );
        }

        wp_send_json( $response );
    }
}

WP_Backbone_List_Table::initialize();