<?php
/**
 * Popular section
 *
 * This is the template for the content of popular section
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */
if ( ! function_exists( 'regular_news_add_popular_section' ) ) :
    /**
    * Add popular section
    *
    *@since Regular News 1.0.0
    */
    function regular_news_add_popular_section() {
    	$options = regular_news_get_theme_options();
        // Check if popular is enabled on frontpage
        $popular_enable = apply_filters( 'regular_news_section_status', true, 'popular_section_enable' );

        if ( true !== $popular_enable ) {
            return false;
        }
        // Get popular section details
        $section_details = array();
        $section_details = apply_filters( 'regular_news_filter_popular_section_details', $section_details );

        if ( empty( $section_details ) ) {
            return;
        }

        // Render popular section now.
        regular_news_render_popular_section( $section_details );
    }
endif;

if ( ! function_exists( 'regular_news_get_popular_section_details' ) ) :
    /**
    * popular section details.
    *
    * @since Regular News 1.0.0
    * @param array $input popular section details.
    */
    function regular_news_get_popular_section_details( $input ) {
        $options = regular_news_get_theme_options();

        
        $content = array();

        $cat_id = ! empty( $options['popular_content_category'] ) ? $options['popular_content_category'] : '';
        $args = array(
            'post_type'         => 'post',
            'posts_per_page'    => 5,
            'cat'               => absint( $cat_id ),
            'ignore_sticky_posts'   => true,
            );                    


            // Run The Loop.
            $query = new WP_Query( $args );
            if ( $query->have_posts() ) : 
                while ( $query->have_posts() ) : $query->the_post();
                    $page_post['id']        = get_the_id();
                    $page_post['auth_id']   = get_the_author_meta('ID');
                    $page_post['title']     = get_the_title();
                    $page_post['url']       = get_the_permalink();
                    $page_post['excerpt']   = regular_news_trim_content( 25 );
                    $page_post['image']  	= has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_id(), 'full' ) : '';

                    // Push to the main array.
                    array_push( $content, $page_post );
                endwhile;
            endif;
            wp_reset_postdata();
            
        if ( ! empty( $content ) ) {
            $input = $content;
        }
        return $input;
    }
endif;
// popular section content details.
add_filter( 'regular_news_filter_popular_section_details', 'regular_news_get_popular_section_details' );


if ( ! function_exists( 'regular_news_render_popular_section' ) ) :
  /**
   * Start popular section
   *
   * @return string popular content
   * @since Regular News 1.0.0
   *
   */
   function regular_news_render_popular_section( $content_details = array() ) {
        $options = regular_news_get_theme_options();

        if ( empty( $content_details ) ) {
            return;
        } ?>

        <div id="hero-section">
           <div class="wrapper">
                <div class="hero-section-wrapper item-wrapper grid">
                    <?php 
                    $i =1;
                    foreach ( $content_details as $content ) : 

                    ?>
                        <article class="grid-item <?php echo ($i ==2) ? 'large-width' : '' ; ?>">
                            <div class="hero-item-wrapper">
                                <div class="featured-image" style="background-image: url('<?php echo esc_url( $content['image'] ); ?>');">
                                    <a href="<?php echo esc_url( $content['url'] ); ?>" class="post-thumbnail-link"></a>
                                </div>

                                <div class="entry-container">
                                    <div class="entry-meta">
                                        <?php  
                                            echo regular_news_author( $content['auth_id'] );
                                            regular_news_posted_on( $content['id'] );
                                        ?>

                                    </div><!-- .entry-meta -->

                                    <header class="entry-header">
                                        <h2 class="entry-title"><a href="<?php echo esc_url( $content['url'] ); ?>" tabindex="0"><?php echo esc_html( $content['title'] ); ?></a></h2>
                                    </header>

                                    <div class="entry-content">
                                        <p><?php echo esc_html( $content['excerpt'] ); ?></p>
                                    </div>
                                </div><!-- .entry-container -->

                                <span class="cat-links">
                                    <?php the_category( '', '', $content['id'] ); ?>
                                </span><!-- .cat-links -->
                            </div><!-- .hero-item-wrapper -->
                        </article><!-- .hentry -->
                    <?php 
                    $i++;
                    endforeach; ?>
                </div>
           </div><!-- .wrapper-->
       </div><!-- #hero-section -->
        
    <?php }
endif;