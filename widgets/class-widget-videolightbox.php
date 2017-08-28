<?php

class Widget_Ultimate_Videolightbox extends Widget_Ultimate_Widget_Base {

    public function __construct() {

        $control_ops = array(
            'width'  => 500,
            'height' => 350,
        );

        //$control_ops = null;
        parent::__construct(
            'widget-ultimate-videolightbox',
            esc_html__( 'Video Lightbox', 'widgets-ultimate' ),
            array(
                'classname'     => 'widget-videolightbox',
                'description'   => esc_html__( 'Display video lightbox', 'widgets-ultimate' )
            ),
            $control_ops
        );
    }

    function config( ){
        $fields = array(
            array(
                'type' =>'editor',
                'name' => 'desc',
                'label' => esc_html__( 'Description', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'text',
                'name' => 'url',
                'label' => esc_html__( 'Video URL', 'widgets-ultimate' ),
            ),

        );

        return $fields;

    }


}
