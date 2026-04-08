<?php

class agregator {

    var $html; //obiekt DOM
    private $html_string; //czysty html
    var $url;
    public $alternative_url; //magazy w któ¶ych memownie przechowują img
    var $hrefs_found = array(); //tablica znalezionych na stronie WSZYSTKICH linkow
    var $social_hrefs_found = array();
    var $guessed_domain_for_images;
    var $pics = array(); // - array of DOM elements <img>
    var $pics_hrefs_found = array();  //adrezy wszystkich obrazkow
    var $pics_out_of_domain = array();
    var $hrefs_of_parents_of_pics_found = array(); // tego nie mają np. memownie hiszpanskie, gdzie nie da sie kliknac w obrazek na glownej 
    var $button_divs_data_hrefs_found = array();
    var $methods_used = array(); //tu przechowujemy nazwy funkcji, które znalazły data-hrefy
    var $logo_url;
    var $language;
    //tu będziemy przechowywać memy
    public $meme_data; //pełne dane do mema 
    var $memes = array();

    //bez tego dekonstruktora olbrzymie nadmiarowe nadkłady pamięci
    function destroy() {
        $this->html = null; //obiekt DOM
        $this->html_string = null; //czysty html
        $this->url = null;
        $this->alternative_url = null; //magazy w któ¶ych memownie przechowują img
        $this->hrefs_found = array(); //tablica znalezionych na stronie WSZYSTKICH linkow
        $this->social_hrefs_found = array();
        $this->guessed_domain_for_images = null;
        $this->pics = array(); // - array of DOM elements <img>
        $this->pics_hrefs_found = array();  //adrezy wszystkich obrazkow
        $this->pics_out_of_domain = array();
        $this->hrefs_of_parents_of_pics_found = array(); // tego nie mają np. memownie hiszpanskie, gdzie nie da sie kliknac w obrazek na glownej 
        $this->button_divs_data_hrefs_found = array();
        $this->methods_used = array(); //tu przechowujemy nazwy funkcji, które znalazły data-hrefy
        $this->logo_url = null;
        $this->languag = null;
        //tu będziemy przechowywać memy
        $this->meme_data = null; //pełne dane do mema 
        $this->memes = array();
    }

   

    function load_html_string($u) {
        //oczyść najpierw pamięć
        gc_collect_cycles();



        return file_get_contents($u);
    }
    
    

    function retag_html_string() {
        //to jest koniecze, by simple_html_dom zauwazyl to, co bylo w iframe'ach
        //TODO: te zmiany nie biora pod uwage popularnych bledow w HTML
//*****************retagujemy zmienną $html_string
        //zamieniamy tagi <fb:like na divy
        $html_string_retagged = str_ireplace("<fb:like ", '<div class="iframe_fb_like_button" ', $this->html_string);
        $html_string_retagged = str_ireplace("/fb:like ", "/div", $html_string_retagged);

//zamieniamy tagi <iframe do divy 
        $html_string_retagged = str_ireplace("<iframe", '<div class="iframe_fb_like_button" ', $html_string_retagged);
        $html_string_retagged = str_ireplace("/iframe>", "/div>", $html_string_retagged);

        //czasmi to jest zakodowane jako zwykly div ala twitter : data-href="http://kotburger.pl/58513" 
//        <div class="fb-like fb_edge_widget_with_comment fb_iframe_widget" data-href="http://kotburger.pl/58513" data-send="false" data-layout="button_count" data-width="120" data-show-faces="false" data-font="arial" fb-xfbml-state="rendered"><span style="height: 20px; width: 94px;"><iframe id="f372d233f4" name="f15fa5bd84" scrolling="no" title="Like this content on Facebook." class="fb_ltr" src="http://www.facebook.com/plugins/like.php?api_key=&amp;channel_url=http%3A%2F%2Fstatic.ak.facebook.com%2Fconnect%2Fxd_arbiter.php%3Fversion%3D27%23cb%3Df5eae5edc%26domain%3Dkotburger.pl%26origin%3Dhttp%253A%252F%252Fkotburger.pl%252Ffeb3e1234%26relation%3Dparent.parent&amp;colorscheme=light&amp;extended_social_context=false&amp;font=arial&amp;href=http%3A%2F%2Fkotburger.pl%2F58513&amp;layout=button_count&amp;locale=pl_PL&amp;node_type=link&amp;sdk=joey&amp;send=false&amp;show_faces=false&amp;width=120" style="border: none; overflow: hidden; height: 20px; width: 94px;"></iframe></span></div>
        //trzeba napisać serię funkcji poszukujących roznych sposobow umieszczanie FB lików


        $this->html_string = $html_string_retagged;
        //   return $html_string_retagged;
    }

    //znajduje divy guzikw poskugujące się data-href //Niektore metody Facebooka, i TWITTER
    function get_BUTTONS_method1_by_data_hrefs() {

        //elementy są układane w oryginalnym porządku jak na  stronie
        //polaczmy elementy tablic w jedna o unikalnych elementah     
        $tmp_data_href_found = $this->html->find('div[data-href]');
        if (sizeof($tmp_data_href_found)) {
            $this->button_divs_data_hrefs_found = array_unique(array_merge($this->button_divs_data_hrefs_found, $tmp_data_href_found));
            $this->methods_used[] = __FUNCTION__;
        }
    }

    function get_BUTTONS_method3_by_anchor_to_FB_sharer() {

//przypadek: (quickmeme.com)
//    <a class="shareonfb" onclick="window.open (this.href, 'child', 'height=400,width=665,scrollbars'); return false" 
//            href="https://www.facebook.com/sharer/sharer.php?u=http://www.quickmeme.com/p/3vo6pq" target="_blank">Share on Facebook</a>
        $tmp_elements = $this->html->find('a[href]');
        $tmp_elements_containing_FB_like_url = array();

        foreach ($tmp_elements as &$key) {
            preg_match_all('/sharer\.php\?u\=(.*?)\"/', $key->href, $m_static, PREG_PATTERN_ORDER);
            //dodajemy atrybut data-href w nodzie o wartosci równej dokładnie socialowemu urlowi, ktory własnie wyregexowalismy z urla ifacebookowego
            if ($m_static[1][0]) {
                $key->attr['data-href'] = $m_static[1][0];

                //    echo($key->href) . "<br>";
                $tmp_elements_containing_FB_like_url[] = $key;
            }
            unset($key);
        }
        if (sizeof($tmp_elements_containing_FB_like_url)) {
            $this->button_divs_data_hrefs_found = array_unique(array_merge($this->button_divs_data_hrefs_found, $tmp_elements_containing_FB_like_url));
            $this->methods_used[] = __FUNCTION__;
        }
    }

    function get_BUTTONS_method2_by_iframe() {

//only iframe case
        $tmp_elements = $this->html->find('a[href]');
        $tmp_elements_containing_FB_like_url = array();

        foreach ($tmp_elements as &$key) {
            preg_match_all('/href\=(.*?)\&amp/', $key->src, $m_static, PREG_PATTERN_ORDER);
            //dodajemy atrybut data-href w nodzie o wartosci równej dokładnie socialowemu urlowi, ktory własnie wyregexowalismy z urla ifacebookowego
            if ($m_static[1][0]) {
                $key->attr['data-href'] = $m_static[1][0]; //dodajemy lub podmieniamy href social
                $tmp_elements_containing_FB_like_url[] = $key;
            }
            unset($key);
        }
        if (sizeof($tmp_elements_containing_FB_like_url)) {
            $this->button_divs_data_hrefs_found = array_unique(array_merge($this->button_divs_data_hrefs_found, $tmp_elements_containing_FB_like_url));
            $this->methods_used[] = __FUNCTION__;
        }
    }

    //veomemes.com case
    function get_BUTTONS_method4_by_iframe_src() {

//szukamy div-ow po zretagowaniu 
        $tmp_elements = $this->html->find('.iframe_fb_like_button');
        $tmp_elements_containing_FB_like_url = array();
//adres jest ukryty w atrybucie src
        foreach ($tmp_elements as &$key) {
            preg_match_all('/href\=(.*?)\&amp/', $key->src, $m_static, PREG_PATTERN_ORDER);
            //dodajemy atrybut data-href w nodzie o wartosci równej dokładnie socialowemu urlowi, ktory własnie wyregexowalismy z urla ifacebookowego
            if ($m_static[1][0]) {
                $key->attr['data-href'] = $m_static[1][0]; //dodajemy lub podmieniamy href social
                $tmp_elements_containing_FB_like_url[] = $key;
            }
            unset($key);
        }
        if (sizeof($tmp_elements_containing_FB_like_url)) {
            $this->button_divs_data_hrefs_found = array_unique(array_merge($this->button_divs_data_hrefs_found, $tmp_elements_containing_FB_like_url));
            $this->methods_used[] = __FUNCTION__;
        }
    }

    //chamsko.pl case - tylko guzki share
    //<a name="fb_share" share_url="http://www.chamsko.pl/44769/Jebaka">Shareee</a>
    function get_BUTTONS_method5_by_old_SHARE_button() {


        $tmp_elements = $this->html->find('a');
        $tmp_elements_containing_FB_like_url = array();

        foreach ($tmp_elements as &$key) {
            if ($key->attr['share_url']) {
                $key->attr['data-href'] = $key->attr['share_url']; //dodajemy href social
                $tmp_elements_containing_FB_like_url[] = $key;
            }
            unset($key);
        }
        if (sizeof($tmp_elements_containing_FB_like_url)) {
            $this->button_divs_data_hrefs_found = array_unique(array_merge($this->button_divs_data_hrefs_found, $tmp_elements_containing_FB_like_url));
            $this->methods_used[] = __FUNCTION__;
        }
    }

    //znajduje divy z divów po retagowaniu iframow FB
    function get_BUTTONS_method6_from_retagged_elems() {

        $tmp_elements = $this->html->find('.iframe_fb_like_button');
        $tmp_elements_containing_FB_like_url = array();
//adres jest ukryty w atrybucie src
        foreach ($tmp_elements as &$key) { {
                $key->attr['data-href'] = $key->href; //dodajemy href social
                $tmp_elements_containing_FB_like_url[] = $key;
            }
            unset($key);
        }
        if (sizeof($tmp_elements_containing_FB_like_url)) {
            $this->button_divs_data_hrefs_found = array_unique(array_merge($this->button_divs_data_hrefs_found, $tmp_elements_containing_FB_like_url));
            $this->methods_used[] = __FUNCTION__;
        }
    }

    //  SOCIAL_HREF .: http://www.facebook.com/pages/naszawiocha/137957626247594
    //removing iinvalidly pointing buttons
    function remove_BUTTONS_pointing_to_outside_or_homepage() {


        $tmp_elements = array();

        foreach ($this->button_divs_data_hrefs_found as $key) {
            if ($this->is_in_domain(($key->attr['data-href'])) && //odrzucamy case wskazywania poza domenę
                    !($key->parent()->href == $this->url) &&
                    !($key->parent()->href == $this->url . "/"))
                ; //próbujemy odrzucić case, gdy href ojca lub href socialny prowadzi do strony głównej (jak zdarza się w repostuj)
            {
                //dodajemy href social
                $tmp_elements[] = $key;
            }
        }

        $this->button_divs_data_hrefs_found = $tmp_elements;
    }

    // szukamy hrefów parentów - jeśli porwadza poza domenę, to powinny wypaść
    // strony typu funnyjunk, nawet jeśli nie mają na głównej całych obrazków
    // musza mieść klikane thumbnaile
    // ich hrefy prowadza już do single memow!!!
    // ydaje się wiec, ze w większości przypadków nie obejdzie się bez zagladania do podstron (single post)  

    function get_repaired_parents_href_to_full_URL($node) {


        if (isset($node->parent()->href)) {
            if (preg_match("/^\//", $node->parent()->href)) {
                $node->parent()->href = substr($node->parent()->href, 1);
            }

            if (!preg_match("/^http:\/\//", $node->parent()->href)) {
                $node->parent()->href = $this->url . "/" . $node->parent()->href;
            }
        }

        return $node->parent()->href;
    }

//************NA STRONIE POJEDYNCZEJ URL ZNAJDZIEMY w metadanyh FB
    //oblicz mniej więcej odleglosc miedzy obrazkiem ($node) a buttonem spolecznosciowym
    function get_closest_button_to_the_img($node) {


        //jezeli nastronie znaleziono buttony spolecznosciowe
        if (sizeof($this->button_divs_data_hrefs_found)) {


            $button_info = array();
            $index = 0;

            $prev->tag_start = 111111111111; //to nie liczba, ale dla pierwszego elementu upraszczam 
            //przeglądamy buttony i ich pozycje względem image w $nodes
            foreach ($this->button_divs_data_hrefs_found as &$key) {
                // miejsce buttona - miejsce obrazka
                //kolejnosc buttonów branych pod uwage jest teraz jak w oryginalnym html
                //przy zwyklym ukladzie: IMG, poniżej jego BUTTON - dystans jest ujemny
                // gdy jego BUTTON JEST NAD - dystans jest dodatni
                $o = $node->tag_start - $key->tag_start;
                //ustalamy czy guzik jest nad zdjecie (1) czy pod zdjęciem (-1)
                if ($o > 0) {
                    $over = 1;
                } else {
                    $over = -1;
                }


                $button_info[] = array("index" => $index,
                    "pointer" => &$key,
                    "distance" => abs($o),
                    "over" => $over,
                    "distance_between_buttons" => abs($prev->tag_start - $key->tag_start),
                    "social_href" => $key->attr['data-href'],
                    "rapaired_parents_href" => $this->get_repaired_parents_href_to_full_URL($node)
                );

                $prev = $key;
                $index++;
                unset($key);
            }

            //szukamy minimalnego dystansu
            $tmp_min_val = 111111111111111111111;

            foreach ($button_info as &$b) {
                if ($b["distance"] < $tmp_min_val) {
                    $tmp_min_val = $b["distance"];


                    $closest_button = $b;
                }
                unset($b);
            }


            return $closest_button;
        }
    }

    function list_image_props($b, $node) {
        //efekt = jesli wśród obrazków będą  skiny buttona Facebooka, beda mialy bardzo małą wartość; mozemy je brać
        //ale i tak nie przejdą przez sito "już było"

        echo "IMG SRC:" . $node->attr["src"] .
        "<div style=\" width: 400px;\"> <img src=\"" . $node->attr["src"] . "\"></div><br>" .
        //     " BUTTON HREF: " . $distances_from_img[$a[0]]->attr["data-href"] .
        "  IMG'S PARENT HREF : " . $b["rapaired_parents_href"] . "<br>" .
        " CLOSEST BUTTON_INDEX ON PAGE : " . $b["index"] . "<br>" .
        " CLOSEST BUTTON_SOCIAL_HREF : <a href=\"" . $b["social_href"] . "\">" . $b["social_href"] . "</a><br>" .
        " CLOSEST BUTTON DISTANCE.: " . $b["distance"] . "<br>" .
        " CLOSEST BUTTON (ABOVE): " . $b["over"] . "<br>" .
        "<br><br>"
        ;
        //zwróc pełne info dla mema
        return array($b, $node);
    }

//if link points to the domain
    function is_in_domain($testurl) {
        $tmpurl = parse_url($testurl);

        $domain = parse_url($this->url);

        if ((strpos($tmpurl['host'], $domain['host']) !== false)) {
            return true;
        } else
            return false;
    }

    function is_in_alternative_domain($testurl) {
        $tmpurl = parse_url($testurl);

        $domain = parse_url($this->alternative_url);

        if ((strpos($tmpurl['host'], $domain['host']) !== false)) {
            return true;
        } else
            return false;
    }

    function is_single_page_in_domain($testurl) {
        $tmpurl = parse_url($testurl);
        $domain = parse_url($this->url);

        if (isset($tmpurl['path']) && $tmpurl['path'] !== "/" && $this->is_in_domain($testurl)) {
            return true;
        } else
            return false;
    }

    /*
     * metoda liczbowa - 
      minimalnie takich linkow moze byc 2 (do fb i do single postu
      maksymalnie - ile tylko guzikow socjalnych na swiecie, ale bez przesady
     * 
     */

    function get_social_ANCHORS($times_min = 2, $times_max = 10) {

        $this->hrefs_found = $this->html->find('a[href]');
        foreach ($this->hrefs_found as &$a) {


            $times_in_decoded = substr_count($this->html_string, $a->href);

            //jesli href anchora jest w naszej domenie (a nie np. na twitterze czy serwisie reklam
            if ($this->is_in_domain($a->href)) {

                //jesli href pojawia sie wiecej przynajmniej dwa razy (ale nie za czesto)
                if (($times_in_decoded >= $times_min) & ($times_in_decoded <= $times_max)) {
                    //  echo $a->tag . " " . $a->href . "   times  " . $times_in_decoded . "<br>";
                    //dodaj, jeśli go jescze nie masz
                    if (!in_array($a->href, $this->social_hrefs_found))
                        $this->social_hrefs_found[] = $a;
                    //dodajemy data-href
                    $a->attr['data-href'] = $a->href;
                }
            }
            unset($a);
        }

        $this->button_divs_data_hrefs_found = array_unique(array_merge($this->button_divs_data_hrefs_found, $this->social_hrefs_found));
        $this->methods_used[] = __FUNCTION__;
    }

    //returns unique pics elements of DOM, which point to our domain
    //
     function get_pics() {

        $this->pics = $this->html->find('img');

        $tmp_pics = array();
        $tmp_pics_hrefs_found = array();
        $this->pics_out_of_domain = array();

        foreach ($this->pics as &$pic) {

            /* uzupelnijmy linki lokalne (bez poczatku: htttp://domena) , nie dające pelnego urla,
             * o nazwe domeny - to najbardziej prawdopodobny brak
             * jesli link zaczyna sie od slasha, to na pew no jest lokalny,
             * wiec usunmy slash najpierw, bo bedzie ich za duzo (beda 2)
             * To SAMO DOTYCZY LINKOW HREF W PARENTACH - LINKOW DO POJEDYNCZYCH POSTOW w memowni
             */

            //jesli obrazke jest zapisany przez data-image i zakodowany w base w URI //veomemes.com case
            if (preg_match("/^data\:image/i", $pic->src)) {
                continue;
            }

            if (preg_match("/^\//i", $pic->src)) {
                $pic->src = substr($pic->src, 1);
            }

            if (!preg_match("/^http:\/\//i", $pic->src)) {
                $pic->src = $this->url . "/" . $pic->src;
            }


//dodaj, jesli urla obrazka jeszcze nie ma na liscie i jest na której z dopuszczlanych domen, tzn na głownej lub alternatywnej
            if (!in_array($pic->src, $tmp_pics_hrefs_found) &&
                    ( $this->is_in_domain($pic->src) )
            ) {
                $tmp_pics_hrefs_found[] = $pic->src;
                $tmp_pics[] = $pic;
            } else {
                //tu zgromadzimy sensowne urle, ktore wskazują poza domenę
                $this->pics_out_of_domain[] = $pic;
                //   echo "img out of domain:" . $pic->src . "<br>";
            }
            unset($pic);
        } //koniec foreach


        return $tmp_pics;
    }

    function guess_domain_for_images() {

        $hrefs = array();

        foreach ($this->pics_out_of_domain as &$pic) {

            $p = parse_url($pic->src);

            //tworzymy liste unikalnych domen, w ktorych osadzone sa obrazki
            if (!in_array($p["host"], $hrefs)) {
                $hrefs[] = $p["host"];
            }
            unset($pic);
        }
        //sortujemy po ilosci wystąpień w tablicy 
        $guessed_domains_for_images = array_count_values($hrefs);
        reset($guessed_domains_for_images);
        //

        return key($guessed_domains_for_images);
    }

    //STWORZ MEMY

    function add_meme($closest_button, $img) {
        $tmp_meme = new meme();
        $tmp_meme->domainurl = $this->url;
        $tmp_meme->original_URL = $img->src;
        $tmp_meme->URL_to_single_post = $closest_button["social_href"];


        $this->memes[] = $tmp_meme;
    }

    //agregator strony glownej i podstron - moze dzialac rekurencyjnie (dwa poziomy)
    function agregator($url, $url_alt, $poziom_rekurencji) {


        $this->url = $url;
        $this->alternative_url = $url_alt; //magazyn obrazkow poza domeną, np. demotywatoryfb.pl 
        $this->html_string = $this->load_html_string($this->url);
        //wazne
        $this->retag_html_string();
        $this->html = str_get_html(urldecode($this->html_string));


        if ($this->html) {

            //metody taksonomiczne - szukamy sladow hrefów na rozne sposoby
            // zbiermay urla do głownej tablicy data-hrefow:    $this->button_divs_data_hrefs_found 
            //kazda z tych metod rownoczesnie wrzuca do obiektu attrybut data-href
            //dodajac go lub modyfikujac

            $this->get_BUTTONS_method1_by_data_hrefs(); //faktopedia.pl,  div + href
            $this->get_BUTTONS_method2_by_iframe();
            $this->get_BUTTONS_method3_by_anchor_to_FB_sharer();  //quickmeme, a + href + facebook sharer
            $this->get_BUTTONS_method4_by_iframe_src(); //veomemes - guziki się nie zgadzają**************
            $this->get_BUTTONS_method5_by_old_SHARE_button(); //chamsko.pl -  stary guzik share 
            $this->get_BUTTONS_method6_from_retagged_elems();
            //wreszcie korzystamy z metody czysto liczbowej - ktora szuka powtarzajacych sie na stronie hrefow
            $this->get_social_ANCHORS(); //guzikow socjalnych moze nie byc ale wtedy na thumsach beda anchory
            //
            //tworzymy listę obrazków - elementów DOM
            $this->pics = $this->get_pics();

            //
            //zgadnijmy alternatywną domene dla obrazków

            $this->guessed_domain_for_images = $this->guess_domain_for_images();
            echo $this->guessed_domain_for_images;

            //wyrzućmy buttony, któwe wskazują poza domenę
            $this->remove_BUTTONS_pointing_to_outside_or_homepage();
            //
            //przeanalizuj odległosci do buttonow dla wszystkich zalezionych pics
            foreach ($this->pics as &$pic) {
                $tmp_unique_img_urls = array();
                //na tej liście będą pojawiać się powtarzające się obrazki, tj powtarzające się URLE w wobiektach img, np do ikonek buttonow
                //i jeśli są w domenie memownie 
                //TODO: konktrprzykład demotywatorów - stworzyć funkcję, ktora bierze pod uwagę listę alternatywnych 
                //domen domen "magazynowych"
                //teraz  mozemy je pomijac
                if (!in_array($pic->src, $tmp_unique_img_urls) &&
                        ($this->is_in_domain($pic->src) || $this->is_in_alternative_domain($pic->src))) {

                    //znajdujemy najbliższy button 

                    $closest_button = $this->get_closest_button_to_the_img($pic);

                    //jeśli istnieje href ojca obrazka $value  - i nie zgadza się z wyliczonym social urlem w $closest_button  - ustaw social url = href ojca
                    if (strlen($pic->parent()->href) > 0 && $this->is_in_domain($pic->parent()->href) && !($pic->parent()->href == $closest_button['social_href'])) {
                        $closest_button['social_href'] = $pic->parent()->href;
                    }



                    //TO juz są pełne dane do mema
                    $this->list_image_props($closest_button, $pic);

                    //TWORZYMY MEMY Z TEJ STRONY
                    $this->add_meme($closest_button, $pic);


                    $tmp_unique_img_urls[] = $pic->src;
                }
                unset($pic);
            }
        }

        //zwolnij pamięć - SIMPLE HTML PARSER SMA TEGO NIE ZROBI!!!
        //  http://www.electrictoolbox.com/php-simple-html-dom-parser-allowed-memory-exhausted/
        unset($this->html);
        unset($this->html_string);



        //jesli jakies memey zostaly stworzone - true
        if (sizeof($this->memes)) {
            return false;
        } else {
            return true;
        }
    }

}

?>
