<?php

class Widget_Ultimate_About extends Widget_Ultimate_Widget_Base {

    public function __construct() {

        $control_ops = array(
            'width'  => 500,
            'height' => 350,
        );

        //$control_ops = null;
        parent::__construct(
            'widget-ultimate-about',
            esc_html__( 'About', 'widgets-ultimate' ),
            array(
                'classname'     => 'widget-about',
                'description'   => esc_html__( 'Display about', 'widgets-ultimate' )
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
                        'type' =>'editor',
                        'name' => 'content',
                        'label' => esc_html__( 'Content', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'checkbox',
                        'name' => 'hide_title',
                        'label' => esc_html__( 'Hide item title.', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'text',
                        'name' => 'url',
                        'label' => esc_html__( 'URL', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'checkbox',
                        'name' => 'link',
                        'label' => esc_html__( 'Link to single page.', 'widgets-ultimate' ),
                    ),
                    
                )
            )

        );

        return $fields;

    }


}
