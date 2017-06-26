<?php

/*
Plugin Name: Widget Ultimate
Plugin URI: http://www.famethemes.com/
Description: The Widget Ultimate gives you a collection of widgets that you can use and customize. All the widgets are built on our powerful framework, giving you advanced forms.
Author: famethemes, shrimp2t
Author URI:  http://www.famethemes.com/
Version: 1.2.5
Text Domain: widgets-ultimate
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

final class Widget_Ultimate {

    public $url;
    public $path;

    function __construct() {

        $this->url =  trailingslashit( plugins_url('', __FILE__) );
        $this->path = trailingslashit( plugin_dir_path( __FILE__) );

        add_action( 'plugins_loaded', array( $this, 'includes' ) );
        add_action( 'wp', array( $this, 'scripts_init' ) );
        add_action( 'wp_ajax_widget_ultimate_search', array( $this, 'ajax_search' ) );
    }

    function scripts_init(){
        if( class_exists( 'FLBuilderModel' ) ) {
            if (!FLBuilderModel::is_builder_active()) {
                return;
            }
            global $wp_widget_factory;
            foreach ($wp_widget_factory->widgets as $class => $widget_obj) {
                if (!empty($widget_obj) && is_object($widget_obj) && is_subclass_of($widget_obj, 'Widget_Ultimate_Widget_Base')) {
                    $widget_obj->front_scripts();
                }
            }
        }
    }

    function get( $var ){
        if ( property_exists( $this, $var ) ) {
            return $this->{ $var };
        }
        return null;
    }

    function includes(){
        require_once $this->path.'inc/widget-base.php';
        require_once $this->path.'inc/class-widget-test.php';
    }

    function ajax_search(){
        $search = isset( $_REQUEST['search'] ) ? $_REQUEST['search'] :  false;
        $tax = isset( $_REQUEST['tax'] ) ? $_REQUEST['tax'] : false;
        $post_type = $_REQUEST['post_type'] ? $_REQUEST['post_type'] : false;
        if ( $tax ) {
            if ( ! taxonomy_exists( $tax ) ) {
                $tax = false;
            }
        }

        if ( $post_type ) {
            if ( ! post_type_exists( $post_type ) ) {
                $post_type = 'page';
            }
        }

        $results = array(
            'type' => '',
            'items' => array()
        );

        if ( $tax ) {
            $terms = get_terms( array(
                'taxonomy' => $tax,
                'search' => $search,
                'orderby' => 'name',
                'order' => 'ASC',
                'number' => '30',
            ) );

            $results['type'] = 'terms';

            if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
                foreach ( $terms as $index => $t ) {
                    $results['items'][ $index ] = array(
                        'title' => $t->name,
                        'id'    => $t->term_id
                    );
                }
            }
        } else {
            $query = new WP_Query( array(
                'post_type'     => $post_type,
                's'             => $search,
                'orderby'       => 'title',
                'order'         => 'ASC',
                'post_status'   => array( 'pending', 'publish', 'future' )
            ) );

            $results['type'] = 'posts';

            if ( $query-have_posts() ) {
                while( $query->have_posts() ) {
                    $query->the_post();
                    $results['items'][ ] = array(
                        'title' => get_the_title(),
                        'id'    => get_the_ID()
                    );
                }
            }

        }

        wp_send_json( $results );
        die();
    }

}

$GLOBALS['Widget_Ultimate'] = new Widget_Ultimate();
