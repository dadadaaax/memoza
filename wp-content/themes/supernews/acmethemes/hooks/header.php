<?php
/**
 * Setting global variables for all theme options saved values
 *
 * @since SuperNews 1.0.0
 *
 * @param null
 * @return null
 *
 */
if ( ! function_exists( 'supernews_set_global' ) ) :

    function supernews_set_global() {
        /*Getting saved values start*/
        $supernews_saved_theme_options = supernews_get_theme_options();
        $GLOBALS['supernews_customizer_all_values'] = $supernews_saved_theme_options;
        /*Getting saved values end*/
    }
endif;
add_action( 'supernews_action_before_head', 'supernews_set_global', 0 );

/**
 * Doctype Declaration
 *
 * @since SuperNews 1.0.0
 *
 * @param null
 * @return null
 *
 */
if ( ! function_exists( 'supernews_doctype' ) ) :
    function supernews_doctype() {
        ?><!DOCTYPE html><html <?php language_attributes(); ?>>
    <?php
    }
endif;
add_action( 'supernews_action_before_head', 'supernews_doctype', 10 );

/**
 * Code inside head tage but before wp_head funtion
 *
 * @since SuperNews 1.0.0
 *
 * @param null
 * @return null
 *
 */
if ( ! function_exists( 'supernews_before_wp_head' ) ) :

    function supernews_before_wp_head() {
        ?>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="profile" href="<?php echo esc_url('http://gmpg.org/xfn/11')?>">
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <?php
    }
endif;
add_action( 'supernews_action_before_wp_head', 'supernews_before_wp_head', 10 );

/**
 * Add body class
 *
 * @since SuperNews 1.0.0
 *
 * @param null
 * @return null
 *
 */
if ( ! function_exists( 'supernews_body_class' ) ) :

    function supernews_body_class( $supernewsbody_classes ) {
        global $supernews_customizer_all_values;
        if ( 'no-image' == $supernews_customizer_all_values['supernews-blog-archive-layout'] ) {
            $supernewsbody_classes[] = 'blog-no-image';
        }
	    if( 1 == $supernews_customizer_all_values['supernews-enable-sticky-sidebar'] ){
		    $supernewsbody_classes[] = 'at-sticky-sidebar';
	    }
	    $supernews_header_logo_menu_display_position = $supernews_customizer_all_values['supernews-header-logo-ads-display-position'];
	    $supernewsbody_classes[] = esc_attr( $supernews_header_logo_menu_display_position );

        $supernewsbody_classes[] = supernews_sidebar_selection();

        return $supernewsbody_classes;
    }
endif;
add_action( 'body_class', 'supernews_body_class', 10, 1);

/**
 * Page start
 *
 * @since SuperNews 1.0.0
 *
 * @param null
 * @return null
 *
 */
if ( ! function_exists( 'supernews_page_start' ) ) :

    function supernews_page_start() {
        ?>
        <div id="page" class="hfeed site">
    <?php
    }
endif;
add_action( 'supernews_action_before', 'supernews_page_start', 15 );

/**
 * Skip to content
 *
 * @since SuperNews 1.0.0
 *
 * @param null
 * @return null
 *
 */
if ( ! function_exists( 'supernews_skip_to_content' ) ) :

    function supernews_skip_to_content() {
        ?>
        <a class="skip-link screen-reader-text" href="#content" title="link"><?php esc_html_e( 'Skip to content', 'supernews' ); ?></a>
    <?php
    }
endif;
add_action( 'supernews_action_before_header', 'supernews_skip_to_content', 10 );

/**
 * Main header
 *
 * @since SuperNews 1.0.0
 *
 * @param null
 * @return null
 *
 */
if ( ! function_exists( 'supernews_header' ) ) :

    function supernews_header() {
        global $supernews_customizer_all_values;
	    $supernews_header_media_position = $supernews_customizer_all_values['supernews-header-media-position'];
	    if( 'very-top' == $supernews_header_media_position ){
		    supernews_header_markup();
	    }
        ?>
        <header id="masthead" class="site-header">
            <div class="top-header-section clearfix">
                <div class="wrapper">
                    <?php
                    if ( 1 == $supernews_customizer_all_values['supernews-show-date'] ){
                        echo ' <div class="header-date top-block">';
                        supernews_date_display();
                        echo "</div>";
                    }
                    if( has_nav_menu( 'top-menu' ) ){
                        ?>
                        <?php wp_nav_menu(array('theme_location' => 'top-menu','container' => 'div', 'container_class' => 'acmethemes-top-nav top-block', 'depth' => 1 )); ?>
                        <?php
                    }
                    if ( isset( $supernews_customizer_all_values['supernews-header-show-search']) && $supernews_customizer_all_values['supernews-header-show-search'] == 1 ):
                        ?>
                        <div class="header-search top-block">
                            <?php get_search_form(); ?>
                        </div>
                        <?php
                    endif;
                    ?>
                </div>
            </div><!-- .top-header-section -->
            <div class="header-wrapper clearfix">
                <div class="header-container">
	                <?php
	                if( 'above-logo' == $supernews_header_media_position ){
		                supernews_header_markup();
	                }
	                ?>
                    <div class="wrapper site-branding clearfix">
                        <?php if ( 'disable' != $supernews_customizer_all_values['supernews-header-id-display-opt'] ):?>
                        <div class="site-logo">
                            <?php
                            if ( 'logo-only' == $supernews_customizer_all_values['supernews-header-id-display-opt'] ):
                                if ( function_exists( 'the_custom_logo' ) ) :
                                    the_custom_logo();
                                else :
                                    if( !empty( $supernews_customizer_all_values['supernews-header-logo'] ) ):
                                        ?>
                                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                                            <img src="<?php echo esc_url( $supernews_customizer_all_values['supernews-header-logo'] ); ?>">
                                        </a>
                                        <?php
                                    endif;/*supernews-header-logo*/
                                endif;
                            else:/*else is title-only or title-and-tagline*/
                                if ( is_front_page() && is_home() ) : ?>
                                    <h1 class="site-title">
                                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
                                    </h1>
                                <?php else : ?>
                                    <p class="site-title">
                                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
                                    </p>
                                <?php endif;
                                if ( 'title-and-tagline' == $supernews_customizer_all_values['supernews-header-id-display-opt'] ):
                                    $description = get_bloginfo( 'description', 'display' );
                                    if ( $description || is_customize_preview() ) : ?>
                                        <p class="site-description"><?php echo esc_html( $description ); ?></p>
                                    <?php endif;
                                endif;
                            endif; ?>
                        </div><!--site-logo-->
                        <?php endif;/*<!--supernews-header-id-display-opt-->*/
                        if ( (!empty( $supernews_customizer_all_values['supernews-header-main-banner-ads'] ) && 'hide' != $supernews_customizer_all_values['supernews-header-main-show-banner-ads'] ) ||
                             is_active_sidebar( 'supernews-header' ) ):
	                        $supernews_header_main_banner_ads_link = $supernews_customizer_all_values['supernews-header-main-banner-ads-link'];
	                        ?>
                            <div class="header-ainfo float-right">
		                        <?php
		                        if (!empty( $supernews_customizer_all_values['supernews-header-main-banner-ads'] ) && 'hide' != $supernews_customizer_all_values['supernews-header-main-show-banner-ads'] ){
			                        ?>
                                    <a href="<?php echo esc_url( $supernews_header_main_banner_ads_link ); ?>" target="_blank">
                                        <img src="<?php echo esc_url( $supernews_customizer_all_values['supernews-header-main-banner-ads'] )?>">
                                    </a>
			                        <?php
		                        }
		                        if( is_active_sidebar( 'supernews-header' ) ) :
			                        dynamic_sidebar( 'supernews-header' );
		                        endif;
		                        ?>
                            </div>
                        <?php endif; ?>
                        <div class="clearfix"></div>
                    </div>
                    <?php
                    if( 'above-menu' == $supernews_header_media_position ){
	                    supernews_header_markup();
                    }
                    $supernews_enable_sticky_menu ='';
                    if( 1 == $supernews_customizer_all_values['supernews-enable-sticky-menu'] ) {
	                    $supernews_enable_sticky_menu = ' supernews-enable-sticky-menu ';
                    }
                    ?>
                    <nav id="site-navigation" class="main-navigation <?php echo $supernews_enable_sticky_menu;?> clearfix">
                        <div class="header-main-menu wrapper clearfix">
                            <?php
                            wp_nav_menu(array('theme_location' => 'primary','container' => 'div', 'container_class' => 'acmethemes-nav'));
                            if ( 1 == $supernews_customizer_all_values['supernews-enable-social'] ) {
                                /*Social Sharing*/
                                /**
                                 * supernews_action_social_links
                                 * @since SuperNews 1.0.0
                                 *
                                 * @hooked supernews_social_links -  10
                                 */
                                do_action('supernews_action_social_links');
                                /* Social Links*/
                            }
                           ?>
                        </div>
                        <div class="responsive-slick-menu clearfix"></div>
                    </nav>
                    <?php
                    if( 'below-menu' == $supernews_header_media_position ){
	                    supernews_header_markup();
                    }
                    ?>
                    <!-- #site-navigation -->
                </div>
                <!-- .header-container -->
            </div>
            <!-- header-wrapper-->
        </header>
        <!-- #masthead -->
    <?php
    }
endif;
add_action( 'supernews_action_header', 'supernews_header', 10 );

/**
 * Before main content
 *
 * @since SuperNews 1.0.0
 *
 * @param null
 * @return null
 *
 */
if ( ! function_exists( 'supernews_before_content' ) ) :

    function supernews_before_content() {
        ?>
        <div class="wrapper content-wrapper clearfix">
        <?php
        global $supernews_customizer_all_values;
        if ( 1 == $supernews_customizer_all_values['supernews-enable-breaking-news'] ) {
            $recent_args = array(
                'numberposts' => 5,
                'post_status' => 'publish',
            );
            $recent_posts = wp_get_recent_posts($recent_args);
            if ( !empty( $recent_posts ) && is_front_page() ):
                if ( !empty( $supernews_customizer_all_values['supernews-breaking-news-title'] ) ){
                    $bn_title = $supernews_customizer_all_values['supernews-breaking-news-title'];
                }
                else{
                    $bn_title = __( 'Recent posts', 'supernews' );
                }
                ?>
                <div class="header-latest-posts bn-wrapper">
                    <div class="bn-title">
                        <?php echo esc_html( $bn_title ); ?>
                    </div>
                    <ul class="bn">
                        <?php foreach ($recent_posts as $recent): ?>
                            <li class="bn-content">
                                <a href="<?php echo esc_url( get_permalink($recent["ID"]) ); ?>" title="<?php echo esc_attr($recent['post_title']); ?>">
                                    <?php echo esc_html( $recent['post_title'] ); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div> <!-- .header-latest-posts -->
                <?php
            endif;
        }
        $supernews_enable_feature = $supernews_customizer_all_values['supernews-enable-feature'];
        if ( is_front_page() && 1 == $supernews_enable_feature ) {
            echo '<div class="clearfix"></div><div class="slider-feature-wrap clearfix">';
            /*Slide*/
            /**
             * supernews_action_feature_slider
             * @since SuperNews 1.0.0
             *
             * @hooked supernews_feature_slider -  0
             */
            do_action('supernews_action_feature_slider');

            echo "</div>";
            $supernews_content_id = "home-content";
        } else {
            $supernews_content_id = "content";
        }
        ?>
    <div id="<?php echo esc_attr( $supernews_content_id ); ?>" class="site-content">
    <?php
        if( 1 == $supernews_customizer_all_values['supernews-show-breadcrumb'] && !is_front_page()){
            supernews_breadcrumbs();
        }

	    $sidebar_layout = supernews_sidebar_selection(get_the_ID());
	    if( 'both-sidebar' == $sidebar_layout ) {
		    echo '<div id="primary-wrap" class="clearfix">';
	    }
    }
endif;
add_action( 'supernews_action_after_header', 'supernews_before_content', 10 );