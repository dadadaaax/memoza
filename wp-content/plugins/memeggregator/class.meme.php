<?php

class meme {

    var $id;
    var $title;
    var $hashID;
    var $localFileData;
    var $localURI;
    var $localURL;
    var $URL_to_single_post; //to jest także url, który się likuje i sharuje
    var $FBbuttonHTML;
    var $original_html_context;
    var $original_URL; //to jest także URLID jpga jako pliku - mema
    var $languge;
    var $country;
    var $DomainName;
    var $like_count;
    var $history_like_count = array();

    /*
     * ustawia coś w rodzaju ID postu - jego URL; do zrobienia: obliczanie hashtagu jpga i 
     * haszowanie wszystkich postow 
     */

    function get_like_count() {

        $fql = 'SELECT url, share_count, total_count
        FROM link_stat WHERE url="' . $this->URL_to_single_post . '"';
        $json = json_decode(file_get_contents('https://api.facebook.com/method/fql.query?format=json&query=' . urlencode($fql)));
        return $json[0]->total_count;
    }

    function update_like_count() {
        //   $this->like_count = $this->get_like_count($this->URL_to_single_post);
        add_post_meta($this->id, 'like_count', $this->like_count, true) || update_post_meta($this->id, 'like_count', $this->like_count);
        return $this->like_count;
    }

    function update_history_like_count() {


        $this->history_like_count = json_decode(get_post_meta($this->id, 'history_like_count', true));

        $this->history_like_count[] = array(date('Y-m-d H:i:s'), $this->like_count);
        $this->history_like_count = json_encode($this->history_like_count);

        add_post_meta($this->id, 'history_like_count', $this->history_like_count, true) || update_post_meta($this->id, 'history_like_count', $this->history_like_count);


        return $this->history_like_count;
    }

    function saveURLID() {
        add_post_meta($this->id, 'URLID', $this->original_URL, true) || update_post_meta($this->id, 'URLID', $this->original_URL);
    }

    function saveLocalURL() {
        add_post_meta($this->id, 'LocalURL', $this->localFileData['url'], true) || update_post_meta($this->id, 'LocalURL', $this->localFileData['url']);
    }

    function saveHASH() {
        add_post_meta($this->id, 'hash', $this->localFileData['hash'], true) || update_post_meta($this->id, 'hash', $this->localFileData['hash']);
    }

    function has_uniqueHASH() {


        //   $found = query_posts('meta_key=hash&meta_value=' . $this->localFileData['hash']);

        $args = array(
            'meta_query' => array(
                array(
                    'key' => 'hash',
                    'value' => $this->localFileData['hash']
                )
            ),
            'fields' => 'ids'
        );
        // perform the query
        $found = new WP_Query($args);

        // you are getting back an array of ids if the key has the same value at another post
        // otherwise it should be empty, but for failsafe reasons we're going to filter out
        // all keys with null, false and empty values, with array_filter(), just to be thorough
        $found = array_filter($found);

        // do something if the key-value-pair exists in another post
        if (!empty($found)) {
            return false;
        } else
            return true;
    }

    function saveDomainName() {
        $p = parse_url($this->original_URL);

        $host = str_replace("http://", "", $p['host']);

        //wybieramy odrzucamy subdomeny
        $host_names = explode(".", $host);
        $bottom_host_name = $host_names[count($host_names) - 2] . "." . $host_names[count($host_names) - 1];

        $this->DomainName = $bottom_host_name;
        add_post_meta($this->id, 'DomainName', $this->DomainName, true) || update_post_meta($this->id, 'DomainName', $this->DomainName);
    }

    function saveMEME_ID() {
        add_post_meta($this->id, 'MEME_ID', $this->URL_to_single_post, true) || update_post_meta($this->id, 'MEME_ID', $this->URL_to_single_post);
        // $this->MEME_ID = $this->URL_to_single_post;
    }

    //title -budujemy ze sluga z URLa społęcznościowego, nazwy hosta i może rozbitej na częsci nazwy pliku

    function save_meme_title() {
        $black_list = "/([+-]?[0-9]+(?:\.[0-9]*)?)|(php|vt|item|img|html|htm|.jpg|.gif|_)/i";


        $parts = parse_url($this->URL_to_single_post);

        $items = preg_split('/[^a-z0-9]/i', $parts['path']);

        $keywords = implode($items, " ") /* . " " . $parts['host'] */;
        //usuwamy stringi z blacklisty 
        $keywords = preg_replace($black_list, "", $keywords);



        $this->title = $keywords; //tworzymy tytul mema z keywordsow
        add_post_meta($this->id, 'title', $keywords, true) || update_post_meta($this->id, 'title', $keywords);


        //update slug -change it with kewywords and data 
        $post_slug = "memejet-" . date('Ymdhi') . "-" . implode($items, " "); //definicjemy nazwę (slug)

        update_post_meta($this->id, 'name', $post_slug);
    }

    /*
     * upload z urla prowadzącego prosto do jpga; plik laduje w library wordpressa
     */

    public function upload_to_medialibrary($url) {

        $result = array();



        if (!empty($url)) {
            // get URL from external resource
            $context = @stream_context_create(array('http' => array('header' => 'Connection: close')));
            $content = @file_get_contents($url);


            if (!empty($content)) {
                // content is not empty then process it
                $result = array('file' => $file, 'url' => $url);

                $upload_tmp_dir = dirname(__FILE__) . '/temp';
                $tempname = $upload_tmp_dir . '/' . date('Ymdhi') . '.tmp';





                file_put_contents($tempname, $content);





                //check image size -throw away if
                list($width, $height) = getimagesize($tempname);

                if (($width > 200 && $height > 200)) {



                    //$basename = "newname.png";
                    // Curious about what this does? See my comment here: http://stackoverflow.com/questions/2273280/how-to-get-the-last-path-in-the-url/7340428#7340428
                    $url_path = parse_url($url, PHP_URL_PATH);
                    $parts = explode('/', $url_path);
                    $basename = end($parts);

                    // Override values before using wp_handle_upload
                    $overrides['test_form'] = FALSE;
                    $overrides['test_upload'] = FALSE;
                    $overrides['test_type'] = TRUE; // FALSE
                    $_file['name'] = $basename;


                    //  $MaxCanvasWidth =600;
                    //ta fukncja przekonwertowuje równiez gif , png ->jpg
                    //   resize_fileimage_to_maxwidth ($tempname, $MaxCanvasWidth);



                    $_file['tmp_name'] = $tempname;
                    $_file['type'] = $type;
                    $_file['size'] = @filesize($tempname);
                    //chmod($newtempname, '0777');
                    // Use modified wp_handle_upload to store the uploaded file under WordPress
                    $fileinfo = dndmedia_wp_handle_upload($_file, $overrides);

                    if (!isset($fileinfo['error'])) {


                        //compute hash for uploaded file
                        $tmph = explode("  ", exec("md5sum $tempname"));
                        $md5 = $tmph[0];

                        $log[] = "File imported from URL and uploaded to WordPress";

                        $url = $fileinfo['url'];
                        $type = $fileinfo['type'];
                        $file = $fileinfo['file'];





                        // remove tmp file
                        @unlink($tempname);

                        // STEP 3 :: Process the attachment (see: http://wordpress.stackexchange.com/questions/17870/media-handle-upload-weird-thing )
                        $log[] = "Step 3: Attaching imported image into post";
                        $attachment = array('post_mime_type' => $type, 'post_title' => $basename, 'post_status' => 'inherit');
                        $attach_id = wp_insert_attachment($attachment, $file, $post_id);
                        require_once ABSPATH . 'wp-admin/includes/image.php';
                        $attach_data = wp_generate_attachment_metadata($attach_id, $file);
                        $attach_res = wp_update_attachment_metadata($attach_id, $attach_data);











                        $log[] = "Ready to send results to client";

                        $result = array(
                            'file' => $file,
                            'name' => $basename,
                            'url' => $url,
                            'type' => $type,
                            'attachment_id' => $attach_id,
                            'attachment_data' => $attach_data,
                            'attachment_result' => $attach_res,
                            'log' => $log,
                            'op' => 'importurl',
                            'width' => $width,
                            'height' => $height,
                            'hash' => $md5
                        );
                    } else {
                        //return new WP_Error( 'upload_error', $fileinfo['error'] );
                        $result['error'][] = "Upload error " . $fileinfo['error'];
                    }
                } else {
                    //return new WP_Error( 'upload_error', $fileinfo['error'] );
                    $result['error'][] = "X or Y size too small";
                    @unlink($tempname);
                }
            } else {

                $result['error'][] = "Empty content error. Cannot import external image";
            }
        } else {
            $result['error'][] = "Empty URL";
        }
        $this->localFileData = $result;






        return $result;
    }

    /*
     * save meme to post with metadata like IDURL, IDHASH, 
     * 
     */

    function saveToPost($mode) {

        //get randomized time (tos suffle posts)


        $delta = 600; //sekund
        // echo date('Y-m-d H:i:s', time() - ($znak * rand(0, $delta)));


        $new_post = array(
            'post_title' => $this->localFileData['name'], //tymczasowo, po znalezioniu keywords podmiana
            //  'post_content' => "<img src=\"" . $this->localFileData['url'] . "\" alt=\"\"  class=\"imgmeme\" /><br>",
            // 'post_category' => array($this->DomainName), //kategoria = host żródła
            //  'tags_input' => array($tags),
            'post_status' => 'publish', // Choose: publish, preview, future, draft, etc.
            'post_type' => 'post', //'post',page' or use a custom post type if you want to
            'post_date' => date('Y-m-d H:i:s', time() - rand(0, $delta))  //save with randomized time (to shuffle posts
        );

        //CREATE AN SAVE BASE POST 
        $pid = wp_insert_post($new_post);

        //**********teraz ustawiamy id mema na memejecie = id postu;
        //jego URLID oraz nazwę domeny, oraz MEME_ID, czyli url do postu w zrodlowej memowni, np :http://besty/54863
        $this->id = $pid;
        $this->saveURLID();
        $this->saveLocalURL();
        $this->saveHASH();

//        if (!($this->has_uniqueHASH())) {
//            wp_delete_post($this->id, true);
//            return false(); //break if hash is not unique;
//        };

        $this->saveDomainName();


        $this->save_meme_title();

        // widocznie odwołanie do FB wiesza całość    $this->update_like_count();

        $this->saveMEME_ID();



        //szukamy id kategorii (na podstawie nazwy domeny)
        $idObj = get_category_by_slug($this->DomainName);
        $cat_id = $idObj->term_id;
        //teraz updatujemy kategorię i tytuł mema
        $just_inserted_post = array();
        $just_inserted_post['ID'] = $this->id;
        // $just_inserted_post['post_title'] = $this->title . " " . date('Y-m-d H:i');
        $just_inserted_post['post_title'] = $this->title;


        $just_inserted_post['post_name'] = $this->title . " " . date('Y-m-d H:i'); //slug

        $just_inserted_post['post_category'] = array($cat_id);
        wp_update_post($just_inserted_post);

        // updatujemy post-attachment, definuiajac mu  "ojca" o numerze $pid (czyli nasz nowo wstawiony wpis)
        $just_inserted_attachment = array();
        $just_inserted_attachment['ID'] = $this->localFileData['attachment_id']; //id jpga w galerii po przeróbkach
        $just_inserted_attachment['post_parent'] = $pid;
        wp_update_post($just_inserted_attachment);


        //*********************************dodajemy WATERMARK do attachement JEŚLI TO JUŻ NIE JEST DRAFT
        if (($mode <> "draft") && (isset($cat_id))) {
            $image_watermark = Image_Watermark();
            $image_watermark->label_domain_name = $this->DomainName;
            $image_watermark->action_context = "at_save_post";

            //TODO

            $image_watermark->categorycolor = get_post_cat_color($pid);
            log_variable($pid, " pid w funkcji SAVE_TO_POST");
            log_variable($cat_id, "cat_id w funkcji SAVE_TO_POST");
            log_variable($image_watermark->categorycolor, " get_post_cat_color w funkcji SAVE_TO_POST");

            $data = wp_get_attachment_metadata($this->localFileData['attachment_id'], false);
            $image_watermark->apply_watermark($data, $this->localFileData['attachment_id'], 'manual');


            wh_log(date("Y-m-d H:i:s") . "WATERMARK creating on attachment " . $this->localFileData['attachment_id'] . "category:" . $image_watermark->label_domain_name);
            unset($image_watermark);
        }

        //ustaw thumbnail dla załączonej qpy ($attach_id)
        update_post_meta($pid, '_thumbnail_id', $this->localFileData['attachment_id']);
    }

    //funkcja na bazie 
    function publishToFB() {

        $options = get_option('NS_SNAutoPoster');
//      var_dump($options);
//      exit;
        foreach ($options['fb'] as $ii => $fbo)
            if ($ii == $_POST['nid']) {
                $fbo['ii'] = $ii;
                $fbo['pType'] = 'aj';



                $fbpo = get_post_meta($this->id, 'snapFB', true); /* echo $this->id."|"; echo $fbpo; */
                $fbpo = maybe_unserialize($fbpo); // prr($fbpo); 

                if (is_array($fbpo) && isset($fbpo[$ii]) && is_array($fbpo[$ii])) {
                    $ntClInst = new nxs_snapClassFB();
                    $fbo = $ntClInst->adjMetaOpt($fbo, $fbpo[$ii]);
                } //prr($fbo);
                //get mean of likes in category
                $category = get_the_category($this->id);
                $likes_mean = get_terms_meta($category[0]->cat_ID, 'like_count_mean', true);
                if ($this->like_count > 0) {
                    // $prcnt_over_mean = number_format(100 * ($this->like_count - $likes_mean) / $this->like_count, 0);
                    // $fbo['fbMsgFormat'] = $fbo['fbMsgFormat'] . " +" . $prcnt_over_mean . "%";
                }



                $result = nxs_doPublishToFB($this->id, $fbo); // if ($result == '200') die("Your post has been successfully sent to FaceBook."); else die($result);
                return $result;
            }
    }

    function destroy() {

        $this->id = null;
        $this->title = null;
        $this->hashID = null;
        $this->localFileData = null;
        $this->localURI = null;
        $this->localURL = null;
        $this->URL_to_single_post = null;
        $this->FBbuttonHTML = null;
        $this->original_html_context = null;
        $this->original_URL = null;
        $this->languge = null;
        $this->country = null;
        $this->DomainName = null;
        $this->like_count = null;
        $this->history_like_count = null;
    }

}

?>
