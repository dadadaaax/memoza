<?php
/**
 * Theme Palace widgets inclusion
 *
 * This is the template that includes all custom widgets of Regular News
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */

/*
 * Add About  widget
 */
require get_template_directory() . '/inc/widgets/about-info-widget.php';


/**
 * Register widgets
 */
function regular_news_register_widgets() {

	register_widget( 'Regular_News_About_Us_Image_Widget' );

}
add_action( 'widgets_init', 'regular_news_register_widgets' );