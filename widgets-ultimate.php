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

$widget_ultimate_url = trailingslashit( plugins_url('', __FILE__) );
$widget_ultimate_path = trailingslashit( plugin_dir_path( __FILE__) );
if ( ! class_exists( 'Widget_Ultimate' ) ) {
    include_once $widget_ultimate_path.'inc/init.php';
}
if ( class_exists( 'Widget_Ultimate' ) ) {
    $GLOBALS['Widget_Ultimate'] = new Widget_Ultimate( $widget_ultimate_path, $widget_ultimate_url );
}

