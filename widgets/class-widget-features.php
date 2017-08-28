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
                'description'   => esc_html__( 'Display Features', 'widgets-ultimate' )
            ),
            $control_ops
        );
    }

    function config(){
        $fields = array(
            array(
                'type' =>'text',
                'name' => 'title',
                'label' => esc_html__( 'Title', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'text',
                'name' => 'subtitle',
                'label' => esc_html__( 'Subtitle', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'editor',
                'name' => 'desc',
                'label' => esc_html__( 'Description', 'widgets-ultimate' ),
            ),

            array(
                'type' =>'group',
                'name' => 'features',
                'label'    => esc_html__( 'Features', 'widgets-ultimate' ),
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

    function the_content( $instance ){
        extract( $instance );

        ?>
        <?php if ( $title ||  $subtitle || $desc ){ ?>
            <div class="section-title-area">
                <?php if ($subtitle != '') echo '<h5 class="section-subtitle">' . esc_html($subtitle) . '</h5>'; ?>
                <?php if ($title != '') echo '<h2 class="section-title">' . esc_html($title) . '</h2>'; ?>
                <?php if ( $desc ) {
                    echo '<div class="section-desc">' . apply_filters( 'onepress_the_content', wp_kses_post( $desc ) ) . '</div>';
                } ?>
            </div>
        <?php } ?>
        <div class="section-content">
            <div class="row">
                <?php
                $layout = intval( get_theme_mod( 'onepress_features_layout', 3 ) );
                foreach ( $data as $k => $f ) {
                    $media = '';
                    $f =  wp_parse_args( $f, array(
                        'icon_type' => 'icon',
                        'icon' => 'gg',
                        'image' => '',
                        'link' => '',
                        'title' => '',
                        'desc' => '',
                    ) );
                    if ( $f['icon_type'] == 'image' && $f['image'] ){
                        $url = onepress_get_media_url( $f['image'] );
                        if ( $url ) {
                            $media = '<span class="icon-image"><img src="'.esc_url( $url ).'" alt=""></span>';
                        }
                    } else if ( $f['icon'] ) {
                        $f['icon'] = trim( $f['icon'] );
                        $media = '<span class="fa-stack fa-5x"><i class="fa fa-circle fa-stack-2x icon-background-default"></i> <i class="feature-icon fa '.esc_attr( $f['icon'] ).' fa-stack-1x"></i></span>';
                    }

                    ?>
                    <div class="feature-item col-lg-<?php echo esc_attr( $layout ); ?> col-sm-6 wow slideInUp">
                        <div class="feature-media">
                            <?php if ( $f['link'] ) { ?><a href="<?php echo esc_url( $f['link']  ); ?>"><?php } ?>
                                <?php echo $media; ?>
                                <?php if ( $f['link'] )  { ?></a><?php } ?>
                        </div>
                        <h4><?php if ( $f['link'] ) { ?><a href="<?php echo esc_url( $f['link']  ); ?>"><?php } ?><?php echo esc_html( $f['title'] ); ?><?php if ( $f['link'] )  { ?></a><?php } ?></h4>
                        <div class="feature-item-content"><?php echo apply_filters( 'the_content', $f['desc'] ); ?></div>
                    </div>
                    <?php
                }// end loop featues

                ?>
            </div>
        </div>
        <?php
    }


}
