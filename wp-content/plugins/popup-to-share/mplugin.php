<?php
/*
Plugin Name: Popup to Share
Plugin URI: http://codigojavaoracle.com/plugin-popup-to-share-wordpress/
Description: This plugin shows sharing buttons for Facebook, Twitter, and Google plus in a pop-up.
Version: 1.3 
Author: Miguel Berlanga, Yolanda Jimenez 
Author URI: http://codigojavaoracle.com/plugin-popup-to-share-wordpress/
*/

if(isset($_REQUEST["texto_cabecera"]) ){  
  $dato = $_REQUEST["texto_cabecera"];
  update_option('cabecera_popup',$dato);
}    

if(isset($_REQUEST["texto_cuerpo"])){  
  $dato2 = $_REQUEST["texto_cuerpo"];
  update_option('textocuer_popup',$dato2);
}

if(isset($_REQUEST["radio_publi"])){  
    $dato3 = $_REQUEST["radio_publi"];
    update_option('publi',$dato3);
  
}
if(isset($_REQUEST["facebook"]) ){  
  $dato4 = $_REQUEST["facebook"];
  update_option('facebook',$dato4);
}    

if(isset($_REQUEST["twitter"])){  
  $dato5 = $_REQUEST["twitter"];
  update_option('twitter',$dato5);
}

if(isset($_REQUEST["google"])){  
    $dato6 = $_REQUEST["google"];
    update_option('google',$dato6);  
}

 
function  menu_popup_comparte (){
add_menu_page("Pop-up to share", "Pop-up to share", 10, "plugin_popup_comparte", "web_plugin_popup_share");
}

function web_plugin_popup_share(){	
add_option('cabecera_popup', 'Sharing our website, you will help us with the maintenance. Thank you!'); 
add_option('textocuer_popup', ' Click on any of these buttons to help us to maintain this website.'); 
add_option('facebook', 'Facebook User'); 
add_option('twitter', 'Twitter User'); 
add_option('google', 'Google+ User'); 
add_option('publi', 'Si'); 
 

$texto_cabecera1 = get_option('cabecera_popup');
$texto_cuerpo1   = get_option('textocuer_popup');

$facebook1= get_option('facebook');
$twitter1= get_option('twitter');
$google1= get_option('google');
$publi1 = get_option('publi');


echo "<div class='wrap'> 
<h2>Welcome to \"Pop-up to share\" </h2>


<div style=\"margin:auto;
position:relative;
width:750px;
height:685px;
font-family: Tahoma, Geneva, sans-serif;
font-size: 14px;
line-height: 24px;
font-style: bold;
color: #09C;
text-decoration: none;
-webkit-border-radius: 10px;
-moz-border-radius: 10px;
border-radius: 10px;
padding:10px;
border: 1px solid #999;
border: inset 1px solid #333;
-webkit-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);
-moz-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);
box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);\"> 
<form action=\"#\" method=\"post\" id=\"form2\">
<font style=\"font-style: bold; font-weight: bold; font-size: 30px; \">Configuration Pannel</font>

<p>This plugin let your followers share the content of your website with their friends through the different social networks.

This panel let you edit the text which appears in the popup. This text could show a message for your readers explaining the importance of sharing you site in the social networks. 

Furthermore, in this panel you can personalize the plugin with your Facebook, Twitter and Google plus user account.</p>

<font style=\"font-style: bold; font-weight: bold;\">Header text:</font> <input type=\"text\" name=\"texto_cabecera\" id=\"valor1\" value=\"$texto_cabecera1\" style=\"width:740px; margin: 0 0 0 10px;display:block; border: 1px solid #999; height: 25px; -webkit-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3); -moz-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3); box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);\"/>
<font style=\"display: block;margin: 0 0 0px 10px;padding: 1px 3px; font-size: 88%;\">This is the text that show your header popup, i.e your title, page name ...</font>

<font style=\"font-style: bold; font-weight: bold;\">Body or message text:</font> <input type=\"text\" name=\"texto_cuerpo\" id=\"valor2\" value=\"$texto_cuerpo1\" style=\"width:740px; margin: 0 0 0 10px;display:block; border: 1px solid #999; height: 25px; -webkit-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3); -moz-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3); box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);\"/>
<font style=\"display: block;margin: 0 0 0px 10px;padding: 1px 3px; font-size: 88%;\">This is the text that show your body popup, i.e explain about that, credits, thanks...</font>


<font style=\"font-style: bold; font-weight: bold;\">Facebook user:</font><input type=\"text\" name=\"facebook\" id=\"valor3\" value=\"$facebook1\" style=\"width:740px; margin: 0 0 0 10px;display:block; border: 1px solid #999; height: 25px; -webkit-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3); -moz-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3); box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);\"/>
<font style=\"display: block;margin: 0 0 0px 10px;padding: 1px 3px; font-size: 88%;\">You must write your Facebook acount , i.e .. www.facebook.com/FACEBOOK_ACOUNT</font>

<font style=\"font-style: bold; font-weight: bold;\">Twitter user:</font> <input type=\"text\" name=\"twitter\" id=\"valor4\" value=\"$twitter1\" style=\"width:740px; margin: 0 0 0 10px; display:block; border: 1px solid #999; height: 25px; -webkit-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3); -moz-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3); box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);\"/>
<font style=\"display: block;margin: 0 0 0px 10px;padding: 1px 3px; font-size: 88%;\">You must write your Twitter acount , i.e .. www.twitter.com/TWITTER_ACOUNT</font>

<font style=\"font-style: bold; font-weight: bold;\">Google plus+:</font> <input type=\"text\" name=\"google\" id=\"valor5\" value=\"$google1\" style=\"width:740px; margin: 0 0 0 10px;display:block; border: 1px solid #999; height: 25px; -webkit-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3); -moz-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3); box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);\"/>
<font style=\"display: block;margin: 0 0 0px 10px;padding: 1px 3px; font-size: 88%;\">You must write the full url of the website that you want that received the plus. For example http://codigojavaoracle.com .If you left it in blank the url will change on each page</font>
";
if ($publi1 == 'Si'){
echo"
<font style=\"font-style: bold; font-weight: bold;\">Help us to continuous developing plugins by showing our credit link (Thank you)</font></br>

<input type=\"radio\" name=\"radio_publi\" value=\"Si\" style=\"margin: 0 0 0 15px\" checked/>Yes, I want to include the credit link.  <br />
<input type=\"radio\" name=\"radio_publi\" value=\"No\" style=\"margin: 0 0 0 15px\"/>No, I preffer do not show the credit link in my site. 
"; }
else{
echo"
<font style=\"font-style: bold; font-weight: bold;\">Show our link Powered by CodigoJavaOracle</font></br>
<input type=\"radio\" name=\"radio_publi\" value=\"Si\" style=\"margin: 0 0 0 15px\" /> Yes, I allowed a credit link.<br />
<input type=\"radio\" name=\"radio_publi\" value=\"No\" style=\"margin: 0 0 0 15px\" checked/> No, I preffer don´t show that.

 ";}
echo "


<INPUT type=\"submit\"  value=\"Save\" style=\"width:100px;
position:absolute;
right:350px;
bottom:10px;
background:#09C;
color:#fff;
font-family: Tahoma, Geneva, sans-serif;
height:30px;
-webkit-border-radius: 15px;
-moz-border-radius: 15px;
border-radius: 15px;
border: 1p solid #999;\"> 
   
</form>
</div> 

</div>";

} 

  
function add_stylesheet() {
wp_enqueue_style('mplugin-css', plugin_dir_url(__FILE__) . 'mplugin.css');
} 

function enqueue_my_scripts(){
wp_enqueue_script( 'mplugin.js', plugin_dir_url(__FILE__) . 'mplugin.js');
wp_enqueue_script('google-plusone', 'https://apis.google.com/js/plusone.js', array(), null);
}
  
add_action("admin_menu","menu_popup_comparte");

add_action('wp_print_styles', 'add_stylesheet');

add_action( 'wp_print_scripts', 'enqueue_my_scripts');

add_action("the_content", "banner_ayuda_wordpress");


function banner_ayuda_wordpress($content){
 
 $cabecera = get_option('cabecera_popup');
 $cuerpo   = get_option('textocuer_popup');
 $publi1 = get_option('publi');
 $elpermalink = get_option ('siteurl'); // imagen
 $elpermalinkg = get_option ('google');
 $elpermalinkf = get_option ('facebook'); // Facebook
 $elpermalinkt = get_option ('twitter'); // Facebook
$banner	=	'  

 
    <div id="over" class="overbox">


   <div id="divimagen" class="divimagen">
<a border="0" href="javascript:hideLightbox();">
<img border="0" style="border:0px; background:0; margin-left:445px; margin-top:-50px;" alt="X" title="Close" src="'. $elpermalink .'/wp-content/plugins/popup-to-share/close.png">
</a>

</div>
<div id="apDiv3">
     

<div align="center"> ' . $cabecera . ' </div>
    </div>
    <div id="apDiv4">
      
      <p align="center">&nbsp;</p>
      <p align="center"> ' . $cuerpo . ' </p>   
    
    <br>  <br>
</div>
<div id="apDiv45">
    <!--Facebook1 -->

 &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp

<iframe src="http://www.facebook.com/plugins/like.php?href=http://'. $elpermalinkf .'
&amp;layout=button_count&amp;show_faces=true&amp;width=100&amp;action=like&amp;font=arial&amp;colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; line-height:20px; margin:5px 15px 0 0; width:140px; height:20px"></iframe>
  

 <!--Follow me twitter -->
 <a href="https://twitter.com/'. $elpermalinkt .'" class="twitter-follow-button" data-show-count="false" data-lang="en">Seguir a @'. $elpermalinkt .'</a>
 
<script>
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
</script>

&nbsp&nbsp

<!-- Google +1. -->
<!-- Coloca esta etiqueta donde quieras que se muestre el botón +1. -->
<div class="g-plusone" data-href="'.$elpermalinkg.'"></div>


<!-- Coloca esta petición de presentación donde creas oportuno. -->
<script type="text/javascript">
  (function() {
    var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
    po.src = \'https://apis.google.com/js/plusone.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
  </div> ';
  
  if ($publi1 == 'Si'){  
    
     $banner= $banner . '  <div id="apDiv5"><div align="center">
        <a href="http://mysexshop.es" target="_blank">Tienda online</a> </div></div>  ';

} 
   else { 
    $banner= $banner .' <div id="apDiv5"><div align="center">Ver. 1.3</div></div> ';
   }
   
   $banner= $banner .'       

 </div><div id="fade" class="fadebox">&nbsp;</div>  ';
    
  $content	=	$content . $banner;
  return $content;
}
?>