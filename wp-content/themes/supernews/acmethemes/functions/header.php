<?php
/**
 * Displays header media
 *
 * @link https://codex.wordpress.org/Custom_Headers
 *
 * @package Acme Themes
 * @subpackage SuperNews
 * @since 1.2.0
 */
function supernews_header_image_markup( $html, $header, $attr ) {

	$output = '';
	$supernews_customizer_all_values = supernews_get_theme_options();
	$supernews_header_image_link = $supernews_customizer_all_values['supernews-header-image-link'];
	$supernews_header_image_link_new_tab = $supernews_customizer_all_values['supernews-header-image-link-new-tab'];
	$output .= '<div class="wrapper header-image-wrap">';
	if ( !empty( $supernews_header_image_link)) {
		$target = "";
		if( 1 == $supernews_header_image_link_new_tab ){
			$target = 'target = _blank';
		}
		$output .= '<a '.esc_attr( $target ) .' href="'.$supernews_header_image_link.'" title="'.esc_attr( get_bloginfo( 'name', 'display' ) ).'" rel="home">';
	}
	$output .= $html;
	if ( !empty( $supernews_header_image_link)) {
		$output .= ' </a>';
	}
	$output .= "</div>";
	return $output;
}
add_filter( 'get_header_image_tag', 'supernews_header_image_markup', 99, 3 );

if ( ! function_exists( 'supernews_header_markup' ) ) :

	function supernews_header_markup() {
		if ( function_exists( 'the_custom_header_markup' ) ) {
			the_custom_header_markup();
		}
		else {
			$header_image = get_header_image();
			if( ! empty( $header_image ) ) {
				$supernews_customizer_all_values = supernews_get_theme_options();
				$supernews_header_image_link = $supernews_customizer_all_values['supernews-header-image-link'];
				$supernews_header_image_link_new_tab = $supernews_customizer_all_values['supernews-header-image-link-new-tab'];
				echo '<div class="wrapper header-image-wrap">';
				if ( !empty( $supernews_header_image_link)) {
					$target = "";
				    if( 1 == $supernews_header_image_link_new_tab ){
				        $target = "target = _blank";
                    }
				    ?>
					<a <?php echo esc_attr( $target ); ?> href="<?php echo esc_url( $supernews_header_image_link ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
				<?php
				}
				?>
                <img src="<?php echo esc_url( $header_image ); ?>" class="header-image" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
				<?php
				if ( !empty( $supernews_header_image_link ) ) { ?>
					</a>
					<?php
				}
				echo "</div>";
			}
		}
	}
endif;