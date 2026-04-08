<?php
/**
 * Headline section
 *
 * This is the template for the content of headline section
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */
if ( ! function_exists( 'regular_news_add_headline_section' ) ) :
    /**
    * Add headline section
    *
    *@since Regular News 1.0.0
    */
    function regular_news_add_headline_section() {
    	$options = regular_news_get_theme_options();
        // Check if headline is enabled on frontpage
        $headline_enable = apply_filters( 'regular_news_section_status', true, 'headline_section_enable' );

        if ( true !== $headline_enable ) {
            return false;
        }
        // Get headline section details
        $section_details = array();
        $section_details = apply_filters( 'regular_news_filter_headline_section_details', $section_details );

        if ( empty( $section_details ) ) {
            return;
        }

        // Render headline section now.
        regular_news_render_headline_section( $section_details );
    }
endif;

if ( ! function_exists( 'regular_news_get_headline_section_details' ) ) :
    /**
    * headline section details.
    *
    * @since Regular News 1.0.0
    * @param array $input headline section details.
    */
    function regular_news_get_headline_section_details( $input ) {
        $options = regular_news_get_theme_options();        
        $content = array();
        
            $cat_id = ! empty( $options['headline_content_category'] ) ? $options['headline_content_category'] : '';
            $args = array(
                'post_type'         => 'post',
                'posts_per_page'    => 3,
                'cat'               => absint( $cat_id ),
                'ignore_sticky_posts'   => true,
                );                    


            // Run The Loop.
            $query = new WP_Query( $args );
            if ( $query->have_posts() ) : 
                while ( $query->have_posts() ) : $query->the_post();
                    $page_post['title']     = get_the_title();
                    $page_post['url']       = get_the_permalink();

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
// headline section content details.
add_filter( 'regular_news_filter_headline_section_details', 'regular_news_get_headline_section_details' );


if ( ! function_exists( 'regular_news_render_headline_section' ) ) :
  /**
   * Start headline section
   *
   * @return string headline content
   * @since Regular News 1.0.0
   *
   */
   function regular_news_render_headline_section( $content_details = array() ) {
        $options = regular_news_get_theme_options();
        $title = ! empty( $options['headline_title'] ) ? $options['headline_title'] : esc_html__( 'Trending Now', 'regular-news' );

        if ( empty( $content_details ) ) {
            return;
        } ?>

        <div id="trending-news">
            <div class="wrapper">
                <div class="news-header">
                    <span class="news-title"><?php echo esc_html( $title ); ?></span>
                </div><!-- .section-header -->

                <div class="trending-news-posts relative modern-slider" data-slick='{"slidesToShow": 1, "slidesToScroll": 1, "infinite": true, "speed": 500, "dots": false, "arrows":true, "autoplay": true, "draggable": true, "fade": true }'>
                    <?php foreach ( $content_details as $content ) : ?>
                        <div class="news-item">
                            <p><?php echo esc_html( $content['title'] ); ?></p>
                        </div><!-- .slick-item -->
                    <?php endforeach; ?>
                </div><!-- .breaking-news-posts -->
            </div><!-- .wrapper -->
        </div><!-- #breaking-news -->
        
    <?php }
endif;