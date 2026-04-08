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
$class = has_post_thumbnail() ? '' : 'no-post-thumbnail';
$readmore = ! empty( $options['read_more_text'] ) ? $options['read_more_text'] : esc_html__( 'Learn More', 'regular-news' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $class ); ?>>
    <div class="archive-post-wrapper">
         <?php if ( has_post_thumbnail() ) : ?>
            <div class="featured-image" style="background-image:url('<?php the_post_thumbnail_url( 'post-thumbnail' ); ?>');">
                <a href="<?php the_permalink(); ?>" class="post-thumbnail-link"></a>
            </div><!-- .featured-image-->
        <?php endif; ?>

        <div class="entry-container">

            <div class="entry-meta">
                <?php  
                    if ( ! $options['hide_author'] ) {
                        echo regular_news_author( get_the_author_meta('ID') );
                    }
                    
                    if ( ! $options['hide_date'] ) {
                        regular_news_posted_on();
                    }
                    
                ?>
            </div>
           
            <header class="entry-header">
                <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            </header>


            <div class="entry-content">
                <p><?php the_excerpt(); ?></p>
            </div><!-- .entry-content -->

            <span class="cat-links">
                <?php echo regular_news_article_footer_meta(); ?>
            </span>

        </div><!-- .entry-container -->
    </div><!-- .archive-post-wrapper -->
</article>

