<?php

/**
 * Widget Base
 */
class Widget_Ultimate_Widget_Base extends WP_Widget
{

    private $config = array(

    );

    public function __construct($id_base = '', $name = '', $widget_options = array(), $control_options = array())
    {
        global $Widget_Ultimate;
        $this->config = array(
            'prefix' => 'widget-ultimate',
            'url'    => $Widget_Ultimate->get('url').'assets/'
        );
        parent::__construct($id_base, $name, $widget_options, $control_options);

       $this->admin_scripts();

        if ( defined( 'ELEMENTOR_VERSION' ) ) {
            add_action('elementor/editor/before_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
            add_action('elementor/editor/footer', array($this, 'render_control_template_scripts'));
        }

    }

    function admin_scripts(){
        add_action( 'admin_print_scripts', array( $this, 'enqueue_admin_scripts' ) );
        add_action( 'admin_footer', array( $this, 'render_control_template_scripts' ) );
    }

    function front_scripts(){
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        add_action( 'wp_print_footer_scripts', array( $this, 'render_control_template_scripts' ) );
    }

    /**
     * Loads the required media files for the media manager and scripts for media widgets.
     *
     * @since 4.8.0
     * @access public
     */
    public function enqueue_admin_scripts() {
        wp_enqueue_media();
        wp_enqueue_editor();
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        global $Widget_Ultimate;

        wp_register_script( 'widget-ultimate-widget-admin', $Widget_Ultimate->get('url').'assets/js/widget-admin.js', array( 'jquery'), false, true );
        wp_register_style( 'widget-ultimate-widget-admin', $Widget_Ultimate->get('url').'assets/css/widget-admin.css' );
        if ( defined( 'ELEMENTOR_VERSION' ) ) {
            wp_enqueue_style('wp-dashicons', site_url('/') . 'wp-includes/css/dashicons.css');
        }
        wp_enqueue_script( 'widget-ultimate-widget-admin' );
        wp_enqueue_style( 'widget-ultimate-widget-admin' );

        // Widget settings
        wp_localize_script( 'widget-ultimate-widget-admin' , get_class( $this ), $this->get_configs() );

    }

    /**
     * Render form template scripts.
     *
     * @since 4.8.0
     * @access public
     */
    public function render_control_template_scripts() {
        ?>
        <script type="text/html" id="tmpl-widget-bundle-fields">
            <# var elementIdPrefix = 'el' + String( Math.random() ).replace( /\D/g, '' ) + '-' #>
            <# _.each( data.fields, function( item, key ){  #>
                <#
                    var name = data.namePrefix, value = '';
                    var n = item.name.indexOf("]");
                    if ( n > 0 ) {
                        name += '['+item.name.replaceAt( n,'][');
                    } else {
                        name += '['+item.name+']';
                    }

                    value = data.values[ key ];
                #>
                <# switch( item.type ){ case 'select':  #>
                    <p class="w-admin-input-wrap">
                        <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>
                        <select id="{{ elementIdPrefix }}-{{ item.name }}" name="{{ name }}">
                            <# _.each( item.options, function( v, k ){  #>
                                <option <# if ( k == value ) { #>selected="selected"<# } #> value="{{ k }}">{{ v }}</option>
                            <# }); // end each #>
                        </select>
                    </p>
                    <# break;  #>

                    <# case 'textarea': #>
                        <p class="w-admin-input-wrap">
                            <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>
                            <textarea rows="6"  class="widefat" id="{{ elementIdPrefix }}-{{ item.name }}" name="{{ name }}" >{{ value }}</textarea>
                        </p>
                    <# break;  #>

                    <# case 'checkbox': #>
                        <p class="w-admin-input-wrap">
                            <input id="{{ elementIdPrefix }}-{{ item.name }}" <# if ( value == "on" ) { #>checked="checked"<# } #> name="{{ name }}" type="checkbox" value="on">
                            <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>
                        </p>
                    <# break;  #>

                    <# case 'editor': #>
                        <p class="w-admin-input-wrap">
                            <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>
                            <textarea rows="6" class="widefat editor" id="{{ elementIdPrefix }}-{{ item.name }}" name="{{ name }}" >{{ value }}</textarea>
                        </p>
                    <# break;  #>

                    <# case 'image': case 'video': case 'file': #>
                        <div class="w-admin-input-wrap">
                            <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>
                            <div class="widget-attachment-input widget-{{ item.type }}-input" data-type="{{ item.type }}">
                                <input class="widefat attachment-id" type="hidden" id="{{ elementIdPrefix }}-{{ item.name }}" name="{{ name }}" value="{{ value }}">
                                <div class="media-item-preview"></div>
                                <p class="media-widget-buttons">
                                    <button type="button" class="button remove-media"><?php esc_html_e( 'Remove', 'widgets-ultimate' ); ?></button>
                                    <button type="button" class="button change-media"><?php esc_html_e( 'Replace', 'widgets-ultimate' ); ?></button>
                                    <button type="button" class="button select-media"><?php esc_html_e( 'Add', 'widgets-ultimate' ); ?></button>
                                </p>

                            </div>
                        </div>
                    <# break;  #>

                    <# case 'color': #>
                        <p class="w-admin-input-wrap color-input">
                            <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>
                            <input class="widefat color-val" type="hidden" id="{{ elementIdPrefix }}-{{ item.name }}" name="{{ name }}" value="{{ value }}">
                            <input class="color-picker widefat" value="{{ value }}">
                        </p>
                    <# break;  #>

                    <# case 'group': #>
                        <div class="w-admin-input-wrap bundle-groups" data-name="{{ name }}" data-id="{{ item.name }}">
                            <div class="list-groups"></div>
                            <a href="#" class="new-item"><?php esc_html_e( 'Add item', 'widgets-ultimate' ); ?></a>
                        </div>
                    <# break;  #>

                    <# default:  #>
                        <p class="w-admin-input-wrap">
                            <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>
                            <input class="widefat" type="text" id="{{ elementIdPrefix }}-{{ item.name }}" name="{{ name }}" value="{{ value }}">
                        </p>
                    <# break;  #>

                <# } // end swicth #>
            <# }); // end each #>
        </script>
        <script type="text/html" id="tmpl-">
            <div class="group-item">
                <div class="group-item-header">
                    <div class="group-item-title"><?php esc_html_e( 'Title here', 'widgets-ultimate' ); ?></div>
                    <div class="group-item-toggle"></div>
                </div>
                <div class="group-fields-inner">
                    <div class="group-action"><a href="#" class="group-item-remove"><?php esc_html_e( 'Remove', 'widgets-ultimate' ); ?></a></div>
                </div>
            </div>';
        </script>
        <?php
    }


    function setup_instance( $instance, $keep_keys = array( ) )
    {
        $r = array();
        foreach ($this->get_configs() as $f) {
            if (isset($f['name'])) {
                if (isset($instance[$f['name']])) {
                    $r[$f['name']] = $instance[$f['name']];
                } else if (isset($f['default']) && empty($instance)) {
                    $r[$f['name']] = $f['default'];
                } else {
                    $r[$f['name']] = null;
                }
            }
        }

        if (is_array($keep_keys)) {
            foreach ($keep_keys as $k) {
                if (isset($instance[$k])) {
                    $r[$k] = $instance[$k];
                } else {
                    $r[$k] = null;
                }
            }
        }

        return $r;
    }

    public function widget($args, $instance)
    {
        esc_html_e('function OnePress_plus_Widget_Base::widget() must be over-ridden in a sub-class.', 'widgets-ultimate');
    }

    function config(){
        return array();
    }

    function setup_fields( $fields,  $lv = 1 ){
        $setup_fields = array();
        if ( $lv > 2 ) { // just support 2 lv
            return $setup_fields;
        }

        foreach ( $fields as $field ) {
            $field = wp_parse_args( $field, array(
                'label'     => '',
                'name'      => '',
                'type'      => '',
                'default'   => null,
            ) );

            switch ( $field['type'] ) {
                case 'select':
                    if ( ! isset( $field['options'] ) || ! is_array( $field['options'] ) ) {
                        $field['options'] = array();
                    }
                    break;
                case 'group':
                    if ( ! isset( $field['fields'] ) || ! is_array( $field['fields'] ) ) {
                        $field['fields'] = array();
                    }

                    if ( ! isset( $field['title_id'] ) || ! is_array( $field['title_id'] ) ) {
                        $field['title_id'] = '';
                    }

                    $field['fields'] = $this->setup_fields( $field['fields'], $lv + 1 );
                    break;
            }

            if ( $field['name'] ) {
                $setup_fields[ $field['name'] ] = $field;
            }
        }
        return $setup_fields;
    }


    function get_configs()
    {
        return $this->setup_fields( $this->config() );

    }



    public function form( $instance )
    {

        $form_id = 'wu_widget_form_'.md5( uniqid( rand(), true ) );
        ?>
        <div id="<?php echo esc_attr( $form_id ); ?>" class="widget-ultimate-fields">
            <div class="bundle-widget-fields" data-values="<?php echo esc_attr( json_encode( $instance ) ); ?>" data-name="<?php echo $this->get_field_name( '__wname__' ); ?>" data-widget="<?php echo esc_attr( get_class( $this ) ); ?>"></div>
            <script type="text/javascript">
                ( function($) {
                    // Init once admin scripts have been loaded
                    $( document).trigger( 'widget-added', [ $( '#<?php echo $form_id; ?>' ) ] );
                } )( jQuery );
            </script>
        </div>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        return $new_instance;
        /*
        $instance = array();
        foreach ($this->get_configs() as $field) {
            $field = wp_parse_args($field, array(
                'name' => '',
            ));

            if ($field['name']) {
                if (isset($new_instance[$field['name']])) {
                    $instance[$field['name']] = $new_instance[$field['name']];
                } else {
                    $instance[$field['name']] = '';
                }
            }
        }
        return $instance;
        */
    }

}