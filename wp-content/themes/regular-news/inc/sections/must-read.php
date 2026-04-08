<?php
/**
 * Must Read  section
 *
 * This is the template for the content of must_read section
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */
if ( ! function_exists( 'regular_news_add_must_read_section' ) ) :
    /**
    * Add must_read section
    *
    *@since Regular News 1.0.0
    */
    function regular_news_add_must_read_section() {
        $options = regular_news_get_theme_options();
        // Check if must_read is enabled on frontpage
        $must_read_enable = apply_filters( 'regular_news_section_status', true, 'must_read_section_enable' );

        if ( true !== $must_read_enable ) {
            return false;
        }
        // Get must_read section details
        $section_details = array();
        $section_details = apply_filters( 'regular_news_filter_must_read_section_details', $section_details );

        if ( empty( $section_details ) ) {
            return;
        }

        // Render must_read section now.
        regular_news_render_must_read_section( $section_details );
    }
endif;

if ( ! function_exists( 'regular_news_get_must_read_section_details' ) ) :
    /**
    * must_read section details.
    *
    * @since Regular News 1.0.0
    * @param array $input must_read section details.
    */
    function regular_news_get_must_read_section_details( $input ) {
        $options = regular_news_get_theme_options();
        
        $content = array();

        $cat_id = ! empty( $options['must_read_content_category'] ) ? $options['must_read_content_category'] : '';
        $args = array(
            'post_type'         => 'post',
            'posts_per_page'    => 4,
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
                    $page_post['image']     = has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_id(), 'full' ) : '';

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
// must_read section content details.
add_filter( 'regular_news_filter_must_read_section_details', 'regular_news_get_must_read_section_details' );


if ( ! function_exists( 'regular_news_render_must_read_section' ) ) :
  /**
   * Start must_read section
   *
   * @return string must_read content
   * @since Regular News 1.0.0
   *
   */
   function regular_news_render_must_read_section( $content_details = array() ) {
        $options = regular_news_get_theme_options();

        if ( empty( $content_details ) ) {
            return;
        } ?>

        <div id="must-read" class="page-section">
                <div class="wrapper">
                    <?php if ( ! empty( $options['must_read_title'] ) ) : ?>
                        <div class="section-header">
                            <h2 class="section-title"><?php echo esc_html( $options['must_read_title'] ); ?></h2>
                        </div><!-- .section-header -->
                    <?php endif; ?>

                    <div class="must-read-wrapper">
                        <?php foreach ( $content_details as $content ) : ?>
                            <article <?php echo ! empty( $content['image'] ) ? 'has' : 'no'; ?>-post-thumbnail>
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

                                        <span class="cat-links">
                                            <?php the_category( '', '', $content['id'] ); ?>
                                        </span><!-- .cat-links -->
                                </div><!-- .entry-container -->
                            </article>
                        <?php endforeach; ?>
                    </div><!-- .must-read-wrapper -->
                </div><!-- .wrapper -->
            </div><!-- #must-read -->
   
    <?php }
endif;
