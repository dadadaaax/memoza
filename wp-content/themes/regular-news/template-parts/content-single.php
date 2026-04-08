<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */
$options = regular_news_get_theme_options();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'clear' ); ?>>

	<?php if ( ! $options['single_post_hide_date'] ) :
        regular_news_posted_on();
	endif; ?>

    <div class="entry-content">
        <?php
			the_content( sprintf(
				/* translators: %s: Name of current post. */
				wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'regular-news' ), array( 'span' => array( 'class' => array() ) ) ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			) );

			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'regular-news' ),
				'after'  => '</div>',
			) );
		?>
    </div><!-- .entry-content -->

    <div class="entry-meta">
    
        <?php if ( ! $options['single_post_hide_author'] ) :
            echo regular_news_author( get_the_author_meta('ID') );
        endif;

		regular_news_single_categories();
		regular_news_entry_footer(); 
		?>
    </div><!-- .entry-meta -->

</article><!-- #post-## -->
