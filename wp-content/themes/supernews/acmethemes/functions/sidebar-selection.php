<?php
/**
 * Select sidebar according to the options saved
 *
 * @since SuperNews 1.0.0
 *
 * @param null
 * @return string
 *
 */
if ( !function_exists('supernews_sidebar_selection') ) :
	function supernews_sidebar_selection( ) {
		wp_reset_postdata();
		$supernews_customizer_all_values = supernews_get_theme_options();
		global $post;
		if(
			isset( $supernews_customizer_all_values['supernews-sidebar-layout'] ) &&
			(
				'left-sidebar' == $supernews_customizer_all_values['supernews-sidebar-layout'] ||
				'both-sidebar' == $supernews_customizer_all_values['supernews-sidebar-layout'] ||
				'middle-col' == $supernews_customizer_all_values['supernews-sidebar-layout'] ||
				'no-sidebar' == $supernews_customizer_all_values['supernews-sidebar-layout']
			)
		){
			$supernews_body_global_class = $supernews_customizer_all_values['supernews-sidebar-layout'];
		}
		else{
			$supernews_body_global_class= 'right-sidebar';
		}

		if ( supernews_is_woocommerce_active() && ( is_product() || is_shop() || is_product_taxonomy() )) {
			if( is_product() ){
				$post_class = get_post_meta( $post->ID, 'supernews_sidebar_layout', true );
				$supernews_wc_single_product_sidebar_layout = $supernews_customizer_all_values['supernews-wc-single-product-sidebar-layout'];

				if ( 'default-sidebar' != $post_class ){
					if ( $post_class ) {
						$supernews_body_classes = $post_class;
					} else {
						$supernews_body_classes = $supernews_wc_single_product_sidebar_layout;
					}
				}
				else{
					$supernews_body_classes = $supernews_wc_single_product_sidebar_layout;

				}
			}
			else{
				if( isset( $supernews_customizer_all_values['supernews-wc-shop-archive-sidebar-layout'] ) ){
					$supernews_archive_sidebar_layout = $supernews_customizer_all_values['supernews-wc-shop-archive-sidebar-layout'];
					if(
						'right-sidebar' == $supernews_archive_sidebar_layout ||
						'left-sidebar' == $supernews_archive_sidebar_layout ||
						'both-sidebar' == $supernews_archive_sidebar_layout ||
						'middle-col' == $supernews_archive_sidebar_layout ||
						'no-sidebar' == $supernews_archive_sidebar_layout
					){
						$supernews_body_classes = $supernews_archive_sidebar_layout;
					}
					else{
						$supernews_body_classes = $supernews_body_global_class;
					}
				}
				else{
					$supernews_body_classes= $supernews_body_global_class;
				}
			}
		}
		elseif( is_front_page() ){
			if( isset( $supernews_customizer_all_values['supernews-front-page-sidebar-layout'] ) ){
				if(
					'right-sidebar' == $supernews_customizer_all_values['supernews-front-page-sidebar-layout'] ||
					'left-sidebar' == $supernews_customizer_all_values['supernews-front-page-sidebar-layout'] ||
					'both-sidebar' == $supernews_customizer_all_values['supernews-front-page-sidebar-layout'] ||
					'middle-col' == $supernews_customizer_all_values['supernews-front-page-sidebar-layout'] ||
					'no-sidebar' == $supernews_customizer_all_values['supernews-front-page-sidebar-layout']
				){
					$supernews_body_classes = $supernews_customizer_all_values['supernews-front-page-sidebar-layout'];
				}
				else{
					$supernews_body_classes = $supernews_body_global_class;
				}
			}
			else{
				$supernews_body_classes= $supernews_body_global_class;
			}
		}

		elseif ( is_singular() && isset( $post->ID ) ) {
			$post_class = get_post_meta( $post->ID, 'supernews_sidebar_layout', true );
			if ( 'default-sidebar' != $post_class ){
				if ( $post_class ) {
					$supernews_body_classes = $post_class;
				} else {
					$supernews_body_classes = $supernews_body_global_class;
				}
			}
			else{
				$supernews_body_classes = $supernews_body_global_class;
			}

		}
		elseif ( is_archive() ) {
			if( isset( $supernews_customizer_all_values['supernews-archive-sidebar-layout'] ) ){
				$supernews_archive_sidebar_layout = $supernews_customizer_all_values['supernews-archive-sidebar-layout'];
				if(
					'right-sidebar' == $supernews_archive_sidebar_layout ||
					'left-sidebar' == $supernews_archive_sidebar_layout ||
					'both-sidebar' == $supernews_archive_sidebar_layout ||
					'middle-col' == $supernews_archive_sidebar_layout ||
					'no-sidebar' == $supernews_archive_sidebar_layout
				){
					$supernews_body_classes = $supernews_archive_sidebar_layout;
				}
				else{
					$supernews_body_classes = $supernews_body_global_class;
				}
			}
			else{
				$supernews_body_classes= $supernews_body_global_class;
			}
		}
		else {
			$supernews_body_classes = $supernews_body_global_class;
		}
		return $supernews_body_classes;
	}
endif;