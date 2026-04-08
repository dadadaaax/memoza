/* global jQuery */
/* global MEMEME */

/*
 * MeMeMe galleries - jQuery Plugin
 * version: 1.6
 * @requires jQuery >= 1.7.0
 * Depends:
 *  isotope.js
 * 
 * Copyright 2017-2019 Nicola Franchini - @nicolafranchini
 */
if (!jQuery.fn.mememeIsotope && jQuery.fn.isotope) {
    jQuery.fn.mememeIsotope = jQuery.fn.isotope;
}
(function( $ ) {
    'use strict';
    $.fn.mememeGallery = function() {
 
        return this.each(function() {
            var $grid = $(this);

            // Prevent double initialization
            if ($grid.data('mememe')) {
                return true;
            }

            $grid.mememeIsotope({
                itemSelector: '.mememe-gallery-item',
                percentPosition: true,
                layoutMode: 'packery',
                // disable scale transform transition when hiding
                hiddenStyle: {
                    opacity: 0,
                    transform: 'translateY(100px)'
                },
                visibleStyle: {
                    opacity: 1,
                    transform: 'translateY(0)'
                },
                masonry: {
                    columnWidth: '.grid-sizer'
                }
            });

            $grid.data('mememe', true);

            // layout Masonry after each image loads
            $grid.imagesLoaded( function() {
                $grid.removeClass('mememe-hidden');
                checkFilters();
            });

            var filterbutt = $grid.prev('.mememe-filters').find('.mememe-filter');

            $(filterbutt).on('click', function(e) {
                e.preventDefault();
                filterbutt.removeClass('active');
                $(this).addClass('active');
                var filter = $(this).attr('data-filter');
                $grid.mememeIsotope({ filter: filter });
            });

            function checkFilters(){
                $(filterbutt).each(function(){
                    var filter = $(this).attr('data-filter');
                    if (filter !== '*') {
                        if ($grid.find(filter).length) {
                            $(this).css('display', 'inline-block');
                        } else {
                            $(this).css('display', 'none');
                        }
                    }
                });
                $grid.mememeIsotope('layout');
            }

            /**
            * Re-layout if JetPack lazy load is active
            * use class .skip-lazy to deactivate
            */

            // // use value of search field to filter
            // var $quicksearch = $grid.parent('.mememe-wrap-gallery').find('.quicksearch').keyup( debounce( function() {
            //     qsRegex = new RegExp( $quicksearch.val(), 'gi' );
            //         $grid.mememeIsotope({filter: function() {
            //             return qsRegex ? $(this).find('.mememe-card > a').attr('title').match( qsRegex ) : true;
            //             }});
            //         }, 200 ) );

            //     // debounce so filtering doesn't happen every millisecond
            //     function debounce( fn, threshold ) {
            //         var timeout;
            //         return function debounced() {
            //             if ( timeout ) {
            //                 clearTimeout( timeout );
            //             }
            //         function delayed() {
            //             fn();
            //             timeout = null;
            //         }
            //         timeout = setTimeout( delayed, threshold || 100 );
            //     }
            // }

            /*
            * Load more function for Memes gallery
            */
            var $loadmorememes = $grid.next('.mememe-loadmorememes');

            var loadMoreMemes = function(e){
                e.stopPropagation();
                e.preventDefault();

                var button = $(e.target);

                button.off('click', loadMoreMemes);

                var preloadholder = button.find('.preload-mememe');

                preloadholder.html('<i class="immm immm-loading immm-spin"></i>');

                var data_thumbsize = button.data('thumbsize');
                var max_page = button.data('max_page');
                var data_current_page = button.data('current_page');
                var data_per_page = button.data('per_page');
                var data_category = button.data('category');
                var data_margin = button.data('margin');
                var data_orderby = button.data('orderby');

                var send_data = {
                    current_page: data_current_page,
                    posts_per_page: data_per_page,
                    margin: data_margin,
                    thumbsize: data_thumbsize,
                    orderby: data_orderby,
                    action: 'mememe_loadmore'
                };

                if (data_category){
                    send_data.category = data_category;
                }

                send_data = $.param(send_data);

                $.ajax({
                    url: MEMEME.ajax_url,
                    type: 'POST',
                    processData: false,
                    data: send_data + "&mememe_nonce="+MEMEME.nonce
                })
                .done(function( data ) {

                    if ( undefined !== data.success && false === data.success ) {
                        return;
                    }

                    if ( data ) { 
                        var jsonData = JSON.parse(data);
                        for (var i = 0; i < jsonData.length; i++) {
                            var $item = $(jsonData[i]);
                            $item.addClass('mememe-hidden');
                            $grid.append($item);
                            var myImg = $item.find('img').attr('src');
                            loadImage(myImg, $item);
                        }
                        data_current_page++;
                        button.data('current_page', data_current_page);
                        preloadholder.html('');
                        checkFilters();

                        // if last page, remove the button
                        if ( data_current_page === max_page ) {
                            button.fadeOut(300, function(){
                                $(this).remove();
                            });
                        }
                    } else {
                        // if no data, remove the button as well
                        button.fadeOut('slow', function(){
                            $(this).remove();
                        });
                    }
                    button.on('click', loadMoreMemes);

                })
                .fail(function() {
                    // console.log('LOAD MORE FAILED');
                    button.on('click', loadMoreMemes);
                });
            };
            $loadmorememes.on('click', loadMoreMemes);

            /*
            * Load more Templates in page
            */
            var $loadmore = $grid.next('.mememe-loadmore');

            var loadMoreTemplates = function(e){

                e.preventDefault();
                e.stopPropagation();

                var loadmorebutton = $(e.target);
                loadmorebutton.off('click', loadMoreTemplates);

                var preloadholder = loadmorebutton.find('.preload-mememe');

                preloadholder.html('<i class="immm immm-loading immm-spin"></i>');

                var datapaging = loadmorebutton.data('paging');
                var margin = loadmorebutton.data('margin');

                var loadlist = $grid.parent().find('.mememe-load-inputs').find('input.mememe-load-tpl-list');

                loadlist.each(function(index, val){

                    var $input = $(val); 

                    // ended all items, remove button
                    if (loadlist.length <= datapaging){
                        loadmorebutton.fadeOut(300, function(){
                            $(this).remove();
                        });
                    }
                    // ended group, stop loading
                    if (index === datapaging) {
                       return false;
                    }
                    var imgPath = $input.val();
                    var title = $input.data('title');
                    var link = $input.data('link');
                    var tags = $input.data('tag');
                    var showtitle = 0;
                    if (loadmorebutton.attr('data-showtitle') ) {
                        showtitle = 1;
                    }
                    var item = mememe_gallery_item(title, link, imgPath, margin, tags, showtitle);
                    var $item = $(item);

                    $item.addClass('mememe-hidden');
                    $grid.append( $item );

                    loadImage(imgPath, $item);

                    $input.remove();
                });
                checkFilters();

                loadmorebutton.on('click', loadMoreTemplates);

                setTimeout(function() {
                    preloadholder.html('');
                }, 300);
            };

            $loadmore.on('click', loadMoreTemplates);

            /**
            * Fade in image after loading
            */
            function loadImage(path, $target) {
                var atarget = $target.find('img');
                $(atarget).one('load', function() {
                    setTimeout(function() {
                        $target.removeClass('mememe-hidden');
                        $grid.mememeIsotope( 'appended', $target );
                    }, 100);
                });
            }

            /**
            * Single item
            */
            function mememe_gallery_item(title, link, imgPath, margin, tags, showtitle){

                var item = '<div class="mememe-gallery-item'+ tags +'" style="padding:'+margin+'px;">';
                item += '<div class="mememe-card"><a title="'+title+'" href="'+link+'"><img src="'+imgPath+'"></a>';
                if (showtitle)  {
                    item += '<a class="mememe-card-body" href="'+link+'"><p class="mememe-card-title">'+title+'</p></a>';
                }
                item += '</div></div>';
                return item;
            }

        });
    };
}( jQuery ));

/*
 * MeMeMe - Advanced Meme Generator - WordPress Plugin call functions
 * @requires jQuery >= 1.7.0
 * Depends:
 *  mememe.js
 *  Owl.carousel.js
 */
(function( $ ) {
    'use strict';
    $.fn.mememeGen = function() {
 
        return this.each(function() {

            var memewrapper = $(this);

            // Prevent double initialization
            if (memewrapper.data('mememegen')) {
                return true;
            }

            memewrapper.data('mememegen', true);

            var form_id = memewrapper.data('formid');

            var carousel = memewrapper.find('.mememe-template-list');
            var form = memewrapper.find('form');
            var templatefield = form.find('#mememe_template_' + form_id);

            form.on('submit', function(e){
                e.preventDefault();
            });

            // Init app
            var memeapp = $(this).find('.mememe-app').mememe({
                color: MEMEME.color,
                outcolor: MEMEME.outcolor,
                drawcolor: MEMEME.color,
                bgcolor: MEMEME.bgcolor,
                direction: MEMEME.direction,
                mode : MEMEME.mode,
                placeholder: MEMEME.placeholder,
                finalwidth : parseInt(MEMEME.finalwidth),
                stroke: parseInt(MEMEME.stroke),
                outline: parseInt(MEMEME.outline),
                textposition : MEMEME.textposition,
                watermark : MEMEME.watermark,
                watermarkposition : MEMEME.watermarkposition,
                fonts : MEMEME.fonts,
                onSave : function(data){
                    mememeSubmit(form, data);
                },
                uploader : MEMEME.uploader,
                labels : MEMEME.labels,
                onImageUpdate : function(){
                    templatefield.val('');
                },
                text_box_num: MEMEME.text_box_num,
                show_tools : MEMEME.show_tools,
                textinit : MEMEME.textinit,
                spacer : MEMEME.spacer
            });
            // Init template carousel
            var memeowl = memewrapper.find('.mememe-template-list');
            var autoplayCarousel = memeowl.data('autoplay');
            var $preloader = '<div class="mememe-preloader"><span class="mememe-loader"><span class="mememe-loader-inner"></span></span></div>';
            var $preloaderScreen = '<div class="mememe-preloader"></div>';

            function enableApp(form){

                var wrapper = form.parent('.wrap-mememe');
                var thisapp = wrapper.find('.mememe-app');
                var carousel = wrapper.find('.mememe-template-list');

                carousel.css('opacity', 1);
                form.css('opacity', 1);

                thisapp.find('.mememe-menu').css('opacity', 1);
                thisapp.find('.wrapmememe').css('opacity', 1);
                thisapp.find('.save-mememe').css('opacity', 1);

                var preloaders = wrapper.find('.mememe-preloader');

                preloaders.removeClass('show-mememe');
            }

            function disableApp(form){

                var wrapper = form.parent('.wrap-mememe');
                var thisapp = wrapper.find('.mememe-app');
                var carousel = wrapper.find('.mememe-template-list');

                if (!form.find('.mememe-preloader').length) {
                    form.append($preloaderScreen);
                }
                if (!thisapp.find('.mememe-preloader').length) {
                    thisapp.append($preloader);
                }
                if (!carousel.find('.mememe-preloader').length) {
                    carousel.append($preloaderScreen);
                }

                form.css('opacity', 0.3);
                carousel.css('opacity', 0.3);

                thisapp.find('.wrapmememe').css('opacity', 0.3);
                thisapp.find('.mememe-menu').css('opacity', 0.3);
                thisapp.find('.save-mememe').css('opacity', 0.3);

                var preloaders = wrapper.find('.mememe-preloader');

                preloaders.addClass('show-mememe');
            }

            function mememeSubmit(form, data) {
                var formTitle = form.find('#mememe_post_title_'+ form_id);
                var totaltext = "";
                var editfields = memeapp.find('.dragmememe .mememe-editme');
                editfields.each(function(index){

                    var thisfield = $(this);
                    // var rows = $(this).find('div');
                    var multidivs = thisfield.find('div');
                    var multip = thisfield.find('p');

                    var firstrowtext = thisfield.text();
                    if (multidivs.length) {
                        firstrowtext = thisfield.text().replace(multidivs.text(), '');
                    }

                    if (multip.length) {
                        firstrowtext = thisfield.text().replace(multip.text(), '');
                    }

                    if (firstrowtext.length) {
                        totaltext += firstrowtext;
                    }

                    if (multidivs.length) {
                        multidivs.each(function(){
                            totaltext += " " + $(this).text();
                        });
                    }
                    if (multip.length) {
                        multip.each(function(){
                            totaltext += " " + $(this).text();
                        });
                    }
                    if (editfields.length > 1 && (index+1) < editfields.length) {
                        totaltext += "... ";
                    }
                });

                var withoutSpace = totaltext.replace(/ /g, "");

                if (formTitle.val().length == 0 && withoutSpace.length) {
                    formTitle.val(totaltext);
                }

                // // Disable app editing
                disableApp(form);

                var datastring = form.serialize();

                $.ajax({
                    url: MEMEME.ajax_url,
                    type: 'POST',
                    processData: false,
                    // timeout: 30000,
                    data: datastring + '&action=mememe_process&page_url=' + MEMEME.url + '&mememe_remote_data=' + data + '&mememe_nonce=' + MEMEME.nonce + '&form_id=' + form_id // set partial string, not all var
                })
                .done(function( data ) {

                    if ( undefined !== data.success && false === data.success ) {
                        return;
                    }

                    var result = JSON.parse(data);

                    if ( result.error ) {
                        if ( !form.find('.mememe-alert').length ) {
                            form.append( '<h3 class="mememe-alert">' + result.error + '</h3>' );
                        } else {
                            form.find('.mememe-alert').html( result.error );  
                        }
                        // Re-enable app editing
                        enableApp(form);
                    }
                    // Redirect to result page
                    if ( result.success ) {
                        window.location.replace( result.success );
                    }
                })
                .fail(function( jqXHR, textStatus ) {
                    if ( !form.find('.mememe-alert').length ) {
                        form.append( '<h3 class="mememe-alert">Error: ' + textStatus + '</h3>' );
                    } else {
                        form.find('.mememe-alert').html( 'Error: ' + textStatus );  
                    }
                    enableApp(form);
                });
            }

            // Get image url from id
            function getImageUrl(loadkey) {
                // Ajax call
                return jQuery.ajax({
                    type: "post",
                    url: MEMEME.ajax_url,
                    data: "action=mememe-get-template-link&imgID="+loadkey+"&mememe_nonce="+MEMEME.nonce
                }).done(function( resp ) {
                });
            }

            // Load first image
            if ( memeapp.length ) {

                var objkeys = memeapp.data('templatelist').toString().split(',');
                var loadkey;

                if ( MEMEME.loadimg ) {
                    // load template with query ?mememe_tpl=template-slug
                    loadkey = MEMEME.loadimg
                } else {
                    if ( memeapp.data('loadshortcodeimg') ) {
                        // Load template from shortcode attribute "template=template_ID"
                        loadkey = memeapp.data('loadshortcodeimg');
                    } else {
                        if ( MEMEME.random ) {
                            // load random image from template list
                            loadkey = objkeys[Math.floor(Math.random()*objkeys.length)];
                        } else {
                            // load first image from template list
                            loadkey = objkeys[0];
                        }
                    }
                }

                $.when(getImageUrl(loadkey)).done(function(loadimg){
                    if (loadimg.length) {
                        // templatefield.attr('value', loadkey);
                        templatefield.val(loadkey);
                        memeapp.updateImg(loadimg);
                    } else {
                        console.log('MeMeMe plugin notice: No template found');
                    }
                });
            }

           // $(window).on('load', function(){
                memeowl.owlCarousel({
                    loop:true,
                    margin:0,
                    nav:false,
                    dots: false,
                    items:5,
                    autoplay:autoplayCarousel,
                    autoplayHoverPause: true,
                    navText: ['<i class="immm immm-angle-left"></i>','<i class="immm immm-angle-right"></i>'],
                    responsiveBaseElement: memeapp,
                    // breakpoint from 0 up
                    responsive:{
                        0:{
                            items:4,
                            nav:false
                        },
                        400:{
                            items:5,
                            nav:false
                        },
                        500:{
                            items:6,
                            nav:true
                        },
                        600:{
                            items:7,
                            nav:true
                        },
                        700:{
                            items:8,
                            nav:true
                        }
                    }
                });
                carousel.css('opacity', 1);

                // Load images from templates
                memeowl.find('a').on('click', function(e){
                // $('.mememe-template-list a').on('click', function(e){
                    e.preventDefault();
                    var thisID = $(this).find('img').data('template');
                    $(this).closest('.wrap-mememe').find('form #mememe_template_' + form_id).val(thisID);
                    connectOwl( $(this), e, memeapp, thisID);
                });
           // });

            // Connect carousel to generator
            function connectOwl(object, event, app, thisID){
                event.preventDefault();
                var thisurl = $(object).attr('href');
                var thisImg = $(object).closest('.wrap-mememe').find('.mememe-placeholder img');

                $.when(getImageUrl(thisID)).done(function(loadimg){
                    if (loadimg.length) {
                        // templatefield.attr('value', thisID);
                        templatefield.val(thisID);
                        app.updateImg(loadimg);
                    } else {
                        console.log('MeMeMe plugin error: getImageUrl FAILED');
                    }
                });
            }

        });
    };
}( jQuery ));

jQuery( document ).ready( function ( $ ) {
    'use strict';
    // Init Generator
    $('.wrap-mememe').mememeGen();

    // Init galleries
    $('.mememe-gallery').mememeGallery();

    // Output watermark to redraw in canvas
    if (MEMEME.watermark_image.length) {
        $('<img id="mememe-watermark" style="display:none;" src="'+ MEMEME.watermark_image +'">').on( 'load', function() {
          $(this).appendTo('body');
        });
    }

    // Update gallery and generator for Beaver Builder backend
    $( '.fl-builder-content' ).on( 'fl-builder.layout-rendered', function(){
        $('.wrap-mememe').mememeGen();
        $('.mememe-gallery').mememeGallery();
    });
    
    // Update gallery and generator for Elementor backend
    if ( window.elementorFrontend ) {
        $(window).on('elementor/frontend/init', function(){
            elementorFrontend.hooks.addAction( 'frontend/element_ready/widget', function( $scope ) {
                $('.wrap-mememe').mememeGen();
                $('.mememe-gallery').mememeGallery();
            });
        });
    }

    /*
     * Meme rating
     */
    $(".mememe-post-like .mememe-vote-btn").on( 'click', function(){
        var thumb = $(this);
        var mememe_post_like = thumb.parent('.mememe-post-like');
     
        // Retrieve post ID from data attribute
        var post_id = mememe_post_like.data("post_id");
        var vote = thumb.data("vote");

        // Ajax call
        $.ajax({
            type: "post",
            url: MEMEME.ajax_url,
            data: "action=mememe-post-like&mememe_nonce="+MEMEME.nonce+"&post_like=&post_id="+post_id+'&vote='+vote,
            success: function(count){
                if ( undefined !== count.success && false === count.success ) {
                    return;
                }
                var getcount = JSON.parse(count);
                mememe_post_like.removeClass('mememe-voted-down').removeClass('mememe-voted-up').addClass('mememe-voted-'+vote);
                $(".mememe-count-up span").text(getcount.up);
                $(".mememe-count-down span").text(getcount.down);
            }
        });
        return false;
    });
});
