<?php

class Widget_Ultimate_Testimonial extends Widget_Ultimate_Widget_Base {

    public function __construct() {

        $control_ops = array(
            'width'  => 500,
            'height' => 350,
        );

        //$control_ops = null;
        parent::__construct(
            'widget-ultimate-testimonial',
            esc_html__( 'Testimonial', 'widgets-ultimate' ),
            array(
                'classname'     => 'widget-testimonial',
                'description'   => esc_html__( 'Display testimonial', 'widgets-ultimate' )
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
                'name' => 'items',
                'label'    => esc_html__( 'Items', 'widgets-ultimate' ),
                'title_id' => 'title', // support text field only
                'fields' => array(
                    array(
                        'type' =>'text',
                        'name' => 'title',
                        'label' => esc_html__( 'Title', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'text',
                        'name' => 'name',
                        'label' => esc_html__( 'Name', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'image',
                        'name' => 'image',
                        'label' => esc_html__( 'Avatar', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'text',
                        'name' => 'subtitle',
                        'label' => esc_html__( 'Subtitle', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'textarea',
                        'name' => 'content',
                        'label' => esc_html__( 'Content', 'widgets-ultimate' ),
                    ),


                )
            )

        );

        return $fields;
    }


}
