<?php
/**
 * content and content wrapper end
 *
 * @since SuperNews 1.0.0
 *
 * @param null
 * @return null
 *
 */
if ( ! function_exists( 'supernews_after_content' ) ) :

    function supernews_after_content() {
      ?>
        </div><!-- #content -->
        </div><!-- content-wrapper-->
    <?php
    }
endif;
add_action( 'supernews_action_after_content', 'supernews_after_content', 10 );

/**
 * Footer content
 *
 * @since SuperNews 1.0.0
 *
 * @param null
 * @return null
 *
 */
if ( ! function_exists( 'supernews_footer' ) ) :

    function supernews_footer() {

        global $supernews_customizer_all_values;
	    if( is_active_sidebar( 'full-width-footer' ) ) :
		    dynamic_sidebar( 'full-width-footer' );
	    endif;
        ?>
        <div class="clearfix"></div>
        <footer id="colophon" class="site-footer">
            <div class="footer-wrapper">
                <div class="top-bottom wrapper">
                    <div id="footer-top">
                        <div class="footer-columns">
                           <?php if( is_active_sidebar( 'footer-col-one' ) ) : ?>
                                <div class="footer-sidebar acme-col-3">
                                    <?php dynamic_sidebar( 'footer-col-one' ); ?>
                                </div>
                            <?php endif;
                            if( is_active_sidebar( 'footer-col-two' ) ) : ?>
                                <div class="footer-sidebar acme-col-3">
                                    <?php dynamic_sidebar( 'footer-col-two' ); ?>
                                </div>
                            <?php endif;
                            if( is_active_sidebar( 'footer-col-three' ) ) : ?>
                                <div class="footer-sidebar acme-col-3">
                                    <?php dynamic_sidebar( 'footer-col-three' ); ?>
                                </div>
                            <?php endif; ?>
                            <div class="clear"></div>
                        </div>
                    </div><!-- #foter-top -->
                    <div class="clearfix"></div>
                 </div><!-- top-bottom-->
                <div class="footer-copyright wrapper">
                    <p class="copyright-text">
                        <?php if( isset( $supernews_customizer_all_values['supernews-footer-copyright'] ) ): ?>
                            <?php echo wp_kses_post( $supernews_customizer_all_values['supernews-footer-copyright'] ); ?>
                        <?php endif; ?>
                    </p>
                    <div class="site-info">
                        <a href="<?php echo esc_url( __( 'https://wordpress.org/', 'supernews' ) ); ?>"><?php printf( esc_html__( 'Proudly powered by %s', 'supernews' ), 'WordPress' ); ?></a>
                        <span class="sep"> | </span>
                        <?php printf( esc_html__( 'Theme: %1$s by %2$s', 'supernews' ), 'SuperNews', '<a href="https://www.acmethemes.com/">Acme Themes</a>' ); ?>
                    </div><!-- .site-info -->
                    <div class="clearfix"></div>
                </div>
            </div><!-- footer-wrapper-->
        </footer><!-- #colophon -->
    <?php
    }
endif;
add_action( 'supernews_action_footer', 'supernews_footer', 10 );

/**
 * Page end
 *
 * @since SuperNews 1.0.0
 *
 * @param null
 * @return null
 *
 */
if ( ! function_exists( 'supernews_page_end' ) ) :

    function supernews_page_end() {
        ?>
        </div><!-- #page -->
    <?php
    }
endif;
add_action( 'supernews_action_after', 'supernews_page_end', 10 );