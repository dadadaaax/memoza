<?php

//ads handling 

/*
 * moving the post with ad code to n-th position (sorted by time)
 */
function move_post_to_nth_pos($postid, $pos) {
    $args = array(
        'posts_per_page' => $pos,
        'offset' => 0,
        'category' => '',
        'orderby' => '',
        'order' => '',
        'include' => '',
        'exclude' => '',
        'meta_key' => '',
        'meta_value' => '',
        'post_type' => 'post',
        'post_mime_type' => '',
        'post_parent' => '',
        'post_status' => 'publish',
        // and the most important line: 
        'filter' => true);
//    
    $found_n_first_posts = get_posts($args);
    //post na pozycji $pos

    $first_before_ad = $found_n_first_posts[$pos - 1];
    $last_date_before_ad = get_the_time('U', $first_before_ad);
//$first_before_ad['post_modified'];
    //   $ad_post = get_post($postid);



    $ad_post['ID'] = $postid;
    $ad_post['post_date'] = date('Y-m-d H:i:s', strval($last_date_before_ad) - 1); //niech reklama będzie ciut starsza od n-tego postu;
    $ad_post['filter'] = true; //dzięĸi temu nie zostaną wyczyszczone tagi HMTL
    //http://pp19dd.com/wordpress-plugin-include-custom-field/

    kses_remove_filters();
    wp_update_post($ad_post);
//Puppies!  Happily create/update posts without WordPress munging your HTML
    kses_init_filters();
}

?>
