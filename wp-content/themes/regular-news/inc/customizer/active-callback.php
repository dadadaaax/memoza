<?php
/**
 * Customizer active callbacks
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */

if ( ! function_exists( 'regular_news_is_breadcrumb_enable' ) ) :
	/**
	 * Check if breadcrumb is enabled.
	 *
	 * @since Regular News 1.0.0
	 * @param WP_Customize_Control $control WP_Customize_Control instance.
	 * @return bool Whether the control is active to the current preview.
	 */
	function regular_news_is_breadcrumb_enable( $control ) {
		return $control->manager->get_setting( 'regular_news_theme_options[breadcrumb_enable]' )->value();
	}
endif;

if ( ! function_exists( 'regular_news_is_pagination_enable' ) ) :
	/**
	 * Check if pagination is enabled.
	 *
	 * @since Regular News 1.0.0
	 * @param WP_Customize_Control $control WP_Customize_Control instance.
	 * @return bool Whether the control is active to the current preview.
	 */
	function regular_news_is_pagination_enable( $control ) {
		return $control->manager->get_setting( 'regular_news_theme_options[pagination_enable]' )->value();
	}
endif;

/**
 * Front Page Active Callbacks
 */

/**
 * Check if topbar section is enabled.
 *
 * @since Regular News 1.0.0
 * @param WP_Customize_Control $control WP_Customize_Control instance.
 * @return bool Whether the control is active to the current preview.
 */
function regular_news_is_topbar_section_enable( $control ) {
	return ( $control->manager->get_setting( 'regular_news_theme_options[topbar_section_enable]' )->value() );
}

/**
 * Check if headline section is enabled.
 *
 * @since Regular News 1.0.0
 * @param WP_Customize_Control $control WP_Customize_Control instance.
 * @return bool Whether the control is active to the current preview.
 */
function regular_news_is_headline_section_enable( $control ) {
	return ( $control->manager->get_setting( 'regular_news_theme_options[headline_section_enable]' )->value() );
}

/**
 * Check if popular section is enabled.
 *
 * @since Regular News 1.0.0
 * @param WP_Customize_Control $control WP_Customize_Control instance.
 * @return bool Whether the control is active to the current preview.
 */
function regular_news_is_popular_section_enable( $control ) {
	return ( $control->manager->get_setting( 'regular_news_theme_options[popular_section_enable]' )->value() );
}

/**
 * Check if cta section is enabled.
 *
 * @since Regular News 1.0.0
 * @param WP_Customize_Control $control WP_Customize_Control instance.
 * @return bool Whether the control is active to the current preview.
 */
function regular_news_is_cta_section_enable( $control ) {
	return ( $control->manager->get_setting( 'regular_news_theme_options[cta_section_enable]' )->value() );
}

/**
 * Check if blog section is enabled.
 *
 * @since Regular News 1.0.0
 * @param WP_Customize_Control $control WP_Customize_Control instance.
 * @return bool Whether the control is active to the current preview.
 */
function regular_news_is_blog_section_enable( $control ) {
	return ( $control->manager->get_setting( 'regular_news_theme_options[blog_section_enable]' )->value() );
}

/**
 * Check if blog section content type is category.
 *
 * @since Regular News 1.0.0
 * @param WP_Customize_Control $control WP_Customize_Control instance.
 * @return bool Whether the control is active to the current preview.
 */
function regular_news_is_blog_section_content_category_enable( $control ) {
	$content_type = $control->manager->get_setting( 'regular_news_theme_options[blog_content_type]' )->value();
	return regular_news_is_blog_section_enable( $control ) && ( 'category' == $content_type );
}

/**
 * Check if blog section content type is recent.
 *
 * @since Regular News 1.0.0
 * @param WP_Customize_Control $control WP_Customize_Control instance.
 * @return bool Whether the control is active to the current preview.
 */
function regular_news_is_blog_section_content_recent_enable( $control ) {
	$content_type = $control->manager->get_setting( 'regular_news_theme_options[blog_content_type]' )->value();
	return regular_news_is_blog_section_enable( $control ) && ( 'recent' == $content_type );
}

/**
 * Check if must_read section is enabled.
 *
 * @since Regular News 1.0.0
 * @param WP_Customize_Control $control WP_Customize_Control instance.
 * @return bool Whether the control is active to the current preview.
 */
function regular_news_is_must_read_section_enable( $control ) {
	return ( $control->manager->get_setting( 'regular_news_theme_options[must_read_section_enable]' )->value() );
}

/**
 * Check if must_read section content type is category.
 *
 * @since Regular News 1.0.0
 * @param WP_Customize_Control $control WP_Customize_Control instance.
 * @return bool Whether the control is active to the current preview.
 */
function regular_news_is_must_read_section_content_category_enable( $control ) {
	$content_type = $control->manager->get_setting( 'regular_news_theme_options[must_read_content_type]' )->value();
	return regular_news_is_must_read_section_enable( $control ) && ( 'category' == $content_type );
}

