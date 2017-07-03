<?php
/**
 * Widget Features
 */
class Widget_Ultimate_Features extends Widget_Ultimate_Widget_Base {


    public function __construct() {

        $control_ops = array(
            'width'  => 500,
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
                'type' =>'editor',
                'name' => 'tagline',
                'label' => esc_html__( 'Tagline', 'widgets-ultimate' ),
                'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'editor',
                'name' => 'desc',
                'label' => esc_html__( 'Description', 'widgets-ultimate' ),
                'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'group',
                'name' => 'features',
                'label'    => esc_html__( 'Features', 'widgets-ultimate' ),
                'desc'     => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
                'title_id' => 'title', // support text field only
                'fields' => array(
                    array(
                        'type' =>'text',
                        'name' => 'title',
                        'label' => esc_html__( 'Title', 'widgets-ultimate' ),
                        'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'icon',
                        'name' => 'icon',
                        'label' => esc_html__( 'Icon', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'image',
                        'name' => 'image',
                        'label' => esc_html__( 'Image', 'widgets-ultimate' ),
                        'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'color',
                        'name' => 'color',
                        'label' => esc_html__( 'color', 'widgets-ultimate' ),
                        'desc' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'editor',
                        'name' => 'desc',
                        'label' => esc_html__( 'Description', 'widgets-ultimate' ),
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
