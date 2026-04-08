<?php
/*
	Template Name: Blog
*/
?>
<?php get_header(); ?>	
<div id="content">
 <?php get_sidebar(); ?>
		
		
	<div id="content_inside">
	<?php
	$args = array(
		 'category_name' => 'blog',
		 'post_type' => 'post',
		 'posts_per_page' => 4,
		 'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1)
		 );
	$x = 0;
	query_posts($args);
	while (have_posts()) : the_post(); ?>                                            
	<?php $box_type = ''; ?>
	<?php if($x == 3 || $x == 7 || $x == 11 || $x == 15 || $x == 19 || $x == 23 || $x == 27 || $x == 31 || $x == 35 || $x == 39 || $x == 43 || $x == 47) { ?>
		<?php $box_type .= ' home_blog_box_last'; ?>
	<?php } ?>
	
	<?php if($x == 3 || $x == 7 || $x == 11 || $x == 15 || $x == 19 || $x == 23 || $x == 27 || $x == 31 || $x == 35 || $x == 39 || $x == 43 || $x == 47) { ?>
		<?php $box_type .= ' home_blog_box_tablet_last'; ?>
	<?php } ?>	
		<div class="home_post_box home_blog_box<?php echo $box_type; ?>">
		<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('blog-image'); ?></a>
		<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
		<p><?php echo ds_get_excerpt('280'); ?></p>
	</div><!--//home_blog_box-->	
	
	<?php if($x == 3 || $x == 7 || $x == 11 || $x == 15 || $x == 19 || $x == 23 || $x == 27 || $x == 31 || $x == 35 || $x == 39 || $x == 43 || $x == 47) { ?>
		<div class="desktop_clear"></div>
	<?php } ?>
	
	<?php if($x == 1 || $x == 3 || $x == 5 || $x == 7 || $x == 9 || $x == 11 || $x == 13 || $x == 15 || $x == 17 || $x == 19 || $x == 21 || $x == 22) { ?>
		<div class="tablet_clear"></div>
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
    
    <?php wp_reset_query(); ?>                            
    
  	
	
</div><!--//content-->  
<?php get_footer(); ?>