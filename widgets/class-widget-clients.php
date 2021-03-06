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
                        'type' =>'image',
                        'name' => 'image',
                        'label' => esc_html__( 'Image', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'text',
                        'name' => 'url',
                        'label' => esc_html__( 'URL', 'widgets-ultimate' ),
                    ),

                    array(
                        'type' =>'checkbox',
                        'name' => 'new_window',
                        'label' => esc_html__( 'Open URL in new tab.', 'widgets-ultimate' ),
                    ),
                    
                )
            )

        );

        return $fields;

    }


}
