<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml">
    <head> 

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
        <title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>          
        <?php wp_head(); ?>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <!--[if lt IE 9]>
        <script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
        <![endif]-->              
        <!--<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>-->
        <script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery-latest.js"></script>

        <script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.gridstreamviewer.js"></script>
        <script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.history.js"></script>
        <script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.viewport.mini.js"></script>

        <script src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.infinitescroll.js" type="text/javascript" charset="utf-8"></script>    
        <script src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.viewport.mini.js" type="text/javascript" charset="utf-8"></script>    



        <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" title="no title" charset="utf-8"/>

        <script type="text/javascript">
            $(document).ready(
                    function($) {

                        // confirmations alerts
                        $('.confirmation').on('click', function() {
                            return confirm('Are you sure?');
                        });

                        $('#content_inside').infinitescroll({
                            navSelector: "div.load_more_text",
                            // selector for the paged navigation (it will be hidden)
                            nextSelector: "div.load_more_text a:first",
                            // selector for the NEXT link (to page 2)
                            itemSelector: "#content_inside .home_post_box"
                                    // selector for all items you'll retrieve
                        }, function(arrayOfNewElems) {




                        });
                    }




            );
        </script>  
    </head>
    <body>


        <div id="fb-root"></div>
        <script>(function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id))
                    return;
                js = d.createElement(s);
                js.id = id;
                js.src = "//connect.facebook.net/pl_PL/sdk.js#xfbml=1&version=v2.3&appId=287588951395038";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));</script>






        <div id="main_container">

            <div id="black_belt">

                <div class="header_container">

                    <a class="header__logo" href="<?php echo get_site_url(); ?>">
                        <img class="header__logo__img" src="<?php bloginfo('stylesheet_directory'); ?>/images/logo.jpg" />
                    </a>
                    <a id="header_top" href="<?php echo get_site_url() . "/?page_id=484715"; ?>">
                        <span>TOP MEMY!</span>
                    </a>


                    <a id="header_menu" href="<?php echo get_site_url() . "/?page_id=2"; ?>">
                        <span class="header_menu__bar"></span>
                        <span class="header_menu__bar"></span>
                        <span class="header_menu__bar"></span>
                    </a>
                    <div id="header_search" >
                        <?php echo do_shortcode('[wpdreams_ajaxsearchlite]'); ?>
                    </div>

                </div><!--//header-->
            </div> <!--black_belt-->

            <div class="clear"></div>         