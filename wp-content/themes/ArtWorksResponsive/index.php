


<?php
get_header();
?>	

<div class="container"> <!--stylesheet needs it-->
    <div id="content_inside">  <!--INFSCROLL needs it-->

        <?php
   

//  http://wordpress.stackexchange.com/questions/2117/query-posts-using-meta-compare-where-meta-value-is-smaller-or-greater-or-equ
//var_dump($args);
        
        $args = array_merge($wp_query->query, array('posts_per_page' => 9, 'cat' => $cat_list . "," . get_category_id('AD')));
        query_posts($args);

        while (have_posts()) : the_post();
            ?>                       


            <?php if (in_category('AD')) { ?> 
                <div class="memejetad" >

                    <?php
                    ;
                } else { //zwykle memy
                    ?> 

                    <div id="<?php echo $post->ID; ?>" slug="<?php echo $post->post_name; ?>" class="home_post_box columns three small" postmid="<?php echo $post->ID; ?>" href="<?php echo get_post_meta($post->ID, 'LocalURL', true); ?>" posthref="<?php echo get_post_meta($post->ID, 'MEME_ID', true); ?> "  >
                    <?php }; ?>






                    <?php if (in_category('TEXT')) { ?> 
                        <?php echo get_the_content(); ?> 

                        <?php
                        ;
                    } else {
                        ?>





                        <div class="home_post_upper_text_box">


                            <div class="meme_title"><?php echo ucfirst(get_post_meta($post->ID, 'title', true)); ?>  </div>



                        </div><!--//home_post_text-->


                        <div class="thumb_img">
                            <?php the_post_thumbnail('home-image'); ?>
                        </div>


                        <div class="full_img">
                            <?php the_content(); ?>
                        </div>


                        <div class="meme_source">  <a href="<?php echo get_post_meta($post->ID, 'MEME_ID', true); ?> ">   <?php echo strtolower(get_post_meta($post->ID, 'DomainName', true)) ?>  </a></div>

                        <div class="home_post_bottom_box">
                        <div>
                            <a href="<?php echo $post->post_name; ?>">   <img class="share_button" src="wp-content/themes/ArtWorksResponsive/images/share_button.png" alt="share" > </a>
                        </div>
                        </div>
                            

                    <?php }; ?>



                </div><!--//column three-->

            <?php endwhile; ?>            


        </div><!--//content_inside-->


        <div class="load_more_cont">
            <div align="center"><div class="load_more_text">

                    <?php
                    ob_start();
                    next_posts_link('<img src="' . get_bloginfo('stylesheet_directory') . '/images/loading-button.png" />');
                    $buffer = ob_get_contents();
                    ob_end_clean();
                    if (!empty($buffer))
                        echo $buffer;
                    ?>

                </div></div>
        </div><!--//load_more_cont-->                    

        <?php wp_reset_query(); ?>                            

    </div><!--//content-->    

    <?php get_footer(); ?>
