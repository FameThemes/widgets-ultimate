<?php
/**
 * Widget Features
 */
class Widget_Ultimate_Features extends Widget_Ultimate_Widget_Base {


    public function __construct() {

        $control_ops = array(
            'width' => 500,
            'height' => 350,
        );

        //$control_ops = null;
        parent::__construct(
            'widget-ultimate-features',
            esc_html__( 'Features', 'widgets-ultimate' ),
            array(
                'classname'     => 'widget-features',
                'description'   => esc_html__( 'DisplayFeatures', 'widgets-ultimate' )
            ),
            $control_ops
        );
    }

    function config( ){
        $fields = array(
            array(
                'type' =>'text',
                'name' => 'title',
                'label' => esc_html__( 'Title', 'widgets-ultimate' ),
                'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'source',
                'name' => 'source',
                'label' => esc_html__( 'Source Category', 'widgets-ultimate' ),
                'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
                'source' => array(
                    //'post_type' => 'page', // or any post type
                    'tax'       => 'category', // or any tax name
                )
            ),

            array(
                'type' =>'source',
                'name' => 'source_post',
                'label' => esc_html__( 'Source Post', 'widgets-ultimate' ),
                'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
                'source' => array(
                    'post_type' => 'post', // or any post type
                )
            ),

            array(
                'type' =>'color',
                'name' => 'color',
                'label' => esc_html__( 'color', 'widgets-ultimate' ),
                'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'textarea',
                'name' => 'textarea',
                'label' => esc_html__( 'Textarea', 'widgets-ultimate' ),
                'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'editor',
                'name' => 'editor',
                'label' => esc_html__( 'Editor', 'widgets-ultimate' ),
                'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'image',
                'name' => 'image',
                'label' => esc_html__( 'Image', 'widgets-ultimate' ),
                'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'video',
                'name' => 'video',
                'label' => esc_html__( 'Video', 'widgets-ultimate' ),
                'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'file',
                'name' => 'file',
                'label' => esc_html__( 'File', 'widgets-ultimate' ),
                'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'select',
                'name' => 'layout',
                'default' => '4',
                'label' => esc_html__( 'Select', 'widgets-ultimate' ),
                'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
                'options' => array(
                    '2'   => 2,
                    '3'   => 3,
                    '4'   => 4,
                    '5'   => 5,
                    '6'   => 6,
                ),
            ),

            array(
                'type' =>'checkbox',
                'name' => 'show_all',
                'default' => 'on',
                'label' => esc_html__( 'Check box here', 'widgets-ultimate' ),
                'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'group',
                'name' => 'group',
                'label'     => esc_html__( 'Group', 'widgets-ultimate' ),
                'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
                'title_id' => 'title', // support text field only
                'fields' => array(
                    array(
                        'type' =>'text',
                        'name' => 'title',
                        'label' => esc_html__( 'Title', 'widgets-ultimate' ),
                        'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'source',
                        'name' => 'source',
                        'label' => esc_html__( 'Source Category', 'widgets-ultimate' ),
                        'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
                        'source' => array(
                            //'post_type' => 'page', // or any post type
                            'tax'       => 'category', // or any tax name
                        )
                    ),

                    array(
                        'type' =>'source',
                        'name' => 'source_post',
                        'label' => esc_html__( 'Source Post', 'widgets-ultimate' ),
                        'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
                        'source' => array(
                            'post_type' => 'post', // or any post type
                        )
                    ),

                    array(
                        'type' =>'color',
                        'name' => 'color',
                        'label' => esc_html__( 'color', 'widgets-ultimate' ),
                        'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'textarea',
                        'name' => 'textarea',
                        'label' => esc_html__( 'Textarea', 'widgets-ultimate' ),
                        'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'image',
                        'name' => 'image',
                        'label' => esc_html__( 'Image', 'widgets-ultimate' ),
                        'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'video',
                        'name' => 'video',
                        'label' => esc_html__( 'Video', 'widgets-ultimate' ),
                        'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'file',
                        'name' => 'file',
                        'label' => esc_html__( 'File', 'widgets-ultimate' ),
                        'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'editor',
                        'name' => 'editor',
                        'label' => esc_html__( 'Editor', 'widgets-ultimate' ),
                        'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'select',
                        'name' => 'layout',
                        'default' => '4',
                        'label' => esc_html__( 'Select', 'widgets-ultimate' ),
                        'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
                        'options' => array(
                            '2'   => 2,
                            '3'   => 3,
                            '4'   => 4,
                            '5'   => 5,
                            '6'   => 6,
                        ),
                    ),

                    array(
                        'type' =>'checkbox',
                        'name' => 'show_all',
                        'default' => 'on',
                        'label' => esc_html__( 'Check box here', 'widgets-ultimate' ),
                        'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
                    ),
                )
            )

        );

        return $fields;

    }

    public function widget( $args, $instance )
    {

        if ( ! isset( $instance['__setup_data'] ) || ! $instance['__setup_data'] === false ){
            $instance = $this->setup_instance( $instance );
        }

        $title = $instance['title'];
        unset($instance['title']);


        echo $args['before_widget'];
        $title = apply_filters( 'widget_title', $title );

        echo rand( );

        echo $args['after_widget'];
    }

}