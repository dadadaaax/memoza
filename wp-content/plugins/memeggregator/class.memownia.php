<?php

/**
 * Description of class
 *
 * @author danny
 */
class memownia {

//put your code here

    var $url;
    var $name;
    var $like_count_mean;

    function memownia() {
        
    }

    function get_like_count_mean() {

        //pokaż wszystkie posty z wybranych kategorii, które są z ostatnich  24h
        $args = array('cat' => get_cat_ID($this->name), 'showposts'=> -1);
        $tmp = query_posts($args);


        $post_count = 0;
        $likes = array();

        if (have_posts()): while (have_posts()) : the_post();
                $postid = get_the_id();

                $likes[] = get_post_meta($postid, 'like_count', true);
  
//echo $postid.",";
                $post_count++;
            endwhile;
        endif;
 
      //  $this->like_count_mean = mmmr($likes, "mean");
        return $this->like_count_mean;
    }
    
    /**
 * add_terms_meta() - adds metadata for terms
 *
 *
 * @param int $terms_id terms (category/tag...) ID
 * @param string $key The meta key to add
 * @param mixed $value The meta value to add
 * @param bool $unique whether to check for a value with the same key
 * @return bool
 */
function save_like_count_mean()
        
{
    
   // add_terms_meta( get_cat_ID($this->name), "like_count_mean", $this->like_count_mean) || 
             update_terms_meta( get_cat_ID($this->name), "like_count_mean", $this->like_count_mean) ;
     
}



function destroy() {
    $this->url = null;
    $this->name = null;
    $this->like_count_mean = null;
}
}
?>
