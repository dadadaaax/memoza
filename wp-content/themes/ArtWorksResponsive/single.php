<?php get_header(); ?>	
<div class="container">
    <div id="single_cont">


        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>     





                <div id="<?php echo $post->ID; ?>" slug="<?php echo $post->post_name; ?>" class="home_post_box columns three big" postmid="<?php echo $post->ID; ?>" href="<?php echo get_post_meta($post->ID, 'LocalURL', true); ?>" posthref="<?php echo get_post_meta($post->ID, 'MEME_ID', true); ?> "  >


                    <div class="home_post_upper_text_box">
                        <div class="meme_title"><?php echo ucfirst(get_post_meta($post->ID, 'title', true)); ?>  </div>


                    </div><!--//home_post_text-->




                    <div class="full_img" style="display: block">
                        <a href="<?php echo get_post_meta($post->ID, 'MEME_ID', true); ?> " target="_blank">     <?php the_content(); ?> </a>



                    <div class="meme_source">  <a href="<?php echo get_post_meta($post->ID, 'MEME_ID', true); ?> ">   <?php echo strtolower(get_post_meta($post->ID, 'DomainName', true)) ?>  </a></div>

                    <div class="bottom_buttons">

                        <div class="sharing_buttons">


                            <div class="fb-like" data-href="<?php echo $_SERVER['REQUEST_URI'] ?>" data-width="150" data-layout="standard" data-action="like" data-show-faces="true" data-share="false
                                 "></div>



                        </div>


                        <div class="download_button">
                            <!--HTML5 ONLY-->
                            <a href="<?php echo get_post_meta($post->ID, 'LocalURL', true); ?>" download=""> <strong>Pobierz </strong> </a>
                        </div>

                    </div>


                    </div>







                    <br /><br />
                    <?php // comments_template();  ?>
                <?php
                endwhile;
            else:
                ?>
                <!--<h3>Sorry, no posts matched your criteria.</h3>-->
<?php endif; ?>                    



<?php // get_sidebar();   ?>

            <div class="clear"></div>

        </div>

    </div>

</div>

<?php get_footer(); ?>