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

}

$GLOBALS['Widget_Ultimate'] = new Widget_Ultimate();
