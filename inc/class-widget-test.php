<?php
/**
 * Widget test
 */
class OnePress_plus_Widget_Test extends Widget_Ultimate_Widget_Base {


    public function __construct() {

        $control_ops = array(
            'width' => 500,
            'height' => 350,
        );
        parent::__construct(
            'onepess_widget_test',
            esc_html__( 'TEST: Widget', 'widgets-ultimate' ),
            array(
                'description'   => esc_html__( 'Display products as tabs layout, recommended for front page', 'widgets-ultimate' )
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
            ),

            array(
                'type' =>'color',
                'name' => 'color',
                'label' => esc_html__( 'color', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'textarea',
                'name' => 'textarea',
                'label' => esc_html__( 'Textarea', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'editor',
                'name' => 'editor',
                'label' => esc_html__( 'Editor', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'image',
                'name' => 'image',
                'label' => esc_html__( 'Image', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'video',
                'name' => 'video',
                'label' => esc_html__( 'Video', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'file',
                'name' => 'file',
                'label' => esc_html__( 'File', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'select',
                'name' => 'layout',
                'default' => '4',
                'label' => esc_html__( 'Select', 'widgets-ultimate' ),
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
            ),

            array(
                'type' =>'group',
                'name' => 'group',
                'default' => 'on',
                'label'     => esc_html__( 'Group', 'widgets-ultimate' ),
                'title_id' => 'title',
                'fields' => array(
                    array(
                        'type' =>'text',
                        'name' => 'title',
                        'label' => esc_html__( 'Title', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'color',
                        'name' => 'color',
                        'label' => esc_html__( 'color', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'textarea',
                        'name' => 'textarea',
                        'label' => esc_html__( 'Textarea', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'image',
                        'name' => 'image',
                        'label' => esc_html__( 'Image', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'video',
                        'name' => 'video',
                        'label' => esc_html__( 'Video', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'file',
                        'name' => 'file',
                        'label' => esc_html__( 'File', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'editor',
                        'name' => 'editor',
                        'label' => esc_html__( 'Editor', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'select',
                        'name' => 'layout',
                        'default' => '4',
                        'label' => esc_html__( 'Select', 'widgets-ultimate' ),
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


        echo $args['after_widget'];
    }

}

function __register_test_widget(){
    register_widget( 'OnePress_plus_Widget_Test' );
}
add_action( 'widgets_init', '__register_test_widget' );