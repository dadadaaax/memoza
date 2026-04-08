<?php
/**
 * Customizer default options
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 * @return array An array of default values
 */

function regular_news_get_default_theme_options() {
	$theme_data = wp_get_theme();
	$regular_news_default_options = array(
		// Color Options
		'header_title_color'			=> '#212121',
		'header_tagline_color'			=> '#212121',
		'header_txt_logo_extra'			=> 'show-all',

		// breadcrumb
		'breadcrumb_enable'				=> true,
		'breadcrumb_separator'			=> '/',
		
		// layout 
		'site_layout'         			=> 'wide',
		'sidebar_position'         		=> 'right-sidebar',
		'post_sidebar_position' 		=> 'right-sidebar',
		'page_sidebar_position' 		=> 'right-sidebar',
		'nav_search_enable'				=> true,

		// excerpt options
		'long_excerpt_length'           => 25,
		'read_more_text'           		=> esc_html__( 'Read More', 'regular-news' ),
		
		// pagination options
		'pagination_enable'         	=> true,
		'pagination_type'         		=> 'default',

		// footer options
		'copyright_text'           		=> sprintf( esc_html_x( 'Copyright &copy; %1$s %2$s. ', '1: Year, 2: Site Title with home URL', 'regular-news' ), '[the-year]', '[site-link]' ) . esc_html__( 'All Rights Reserved | ', 'regular-news' ),
		'scroll_top_visible'        	=> true,

		// reset options
		'reset_options'      			=> false,
		
		// homepage options
		'enable_frontpage_content' 		=> false,

		// homepage sections sortable
		'sortable' 						=> 'headline,popular,cta,blog,must_read',

		// blog/archive options
		'your_latest_posts_title' 		=> esc_html__( 'Blogs', 'regular-news' ),
		'hide_category'					=> false,
		'hide_author'					=> false,
		'hide_date'						=> false,

		// single post theme options
		'single_post_hide_date' 		=> false,
		'single_post_hide_author'		=> false,
		'single_post_hide_category'		=> false,
		'single_post_hide_tags'			=> false,

		/* Front Page */

		// Headline
		'headline_section_enable'		=> false,
		'headline_title'				=> esc_html__( 'Breaking News', 'regular-news' ),

		// Hero
		'popular_section_enable'		=> false,

		// call to action
		'cta_section_enable'			=> false,

		// Must Read
		'must_read_section_enable'		=> false,
		'must_read_title'				=> esc_html__( 'Must Read Articles', 'regular-news' ),

		// blog
		'blog_section_enable'			=> false,
		'blog_content_type'				=> 'recent',
		'blog_title'					=> esc_html__( 'Recently Viewed', 'regular-news' ),

	);

	$output = apply_filters( 'regular_news_default_theme_options', $regular_news_default_options );

	// Sort array in ascending order, according to the key:
	if ( ! empty( $output ) ) {
		ksort( $output );
	}

	return $output;
}