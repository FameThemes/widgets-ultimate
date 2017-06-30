<?php

/**
 * Widget Base
 */
class Widget_Ultimate_Widget_Base extends WP_Widget
{

    private $config = array();

    private $fields = null;


    public function __construct($id_base = '', $name = '', $widget_options = array(), $control_options = array())
    {
        global $Widget_Ultimate;
        $this->config = array(
            'prefix' => 'widget-ultimate',
            'url'    => $Widget_Ultimate->get('url').'assets/'
        );
        parent::__construct($id_base, $name, $widget_options, $control_options);

        $this->fields = $this->get_configs();

        $this->admin_scripts();

        if ( defined( 'ELEMENTOR_VERSION' ) ) {
            add_action('elementor/editor/before_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
            add_action('elementor/editor/footer', array($this, 'render_control_template_scripts'));
        }

    }

    function admin_scripts(){
        add_action( 'admin_print_scripts', array( $this, 'enqueue_admin_scripts' ) );
        add_action( 'admin_footer', array( $this, 'render_control_template_scripts' ) );
        add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        add_action( 'customize_controls_print_footer_scripts', array( $this, 'render_control_template_scripts' ) );
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
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        global $Widget_Ultimate;

        wp_register_script( 'widget-ultimate-widget-admin', $Widget_Ultimate->get('url').'assets/js/widget-admin.js', array( 'jquery'), false, true );
        wp_register_style( 'widget-ultimate-widget-admin', $Widget_Ultimate->get('url').'assets/css/widget-admin.css' );
        if ( defined( 'ELEMENTOR_VERSION' ) ) {
            $this->plugin = Elementor\Plugin::instance();
            if ( !empty( $this->plugin->preview ) && method_exists( $this->plugin->preview, 'is_preview_mode' ) && $this->plugin->preview->is_preview_mode() ) {
                wp_enqueue_style('wp-dashicons', site_url('/') . 'wp-includes/css/dashicons.css');
            }

        }
        wp_enqueue_script( 'widget-ultimate-widget-admin' );
        wp_enqueue_style( 'widget-ultimate-widget-admin' );


        // Widget settings
        wp_localize_script( 'widget-ultimate-widget-admin' , get_class( $this ), $this->get_configs() );
        wp_localize_script( 'widget-ultimate-widget-admin' , 'WIDGET_US', array(
            'ajax' => admin_url( 'admin-ajax.php' )
        ) );

    }

    /**
     * Render form template scripts.
     *
     * @since 4.8.0
     * @access public
     */
    public function render_control_template_scripts() {

        wp_dropdown_pages();
        ?>
        <script type="text/html" id="tmpl-widget-bundle-fields">
            <#
                var elementIdPrefix = 'el' + String( Math.random() ).replace( /\D/g, '' ) + '-'
                var source;
            #>
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
                    <div class="w-admin-input-wrap">
                        <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>
                        <# if ( item.desc  ){  #>
                            <div class="item-desc">{{{ item.desc }}}</div>
                        <# } #>
                        <select id="{{ elementIdPrefix }}-{{ item.name }}" name="{{ name }}">
                            <# _.each( item.options, function( v, k ){  #>
                                <option <# if ( k == value ) { #>selected="selected"<# } #> value="{{ k }}">{{ v }}</option>
                            <# }); // end each #>
                        </select>
                    </div>
                    <# break;  #>

                    <# case 'textarea': #>
                        <div class="w-admin-input-wrap">
                            <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>
                            <# if ( item.desc  ){  #>
                                <div class="item-desc">{{{ item.desc }}}</div>
                            <# } #>
                            <textarea rows="6"  class="widefat" id="{{ elementIdPrefix }}-{{ item.name }}" name="{{ name }}" >{{ value }}</textarea>
                        </div>
                    <# break;  #>

                    <# case 'checkbox': #>
                        <div class="w-admin-input-wrap">
                            <input id="{{ elementIdPrefix }}-{{ item.name }}" <# if ( value == "on" ) { #>checked="checked"<# } #> name="{{ name }}" type="checkbox" value="on">
                            <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>

                            <# if ( item.desc  ){  #>
                                <div class="item-desc">{{{ item.desc }}}</div>
                            <# } #>
                        </div>
                    <# break;  #>

                    <# case 'editor': #>
                        <div class="w-admin-input-wrap">
                            <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>
                            <# if ( item.desc  ){  #>
                                <div class="item-desc">{{{ item.desc }}}</div>
                            <# } #>
                            <textarea rows="6" class="widefat editor" id="{{ elementIdPrefix }}-{{ item.name }}" name="{{ name }}" >{{ value }}</textarea>
                        </div>
                    <# break;  #>

                    <# case 'source': source = JSON.stringify( item.source ); if( ! value ) { value = {}; }   #>
                        <div class="w-admin-input-wrap object-source "  data-source="{{ source }}">
                            <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>
                            <# if ( item.desc  ){  #>
                                <div class="item-desc">{{{ item.desc }}}</div>
                            <# } #>
                            <div class="object-label-w">
                                <div class="object-label">{{ value.name }}</div>
                                <span class="object-clear"><span class="dashicons dashicons-no-alt"></span></span>
                            </div>
                            <div class="object-ajax-search">
                                <input class="widefat object-ajax-input" type="text" placeholder="<?php esc_attr_e( 'Type keyword...', 'widgets-ultimate' ); ?>" id="{{ elementIdPrefix }}-{{ item.name }}">
                                <input class="object-id" type="hidden" name="{{ name }}"  value="{{ value.id }}">
                                <ul class="object-results"></ul>
                            </div>
                        </div>
                    <# break;  #>

                    <# case 'image': case 'video': case 'file': #>
                        <div class="w-admin-input-wrap">
                            <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>
                            <# if ( item.desc  ){  #>
                                <div class="item-desc">{{{ item.desc }}}</div>
                            <# } #>
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
                        <div class="w-admin-input-wrap">
                            <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>
                            <# if ( item.desc  ){  #>
                                <div class="item-desc">{{{ item.desc }}}</div>
                            <# } #>
                            <div class="color-input">
                                <input class="widefat color-val" type="hidden" id="{{ elementIdPrefix }}-{{ item.name }}" name="{{ name }}" value="{{ value }}">
                                <input class="color-picker widefat" value="{{ value }}">
                            </div>
                        </div>
                    <# break;  #>

                    <# case 'group': #>
                        <div class="w-admin-input-wrap bundle-groups" data-name="{{ name }}" data-id="{{ item.name }}">
                            <# if ( item.label  ){  #>
                            <label for="group-label">{{{ item.label }}}</label>
                            <# } #>
                            <# if ( item.desc  ){  #>
                                <div class="item-desc">{{{ item.desc }}}</div>
                            <# } #>
                            <div class="list-groups"></div>
                            <a href="#" class="new-item"><?php esc_html_e( 'Add item', 'widgets-ultimate' ); ?></a>
                        </div>
                    <# break;  #>

                    <# default:  #>
                        <div class="w-admin-input-wrap">
                            <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>
                            <# if ( item.desc  ){  #>
                                <div class="item-desc">{{{ item.desc }}}</div>
                            <# } #>
                            <input class="widefat" type="text" id="{{ elementIdPrefix }}-{{ item.name }}" name="{{ name }}" value="{{ value }}">
                        </div>
                    <# break;  #>

                <# } // end swicth #>
            <# }); // end each #>
        </script>
        <script type="text/html" id="tmpl-widget-group-item">
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
                case 'source':
                    if ( ! isset( $field['source'] ) || ! is_array( $field['source'] ) ) {
                        $field['source'] = array();
                    }
                    $field['source'] = wp_parse_args( $field['source'], array(
                        'post_type' => '', // or any post type
                        'tax'       => '', // or any tax name
                    ) );
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

        if (  $this->fields ) {
            return $this->fields;
        }

        $this->fields = $this->setup_fields( $this->config() );
        return $this->fields;
    }

    function setup_values( $values = array(), $fields = array() ){
        if ( ! is_array( $values ) ) {
            return $values;
        }

        foreach ( $values as $key => $value ) {
            if ( isset( $fields[ $key ] ) ) {
                switch ( $fields[ $key ]['type']  ) {
                    case 'source':
                        if ( $fields[ $key ]['source']['tax'] != '' ) {
                            $t = get_term( $value, $fields[ $key ]['source']['tax'], ARRAY_A );
                            if ( ! is_wp_error( $t ) && ! empty( $t ) ) {
                                $values[ $key ] = $t;
                                $values[ $key ]['id']   = $t['term_id'];
                                $values[ $key ]['name'] = $t['name'];
                            } else {
                                $values[ $key ] = null;
                            }
                        } else {
                            $p = get_post( $value, ARRAY_A );
                            if ( $p && get_post_type( $p ) == $fields[ $key ]['source']['post_type'] ) {
                                $values[ $key ] = $p;
                                $values[ $key ]['id'] = $p['ID'];
                                $values[ $key ]['name'] = $p['post_title'];
                            } else {
                                $values[ $key ] = null;
                            }
                        }
                        break;

                    case 'group':
                        foreach ( $value as $_k => $_v ) {
                            $values[ $key ][ $_k ] = $this->setup_values( $value[ $_k ], $fields[ $key ]['fields'] );
                        }
                        break;
                }

            }
        }

        return $values;
    }


    public function form( $instance )
    {

        $form_id = 'wu_widget_form_'.md5( uniqid( rand(), true ) );
        $instance = $this->setup_values( $instance, $this->get_configs() );
        ?>
        <div id="<?php echo esc_attr( $form_id ); ?>" class="widget-ultimate-fields">

            <div class="bundle-widget-fields" data-values="<?php echo esc_attr( json_encode( $instance ) ); ?>" data-name="<?php echo $this->get_field_name( '__wname__' ); ?>" data-widget="<?php echo esc_attr( get_class( $this ) ); ?>">
                <input type="hidden" class="wu_input_base" name="<?php echo $this->get_field_name( 'wu_base' ); ?>">
            </div>
            <script type="text/javascript">
                ( function($) {
                    // Init once admin scripts have been loaded
                    $( document).trigger( 'widget-ultimate-added', [ $( '#<?php echo $form_id; ?>' ) ] );
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