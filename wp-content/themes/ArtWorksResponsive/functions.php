<?php
include('settings.php');
if (function_exists('add_theme_support')) {
    add_theme_support('menus');
}

function get_category_id($cat_name) {
    $term = get_term_by('name', $cat_name, 'category');
    return $term->term_id;
}

if (function_exists('add_theme_support')) { // Added in 2.9
    add_theme_support('post-thumbnails');
    // add_image_size('slide-image',852,282,true);
    //add_image_size('home-image', 250, 250, true);
//  add_image_size('blog-image',680,280,true);
}
if (function_exists('register_sidebar')) {
    register_sidebar(array(
        'name' => 'Sidebar',
        'before_widget' => '<div class="side_box">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="side_title">',
        'after_title' => '</h3>',
    ));

    register_sidebar(array(
        'name' => 'Footer Widget 1',
        'before_widget' => '<div class="footer_box">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="footer_title">',
        'after_title' => '</h3>',
    ));

    register_sidebar(array(
        'name' => 'Footer Widget 2',
        'before_widget' => '<div class="footer_box">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="footer_title">',
        'after_title' => '</h3>',
    ));

    register_sidebar(array(
        'name' => 'Footer Widget 3',
        'before_widget' => '<div class="footer_box">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="footer_title">',
        'after_title' => '</h3>',
    ));
}

function catch_that_image() {
    global $post, $posts;
    $first_img = '';
    ob_start();
    ob_end_clean();
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    $first_img = $matches [1] [0];
    if (empty($first_img)) { //Defines a default image
        $first_img = "/images/post_default.png";
    }
    return $first_img;
}

function ds_get_excerpt($num_chars) {
    $temp_str = substr(strip_tags(get_the_content()), 0, $num_chars);
    $temp_parts = explode(" ", $temp_str);
    $temp_parts[(count($temp_parts) - 1)] = '';

    if (strlen(strip_tags(get_the_content())) > 125)
        return implode(" ", $temp_parts) . '...';
    else
        return implode(" ", $temp_parts);
}

// **** PRODUCTION - Template1 Search START ****
class template1_search extends WP_Widget {

    function template1_search() {
        parent::WP_Widget(false, 'ArtWorks Search');
    }

    function widget($args, $instance) {
        $args['search_title'] = $instance['search_title'];
        t1_func_search($args);
    }

    function update($new_instance, $old_instance) {
        return $new_instance;
    }

    function form($instance) {
        $search_title = esc_attr($instance['search_title']);
        ?>
        <p><label for="<?php echo $this->get_field_id('search_title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('search_title'); ?>" name="<?php echo $this->get_field_name('search_title'); ?>" type="text" value="<?php echo $search_title; ?>" /></label></p>
        <?php
    }

}

function t1_func_search($args = array(), $displayComments = TRUE, $interval = '') {
    global $wpdb;
    echo $args['before_widget'];

    if ($args['search_title'] != '')
        echo $args['before_title'] . $args['search_title'] . $args['after_title'];
    ?>
    <div class="t1_search_cont">
        <form role="search" method="get" id="searchform" action="<?php echo home_url('/'); ?>">
            <input type="text" name="s" id="s" />
            <INPUT TYPE="image" SRC="<?php bloginfo('stylesheet_directory'); ?>/images/search-icon.jpg" class="t1_search_icon" BORDER="0" ALT="Submit Form">
        </form>
    </div><!--//t1_search_cont-->
    <?php
    echo $args['after_widget'];
    wp_reset_query();
}

register_widget('template1_search');
// **** PRODUCTION - Template1 Search END ****
// EX POST CUSTOM FIELD START
$prefix = 'ex_';
$meta_box = array(
    'id' => 'my-meta-box',
    'title' => 'Custom meta box',
    'page' => 'post',
    'context' => 'normal',
    'priority' => 'high',
    'fields' => array(
        /*        array(
          'name' => 'Text box',
          'desc' => 'Enter something here',
          'id' => $prefix . 'text',
          'type' => 'text',
          'std' => 'Default value 1'
          ),
          array(
          'name' => 'Textarea',
          'desc' => 'Enter big text here',
          'id' => $prefix . 'textarea',
          'type' => 'textarea',
          'std' => 'Default value 2'
          ),
          array(
          'name' => 'Select box',
          'id' => $prefix . 'select',
          'type' => 'select',
          'options' => array('Option 1', 'Option 2', 'Option 3')
          ),
          array(
          'name' => 'Radio',
          'id' => $prefix . 'radio',
          'type' => 'radio',
          'options' => array(
          array('name' => 'Name 1', 'value' => 'Value 1'),
          array('name' => 'Name 2', 'value' => 'Value 2')
          )
          ), */
        array(
            'name' => 'Box',
            'id' => $prefix . 'show_in_slideshow',
            'type' => 'checkbox'
        )
    )
);
add_action('admin_menu', 'mytheme_add_box');

// Add meta box
function mytheme_add_box() {
    global $meta_box;
    add_meta_box($meta_box['id'], $meta_box['title'], 'mytheme_show_box', $meta_box['page'], $meta_box['context'], $meta_box['priority']);
}

// Callback function to show fields in meta box
function mytheme_show_box() {
    global $meta_box, $post;
    // Use nonce for verification
    echo '<input type="hidden" name="mytheme_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
    echo '<table class="form-table">';
    foreach ($meta_box['fields'] as $field) {
        // get current post meta data
        $meta = get_post_meta($post->ID, $field['id'], true);
        echo '<tr>',
        '<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
        '<td>';
        switch ($field['type']) {
            case 'text':
                echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />', '<br />', $field['desc'];
                break;
            case 'textarea':
                echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>', '<br />', $field['desc'];
                break;
            case 'select':
                echo '<select name="', $field['id'], '" id="', $field['id'], '">';
                foreach ($field['options'] as $option) {
                    echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                }
                echo '</select>';
                break;
            case 'radio':
                foreach ($field['options'] as $option) {
                    echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
                }
                break;
            case 'checkbox':
                echo '<input type="checkbox" value="Yes" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
                break;
        }
        echo '<td>',
        '</tr>';
    }
    echo '</table>';
}

add_action('save_post', 'mytheme_save_data');

// Save data from meta box
function mytheme_save_data($post_id) {
    global $meta_box;
    // verify nonce
    if (isset($_POST['mytheme_meta_box_nonce']) &&
            !wp_verify_nonce($_POST['mytheme_meta_box_nonce'], basename(__FILE__))
    ) {
        return $post_id;
    }
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    // check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    foreach ($meta_box['fields'] as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = $_POST[$field['id']];
        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    }
}

add_action('send_headers', 'add_header_mobile_ajax');

function add_header_mobile_ajax() {
    header('Access-Control-Allow-Origin: kupamemow.pl');
}

function my_init_method() {
    // biblioteka obslugująca hashe w URL
    if (!is_admin()) {
        $url = get_stylesheet_directory_uri() . '/js/';
        wp_register_script(
                'hash-change', "{$url}jquery.ba-hashchange.min.js", array('jquery'), //requires jQuery
                NULL, //Version Nr
                true //doent loads in footer
        );
        wp_register_script(// SuperFish
                'ajax-navigation', "{$url}ajax-navigation.js", array('hash-change'), //requires jQuery
                NULL, //Version Nr
                true //doent loads in footer
        );
    }
}

add_action('wp_enqueue_scripts', 'my_init_method');



define('WP_POST_REVISIONS', false);

function disable_autosave() {
    wp_deregister_script('autosave');
}

add_action('wp_print_scripts', 'disable_autosave');

// function to sort an array by the key of his sub-array. 
function sksort(&$array, $subkey = "id", $sort_ascending = false) {

    if (count($array))
        $temp_array[key($array)] = array_shift($array);

    foreach ($array as $key => $val) {
        $offset = 0;
        $found = false;
        foreach ($temp_array as $tmp_key => $tmp_val) {
            if (!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey])) {
                $temp_array = array_merge((array) array_slice($temp_array, 0, $offset), array($key => $val), array_slice($temp_array, $offset)
                );
                $found = true;
            }
            $offset++;
        }
        if (!$found)
            $temp_array = array_merge($temp_array, array($key => $val));
    }

    if ($sort_ascending)
        $array = array_reverse($temp_array);
    else
        $array = $temp_array;
}

function mmmr($array, $output = 'mean') {
    if (!is_array($array)) {
        return FALSE;
    } else {
        switch ($output) {
            case 'mean':
                $count = count($array);
                $sum = array_sum($array);
                $total = $sum / $count;
                break;
            case 'median':
                rsort($array);
                $middle = round(count($array) / 2);
                $total = $array[$middle - 1];
                break;
            case 'mode':
                $v = array_count_values($array);
                arsort($v);
                foreach ($v as $k => $v) {
                    $total = $k;
                    break;
                }
                break;
            case 'range':
                sort($array);
                $sml = $array[0];
                rsort($array);
                $lrg = $array[0];
                $total = $lrg - $sml;
                break;
        }
        return $total;
    }
}



 function file_get_contents_in_vpn($u) {
        $auth = base64_encode('Deckard2:1q2w3e4r');

        $aContext = array(
            'http' => array(
                'proxy' => 'vpn.vpnreactor.net',
                'request_fulluri' => true,
                'header' => "Proxy-Authorization: Basic $auth",
            ),
        );
        $cxContext = stream_context_create($aContext);

        return file_get_contents($u, False, $cxContext);
    }



// add featured image thumbnails to WordPress admin columns
// add_theme_support( 'post-thumbnails' ); // theme should support
function themename_add_post_thumbnail_column($cols) { // add the thumb column
    // output feature thumb in the end
    //$cols['themename_post_thumb'] = __( 'Featured image', 'themename' );
    //return $cols;
    // output feature thumb in a different column position
    $cols_start = array_slice($cols, 0, 2, true);
    $cols_end = array_slice($cols, 2, null, true);
    $custom_cols = array_merge(
            $cols_start, array('themename_post_thumb' => __('Featured image', 'themename')), $cols_end
    );
    return $custom_cols;
}

add_filter('manage_posts_columns', 'themename_add_post_thumbnail_column', 5); // add the thumb column to posts
add_filter('manage_pages_columns', 'themename_add_post_thumbnail_column', 5); // add the thumb column to pages

function themename_display_post_thumbnail_column($col, $id) { // output featured image thumbnail
    switch ($col) {
        case 'themename_post_thumb':
            if (function_exists('the_post_thumbnail')) {
                echo the_post_thumbnail('thumbnail');
            } else {
                echo __('Not supported in theme', 'themename');
            }
            break;
    }
}

add_action('manage_posts_custom_column', 'themename_display_post_thumbnail_column', 5, 2); // add the thumb to posts
add_action('manage_pages_custom_column', 'themename_display_post_thumbnail_column', 5, 2); // add the thumb to pages
//function the_slug() {
//    $post_data = get_post($post->ID, ARRAY_A);
//    $slug = $post_data['post_name'];
//    return $slug; 
//}
// Add Shortcode

function custom_shortcode_top_memes($atts, $content = null) {

    // Attributes
    extract(shortcode_atts(
                    array(
        'number' => '9',
                    ), $atts)
    );


    // Code 
//        
//                  $args = array(
//                'meta_query' => array(
//                    array(
//                        'key' => 'like_count',
//                        'compare' => '>=',
//                        'value' => 4000,
//                        'type' => 'numeric'
//                    )),
//                'posts_per_page' => 4,
//);
//                  




    $args = array(
        'meta_key' => 'like_count',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'posts_per_page' => 6 //JAK TO UZMIENNIC ??????????????????/
    );

    // Cdodatkowy filtr czasowy
    function filter_where($where = '') {
        // posts in the last 30 days
        $where .= " AND post_date > '" . date('Y-m-d', strtotime('-24 hours')) . "'";
        return $where;
    }

    add_filter('posts_where', 'filter_where');




    ob_start(); //otwieramy bufor    
  
    $the_query = new WP_Query();
    $the_query->query($args);

    if ($the_query->have_posts()) : while ($the_query->have_posts()) :
            $the_query->the_post();
    
    
    
            ?>



                    <div class="home_post_upper_text_box">


                        <div class="meme_title"><?php echo ucfirst(get_post_meta( $the_query->post->ID, 'title', true)); ?>  </div>



                    </div><!--//home_post_text-->


                    <div class="thumb_img">
                        <?php the_post_thumbnail('home-image'); ?>
                    </div>


                    <div class="full_img">
                        <?php the_content(); ?>
                    </div>


                    <div class="meme_source">  <a href="<?php echo get_post_meta( $the_query->post->ID, 'MEME_ID', true); ?> ">   <?php echo strtolower(get_post_meta( $the_query->post->ID, 'DomainName', true)) ?>  </a></div>

                    <div class="home_post_bottom_box">
                        <div> 
                            <a href="<?php echo  $the_query->post->post_name; ?>">   <img class="share_button" src="wp-content/themes/ArtWorksResponsive/images/share_button.png" alt="share" > </a>
                        </div> 
                    </div>





            <?php
        endwhile;
    endif;
    remove_filter('posts_where', 'filter_where');

    wp_reset_query();
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

add_shortcode('top_memes', 'custom_shortcode_top_memes');
?>