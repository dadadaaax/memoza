<?php
/**
 * Theme Palace options
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */

/**
 * List of pages for page choices.
 * @return Array Array of page ids and name.
 */
function regular_news_page_choices() {
    $pages = get_pages();
    $choices = array();
    $choices[0] = esc_html__( '--Select--', 'regular-news' );
    foreach ( $pages as $page ) {
        $choices[ $page->ID ] = $page->post_title;
    }
    return  $choices;
}

/**
 * List of posts for post choices.
 * @return Array Array of post ids and name.
 */
function regular_news_post_choices() {
    $posts = get_posts( array( 'numberposts' => -1 ) );
    $choices = array();
    $choices[0] = esc_html__( '--Select--', 'regular-news' );
    foreach ( $posts as $post ) {
        $choices[ $post->ID ] = $post->post_title;
    }
    return  $choices;
}

/**
 * List of category for category choices.
 * @return Array Array of post ids and name.
 */
function regular_news_category_choices() {
    $tax_args = array(
        'hierarchical' => 0,
        'taxonomy'     => 'category',
    );
    $taxonomies = get_categories( $tax_args );
    $choices = array();
    $choices[0] = esc_html__( '--Select--', 'regular-news' );
    foreach ( $taxonomies as $tax ) {
        $choices[ $tax->term_id ] = $tax->name;
    }
    return  $choices;
}

if ( ! function_exists( 'regular_news_site_layout' ) ) :
    /**
     * Site Layout
     * @return array site layout options
     */
    function regular_news_site_layout() {
        $regular_news_site_layout = array(
            'wide'  => get_template_directory_uri() . '/assets/images/full.png',
            'boxed-layout' => get_template_directory_uri() . '/assets/images/boxed.png',
        );

        $output = apply_filters( 'regular_news_site_layout', $regular_news_site_layout );
        return $output;
    }
endif;

if ( ! function_exists( 'regular_news_selected_sidebar' ) ) :
    /**
     * Sidebars options
     * @return array Sidbar positions
     */
    function regular_news_selected_sidebar() {
        $regular_news_selected_sidebar = array(
            'sidebar-1'             => esc_html__( 'Default Sidebar', 'regular-news' ),
            'optional-sidebar'      => esc_html__( 'Optional Sidebar 1', 'regular-news' ),
        );

        $output = apply_filters( 'regular_news_selected_sidebar', $regular_news_selected_sidebar );

        return $output;
    }
endif;


if ( ! function_exists( 'regular_news_global_sidebar_position' ) ) :
    /**
     * Global Sidebar position
     * @return array Global Sidebar positions
     */
    function regular_news_global_sidebar_position() {
        $regular_news_global_sidebar_position = array(
            'right-sidebar' => get_template_directory_uri() . '/assets/images/right.png',
            'no-sidebar'    => get_template_directory_uri() . '/assets/images/full.png',
        );

        $output = apply_filters( 'regular_news_global_sidebar_position', $regular_news_global_sidebar_position );

        return $output;
    }
endif;


if ( ! function_exists( 'regular_news_sidebar_position' ) ) :
    /**
     * Sidebar position
     * @return array Sidbar positions
     */
    function regular_news_sidebar_position() {
        $regular_news_sidebar_position = array(
            'right-sidebar' => get_template_directory_uri() . '/assets/images/right.png',
            'no-sidebar'    => get_template_directory_uri() . '/assets/images/full.png',
            'no-sidebar-content'   => get_template_directory_uri() . '/assets/images/boxed.png',
        );

        $output = apply_filters( 'regular_news_sidebar_position', $regular_news_sidebar_position );

        return $output;
    }
endif;


if ( ! function_exists( 'regular_news_pagination_options' ) ) :
    /**
     * Pagination
     * @return array site pagination options
     */
    function regular_news_pagination_options() {
        $regular_news_pagination_options = array(
            'numeric'   => esc_html__( 'Numeric', 'regular-news' ),
            'default'   => esc_html__( 'Default(Older/Newer)', 'regular-news' ),
        );

        $output = apply_filters( 'regular_news_pagination_options', $regular_news_pagination_options );

        return $output;
    }
endif;

if ( ! function_exists( 'regular_news_switch_options' ) ) :
    /**
     * List of custom Switch Control options
     * @return array List of switch control options.
     */
    function regular_news_switch_options() {
        $arr = array(
            'on'        => esc_html__( 'Enable', 'regular-news' ),
            'off'       => esc_html__( 'Disable', 'regular-news' )
        );
        return apply_filters( 'regular_news_switch_options', $arr );
    }
endif;

if ( ! function_exists( 'regular_news_hide_options' ) ) :
    /**
     * List of custom Switch Control options
     * @return array List of switch control options.
     */
    function regular_news_hide_options() {
        $arr = array(
            'on'        => esc_html__( 'Yes', 'regular-news' ),
            'off'       => esc_html__( 'No', 'regular-news' )
        );
        return apply_filters( 'regular_news_hide_options', $arr );
    }
endif;
