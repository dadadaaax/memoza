/*!
 * Custom JS
 * @package Acme Themes
 * @subpackage SuperNews
 */
jQuery(document).ready(function($) {
    $('.header-wrapper .menu').slicknav({
        allowParentLinks :true,
        duration: 500,
        prependTo: '.header-wrapper .responsive-slick-menu',
        easingOpen: "swing",
        'closedSymbol': '+',
        'openedSymbol': '-'
    });
    // ticker
    $('.bn').show().bxSlider({
        speed: 1000,
        auto: true,
        controls: false,
        pager: false,
        autoHover : true,
        mode:'fade'
    });

    /*featured slider*/
    $('.at-home-slider').each(function(){
        home_bxslider = $(this);
        home_bxslider.show().bxSlider({
            autoHover : true,
            pager: false,
            nextText: '<i class="fa fa-angle-right"></i>',
            prevText: '<i class="fa fa-angle-left"></i>'
        });
    });

    //for menu
    $('.header-wrapper #site-navigation .menu-main-menu-container').addClass('clearfix');
    jQuery('.menu-item-has-children > a').click(function(){
        var at_this = jQuery(this);
        if( at_this.hasClass('at-clicked')){
            return true;
        }
        var at_width = jQuery(window).width();
        if( at_width > 992 && at_width <= 1230 ){
            at_this.addClass('at-clicked');
            return false;
        }
    });

    /*sticky menu*/
    var menu_sticky_height = $('#masthead').height() - $('#site-navigation').height() - $('.main-navigation.trends').height();
    $(window).scroll(function(){
        if ( $(this).scrollTop() > menu_sticky_height) {
            $('.supernews-enable-sticky-menu').css({"position": "fixed", "top": "0","right": "0","left": "0","z-index":'999'});
            $('.supernews-enable-sticky-menu .header-main-menu').css('margin','0 auto');
        }
        else {
            $('.supernews-enable-sticky-menu').removeAttr( 'style' );
            $('.supernews-enable-sticky-menu .header-main-menu').removeAttr( 'style' );
        }
        if ( $(this).scrollTop() > menu_sticky_height) {
            $('.sm-up-container').show();
        }
        else {
            $('.sm-up-container').hide();
        }
    });

    //Sticky Sidebar
    if($('body').hasClass('at-sticky-sidebar')){
        if($('body').hasClass('both-sidebar')){
            $('#primary-wrap, #secondary-right, #secondary-left').theiaStickySidebar();
        }
        else{
            $('.secondary-sidebar, #primary').theiaStickySidebar();
        }
    }
});