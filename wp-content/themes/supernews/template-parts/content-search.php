<?php
/**
 * Template part for displaying results in search pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Acme Themes
 * @subpackage SuperNews
 */
global $supernews_customizer_all_values;
$supernews_blog_no_image = '';
if( !has_post_thumbnail() || 'no-image' == $supernews_customizer_all_values['supernews-blog-archive-layout'] ) {
	$supernews_blog_no_image = 'blog-no-image';
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $supernews_blog_no_image ); ?>>
	<?php
	if( has_post_thumbnail() && 'show-image' == $supernews_customizer_all_values['supernews-blog-archive-layout'] ) {
		?>
		<!--post thumbnal options-->
		<div class="post-thumb">
			<?php
			$thumb = 'large';
			if( 'no-sidebar' == $supernews_customizer_all_values['supernews-sidebar-layout'] ){
				$thumb = 'full';
			}
			?>
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( $thumb );?>
			</a>
			<?php supernews_list_category(); ?>
		</div><!-- .post-thumb-->
		<?php
	}
	?>
	<div class="post-content">
		<header class="entry-header">
			<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
			<div class="entry-meta">
				<?php
                if ( 'post' === get_post_type() ) :
                    supernews_posted_on();
                endif;
                supernews_entry_footer();
                ?>
			</div><!-- .entry-meta -->
		</header><!-- .entry-header -->
		<div class="entry-content">
			<?php
			the_excerpt();
			if( !empty( $supernews_blog_archive_read_more ) ){
				?>
                <a class="read-more" href="<?php the_permalink(); ?> ">
					<?php echo esc_html( $supernews_blog_archive_read_more ); ?>
                </a>
				<?php
			}
			?>
		</div><!-- .entry-content -->
	</div>
</article><!-- #post-## -->