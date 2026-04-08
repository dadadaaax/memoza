<?php
if( !function_exists( 'supernews_demo_nav_data') ){
    function supernews_demo_nav_data(){
        $demo_navs = array(
            'primary'  => 'Primary Menu',
            'top-menu'  => 'Top menu'
        );
        return $demo_navs;
    }
}
add_filter('acme_demo_setup_nav_data','supernews_demo_nav_data');

if( !function_exists( 'supernews_update_image_size') ){
	function supernews_update_image_size(){
		/*Thumbnail Size*/
		update_option( 'thumbnail_size_w', 500 );
		update_option( 'thumbnail_size_h', 280 );
		update_option( 'thumbnail_crop', 1 );

		/*Medium Image Size*/
		update_option( 'medium_size_w', 660 );
		update_option( 'medium_size_h', 365 );

		/*Large Image Size*/
		update_option( 'large_size_w', 840 );
		update_option( 'large_size_h', 840 );
	}
}
add_action( 'acme_demo_setup_before_import', 'supernews_update_image_size' );
add_action( 'wp_ajax_acme_demo_setup_before_import', 'supernews_update_image_size' );