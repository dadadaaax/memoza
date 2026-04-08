<?php
/**
 * The template for woocommerce pages.
 *
 * @package Acmethemes
 * @subpackage SuperNews
 */
get_header(); ?>
<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <?php if ( have_posts() ) :
            woocommerce_content();
        endif;
        ?>

    </main><!-- #main -->
</div><!-- #primary -->
<?php
get_sidebar( 'left' );
get_sidebar();
get_footer();