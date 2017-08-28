<?php
if ( ! class_exists( 'Widget_Ultimate' ) ) {
    final class Widget_Ultimate
    {
        public $url;
        public $path;

        function __construct($path, $url)
        {

            //$this->url =  trailingslashit( plugins_url('', __FILE__) );
            //$this->path = trailingslashit( plugin_dir_path( __FILE__) );

            $this->url = $url;
            $this->path = $path;

            $this->includes();
            add_action('wp', array($this, 'scripts_init'));
            add_action('wp_ajax_widget_ultimate_search', array($this, 'ajax_search'));
            add_action('wp_ajax_widget_ultimate_icons', array($this, 'ajax_icons'));

        }

        static function l10n( $key = null ){
            $l10n = array(
                'font-awesome-name' =>  esc_html__( 'FontAwesome', 'glow' ),
                'remove'            =>  esc_html__( 'Remove', 'glow' ),
                'replace'           =>  esc_html__( 'Replace', 'glow' ),
                'add'               =>  esc_html__( 'Add', 'glow' ),
                'select'            =>  esc_html__( 'Select', 'glow' ),
                'add-item'          =>  esc_html__( 'Add Item', 'glow' ),
                'group_item_title'  =>  esc_html__( 'Untitled', 'glow' ),
                'title-here'        =>  esc_html__( 'Title here', 'glow' ),
                'icons'             =>  esc_html__( 'Icons', 'glow' ),
                'search-icon'       =>  esc_html__( 'Search icons', 'glow' ),
                'type-keyword'      =>  esc_html__( 'Type keyword...', 'glow' ),
            );
            if ( isset( $l10n[ $key ] ) ) {
                return $l10n[ $key ];
            } else {
                return $l10n;
            }
        }

        function scripts_init()
        {
            if (class_exists('FLBuilderModel')) {
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

        function get($var)
        {
            if (property_exists($this, $var)) {
                return $this->{$var};
            }
            return null;
        }

        function register_widgets()
        {

            if ( defined( 'WP_DEBUG' ) ) {
                if ( WP_DEBUG ) {
                    if( file_exists( $this->path . 'widgets/class-widget-test.php' ) ) {
                        require_once $this->path . 'widgets/class-widget-test.php';
                        register_widget('Widget_Ultimate_Test');
                    }
                }
            }
            
            $widgets = array(
                'features', 'clients', 'about', 'services', 'videolightbox',
                'gallery', 'projects', 'counter', 'testimonial', 'pricing', 'cta',
                'team', 'news', 'contact', 'map', 'hero'
            );

            $widgets = apply_filters('widget_ultimate_register_widgets', $widgets);

            foreach ($widgets as $widget) {
                $file = $this->path . 'widgets/class-widget-' . $widget . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    $class_name = 'Widget_Ultimate_' . ucfirst($widget);
                    if (class_exists($class_name)) {
                        register_widget($class_name);
                    }
                }
            }

        }

        function includes()
        {
            require_once $this->path . 'inc/class-font-icons.php';
            require_once $this->path . 'inc/widget-base.php';
            add_action('widgets_init', array($this, 'register_widgets'));
        }

        function ajax_icons()
        {
            if (!class_exists('Widget_Ultimate_Font_Icons')) {
                require_once $this->path . 'inc/class-font-icons.php';
            }
            global $Widget_Ultimate_Font_Icons;
            $Widget_Ultimate_Font_Icons->ajax();
        }

        function ajax_search()
        {
            $search = isset($_REQUEST['search']) ? $_REQUEST['search'] : false;
            $tax = isset($_REQUEST['tax']) ? $_REQUEST['tax'] : false;
            $post_type = $_REQUEST['post_type'] ? $_REQUEST['post_type'] : false;
            if ($tax) {
                if (!taxonomy_exists($tax)) {
                    $tax = false;
                }
            }

            if ($post_type) {
                if (!post_type_exists($post_type)) {
                    $post_type = 'page';
                }
            }

            $results = array(
                'type' => '',
                'items' => array()
            );

            if ($tax) {
                $terms = get_terms(array(
                    'taxonomy' => $tax,
                    'search' => $search,
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'number' => '30',
                ));

                $results['type'] = 'terms';

                if (!is_wp_error($terms) && !empty($terms)) {
                    foreach ($terms as $index => $t) {
                        $results['items'][$index] = array(
                            'title' => $t->name,
                            'id' => $t->term_id
                        );
                    }
                }
            } else {
                $query = new WP_Query(array(
                    'post_type' => $post_type,
                    's' => $search,
                    'orderby' => 'title',
                    'order' => 'ASC',
                    'post_status' => array('pending', 'publish', 'future')
                ));

                $results['type'] = 'posts';

                if ($query - have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        $results['items'][] = array(
                            'title' => get_the_title(),
                            'id' => get_the_ID()
                        );
                    }
                }

            }

            wp_send_json($results);
            die();
        }

    }
}