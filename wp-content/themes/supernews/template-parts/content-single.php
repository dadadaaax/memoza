<?php
/**
 * Template part for displaying single posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Acme Themes
 * @subpackage SuperNews
 */
global $supernews_customizer_all_values;
$supernews_blog_no_image = '';
if( !has_post_thumbnail() ) {
	$supernews_blog_no_image = 'blog-no-image';
}
$supernews_single_image_layout = $supernews_customizer_all_values['supernews-single-image-layout'];
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $supernews_blog_no_image ); ?>>
	<!--post thumbnal options-->
	<?php if( has_post_thumbnail( ) && 'disable' != $supernews_single_image_layout ) {
		?>
		<div class="post-thumb">
			<?php
			the_post_thumbnail( $supernews_single_image_layout );
            supernews_list_category();
            ?>
		</div><!-- .post-thumb-->
	<?php
	}
	?>
	<div class="post-content">
		<header class="entry-header">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			<div class="entry-meta">
				<?php if ( 'post' === get_post_type() ) : ?>
					<?php supernews_posted_on(); ?>
				<?php endif; ?>
				<?php supernews_entry_footer( 1 ); ?>
			</div><!-- .entry-meta -->
		</header><!-- .entry-header -->
		<div class="entry-content">
			<?php
            the_content();
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'supernews' ),
				'after'  => '</div>',
			) );
			?>
		</div><!-- .entry-content -->
	</div>
</article><!-- #post-## -->