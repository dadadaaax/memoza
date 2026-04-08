<?php
/*
  Plugin Name: Memeggregator
  Plugin URI:
  Description: downloads and saves memes
  Version: The Plugin's Version Number, e.g.: 1.0
  Author: pck
  Author URI: pck
  License: commercial
 */

function wh_log($log_msg) {
    $log_filename = "log";
    if (!file_exists($log_filename)) {
        // create directory/folder uploads.
        mkdir($log_filename, 0777, true);
    }
    $log_file_data = $log_filename . '/log_' . date('d-M-Y') . '.log';
    // if you don't add `FILE_APPEND`, the file will be erased each time you add a log
    file_put_contents($log_file_data, $log_msg . "\n", FILE_APPEND);
}

function log_variable($log_var, $title) {


    $log_filename = "log";
    if (!file_exists($log_filename)) {
        // create directory/folder uploads.
        mkdir($log_filename, 0777, true);
    }
    $log_file_data = $log_filename . '/log_' . date('d-M-Y') . '.log';
    // if you don't add `FILE_APPEND`, the file will be erased each time you add a log


    file_put_contents($log_file_data, date("H:i:s") . '-------------VARIABLE: ' . $title . ':' . $log_var . "\n", FILE_APPEND);
}

include_once(dirname(__FILE__) . "/simplehtmldom/simple_html_dom.php");
include_once(dirname(__FILE__) . "/class.aggregator.php");
include_once(dirname(__FILE__) . "/class.meme.php");
include_once(dirname(__FILE__) . "/class.ads.php");
include_once(dirname(__FILE__) . "/class.memownia.php");

//funkcja zwraca color categorii wstawiany przez plugin Colorful Categories
function get_post_cat_color($post_ID) {
    $cat_ID = get_the_category($post_ID)[0]->cat_ID;
    if (isset($cat_ID)) {
        $term_meta = get_term_meta($cat_ID, "cc_color", true);
        //TODO
        //   log_variable($term_meta, "$term_meta");
        return $term_meta;
    } else {
        return null;
    }
}

function watermark_cat($post_ID, $post, $update) {

    wh_log("                                            ");
    wh_log(date("H:i:s") . "Try WATERMARK on post ----------------> " . $post_ID);

    log_variable(get_post_type($post_ID), "post_type");


//die ();



    if (get_post_type($post_ID) == 'post') {

        $label_domain_name = get_the_category($post_ID)[0]->name;
        log_variable($label_domain_name, "label_domain_name");


        $category_color = get_post_cat_color($post_ID);

        //TODO - to nie może być tak adhocowo zrobione: wklajeanie watermarku trzeba uzależnic od innego parametru niż nazwa categorii 0
        
           $attachment_id = $post_ID + 1; // z braku lepszej metody obliczania attachment_id

  $is_watermarked = boolval(intval(get_post_meta(  $attachment_id,"iw-is-watermarked")[0]));
                    log_variable($is_watermarked, "iw-is-watermarked");

        if ((isset($category_color)) ) {



         


            $data = wp_get_attachment_metadata($attachment_id, false);

            $image_watermark = Image_Watermark();
            //przekaż do obiektu Watermark skąd  
            $image_watermark->action_context = "at_click_save_post";
            $image_watermark->label_domain_name = $label_domain_name;
            $image_watermark->categorycolor = $category_color;

            log_variable($image_watermark->action_context, "action_context");
            log_variable($label_domain_name, "label_domain_name");
            log_variable($image_watermark->categorycolor, "cat_color");

            $image_watermark->apply_watermark($data, $attachment_id, 'manual');
            unset($image_watermark);
        }
    }

    wh_log(date("H:i:s") . "WATERMARK on attachment_id " . $attachment_id . "category: " . $label_domain_name . " watermarked:" . $image_watermark->is_watermarked_metakey);
}

//20 - czy to trzeba wywołać, kiedy metatagi sa już zapisane (w metatagu jest color kategorii)
add_action('save_post', 'watermark_cat', 20, 3);











add_action('wp', 'memegregator_process');

function memegregator_process() {

    
    
    
    
    
    
    

    global $wp_query;
    wh_log(date("Y-m-d H:i:s") . " MEMEGRAGATOR START **********************************************");
    //wh_log(date("Y-m-d H:i:s") . " * " . implode("|", $wp_query->query_vars));
// jesli jest ciasteczko z kategoriami, wyświetl tylko te kategorie
    if (isset($_COOKIE['jstree_select'])) {
        //przeszukujemy ciasteczko i zmieniamy listę wezłow drzewa na listę kategorii
        $cat_list = str_replace("#phtml_", "", $_COOKIE['jstree_select']);
    } else {
        $cat_list = '67';
    }


    
    
    
    
    
    
//zaladuj memy, jesli parametr strony wystepuje w URLu
    if (isset($wp_query->query_vars['memectrl'])) {

        if (
                $wp_query->query_vars['memectrl'] == "xml_cats" ||
                $wp_query->query_vars['memectrl'] == "xml_job_request" ||
                $wp_query->query_vars['memectrl'] == "xml_pre_upload" ||
                $wp_query->query_vars['memectrl'] == "xml_real_upload"
        ) {
            $args = array(
                'show_option_all' => '',
                'orderby' => 'name',
                'order' => 'ASC',
                'style' => 'list',
                'show_count' => 0,
                'hide_empty' => 0,
                'use_desc_for_title' => 1,
                'child_of' => 0,
                'feed' => '',
                'feed_type' => '',
                'feed_image' => '',
                'exclude' => '',
                'exclude_tree' => '',
                'include' => '',
                'hierarchical' => 1,
                'title_li' => __(''),
                'show_option_none' => __('No categories'),
                'number' => null,
                'echo' => 1,
                'depth' => 0,
                'current_category' => 0,
                'pad_counts' => 0,
                'taxonomy' => 'category',
                'walker' => null
            );

            $_memejest_cats = get_categories($args);


            if (0 && isset($_GET["format"]) && $_GET["format"] == "json") {
                echo json_encode($_dummy_data);
            } elseif (1) {

                if ($wp_query->query_vars['memectrl'] == "xml_cats") {
                    //header("Content-type: text/plain;\r\n");
                    $xml_info = new SimpleXMLElement("<?xml version=\"1.0\"?><memejet_info></memejet_info>");
                    foreach ($_memejest_cats as $_meme_one_category) {
                        if ($_meme_one_category->parent == 0) {
                            $xml_info_subtree_head = $xml_info->addChild("language");
                            $xml_info_subtree_head->addChild("langId", $_meme_one_category->cat_ID);
                            $xml_info_subtree_head->addChild("langName", $_meme_one_category->name);
                            $xml_info_subtree_sites = $xml_info_subtree_head->addChild("sites");
                            foreach ($_memejest_cats as $_meme_subtree_one_category) {
                                //var_dump($_meme_one_category->name . "::" .$_meme_subtree_one_category->name);
                                //var_dump($_meme_subtree_one_category->parent."::".$_meme_one_category->cat_ID);
                                if ($_meme_subtree_one_category->parent == $_meme_one_category->cat_ID) {
                                    $xml_info_subtree_sites->addChild("site", $_meme_subtree_one_category->name);
                                    //echo "\n".$_meme_subtree_one_category->name . "<br>\n"; 
                                }
                            }
                        }
                    }
                    echo $xml_info->asXML();
                } elseif ($wp_query->query_vars['memectrl'] == "xml_job_request") {
                    $_found_refresh_category = FALSE;
                    $_found_refresh_category_obj = NULL;

                    $_random_task_chance = rand(0, 10);
                    $_random_minimum_chance = 0; // 0 - 100%, 10 - 0%
                    if ($_random_task_chance > $_random_minimum_chance) {
                        $found_tries = 0;

                        do {
                            $found_tries++;

                            $_cats_array_rand_key = array_rand($_memejest_cats);
                            $_meme_subtree_one_category = $_memejest_cats[$_cats_array_rand_key];
                            if ($_meme_subtree_one_category->parent != 0) {

                                // @TODO - dodać spradzanie czy kategoria 
                                // nie była juz dawno odświzeżana
                                $_found_refresh_category = TRUE;
                                $_found_refresh_category_obj = $_meme_subtree_one_category;
                            }
                        } while ($_found_refresh_category == FALSE && $found_tries < 100);
                    }

                    $xml_info = new SimpleXMLElement("<?xml version=\"1.0\"?><memejet_info></memejet_info>");
                    if ($_found_refresh_category) {
                        $xml_info_subtree_head = $xml_info->addChild("randomCategory");
                        $xml_info_subtree_head->addChild("catergoryId", $_found_refresh_category_obj->cat_ID);
                        $xml_info_subtree_head->addChild("catergoryName", $_found_refresh_category_obj->name);
                    } else {
                        $xml_info_subtree_head = $xml_info->addChild("notice");
                        $xml_info_subtree_head->addChild("errorId", "0x001");
                        $xml_info_subtree_head->addChild("errorSting", "NO_CATEGORY");
                    }
                    echo $xml_info->asXML();
                } elseif ($wp_query->query_vars['memectrl'] == "xml_pre_upload" && isset($wp_query->query_vars['memeparam_url'])
                ) {

                    $args = array(
                        'meta_query' => array(
                            array(
                                'key' => 'MEME_ID',
                                'value' => $wp_query->query_vars['memeparam_url'],
                                'compare' => '='
                            )
                        )
                    );

                    $xml_info = new SimpleXMLElement("<?xml version=\"1.0\"?><memejet_info></memejet_info>");
                    if (have_posts()) {
                        $xml_info_subtree_head = $xml_info->addChild("notice");
                        $xml_info_subtree_head->addChild("errorId", "0x002");
                        $xml_info_subtree_head->addChild("errorSting", "POST_EXISTS"
                                . get_the_ID() . $wp_query->query_vars['memeparam_url']
                        );
                    } else {
                        $xml_info_subtree_head = $xml_info->addChild("success");
                        $xml_info_subtree_head->addChild("successMessage", "ready for upload");
                    }
                    echo $xml_info->asXML();
                } elseif ($wp_query->query_vars['memectrl'] == "xml_real_upload" && isset($wp_query->query_vars['memeparam_url']) && isset($wp_query->query_vars['memeparam_catid']) && isset($wp_query->query_vars['memeparam_title'])
                ) {

                    $_memeparam_url = $wp_query->query_vars['memeparam_url'];
                    $_memeparam_catid = $wp_query->query_vars['memeparam_catid'];
                    $_memeparam_title = $wp_query->query_vars['memeparam_title'];

                    $args = array(
                        'cat' => intval($_memeparam_catid),
                        'meta_query' => array(
                            array(
                                'key' => 'MEME_ID',
                                'value' => $_memeparam_url,
                            // 'compare' => 'LIKE'
                            )
                        )
                    );

                    $found_harversted = query_posts($args);

                    $xml_info = new SimpleXMLElement("<?xml version=\"1.0\"?><memejet_info></memejet_info>");
                    if (have_posts()) {
                        $xml_info_subtree_head = $xml_info->addChild("notice");
                        $xml_info_subtree_head->addChild("errorId", "0x003");
                        $xml_info_subtree_head->addChild("errorSting", "POST_EXISTS");
                    } else {
                        // tutaj następuje dodawanie do bazy
                        // na razie do typu draft
                        $args = array(
                            'post_status' => 'draft',
                            'cat' => intval($_memeparam_catid),
                            'meta_query' => array(
                                array(
                                    'key' => 'MEME_ID',
                                    'value' => $_memeparam_url,
                                // 'compare' => 'LIKE'
                                )
                            )
                        );

                        $found_harversted = query_posts($args);
                        if (have_posts()) {

                            // zwiększenie liczby dodań

                            $c = get_post_meta(get_the_ID(), 'DraftCounter', true);

                            $m = new meme();
                            $m->id = get_the_ID();
                            $m->setDraftCounter(++$c);
                        } else {
                            // dodaję pierwszy raz
                            // wpierw attachement
                            $m = new meme();
                            $attachment_id = $m->insertAttachmentFromPostdata('file_upload');

                            // później meme
                            $m->localFileData = array(
                                "name" => $_memeparam_title,
                                "url" => $_memeparam_url,
                                "post_category" => array($_memeparam_catid),
                                "attachment_id" => $attachment_id
                            );

                            $m->domainurl = $_memeparam_url;
                            $m->original_URL = $_memeparam_url;
                            if (!defined('DOING_AUTOSAVE'))
                                define('DOING_AUTOSAVE', 1);
                            $m->saveToPost('draft');
                            $m->setDraftCounter(1);
                        }
                        $xml_info_subtree_head = $xml_info->addChild("success");
                        $xml_info_subtree_head->addChild("postId", $m->id);
                    }
                    echo $xml_info->asXML();
                } // if = przejscie przez kategorie zadań
            }// if crtl param
            //wp_list_categories_as_xml($args);
            //  var_dump ( get_categories());
        }


        if (
                isset($wp_query->query_vars['memectrl']) &&
                ($wp_query->query_vars['memectrl']) == "load_memes"
        ) {

            wh_log(date("Y-m-d H:i:s") . " RIP start  ");

//ripuj jedną site (z urla) lub wszystkie 
            //delete_all_unattachedfiles ();
            if (isset($wp_query->query_vars['site']) && ($wp_query->query_vars['site']) <> "") {

//                watermark_cat(2627151, $post, $update );
//                die();
                rip_all_memes($wp_query->query_vars['site']);
            } else {
                rip_all_memes("");
            }

            //odśwież info o lajkach
            update_todays_posts_like_counts($cat_list);
            //updatuj średnie lików dla kategorii;
            update_mean_for_all_categories();
        }


        if ($wp_query->query_vars['memectrl'] == "todays_like_count") {//znajdź lajki postów z ostantich 24h, z kategorii zaznaczonych w ciastku
//        $last_id= 47982;   
//         
//        $c = curl_init();
//            curl_setopt($c, CURLOPT_URL, 'http://kupamemow.pl/wp-admin/admin-ajax.php');
//            curl_setopt($c, CURLOPT_POST, 1);//przesylamy metodą post
//            curl_setopt($c, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest"));
//            curl_setopt($c, CURLOPT_POSTFIELDS, 'action=rePostToFB&id='.$last_id.'&nid=0&_wpnonce='.wp_create_nonce()); //dane do wyslania
//            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
//            $page = curl_exec($c);
//            curl_close($c);
//echo 'Wynik: <br>'.$page;
            $arr_likes = update_todays_posts_like_counts($hours = '-24 hours', $cat_list);
            //posortuj po ilości lików
            if (count($arr_likes)) {
                sksort($arr_likes, "like_count");

                $last_id = null;
                foreach ($arr_likes as $row) {
                    $count = $count + 1;
                    echo $row["like_count"] . "x like,  " . $row["social_url"] . ",  " . $row["date"] . "<br>";

                    // dla pierwszego - wyciagam ID
                    if ($last_id == null) {
                        $last_id = $row["id"];
                        break;
                    }
                }




                echo $i . "memes for last 24h";
            } else
                echo "no post for last 24h";
        }



        if ($wp_query->query_vars['memectrl'] == "get_mean_likes") {

// update_terms_meta( get_cat_ID("demotywatory.pl"), "like_count_mean", "xxx") ;

            update_mean_for_all_categories();
        }




        exit();
    }
}

$lang_chosen = "Polish";

//rejestracja parametru, ktorym bedzie sie ladowac 
add_filter('query_vars', 'my_queryvars');

function my_queryvars($qvars) {
    $qvars[] = 'memectrl';
    $qvars[] = 'memeparam_url';
    $qvars[] = 'memeparam_catid';
    $qvars[] = 'memeparam_title';
    $qvars[] = 'showbest';
    $qvars[] = 'site';

    return $qvars;
}

$args = array(
    'type' => 'post',
    'child_of' => '',
    'parent' => get_term_by('name', $lang_chosen, 'category')->term_id,
    //'orderby'                  => 'name',
    'order' => 'ASC',
    'hide_empty' => 0,
    'hierarchical' => 1,
    'exclude' => '',
    'include' => '',
    'number' => '',
    'taxonomy' => 'category',
    'pad_counts' => false
);
$lista_memowni = array();
$lista_wszystkich_memowni_dla_jezyka = get_categories($args);
foreach ($lista_wszystkich_memowni_dla_jezyka as &$memownia) {
    $lista_memowni[] = $memownia->name;
}

// var_dump($lista_memowni);
function mail_report($adr, $html) {



    $mailtext = "<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
    </head>
    <body>
       " . $html . "
    </body>
</html>";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
    $headers .= $mail_From . "\r\n";
    mail($adr, "Memejet report", $mailtext, $headers);

// wp_mail($adr, "Memejet report", $mailtext);
}

function delete_all_unattachedfiles() {

    $excluded_cats_list = "-" . get_category_id('AD') . "," . "-" . get_category_id('TECH') . "," . "-" . get_category_id('TEXT') . ",";

    $unattachedmediaargs = array(
        'post_type' => 'attachment',
        'category' => $excluded_cats_list,
        'numberposts' => -1,
        'post_status' => null,
        'post_parent' => 0
    );
    $unattachedmedia = get_posts($unattachedmediaargs);
    echo 'deleting ' . sizeof($unattachedmedia) . " alone attachments, wait...";
    flush();
    ob_flush();
    if ($unattachedmedia) {
        foreach ($unattachedmedia as &$unattached) {
            wp_delete_attachment($unattached->ID, true);
        }
    }
}

//usun n najstarszych postów, które nie są echnicznyi ani reklamami (with attachemnts)
function delete_n_oldest_posts($number) {

    $excluded_cats_list = "-" . get_category_id('AD') . "," . "-" . get_category_id('TECH') . "," . "-" . get_category_id('TEXT') . ",";

    $args = array(
        'posts_per_page' => $number,
        'offset' => 0,
        'category' => $excluded_cats_list,
        'orderby' => 'post_date',
        'order' => 'ASC',
        'include' => '',
        'exclude' => '',
        'meta_key' => '',
        'meta_value' => '',
        'post_type' => 'post',
        'post_mime_type' => '',
        'post_parent' => '',
        'post_status' => 'publish',
        'suppress_filters' => true);

    $posts = get_posts($args);

    echo 'deleting ' . sizeof($posts) . " posts with attachments, wait...";


    if ($posts) {
        foreach ($posts as &$post) {

            //najpierw wywal attachment (dziecko postu)
            $post_attachments = get_children($post->ID);
            if ($post_attachments) {
                foreach ($post_attachments as &$attachment) {
                    wp_delete_attachment($attachment->ID, true);
                }
                unset($attachment);
            }


            wp_delete_post($post->ID, true);
        }
        unset($post);
    }
}

//function is_old_post($days = 5) {
//    $days = (int) $days;
//    $offset = $days*60*60*24;
//    if ( get_post_time() < date('U') - $offset )
//         return true; 
//    return false;
// }
// 
// function mg_remove_old_entries() {
//  $posts = get_posts( [
//    'numberposts' => -1,
//    'post_type' => 'vfb_entry',
//    'date_query' => [
//      // get all the posts from the database which are older than 60 days
//      'before' => date( "Y-m-d H:i:s", strtotime( '-60 days' ) ),
//    ],
//  ]);
//
//  if( !empty($posts) ) {
//    foreach( $posts as $post ) {
//      wp_delete_post( $post->ID ); //remove the post from the database
//    }
//  }
//}

 


function delete_older_than_posts($days) {

    //0 - id kategorie "bez kategorii" - posty bez kategorii nie będą usuwane, dodają je redaktorzy
    $excluded_cats_list = "-0,"   . "-" . get_category_id('AD') . ",". "-" . get_category_id('memoza') . "," . "-" . get_category_id('TECH') . "," 
. "-" . get_category_id('TEXT') . ",";
 
    $args = array(
        'numberposts' => -1,
        'date_query' => [
            // get all the posts from the database which are older than 60 days
            'before' => $days
        ],
        'offset' => 0,
        'category' => $excluded_cats_list,
        'orderby' => 'post_date',
        'include' => '',
        'exclude' => '',
        'meta_key' => '',
        'meta_value' => '',
        'post_type' => 'post',
        'post_mime_type' => '',
        'post_parent' => '',
        'post_status' => 'publish',
        'suppress_filters' => true);

    $posts = get_posts($args);

    wh_log('found ' . sizeof($posts) . " OLD RIPPED posts with attachments... before " . $args['date_query']['before']);



    if ($posts) {
        foreach ($posts as &$post) {

            $cat_id = get_the_category($post->ID)[0]->cat_ID;

            $child = get_category($cat_id);

//from your child category, grab parent ID
            $parent = $child->parent;

//load object for parent category
            $parent_name = get_category($parent);

//grab a category name
            $parent_name = $parent_name->name;




            wh_log($post->ID . "post,  cat: " . $cat_id . " parent cat:" . $parent_name);



            if ($parent_name == 'Polish') {
                //najpierw wywal attachment (dziecko postu)
                $post_attachments = get_children($post->ID);
                if ($post_attachments) {
                    foreach ($post_attachments as &$attachment) {
                        wp_delete_attachment($attachment->ID, true);
                    }
                    unset($attachment);
                }


                wp_delete_post($post->ID, true);
            }


            $whatever = get_the_category($post->ID)[0]->cat_ID;
            $whatever = 1;
        }
    }
    unset($post);
}

function get_posts_count_from_last_24h($post_type = 'post') {
    global $wpdb;

    $numposts = $wpdb->get_var(
            $wpdb->prepare(
                    "SELECT COUNT(ID) " .
                    "FROM {$wpdb->posts} " .
                    "WHERE " .
                    "post_status='publish' " .
                    "AND post_type= %s " .
                    "AND post_date> %s", $post_type, date('Y-m-d H:i:s', strtotime('-24 hours'))
            )
    );
    return $numposts;
}
 
//returns array of todays likes in category
function update_todays_posts_like_counts($cat_list) {

    $posts_count = get_posts_count_from_last_24h();
    if ($posts_count == 0)
        return null;

    global $cat_list;
    $arr = array();


    //TODO: uogolnic to na wszystkie jezyki
    if ($cat_list == '67') {
        $cat_list = '';
    }

    // $excluded_cats_list = "-" . get_category_id('AD') . "," . "-" . get_category_id('TECH') . "," . "-" . get_category_id('TEXT') . ",";
    //pokaż wszystkie posty z wybranych kategorii, które są z ostatnich  24h
    $args = array('cat' => $cat_list, 'showposts' => $posts_count);
    $tmp = query_posts($args);

    if (have_posts()): while (have_posts()) : the_post();
            $postid = get_the_id();

            $tmp_meme = new meme();
            $tmp_meme->id = $postid;
            $tmp_meme->URL_to_single_post = get_post_meta($postid, 'MEME_ID', true);

            $likes = $tmp_meme->update_like_count();
            $tmp_meme->update_history_like_count();


            $category = get_the_category($tmp_meme->id);
            $likes_mean = get_terms_meta($category[0]->cat_ID, 'like_count_mean', true);

            //jeśłi post ma więcej lików niź 2 (1 bierze się już z wrzucenia na fb) i więcej niż średnia w kategorii  i jeśli pole danej 'snapFB' jest puste - wrzuc na profil FB
//            if (($likes>10) &&
//                ($likes>(10*$likes_mean)) &&
//                (get_post_meta($postid, 'snapFB', true)=="")  )
//            {  
//               if ($likes==0) {  mail_report("contact@kupamemow.pl", "likes=0, ". $tmp_meme->URL_to_single_post );}
//               
//            $tmp_meme ->publishToFB();
//              
//            } 

            $arr[] = array('id' => $postid, 'like_count' => $likes, 'social_url' => $tmp_meme->URL_to_single_post, 'date' => date("F j, Y  g:i a"));

            $tmp_meme->destroy();


        endwhile;
    endif;

    return $arr;
}

function update_mean_for_all_categories() {
    $_memejest_cats = get_categories($args);


    foreach ($_memejest_cats as $value) {
//  $excluded_cats_list = "-" . get_category_id('AD') . "," . "-" . get_category_id('TECH') . "," . "-" . get_category_id('TEXT') . ",";

        $memownia = new memownia();
        $memownia->name = ($value->name);

        echo $memownia->name . ", " . $memownia->get_like_count_mean() . " mean x LIKED <br>";
        $memownia->save_like_count_mean();
        $memownia->destroy();
    }
}

function rip_all_memes($single_site) {


    
    //zacznij od skasowania starych postów
    delete_older_than_posts('-2 days');

//    move_post_to_nth_pos(93061, 2);  //(google ADS)
    // delete_n_oldest_posts(15000);
    // delete_all_unattachedfiles();
    // exit;
    //debug file
    $file = plugin_dir_path(__FILE__) . "debug_memejet.txt";
    file_put_contents($file, "\n *****************START*******************" . date("Y-m-d | h:i:s") . "\n", FILE_APPEND);
    //global $lista_memowni; //= array('wiocha.pl');
    global $lista_memowni;


    //jeśli ripujemy tylko jedno źródło
    if ($single_site <> "") {

        $lista_memowni = array($single_site);
    }


    //  $lista_memowni = array ("wyciagamykarteczki.pl","bezuzyteczna.pl");
    shuffle($lista_memowni);  //pomieszajmy listę memów, żeby nadać jescze wiekszej przypadkowosci sortowaniu
    $agregatory = array();
    $tmp_raport = "memestore, found, methods_that_found_sth: <br>---------------------------<br>";
    //  mail_report("dadadaaa@gmail.com", $tmp_raport);

    foreach ($lista_memowni as &$memownia) {

        $memesfound = array();
        $memownia = trim($memownia);
        //  $memownia = "mistrzowie.org";
        $poziom_rekurencji = 2;


        file_put_contents($file, "MEMORY USAGE: " . memory_get_usage() . " \n", FILE_APPEND);
        $agr = new agregator('http://' . $memownia, 'http://' . $magazyn, $poziom_rekurencji);
        //zapoiszmy dane 
        file_put_contents($file, " \n" . $agr->url . " \n" . "<img> outer-domain hrefs go mostly to domain: " . $agr->guessed_domain_for_images . " \n", FILE_APPEND);
        $agregatory[] = $agr;
        foreach ($agr->memes as &$tmp_meme) {
            $memesfound[] = $tmp_meme;
        }
        unset($tmp_meme);

        //zanim zniszczymy obiekt zapamiętajmy wartości

        if (sizeof($memesfound)) {
            //pomieszajmy memy
            //  shuffle($memesfound);
            foreach ($memesfound as &$m) {
                //szukamy postow z MEME_ID, których jeszcze nie było
                //jesli znajdziemy, to znaczy ze juz tego mema mamy i go nie ładujemy 
                //   $found_harversted = query_posts('showposts=-1&meta_key=MEME_ID&meta_value=' . $m->URL_to_single_post);

                $found_harversted = get_posts(array(
                    'meta_key' => 'MEME_ID',
                    'meta_value' => $m->URL_to_single_post
                ));

                if (empty($found_harversted)) {
                    //NO POST yet, create




                    $res = $m->upload_to_medialibrary($m->original_URL);
//jesli nadal sie do uploadu i zostal zaladowany
                    if (!$res['error']) {
                        $m->saveToPost("agregator_save");
                        file_put_contents($file, $counter . "; " . $m->URL_to_single_post . "; " . $m->original_URL . "; " . $res['width'] . "; " . $res['height'] . "\n", FILE_APPEND);
                        $counter = $counter + 1;
                    }
                }
                $found_harversted = null;
            }
            unset($m);
        }


        $methods_used = json_encode($agr->methods_used);
        $tmp_raport .= $memownia . "; " . sizeof($agr->memes) . "; " . $methods_used . "; <br>";

        //BARDZO WAŻNE - dedykowany destruktor, bez niego olbrzymie zyzycie pamieci, mimo użycia unset($agr)
        $agr->destroy(); //usuwamy dość pamięciożerny obiekt
        //  unset($m); 
    }
    //skasuj tyle, ile dodałeś;
    //   delete_n_oldest_posts($counter);


    file_put_contents($file, "found: " . sizeof($memesfound) . "; added: " . $counter . ";" . "\n ", FILE_APPEND);
    file_put_contents($file, "deleting : " . $counter . " oldest posts" . "\n ", FILE_APPEND);
    file_put_contents($file, "\n ---KONIEC----", FILE_APPEND);


    //*****************REKLAMY - AD- przenieś post o tym numerze na miejsce nr 2 
    //   move_post_to_nth_pos(93061, 2);  //(google ADS)
    //  move_post_to_nth_pos(122478, 6);  //(google ADS)
    //mail_report("dadadaaa@gmail.com", $tmp_raport);
    //**********************GET LIKES 
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function get_category_id($cat_name) {
    $term = get_term_by('name', $cat_name, 'category');
    return $term->term_id;
}

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


                <div class="meme_title"><?php echo ucfirst(get_post_meta($the_query->post->ID, 'title', true)); ?>  </div>



            </div><!--//home_post_text-->


            <div class="thumb_img">
            <?php the_post_thumbnail('home-image'); ?>
            </div>


            <div class="full_img">
            <?php the_content(); ?>
            </div>


            <div class="meme_source">  <a href="<?php echo get_post_meta($the_query->post->ID, 'MEME_ID', true); ?> ">   <?php echo strtolower(get_post_meta($the_query->post->ID, 'DomainName', true)) ?>  </a></div>

            <div class="home_post_bottom_box">
                <div> 
                    <a href="<?php echo $the_query->post->post_name; ?>">   <img class="share_button" src="wp-content/themes/ArtWorksResponsive/images/share_button.png" alt="share" > </a>
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
