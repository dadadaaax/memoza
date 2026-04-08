<?php

/* Plugin Name: wp-jsTree
 * Plugin URI: 
 * Description: JsTree is a free js tree widget
 * ******************************************************************
 *
 * Author: pck, core http://www.jstree.com/
 * Author URI: http://www.jstree.com/
 * Version: 1.0-rc3
 *

 * 
 *
 */

//wstawiaj skrypty tylko jesli to jesli to strona Settings - inaczej oszczedzaj transfer
add_action('wp_enqueue_scripts', 'check_page');

function check_page() {
    global $post;
    if ($post->ID == 2) {



        wp_register_script('jquery-jstree', plugins_url() . '/wp-jsTree/jquery.jstree.js', array('jquery'), '1.8.3', false);
        wp_enqueue_script('jquery-jstree');

        wp_register_script('jquery-cookie', plugins_url() . '/wp-jsTree/_lib/jquery.cookie.js', array('jquery'), '1.8.3', false);
        wp_enqueue_script('jquery-cookie');

//moj skrypt wstawiajacy drzewo do DIV
        wp_register_script('wp-categories-treee-setup', plugins_url() . '/wp-jsTree/wp-categories-treee-setup.js', array('jquery-jstree'), '', false);
        wp_enqueue_script('wp-categories-treee-setup');


//add_filter('the_content', 'suggest_category', 20);
//
//function suggest_category($content) {
//
//
//    // Add comments_template o
//    $content = sprintf(
//            '<h1 class=\"single_title\">Suggest website</h1>' . comments_template(), $content
//    );
//
//    // Returns the content.
//    return $content;
//}

add_filter('the_content', 'categories_tree', 20);

function categories_tree($content) {

  // $detected_lang  =substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    
    
    
           $args = array(
            'show_option_all' => '',
            'orderby' => 'name',
            'order' => 'ASC',
            'style' => 'list',
            'show_count' => 0,
            'hide_empty' => 1,
            'use_desc_for_title' => 1,
            'child_of' => 0,
            'feed' => '',
            'feed_type' => '',
            'feed_image' => '',
            'exclude' => '1,20,95,96,127', // nie bierzemy pod uwagę kategorii root, TEXT, TEXT, AD
            'number' => null,
            'echo' => 1,
            'depth' => 0,
            'current_category' => 0,
            'pad_counts' => 0,
            'taxonomy' => 'category',
            'walker' => null
        );

    $_memejest_cats = get_categories($args);

    $xml_info = new SimpleXMLElement("<?xml version=\"1.0\"?><div></div>");
    $xml_info->addAttribute("class", "demo");
    $xml_info->addAttribute("id", "LangSourceTree");
    //glowny ul    
    $x = $xml_info_subtree_head = $xml_info->addChild("ul");


    foreach ($_memejest_cats as $_meme_one_category) {
        if (($_meme_one_category->parent == 0)) {

            $el_listy = $x->AddChild("li");
            $el_listy->addAttribute("id", "phtml_" . $_meme_one_category->cat_ID);
            $a = $el_listy->addChild("a", $_meme_one_category->name);
            $a->addAttribute("href", "#");



            $z = $el_listy->addChild("ul");
            foreach ($_memejest_cats as $_meme_subtree_one_category) {

                if ($_meme_subtree_one_category->parent == $_meme_one_category->cat_ID) {

                    $u = $z->addChild("li");
                    $u->addAttribute("id", "phtml_" . $_meme_subtree_one_category->cat_ID);
                    $a = $u->addChild("a", $_meme_subtree_one_category->name);
                    $a->addAttribute("href", "#");
                }
            }
        }
    }



    // Returns the content.
    return  /*$detected_lang.*/
            $xml_info->asXML().  '<h1 class=\"single_title\">Suggest website</h1>' . 
           $content;
} //zrob to na Contact Form 


    }
}

?>






