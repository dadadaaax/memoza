<?php
/**
 * Call to action section
 *
 * This is the template for the content of cta section
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */
if ( ! function_exists( 'regular_news_add_cta_section' ) ) :
    /**
    * Add cta section
    *
    *@since Regular News 1.0.0
    */
    function regular_news_add_cta_section() {
    	$options = regular_news_get_theme_options();
        // Check if cta is enabled on frontpage
        $cta_enable = apply_filters( 'regular_news_section_status', true, 'cta_section_enable' );

        if ( true !== $cta_enable ) {
            return false;
        }
        // Get cta section details
        $section_details = array();
        $section_details = apply_filters( 'regular_news_filter_cta_section_details', $section_details );

        if ( empty( $section_details ) ) {
            return;
        }

        // Render cta section now.
        regular_news_render_cta_section( $section_details );
    }
endif;

if ( ! function_exists( 'regular_news_get_cta_section_details' ) ) :
    /**
    * cta section details.
    *
    * @since Regular News 1.0.0
    * @param array $input cta section details.
    */
    function regular_news_get_cta_section_details( $input ) {
        $options = regular_news_get_theme_options();
        
        $content = array();
            $post_id = ! empty( $options['cta_content_post'] ) ? $options['cta_content_post'] : '';
            $args = array(
                'post_type'         => 'post',
                'p'                 => $post_id,
                'posts_per_page'    => 1,
                'ignore_sticky_posts' => true,
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
                    $page_post['image']  	= has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_id(), 'large' ) : '';

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
// cta section content details.
add_filter( 'regular_news_filter_cta_section_details', 'regular_news_get_cta_section_details' );


if ( ! function_exists( 'regular_news_render_cta_section' ) ) :
  /**
   * Start cta section
   *
   * @return string cta content
   * @since Regular News 1.0.0
   *
   */
   function regular_news_render_cta_section( $content_details = array() ) {
        $options = regular_news_get_theme_options();

        if ( empty( $content_details ) ) {
            return;
        } 

        foreach ( $content_details as $content ) : ?>
            <div id="cta" style="background-image: url('<?php echo esc_url( $content['image'] ); ?>');">
                <div class="overlay"></div>
                <div class="wrapper">
                    <article> 
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

                            <span class="cat-links">
                                <?php the_category( '', '', $content['id'] ); ?>
                            </span><!-- .cat-links -->
                        </div><!-- .entry-container -->
                    </article>
                </div><!-- .wrapper -->
            </div><!-- #cta -->

        <?php endforeach;
    }
endif;