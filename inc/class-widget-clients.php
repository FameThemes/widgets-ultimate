<?php
/**
 * Widget Features
 */
class Widget_Ultimate_Clients extends Widget_Ultimate_Widget_Base {


    public function __construct() {

        $control_ops = array(
            'width'  => 500,
            'height' => 350,
        );

        //$control_ops = null;
        parent::__construct(
            'widget-ultimate-clients',
            esc_html__( 'Clients', 'widgets-ultimate' ),
            array(
                'classname'     => 'widget-clients',
                'description'   => esc_html__( 'Display clients', 'widgets-ultimate' )
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
                'type' =>'text',
                'name' => 'tagline',
                'label' => esc_html__( 'Tagline', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'editor',
                'name' => 'desc',
                'label' => esc_html__( 'Description', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'group',
                'name' => 'clients',
                'label'    => esc_html__( 'Clients', 'widgets-ultimate' ),
                'title_id' => 'title', // support text field only
                'fields' => array(
                    array(
                        'type' =>'text',
                        'name' => 'title',
                        'label' => esc_html__( 'Title', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'select',
                        'name' => 'icon_type',
                        'default' => '4',
                        'label' => esc_html__( 'Icon type', 'widgets-ultimate' ),
                        'options' => array(
                            'font'   => esc_html__( 'Font icon', 'widgets-ultimate' ),
                            'image'   => esc_html__( 'Image', 'widgets-ultimate' ),
                        ),
                    ),

                    array(
                        'type' =>'icon',
                        'name' => 'icon',
                        'label' => esc_html__( 'Icon', 'widgets-ultimate' ),
                        'required' => array(
                            'when' => 'icon_type' ,
                            'is' => 'font'
                        )
                    ),

                    array(
                        'type' =>'color',
                        'name' => 'icon_color',
                        'label' => esc_html__( 'Icon color', 'widgets-ultimate' ),
                        'required' => array(
                            'when' => 'icon_type' ,
                            'is' => 'font'
                        )
                    ),

                    array(
                        'type' =>'image',
                        'name' => 'image',
                        'label' => esc_html__( 'Image', 'widgets-ultimate' ),
                        'required' => array(
                            'when' => 'icon_type' ,
                            'is' => 'image'
                        ),
                    ),

                    array(
                        'type' =>'editor',
                        'name' => 'desc',
                        'label' => esc_html__( 'Description', 'widgets-ultimate' ),
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
