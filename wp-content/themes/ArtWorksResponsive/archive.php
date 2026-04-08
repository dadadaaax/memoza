<?php get_header(); ?>	

<div id="content">
	<div id="content_inside">
    <?php
    global $wp_query;
        
    $args = array_merge( $wp_query->query, array( 'posts_per_page' => 30 ) );
    query_posts( $args );        
    $x = 0;
    while (have_posts()) : the_post(); ?>                        
    
	<?php $box_type = ''; ?>
            <?php if ($x == 2 || $x == 5 || $x == 8 || $x == 11 || $x == 14 || $x == 17 || $x == 20 || $x == 23 || $x == 26 || $x == 28 || $x == 31 || $x == 34 || $x == 37 || $x == 40 || $x == 43 || $x == 46 || $x == 49 || $x == 52 || $x == 55 || $x == 58 || $x == 61 || $x == 64) { ?>
                <?php //$box_type .= ' home_post_box_last'; ?>
            <?php } ?>

            <?php if ($x == 2 || $x == 5 || $x == 8 || $x == 11 || $x == 14 || $x == 17 || $x == 20 || $x == 23 || $x == 26 || $x == 28 || $x == 31 || $x == 34 || $x == 37 || $x == 40 || $x == 43 || $x == 46 || $x == 49 || $x == 52 || $x == 55 || $x == 58 || $x == 61 || $x == 64) { ?>
                <?php $box_type .= ' home_post_box'; ?>
            <?php } ?>	  
            <div class="home_post_box<?php echo $box_type; ?>" >

            <?php    
             if   (in_category( 'TEXT' )) {
                       echo get_the_content();
              ?>  
                 
             <?php ;} else { ?>
                
                <a postmid="<?php echo $post->ID ; ?>" href="<?php echo get_post_meta($post->ID, 'LocalURL', true); ?>" posthref="<?php echo get_post_meta($post->ID, 'MEME_ID', true); ?>" rel="lightbox">
            <?php the_post_thumbnail('home-image'); ?>
                </a>

               <?php }; ?>
                
                <div class="home_post_text">
                    <h3><?php echo get_post_meta($post->ID, 'DomainName', true);  // the_title();  ?></h3>

                </div><!--//home_post_text-->	
            </div><!--//home_post_box-->

    <?php if ($x == 2 || $x == 5 || $x == 8 || $x == 11 || $x == 14 || $x == 17 || $x == 20 || $x == 23 || $x == 26 || $x == 28 || $x == 31 || $x == 34 || $x == 37 || $x == 40 || $x == 43 || $x == 46 || $x == 49 || $x == 52 || $x == 55 || $x == 58 || $x == 61 || $x == 64) { ?>
                <!--<div class="desktop_clear"></div>-->
    <?php } ?>

            <?php if ($x == 2 || $x == 5 || $x == 8 || $x == 11 || $x == 14 || $x == 17 || $x == 20 || $x == 23 || $x == 26 || $x == 28 || $x == 31 || $x == 34 || $x == 37 || $x == 40 || $x == 43 || $x == 46 || $x == 49 || $x == 52 || $x == 55 || $x == 58 || $x == 61 || $x == 64) { ?>
                <!--		<div class="tablet_clear"></div>-->
            <?php } ?>
            <?php $x++; ?>
        <?php endwhile; ?>            

        <div class="clear"></div>	

    </div><!--//content_inside-->
    

    
    <div class="clear"></div>
    <div class="load_more_cont">
        <div align="center"><div class="load_more_text">
        
        <?php
        
        ob_start();
	next_posts_link('<img src="' . get_bloginfo('stylesheet_directory') . '/images/loading-button.png" />');
	$buffer = ob_get_contents();
	ob_end_clean();
	if(!empty($buffer)) echo $buffer;
        ?>
        
        </div></div>
    </div><!--//load_more_cont-->                    
    
    <?php wp_reset_query(); ?>                            h
    
</div><!--//content-->    
	
<?php get_footer(); ?>