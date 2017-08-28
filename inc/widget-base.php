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

        wp_register_script( 'jquery-visibly', $Widget_Ultimate->get('url').'assets/js/jquery-visibly.js', array( 'jquery'), false, true );
        wp_register_script( 'widget-ultimate-widget-admin', $Widget_Ultimate->get('url').'assets/js/widget-admin.js', array( 'jquery', 'jquery-visibly' ), false, true );
        wp_register_style( 'widget-ultimate-widget-admin', $Widget_Ultimate->get('url').'assets/css/widget-admin.css' );
        wp_register_style( 'font-awesome', $Widget_Ultimate->get('url').'assets/font-awesome/css/font-awesome.css' );
        if ( defined( 'ELEMENTOR_VERSION' ) ) {
            $this->plugin = Elementor\Plugin::instance();
            if ( !empty( $this->plugin->preview ) && method_exists( $this->plugin->preview, 'is_preview_mode' ) && $this->plugin->preview->is_preview_mode() ) {
                wp_enqueue_style('wp-dashicons', site_url('/') . 'wp-includes/css/dashicons.css');
            }

        }
        wp_enqueue_script( 'widget-ultimate-widget-admin' );
        wp_enqueue_style( 'widget-ultimate-widget-admin' );
        wp_enqueue_style( 'font-awesome' );

        // Widget settings
        wp_localize_script( 'widget-ultimate-widget-admin' , get_class( $this ), $this->get_configs() );
        wp_localize_script( 'widget-ultimate-widget-admin' , 'WIDGET_US', array(
            'ajax' => admin_url( 'admin-ajax.php' ),
            'group_item_title' => Widget_Ultimate::l10n('group_item_title'),
            'remove' => Widget_Ultimate::l10n( 'remove' ),
        ) );

    }

    /**
     * Render form template scripts.
     *
     * @since 4.8.0
     * @access public
     */
    public function render_control_template_scripts() {
        if ( isset( $GLOBALS['render_control_template_scripts_loaded'] ) ) {
            return;
        }
        $GLOBALS['render_control_template_scripts_loaded'] = 1;
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


                    var visibly = '';
                    if ( item.cond ) {
                        visibly = item.cond.replace(/__id__/g, elementIdPrefix+'-' );
                    }
                #>
                <# switch( item.type ){ case 'select':  #>
                    <div class="w-admin-input-wrap" <# if( visibly ) { #> visibly="{{ visibly }}"<# } #>>
                        <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>
                        <# if ( item.desc  ){  #>
                            <div class="item-desc">{{{ item.desc }}}</div>
                        <# } #>
                        <div class="select-wrapper">
                            <select class="fid-{{ item.name }}" id="{{ elementIdPrefix }}-{{ item.name }}" name="{{ name }}">
                                <# _.each( item.options, function( v, k ){  #>
                                    <option <# if ( k == value ) { #>selected="selected"<# } #> value="{{ k }}">{{ v }}</option>
                                <# }); // end each #>
                            </select>
                        </div>
                    </div>
                    <# break;  #>

                    <# case 'textarea': #>
                        <div class="w-admin-input-wrap" <# if( visibly ) { #> visibly ="{{ visibly }}"<# } #>>
                            <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>
                            <# if ( item.desc  ){  #>
                                <div class="item-desc">{{{ item.desc }}}</div>
                            <# } #>
                            <textarea rows="6"  class="widefat" id="{{ elementIdPrefix }}-{{ item.name }}" name="{{ name }}" >{{ value }}</textarea>
                        </div>
                    <# break;  #>

                    <# case 'checkbox': #>
                        <div class="w-admin-input-wrap input-wrap-{{ item.type }}"<# if( visibly ) { #> visibly="{{ visibly }}"<# } #>>
                            <input id="{{ elementIdPrefix }}-{{ item.name }}" <# if ( value == "on" ) { #>checked="checked"<# } #> name="{{ name }}" type="checkbox" value="on">
                            <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>

                            <# if ( item.desc  ){  #>
                                <div class="item-desc">{{{ item.desc }}}</div>
                            <# } #>
                        </div>
                    <# break;  #>

                    <# case 'editor': #>
                        <div class="w-admin-input-wrap"<# if( visibly ) { #> visibly="{{ visibly }}"<# } #>>
                            <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>
                            <# if ( item.desc  ){  #>
                                <div class="item-desc">{{{ item.desc }}}</div>
                            <# } #>
                            <textarea rows="6" class="widefat editor" id="{{ elementIdPrefix }}-{{ item.name }}" name="{{ name }}" >{{ value }}</textarea>
                        </div>
                    <# break;  #>

                    <# case 'source': source = JSON.stringify( item.source ); if( ! value ) { value = {}; }   #>
                        <div class="w-admin-input-wrap object-source name-{{ item.name }}"  data-source="{{ source }}"<# if( visibly ) { #> visibly="{{ visibly }}"<# } #>>
                            <label for="{{ elementIdPrefix }}-{{ item.name }}-label">{{{ item.label }}}</label>
                            <# if ( item.desc  ){  #>
                                <div class="item-desc">{{{ item.desc }}}</div>
                            <# } #>
                            <div class="object-label-w">
                                <input class="widefat object-label" readonly value="{{ value.name }}">
                                <span class="object-clear"><span class="dashicons dashicons-no-alt"></span></span>
                            </div>
                            <div class="object-ajax-search">
                                <input class="widefat object-ajax-input" type="text" id="{{ elementIdPrefix }}-{{ item.name }}-label" placeholder="<?php echo esc_attr( Widget_Ultimate::l10n('type-keyword') ); ?>">
                                <input class="object-id fid-{{ item.name }}" type="hidden" name="{{ name }}" id="{{ elementIdPrefix }}-{{ item.name }}"  value="{{ value.id }}">
                                <ul class="object-results"></ul>
                            </div>
                        </div>
                    <# break;  #>

                    <# case 'image': case 'video': case 'file': #>
                        <#
                            var c = '', preview = '';
                            if ( typeof value !== 'object' ) {
                                value = {};
                            }
                            if ( value ) {
                                if ( value.preview ) {
                                    c = 'attachment-added';
                                }
                            }

                            #>
                        <div class="w-admin-input-wrap name-{{ item.name }}"<# if( visibly ) { #> visibly="{{ visibly }}"<# } #>>
                            <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>
                            <# if ( item.desc  ){  #>
                                <div class="item-desc">{{{ item.desc }}}</div>
                            <# } #>
                            <div class="widget-attachment-input widget-{{ item.type }}-input {{ c }}" data-type="{{ item.type }}">
                                <input class="widefat attachment-id" type="hidden" id="{{ elementIdPrefix }}-{{ item.name }}" name="{{ name }}" value="{{ value.id }}">
                                <div class="media-item-preview">{{{ value.preview }}}</div>
                                <p class="media-widget-buttons">
                                    <button type="button" class="button remove-media"><?php echo Widget_Ultimate::l10n('remove'); ?></button>
                                    <button type="button" class="button change-media"><?php echo Widget_Ultimate::l10n('replace'); ?></button>
                                    <button type="button" class="button select-media"><?php echo Widget_Ultimate::l10n('add' ); ?></button>
                                </p>

                            </div>
                        </div>
                    <# break;  #>

                    <# case 'color': #>
                        <div class="w-admin-input-wrap name-{{ item.name }}"<# if( visibly ) { #> visibly="{{ visibly }}"<# } #>>
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

                    <# case 'icon': #>
                        <div class="w-admin-input-wrap object-icon name-{{ item.name }}" <# if( visibly ) { #> visibly="{{ visibly }}"<# } #>>
                            <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>
                            <# if ( item.desc  ){  #>
                                <div class="item-desc">{{{ item.desc }}}</div>
                            <# } #>
                            <div class="object-label-w object-icon-picker">
                                <div class="icon-preview"><i class="{{ value }}"></i></div>
                                <input class="widefat object-label" name="{{ name }}" value="{{ value }}" readonly>
                                <span class="object-clear"><span class="dashicons dashicons-no-alt"></span></span>
                            </div>
                        </div>
                    <# break;  #>

                    <# case 'group': #>
                        <div class="w-admin-input-wrap bundle-groups" data-name="{{ name }}" data-id="{{ item.name }}"<# if( visibly ) { #> visibly="{{ visibly }}"<# } #>>
                            <# if ( item.label  ){  #>
                            <label for="group-label">{{{ item.label }}}</label>
                            <# } #>
                            <# if ( item.desc  ){  #>
                                <div class="item-desc">{{{ item.desc }}}</div>
                            <# } #>
                            <div class="list-groups"></div>
                            <a href="#" class="new-item"><?php echo Widget_Ultimate::l10n( 'add-item' ); ?></a>
                        </div>
                    <# break;  #>
                    <?php do_action( 'widget-ultimate-more-fields' ); ?>
                    <# default:  #>
                        <div class="w-admin-input-wrap"<# if( visibly ) { #> visibly="{{ visibly }}"<# } #>>
                            <label for="{{ elementIdPrefix }}-{{ item.name }}">{{{ item.label }}}</label>
                            <# if ( item.desc  ){  #>
                                <div class="item-desc">{{{ item.desc }}}</div>
                            <# } #>
                            <input class="widefat wu-text fid-{{ item.name }}" type="text" data-id="{{ item.name }}" id="{{ elementIdPrefix }}-{{ item.name }}" name="{{ name }}" value="{{ value }}">
                        </div>
                    <# break;  #>

                <# } // end swicth #>
            <# }); // end each #>
        </script>
        <script type="text/html" id="tmpl-widget-group-item">
            <div class="group-item">
                <div class="group-item-header">
                    <div class="group-item-title"><?php echo Widget_Ultimate::l10n('title-here'); ?></div>
                    <div class="group-item-toggle"></div>
                </div>
                <div class="group-fields-inner">
                    <div class="group-action"><a href="#" class="group-item-remove"><?php echo Widget_Ultimate::l10n('remove'); ?></a></div>
                </div>
            </div>';
        </script>


        <div tabindex="0" id="widgets-ultimate-icons-picker">
            <div class="media-modal wp-core-ui">
                <button type="button" class="media-modal-close"><span class="media-modal-icon"><span class="screen-reader-text">Close media panel</span></span>
                </button>
                <div class="media-modal-content">
                    <div class="media-frame mode-select wp-core-ui hide-menu">

                        <div class="media-frame-title">
                            <h1><?php echo Widget_Ultimate::l10n('icons'); ?><span class="dashicons dashicons-arrow-down"></span></h1>
                        </div>

                        <div class="media-frame-router">
                            <div id="icons-picker-media-router" class="media-router">

                            </div>
                        </div>

                        <div class="media-frame-content">
                            <div class="attachments-browser">

                                <div class="media-toolbar">
                                    <div class="media-toolbar-secondary">
                                        <input placeholder="<?php echo esc_attr( Widget_Ultimate::l10n('search-icon') ); ?>" id="icons-search-input" class="search" type="search">
                                    </div>
                                    <div class="media-toolbar-primary search-form">

                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="media-frame-toolbar">
                            <div class="media-toolbar">
                                <div class="media-toolbar-secondary"></div>
                                <div class="media-toolbar-primary search-form">
                                    <button type="button" class="button media-button button-primary button-large media-button-select" disabled="disabled"><?php echo Widget_Ultimate::l10n('select'); ?></button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="media-modal-backdrop"></div>
        </div>

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
                'required'  => '',
                'cond'      => '',
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
                    if ( ! isset( $field['title_id'] ) ) {
                        $field['title_id'] = '';
                    }
                    $field['fields'] = $this->setup_fields( $field['fields'], $lv + 1 );
                    break;
            }

            if ( ! $field['cond'] ) {
                if ( is_array( $field['required'] ) ) {
                    $field['cond'] = $this->setup_conditional( $field['required'] );
                }
            }

            if ( $field['name'] ) {
                $setup_fields[ $field['name'] ] = $field;
            }
        }
        return $setup_fields;
    }

    function setup_conditional( $required ){
        if ( ! $required || ! is_array( $required ) ) {
            return false;
        }

        if ( isset( $required['when'] ) && isset( $required['is'] ) ) {
            if ( is_array( $required['is'] ) ) {
                $required['is'] = join( ',', $required['is'] );
            }
            $cond = sprintf( '__id__%1$s:%2$s',  $required['when'], $required['is'] );
        } else {
            $cond = array();
            foreach ( $required as $r ) {
                if ( isset( $r['when'] ) && isset( $r['is'] ) ) {
                    if ( is_array( $r['is'] ) ) {
                        $r['is'] = join(',', $r['is']);
                    }
                    $cond[] = sprintf('__id__%1$s:%2$s', $r['when'], $r['is']);
                }
            }
            $cond = join( ';', $cond );
        }
        return $cond;
    }


    function get_configs()
    {
        if (  $this->fields ) {
            return $this->fields;
        }
        $this->fields = $this->setup_fields( $this->config() );
        return $this->fields;
    }

    function setup_default_values( $values = array(), $fields = array() ){
        if ( ! is_array( $values ) ) {
            $values = array();
        }
        foreach ( $fields as $f ) {
            if ( ! isset( $values[ $f['name'] ] ) ) {
                $values[ $f['name'] ] = $f['default'];
            }
            if ( $f['type'] == 'group' ) {
                foreach( ( array ) $values[ $f['name'] ]  as $k => $v ) {
                    $values[ $f['name'] ][ $k ] = $this->setup_default_values( $v, $f['fields'] );
                }
            }
        }
        return $values;
    }

    /**
     * Get Image from value
     * @param string|array $value Value to get image, can be array or image id
     * @param string $size Image size
     * @return string|bool
     */
    function get_image( $value = null, $size = 'full' ){
        if ( ! $value ) {
            return false;
        }
        if ( is_numeric( $value ) ) {
            $src = wp_get_attachment_image_src( $value, $size );
            if ( ! $src ) {
                return false;
            }
            return $src[0];
        } elseif( is_string( $value ) ) {
            return $value;
        } elseif ( is_array( $value ) ) {
            $value = wp_parse_args( $value, array(
                'id' => '',
                'src' => ''
            ) );
            if ( $value['id'] && is_numeric( $value['id'] ) ) {
                $src = wp_get_attachment_image_src( $value['id'], $size );
                if ( ! $src ) {
                    return false;
                }
                return $src[0];
            }
        }

        return false;
    }

    function setup_values( $values = array(), $fields = array() ){
        if ( ! is_array( $values ) ) {
            return $values;
        }
        foreach ( $fields as $f ) {
            if ( ! isset( $values[ $f['name'] ] ) ) {
                $values[ $f['name'] ] = $f['default'];
            }
        }

        foreach ( $values as $key => $value ) {
            if ( isset( $fields[ $key ] ) ) {
                switch ( $fields[ $key ]['type']  ) {
                    case 'image':
                        $src = false;
                        if ( is_numeric( $value ) ) {
                            $src = wp_get_attachment_image_src( $value, 'thumbnail' );
                            if ( $src ) {
                                $src = $src[0];
                            }
                        } else {
                            $src = $value;
                        }

                        $values[ $key ] = array(
                            'id' => '',
                            'preview' => ''
                        );
                        if (  $src ) {
                            $values[ $key ] = array(
                                'id' => $value,
                                'preview' => '<img src="'.esc_url( $src ).'" alt="">'
                            );
                        }
                        break;

                    case 'video':
                        $src = wp_get_attachment_url( $value );
                        $values[ $key ] = array(
                            'id' => '',
                            'preview' => ''
                        );

                        if (  $src ) {
                            $mime = get_post_mime_type( $value );
                            $preview = '<video width="100%" height="" controls><source src="'.esc_url( $src ).'" type="'.esc_attr( $mime ).'">Your browser does not support the video tag.</video>';
                            $values[ $key ] = array(
                                'id' => $value,
                                'preview' => $preview
                            );
                        }
                        break;
                    case 'file':
                        $src = wp_get_attachment_url( $value );
                        $values[ $key ] = array(
                            'id' => '',
                            'preview' => ''
                        );

                        if (  $src ) {
                            $preview = '<a href="'.esc_url( $src ).'" target="_blank">'.basename( $src ).'</a>';
                            $values[ $key ] = array(
                                'id' => $value,
                                'preview' => $preview
                            );
                        }
                        break;
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
                            if ( $p && $p['post_type'] == $fields[ $key ]['source']['post_type'] ) {
                                $values[ $key ] = $p;
                                $values[ $key ]['id'] = $p['ID'];
                                $values[ $key ]['name'] = $p['post_title'];
                            } else {
                                $values[ $key ] = null;
                            }
                        }
                        break;

                    case 'group':
                        foreach ( ( array ) $value as $_k => $_v ) {
                            $values[ $key ][ $_k ] = $this->setup_values( $value[ $_k ], $fields[ $key ]['fields'] );
                        }
                        break;
                }

            } else {
                unset( $values[ $key ] );
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

    function sanitize( $values = array(), $fields = array() ){
        if ( ! is_array( $values ) ) {
            return wp_kses_post( $values );
        }

        foreach ( $values as $key => $value ) {
            if ( isset( $fields[ $key ] ) ) {
                switch ( $fields[ $key ]['type']  ) {
                    case 'image':  case 'video': case 'file':  case 'source':
                        $values[ $key ] = absint( $value );
                        break;
                    case 'group':
                        foreach ( ( array ) $value as $_k => $_v ) {
                            $values[ $key ][ $_k ] = $this->setup_values( $value[ $_k ], $fields[ $key ]['fields'] );
                        }
                        break;
                    case 'color':
                        $value = sanitize_hex_color_no_hash( $value );
                        if ( $value ) {
                            $value = '#'.$value;
                        }
                        $values[ $key ] = $value;
                        break;
                    case 'select':
                        if ( isset( $field['options'][ $value ] ) ) {
                            $values[ $key ] = $value;
                        } else {
                            $values[ $key ] = $field['default'];
                        }
                        break;
                    case 'icon':
                        $values[ $key ] = sanitize_text_field( $value );
                        break;
                    case 'checkbox':
                        $values[ $key ] = ( $value ) ? 'on' : false ;
                        break;
                    default:
                        $values[ $key ] = wp_kses_post( $value  );
                }

            }
        }

        foreach ( $fields as $f ) {
            if ( ! isset( $values[ $f['name'] ] ) ) {
                $values[ $f['name'] ] = $f['default'];
            }
        }

        return $values;
    }


    public function update($new_instance, $old_instance)
    {
        //return $new_instance;
        return $this->sanitize( $new_instance );
    }

    function the_content( $instance ){
        var_dump( $instance );
    }

    public function widget($args, $instance)
    {
        $instance = $this->setup_values( $instance, $this->get_configs() );
        $title = false;
        if ( isset( $instance['title'] ) ) {
            $title = $instance['title'];

        }
        echo $args['before_widget'];

        if ( ! empty( $title ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }


        $this->the_content( $instance );

        echo $args['after_widget'];
    }


}