<?php
$wpfp_before = "";
echo "<div class='wpfp-span'>";

?>


<div id="fav_memes_container">
    
<?php



if (!empty($user)) {
    if (wpfp_is_user_favlist_public($user)) {
        $wpfp_before = "$user's Favorite Posts.";
    } else {
        $wpfp_before = "$user's list is not public.";
    }
}

if ($wpfp_before):
    echo '<div class="wpfp-page-before">' . $wpfp_before . '</div>';
endif;


if ($favorite_post_ids) {
    $favorite_post_ids = array_reverse($favorite_post_ids);
    $post_per_page = wpfp_get_option("post_per_page");
    $page = intval(get_query_var('paged'));

    $qry = array('post__in' => $favorite_post_ids, 'posts_per_page' => $post_per_page, 'orderby' => 'post__in', 'paged' => $page);
    // custom post type support can easily be added with a line of code like below.
    // $qry['post_type'] = array('post','page');
    query_posts($qry);

    while (have_posts()) : the_post();
        ?>

 
       

            <div class="home_post_box fav">

                <a href="<?php the_permalink(); ?>" >  <?php the_post_thumbnail('thumbnail'); ?>
                   <div class="home_post_text h5">
                        <?php the_title(); ?> 
                    </div>
                </a> 
                
                   <div class="button_fav fav_meme_remove_link "> 
                <?php wpfp_remove_favorite_link(get_the_ID()); ?> 
            </div>
            </div>        
         


        <?php
    endwhile;

    echo '<div class="navigation">';
    if (function_exists('wp_pagenavi')) {
        wp_pagenavi();
    } else {
        ?>
        <div class="alignleft"><?php next_posts_link(__('&larr; Previous Entries', 'buddypress')) ?></div>
        <div class="alignright"><?php previous_posts_link(__('Next Entries &rarr;', 'buddypress')) ?></div>
    <?php
    }
    echo '</div>';

    wp_reset_query();
} else {
    $wpfp_options = wpfp_get_options();
    echo "<li>";
    echo $wpfp_options['favorites_empty'];
    echo "</li>";
}


?>
<div id="fav_desktop_clear"> </div>

        <div id="fav_meme_remove_all_links" class="button_fav"> 
               <?php wpfp_clear_list_link(); ?>
           </div>
                   

<?php
wpfp_cookie_warning();
?>


</div> <!--Main container ends-->


