<?php
/**
 * Excerpt length 90 return
 *
 * @since SuperNews 1.0.0
 *
 * @param null
 * @return null
 *
 */
if ( !function_exists('supernews_alter_excerpt') ) :
    function supernews_alter_excerpt( $length ){
		if( is_admin() ){
			return $length;
		}
        return 90;
    }
endif;
add_filter('excerpt_length', 'supernews_alter_excerpt');

/**
 * Add ... for more view
 *
 * @since SuperNews 1.0.0
 *
 * @param null
 * @return null
 *
 */

if ( !function_exists('supernews_excerpt_more') ) :
    function supernews_excerpt_more( $more ) {
		if( is_admin() ){
			return $more;
		}
        return '&hellip;';
    }
endif;
add_filter('excerpt_more', 'supernews_excerpt_more');