<?php
class Widget_Ultimate_Font_Icons {

    function ajax(){
        wp_send_json( $this->get_icons() );
    }

    function get_icons(){
        $icons = array(
            'font-awesome' => array(
                'name' => esc_html__( 'FontAwesome', 'widgets-ultimate' ),
                'icons' => $this->get_font_awesome_icons(),
                'class_config' => 'fa __icon_name__' // __icon_name__ will replace by icon class name
            )
        );

        return apply_filters( 'widget_ultimate_get_font_icons', $icons );
    }

    /**
     * Remove items from an array
     * @param  array                $array                  The array to manage
     * @param  void                 $element                An array or a string of the item to remove
     * @return array                                        The cleaned array with resetted keys
     */
    function array_delete($array, $element) {
        return (is_array($element)) ? array_values(array_diff($array, $element)) : array_values(array_diff($array, array($element)));
    }


    function get_font_awesome_icons(){
        /**
         * Available Font Awesome icons
         *
         * Get all icons from a font-awesome.css file and list in json mode
         *
         * @author Alessandro Gubitosi <gubi.ale@iod.io>
         * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3
        */

        global $Widget_Ultimate;

        $icons_file = $Widget_Ultimate->get('path').'assets/font-awesome/css/font-awesome.css';
        if ( ! is_readable( $icons_file ) ) {
            return array();
        }
        $parsed_file = file_get_contents($icons_file);
        preg_match_all("/fa\-([a-zA-z0-9\-]+[^\:\.\,\s])/", $parsed_file, $matches);
        $exclude_icons = array("fa-lg", "fa-2x", "fa-3x", "fa-4x", "fa-5x", "fa-ul", "fa-li", "fa-fw", "fa-border", "fa-pulse", "fa-rotate-90", "fa-rotate-180", "fa-rotate-270", "fa-spin", "fa-flip-horizontal", "fa-flip-vertical", "fa-stack", "fa-stack-1x", "fa-stack-2x", "fa-inverse", 'fa-pull-left', 'fa-pull-right');
        return $this->array_delete($matches[0], $exclude_icons);
    }

}

$GLOBALS['Widget_Ultimate_Font_Icons'] = new Widget_Ultimate_Font_Icons();