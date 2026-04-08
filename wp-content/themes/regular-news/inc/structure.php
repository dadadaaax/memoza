<?php
/**
 * Theme Palace basic theme structure hooks
 *
 * This file contains structural hooks.
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */

$options = regular_news_get_theme_options();


if ( ! function_exists( 'regular_news_doctype' ) ) :
	/**
	 * Doctype Declaration.
	 *
	 * @since Regular News 1.0.0
	 */
	function regular_news_doctype() {
	?>
		<!DOCTYPE html>
			<html <?php language_attributes(); ?>>
	<?php
	}
endif;

add_action( 'regular_news_doctype', 'regular_news_doctype', 10 );


if ( ! function_exists( 'regular_news_head' ) ) :
	/**
	 * Header Codes
	 *
	 * @since Regular News 1.0.0
	 *
	 */
	function regular_news_head() {
		?>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
			<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		<?php endif;
	}
endif;
add_action( 'regular_news_before_wp_head', 'regular_news_head', 10 );

if ( ! function_exists( 'regular_news_page_start' ) ) :
	/**
	 * Page starts html codes
	 *
	 * @since Regular News 1.0.0
	 *
	 */
	function regular_news_page_start() {
		?>
		<div id="page" class="site">
			<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'regular-news' ); ?></a>
			<div class="menu-overlay"></div>

		<?php
	}
endif;
add_action( 'regular_news_page_start_action', 'regular_news_page_start', 10 );

if ( ! function_exists( 'regular_news_page_end' ) ) :
	/**
	 * Page end html codes
	 *
	 * @since Regular News 1.0.0
	 *
	 */
	function regular_news_page_end() {
		?>
		</div><!-- #page -->
		<?php
	}
endif;
add_action( 'regular_news_page_end_action', 'regular_news_page_end', 10 );

if ( ! function_exists( 'regular_news_header_start' ) ) :
	/**
	 * Header start html codes
	 * 
	 * @since Regular News 1.0.0
	 *
	 */
	function regular_news_header_start() {
	$options = regular_news_get_theme_options();
		$header_background_image = ! empty( $options['header_background_image'] ) ? $options['header_background_image'] : get_template_directory_uri() . '/assets/uploads/header.png'; ?>

		<header id="masthead" class="site-header" role="banner" style="background-image:url(<?php echo esc_url( $header_background_image ); ?>)">
		<div class="overlay"></div>
		<?php
	}
endif;
add_action( 'regular_news_header_action', 'regular_news_header_start', 10 );

if ( ! function_exists( 'regular_news_site_branding' ) ) :
	/**
	 * Site branding codes
	 *
	 * @since Regular News 1.0.0
	 *
	 */
	function regular_news_site_branding() {
		$options  = regular_news_get_theme_options();
		$header_txt_logo_extra = $options['header_txt_logo_extra'];		
		?>
		<div class="wrapper">
            <div class="site-branding-wrapper">
				<div class="site-branding">
					<?php if ( in_array( $header_txt_logo_extra, array( 'show-all', 'logo-title', 'logo-tagline' ) )  ) { ?>
						<div class="site-logo">
							<?php the_custom_logo(); ?>
						</div>
					<?php } 
					if ( in_array( $header_txt_logo_extra, array( 'show-all', 'title-only', 'logo-title', 'show-all', 'tagline-only', 'logo-tagline' ) ) ) : ?>
						<div id="site-details">
							<?php
							if( in_array( $header_txt_logo_extra, array( 'show-all', 'title-only', 'logo-title' ) )  ) {
								if ( regular_news_is_latest_posts() ) : ?>
									<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
								<?php else : ?>
									<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
								<?php
								endif;
							} 
							if ( in_array( $header_txt_logo_extra, array( 'show-all', 'tagline-only', 'logo-tagline' ) ) ) {
								$description = get_bloginfo( 'description', 'display' );
								if ( $description || is_customize_preview() ) : ?>
									<p class="site-description"><?php echo esc_html( $description ); /* WPCS: xss ok. */ ?></p>
								<?php
								endif; 
							}?>
						</div>
			    	<?php endif; ?>
				</div><!-- .site-branding -->

				<?php if ( ! empty( $options['ads_image'] ) && ! empty( $options['ads_url'] ) ) : ?>
					<div class="site-advertisement">
	                    <a href="<?php echo esc_url( $options['ads_url'] ); ?>"><img src="<?php echo esc_url( $options['ads_image'] ); ?>"></a>
	                </div><!-- .site-advertisement -->
	            <?php endif; ?>
			</div><!-- .site-branding-wrapper -->
		</div><!-- .wrapper -->
		<?php
	}
endif;
add_action( 'regular_news_header_action', 'regular_news_site_branding', 20 );

if ( ! function_exists( 'regular_news_site_navigation' ) ) :
	/**
	 * Site navigation codes
	 *
	 * @since Regular News 1.0.0
	 *
	 */
	function regular_news_site_navigation() {
		$options = regular_news_get_theme_options();
		?>
		<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
			<?php
			echo regular_news_get_svg( array( 'icon' => 'menu' ) );
			echo regular_news_get_svg( array( 'icon' => 'close' ) );
			?>		
			<span class="menu-label"><?php esc_html_e( 'Primary Menu', 'regular-news' ); ?></span>			
		</button>
		<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="Primary Menu">
			<div class="wrapper">
				<?php 
					$search = '';
					if ( $options['nav_search_enable'] ) :
						$search .= '<li class="main-navigation-search">';
						$search .= '<a href="#" class=""><span class="screen-reader-text">'. esc_html__('search', 'regular-news').'</span>';
						$search .= regular_news_get_svg( array( 'icon' => 'search' ) );
						$search .= regular_news_get_svg( array( 'icon' => 'close' ) );
						$search .= '</a><div id="search">';
						$search .= get_search_form( $echo = false );
		                $search .= '</div></li>';
	                endif;

	                wp_nav_menu(

	                	array(
	                		'theme_location' => 'primary',
	                		'container' => false,
	                		'menu_class' => 'menu nav-menu',
	                		'menu_id' => 'primary-menu',
	                		'echo' => true,
	                		'fallback_cb' => 'regular_news_menu_fallback_cb',
	                		'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s' . $search . '</ul>',
	                		)

	                	);
	        	?>
        	</div><!-- .wrapper -->
		</nav><!-- #site-navigation -->
		<?php
	}
endif;
add_action( 'regular_news_header_action', 'regular_news_site_navigation', 30 );


if ( ! function_exists( 'regular_news_header_end' ) ) :
	/**
	 * Header end html codes
	 *
	 * @since Regular News 1.0.0
	 *
	 */
	function regular_news_header_end() {
		?>
		</header><!-- #masthead -->
		<?php
	}
endif;

add_action( 'regular_news_header_action', 'regular_news_header_end', 50 );

if ( ! function_exists( 'regular_news_content_start' ) ) :
	/**
	 * Site content codes
	 *
	 * @since Regular News 1.0.0
	 *
	 */
	function regular_news_content_start() {
		?>
		<div id="content" class="site-content">
		<?php
	}
endif;
add_action( 'regular_news_content_start_action', 'regular_news_content_start', 10 );

if ( ! function_exists( 'regular_news_header_image' ) ) :
	/**
	 * Header Image codes
	 *
	 * @since Regular News 1.0.0
	 *
	 */
	function regular_news_header_image() {
		if ( regular_news_is_frontpage() )
			return;
		$header_image = get_header_image();
		if ( is_singular() ) :
			$header_image = has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_id(), 'full' ) : $header_image;
		endif;
		?>

		<div id="page-site-header" class="relative" style="background-image: url('<?php echo esc_url( $header_image ); ?>');">
            <div class="overlay"></div>
            <div class="wrapper">
                <header class="page-header">
                    <h2 class="page-title"><?php regular_news_custom_header_banner_title(); ?></h2>
                </header>

                <?php regular_news_add_breadcrumb(); ?>
            </div><!-- .wrapper -->
        </div><!-- #page-site-header -->
		<?php
	}
endif;
add_action( 'regular_news_header_image_action', 'regular_news_header_image', 10 );

if ( ! function_exists( 'regular_news_add_breadcrumb' ) ) :
	/**
	 * Add breadcrumb.
	 *
	 * @since Regular News 1.0.0
	 */
	function regular_news_add_breadcrumb() {
		$options = regular_news_get_theme_options();
		// Bail if Breadcrumb disabled.
		$breadcrumb = $options['breadcrumb_enable'];
		if ( false === $breadcrumb ) {
			return;
		}
		
		// Bail if Home Page.
		if ( regular_news_is_frontpage() ) {
			return;
		}

		echo '<div id="breadcrumb-list">';
				/**
				 * regular_news_simple_breadcrumb hook
				 *
				 * @hooked regular_news_simple_breadcrumb -  10
				 *
				 */
				do_action( 'regular_news_simple_breadcrumb' );
		echo '</div><!-- #breadcrumb-list -->';
		return;
	}
endif;

if ( ! function_exists( 'regular_news_content_end' ) ) :
	/**
	 * Site content codes
	 *
	 * @since Regular News 1.0.0
	 *
	 */
	function regular_news_content_end() {
		?>
			<div class="menu-overlay"></div>
		</div><!-- #content -->
		<?php
	}
endif;
add_action( 'regular_news_content_end_action', 'regular_news_content_end', 10 );

if ( ! function_exists( 'regular_news_footer_start' ) ) :
	/**
	 * Footer starts
	 *
	 * @since Regular News 1.0.0
	 *
	 */
	function regular_news_footer_start() {
		?>
		<footer id="colophon" class="site-footer" role="contentinfo">
		<?php
	}
endif;
add_action( 'regular_news_footer', 'regular_news_footer_start', 10 );

if ( ! function_exists( 'regular_news_footer_site_info' ) ) :
	/**
	 * Footer starts
	 *
	 * @since Regular News 1.0.0
	 *
	 */
	function regular_news_footer_site_info() {
		$theme_data = wp_get_theme();
        $options = regular_news_get_theme_options();
        $search = array( '[the-year]', '[site-link]' );
	
       	$replace = array( date( 'Y' ), '<a href="'. esc_url( home_url( '/' ) ) .'">'. esc_attr( get_bloginfo( 'name', 'display' ) ) . '</a>' );

        $options['copyright_text'] = str_replace( $search, $replace, $options['copyright_text'] );
        $copyright_text = $options['copyright_text']; 
        $poweredby_text = esc_html( $theme_data->get( 'Name') ) . '&nbsp;' . esc_html__( 'by', 'regular-news' ). '&nbsp;<a target="_blank" href="'. esc_url( $theme_data->get( 'AuthorURI' ) ) .'">'. esc_html( ucwords( $theme_data->get( 'Author' ) ) ) .'</a>';
        ?>
		<div class="site-info col-2">
                <div class="wrapper">
                    <span class="copyright-text">
	                   	<?php echo regular_news_santize_allow_tag( $copyright_text ); ?>	            
	               		<?php 
	               			echo regular_news_santize_allow_tag( $poweredby_text );
	                		if ( function_exists( 'the_privacy_policy_link' ) ) {
								the_privacy_policy_link( ' | ' );
							}
	                	?>
                	</span>
                </div><!-- .wrapper -->    
            </div><!-- .site-info -->

		<?php
	}
endif;
add_action( 'regular_news_footer', 'regular_news_footer_site_info', 40 );

if ( ! function_exists( 'regular_news_footer_scroll_to_top' ) ) :
	/**
	 * Footer starts
	 *
	 * @since Regular News 1.0.0
	 *
	 */
	function regular_news_footer_scroll_to_top() {
		$options  = regular_news_get_theme_options();
		if ( true === $options['scroll_top_visible'] ) : ?>
			<div class="backtotop"><?php echo regular_news_get_svg( array( 'icon' => 'up' ) ); ?></div>
		<?php endif;
	}
endif;
add_action( 'regular_news_footer', 'regular_news_footer_scroll_to_top', 40 );

if ( ! function_exists( 'regular_news_footer_end' ) ) :
	/**
	 * Footer starts
	 *
	 * @since Regular News 1.0.0
	 *
	 */
	function regular_news_footer_end() {
		?>
		</footer>
		<div class="popup-overlay"></div>
		<?php
	}
endif;
add_action( 'regular_news_footer', 'regular_news_footer_end', 100 );
