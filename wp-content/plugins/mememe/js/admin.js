/*
 * MeMeMe - Advanced Meme Generator - WordPress Plugin
 * Copyright Nicola Franchini - @nicolafranchini
 * Depends:
 *  jquery.ui.autocomplete.js
 *  jquery.ui.tabs.js
 */
jQuery(document).ready( function($){
    'use strict';
    /*
     * Settings tabs
     */
    $('.cmb2-metabox').each( function() {
        var tabs = $(this).find('.tabme');

        if (tabs.length > 1) {
            var metabox = $(this),
            nav = $('<ul class="mememe-tab-nav" style="margin:1em 0;" />');
                    
            tabs.each( function(index) {
                nav.append('<li><a class="mememe-nav-tab" href="#' + metabox.attr('id') + '-tab-' + index + '">' + $(this).find('.cmb2-metabox-title').text() + '</a></li>');

                $(this).nextUntil('.tabme').addBack().wrapAll('<div id="' + metabox.attr('id') + '-tab-' + index + '" class="tab" />');
           });

           $(this).prepend(nav);
           nav.after('<div style="clear:both"></div>');

           $(this).tabs();
        }
    });

    var fontselectors = '#mememe_option_font_repeat input[type="text"]';
    var titleselectors = '#mememe_option_font_repeat input[type="text"], #mememe_option_custom_font_repeat input[type="text"]';
    /*
     * Set Google fonts autocomplete
     */
    function callMagic( data ){
        var parsed = JSON.parse(data);
        if ( parsed.error ) {
          $('#mememe_option_google_api').focus();
            console.log( parsed.error );
          return false;
        }

        var parsedsource = $.map(parsed, function (key) {
          return key.family;
        });
        
        parsedsource.sort();

        // search inside Google Fonts library
        $(document).on('keydown.autocomplete', fontselectors, function() {
            $(this).autocomplete({
                autoFocus: true,
                // minLength: 2,
                source: function( request, response ) {
                    var matches = $.map( parsedsource, function(parsedsource) {
                        if ( parsedsource.toLowerCase().indexOf(request.term.toLowerCase()) === 0 ) {
                          return parsedsource;
                        }
                    });
                    response(matches);
                },

                select: function(event,ui){ 
                    $(this).closest('.postbox').find('.cmb-group-title').text(ui.item.label);
                },
                // set first selected item on enter
                change: function (event, ui) {
                    if (ui.item == null){ 
                        // here is null if entered value is not match in suggestion list
                        $(this).val('');
                    }
                }
            });
        });
    }

    /*
     * Get Google fonts list.
     */
    $.ajax({
        url: ajaxurl,
        type: 'POST',
        data:{
            action: 'mememe_get_google_font_lib' // function in admin.php that will be triggered
        },
        success: function( data ){
            callMagic( data );
        }
    });

    /*
     * Update block title.
     */
    function updateTitles(){
        // Update box title on font selection
        $(titleselectors).each(function(){
            if ($(this).val().length) {
              $(this).closest('.postbox').find('.cmb-group-title').text($(this).attr('value'));
            }
        });
    }

    $("#mememe_option_font_repeat, #mememe_option_custom_font_repeat").on({
        // cmb2_add_row: function() {
        //     updateTitles();
        // },
        cmb2_remove_row: function(e) {
            setTimeout(updateTitles, 500)
        },
        cmb2_shift_rows_complete: function() {
            updateTitles();
        }
    });

    updateTitles();

    /*
     * Clean blank spaces around the google api key field.
     */
    $(document).on('focusout input', '#mememe_option_google_api', function(){
        var trimmed = $.trim($(this).val());
        $(this).val(trimmed);
    });

});
