/*
 * MeMeMe - Advanced Meme Generator - WordPress Plugin
 * Copyright Nicola Franchini - @nicolafranchini
 * Depends:
 *  jquery.ui.autocomplete.js
 */
jQuery(document).ready( function($){
    'use strict';

    /*
     * Set Widget Tags autocomplete
     */
    function mememe_split_tags( val ) {
      return val.split( /,\s*/ );
    }
    function mememe_extractLast_tag( term ) {
      return mememe_split_tags( term ).pop();
    }

    function callMagicMememeTags(){
        var default_tags = MEMEMEwidgets.available_tags;

        $( ".mememe-tag-suggest" ).each(function(){
            var magicinput = $(this);

            // Prevent double initialization
            if (magicinput.data('mememagictags')) {
                return true;
            }

            magicinput
              // don't navigate away from the field on tab when selecting an item
              .on( "keydown", function( event ) {
                if ( event.keyCode === $.ui.keyCode.TAB &&
                    $( this ).autocomplete( "instance" ).menu.active ) {
                  event.preventDefault();
                }
              })
              .autocomplete({
                minLength: 0,
                source: function( request, response ) {
                  // delegate back to autocomplete, but extract the last term
                  response( $.ui.autocomplete.filter(
                    default_tags, mememe_extractLast_tag( request.term ) ) );
                },
                focus: function() {
                  // prevent value inserted on focus
                  return false;
                },
                select: function( event, ui ) {
                  var terms = mememe_split_tags( this.value );
                  // remove the current input
                  terms.pop();
                  // add the selected item
                  terms.push( ui.item.value );
                  // add placeholder to get the comma-and-space at the end
                  terms.push( "" );
                  this.value = terms.join( ", " );
                  return false;
                }
            });

            magicinput.data('mememagictags', true);
        });
    }

    $(document).on('widget-added', function(event, widget) {
        callMagicMememeTags();
    });

    $(document).on('widget-updated', function(event, widget) {
        callMagicMememeTags();
    });

    $(document).on('widget-synced', function(event, widget) {
        callMagicMememeTags();
    });

    callMagicMememeTags();
});
