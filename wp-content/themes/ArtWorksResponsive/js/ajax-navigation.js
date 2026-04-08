//jQuery(document).ready(function() {
//
//    mainContent = jQuery("#main_container");
//    //jeśli w ogole na stronie jest #qpa_container (czyli jestesmy na przegladaniu )
//    if (mainContent.length) {
//         
//        siteUrl = "http://" + top.location.host.toString();
//        url = ''; 
//    
//        jQuery(document).delegate("a[href^='"+siteUrl+"']:not([href*=/wp-admin/]):not([href*=/wp-login.php]):not([href$=/feed/]))", "click", function() {
//            location.hash = this.pathname;
//            //  alert ("regex spelniony, link: "+location.valueOf());
//            return false;
//      
//        }); 
////            jQuery("#searchform").submit(function(e) {
////                location.hash = '?s=' + jQuery("#s").val();
////                e.preventDefault();
////            }); 
//        jQuery(window).bind('hashchange', function(){
//         
//            if (location.hash !="#/") {
//                url = window.location.hash.substring(1);
//                //  alert ("zmiana hasha: "+url+", hash:"+window.location.hash );
//         
//         
//                if (!url) {
//                    return;
//                } 
//                // spacja przed #container jest konsekwencja kodowania urla
//                url = url + " #main_container"; 
//                mainContent.animate({
//                    opacity: "0.01"
//                }).load(url, function() {
//                    mainContent.animate({
//                        opacity: "1"
//                    });
//                });
//            }   else {location='http://localhost/Memejet/'} //jelis ssciezka hasha jest pusta, idz do głownej
//        
//        });
//        jQuery(window).trigger('hashchange');
//    
//    }
//    
//});