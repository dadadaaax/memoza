/*
 * MeMeMe - WordPress Plugin
 * version: 1.0
 * Setup shortcode button and modal window
 *  
 * Copyright 2017-2018 Nicola Franchini - @nicolafranchini
 */
(function($) {
	var cats = $.parseJSON( MEMEMEadmin.categories );
	var cats_active = [ { text: MEMEMEadmin._all_categories, value : 0 } ];

	$.each( cats, function( index, value ) {
		cats_active.push({ text: value, value : index });
	});

    var thumbsize = $.parseJSON( MEMEMEadmin.thumbsize );
    var thumbsize_active = [];

    $.each( thumbsize, function( index, value ) {
        thumbsize_active.push({ text: value, value : index });
    });

	var mememe_columns = [ 
		{ text: MEMEMEadmin._responsive, value : 0 },
		{ text: '1', value : 1 },
		{ text: '2', value : 2 },
		{ text: '3', value : 3 },
		{ text: '4', value : 4 },
		{ text: '5', value : 5 },
		{ text: '6', value : 6 },
		{ text: '7', value : 7 },
		{ text: '8', value : 8 },
		{ text: '9', value : 9 },
		];
    var mememe_order = [
        { text: 'DESC', value : 0 },
        { text: 'ASC', value : 'ASC' },
    ];
    var mememe_orderby = [
        { text: 'Date', value : 0 },
        { text: 'Title', value : 'title' },
        { text: 'Random', value : 'rand' },
        // { text: 'ID', value : 'ID' },
    ];

    tinymce.PluginManager.add('mememe_shortcode', function( editor, url ) {
        editor.addButton( 'mememe_shortcode', {
            title: 'MeMeMe',
            type: 'menubutton',
            image: url + '/images/mememe-icon.png',
            menu: [
                {
                    text: MEMEMEadmin._generator,
                    onclick: function() {

                        var win = editor.windowManager.open( {
                            width : 570,
                            height: 200,
                            title: MEMEMEadmin._generator,
                            body: [
                            {
                                type   : 'checkbox',
                                name   : 'mememe_carousel',
                                label  : MEMEMEadmin._hide_carousel,
                                // text   : MEMEMEadmin._hide_carousel,
                                onclick : function(){
                                    if (this.checked()) {
                                        tinyMCE.DOM.setStyle( 'mememe_carousel_limit','display', 'none');
                                        tinyMCE.DOM.setStyle( 'mememe_carousel_limit-l','display', 'none');
                                        tinyMCE.DOM.setStyle( 'mememe_random_carousel','display', 'none');
                                        tinyMCE.DOM.setStyle( 'mememe_random_carousel-l','display', 'none');
                                        tinyMCE.DOM.setStyle( 'mememe_autoplay_carousel','display', 'none');
                                        tinyMCE.DOM.setStyle( 'mememe_autoplay_carousel-l','display', 'none');
                                    } else {
                                        tinyMCE.DOM.setStyle( 'mememe_carousel_limit','display', 'block');
                                        tinyMCE.DOM.setStyle( 'mememe_carousel_limit-l','display', 'block');
                                        tinyMCE.DOM.setStyle( 'mememe_random_carousel','display', 'block');
                                        tinyMCE.DOM.setStyle( 'mememe_random_carousel-l','display', 'block');
                                        tinyMCE.DOM.setStyle( 'mememe_autoplay_carousel','display', 'block');
                                        tinyMCE.DOM.setStyle( 'mememe_autoplay_carousel-l','display', 'block');
                                    }
                                },
                            },
                            {
                                type   : 'textbox',
                                subtype: 'number',
                                name   : 'mememe_carousel_limit',
                                id     : 'mememe_carousel_limit',
                                label  : MEMEMEadmin._max_templates,
                                value : 0 
                            },
                            {
                                type   : 'checkbox',
                                name   : 'mememe_random_carousel',
                                id     : 'mememe_random_carousel',
                                label   : MEMEMEadmin._random_templates,
                                // checked : true
                            },
                            {
                                type   : 'checkbox',
                                name   : 'mememe_autoplay_carousel',
                                id     : 'mememe_autoplay_carousel',
                                label   : MEMEMEadmin._autoplay_carousel,
                            }
                            ],
                            onsubmit: function( e ) {
                                var carousel = '';
                                var random = '';
                                var limit = '';
                                var autoplay = '';

                                if (e.data.mememe_carousel) {
                                    carousel = ' nocarousel="1"';
                                } else {
                                    if (e.data.mememe_random_carousel) {
                                        random = ' random="1"';
                                    }
                                    if (e.data.mememe_carousel_limit > 0) {
                                        limit = ' limit="' + Math.round(Math.abs(e.data.mememe_carousel_limit)) + '"';
                                    }
                                    if (e.data.mememe_autoplay_carousel) {
                                        autoplay = ' autoplay="1"';
                                    }
                                }
                                editor.insertContent( '[mememe' + carousel + limit + random + autoplay + ']' );
                            }
                        });
                    }
                },
                {
                    text: MEMEMEadmin._gallery,
                    onclick: function() {
                        editor.windowManager.open( {
                        	width : 570,
                        	height: 380,
                            title: MEMEMEadmin._list_memes,
                            body: [
                            {
                                type   : 'listbox',
                                name   : 'category',
                                label  : MEMEMEadmin._category,
                                values : cats_active,
                            },
                            {
                                type   : 'listbox',
                                name   : 'thumbsize',
                                label  : MEMEMEadmin._thumbnail_size,
                                values : thumbsize_active,
                                value  : 'mememe-thumb'
                            },
                            {
                                type   : 'textbox',
                                subtype: 'number',
                                name   : 'margin',
                                label  : MEMEMEadmin._margin,
                                value  : 0
                            },
                            {
                                type   : 'textbox',
                                subtype: 'number',
                                name   : 'per_page',
                                label  : MEMEMEadmin._posts_per_page,
                                value  : MEMEMEadmin.per_page_default
                            },
                            {
			                    type   : 'listbox',
			                    name   : 'columns',
			                    label  : MEMEMEadmin._columns,
			                    values : mememe_columns,
			                    value  : 0 // Sets the default
                            },
                            // {
                            //     type   : 'listbox',
                            //     name   : 'orderby',
                            //     label  : MEMEMEadmin._orderby,
                            //     values : mememe_orderby,
                            //     value  : 'date'
                            // },
                            // {
                            //     type   : 'listbox',
                            //     name   : 'order',
                            //     label  : MEMEMEadmin._order,
                            //     values : mememe_order,
                            //     value  : 'DESC'
                            // },
                            {
                                type   : 'checkbox',
                                name   : 'mememe_author',
                                id     : 'mememe_author',
                                label   : MEMEMEadmin._author,
                                // checked : true
                            },
                            {
                                type   : 'textbox',
                                name   : 'customclass',
                                label  : MEMEMEadmin._custom_class,
                            }
                            ],
                            onsubmit: function( e ) {

                            	var cat = '';
                            	if (e.data.category) {
                            		cat = ' category="' + e.data.category + '"';
                            	}

                            	var cols = '';
                            	if (e.data.columns) {
                            		cols = ' columns="' + e.data.columns + '"';
                            	}

                                // var ord = '';
                                // if (e.data.order) {
                                //     ord = ' order="' + e.data.order + '"';
                                // }

                                // var ordby = '';
                                // if (e.data.orderby) {
                                //     ordby = ' orderby="' + e.data.orderby + '"';
                                // }

                                var perpage = '';
                                if (e.data.per_page != MEMEMEadmin.per_page_default) {
                                    perpage = ' per_page="' + e.data.per_page + '"';
                                }

                                var thumbsize = '';
                                if (e.data.thumbsize != 'mememe-thumb') {
                                    thumbsize = ' thumbsize="' + e.data.thumbsize + '"';
                                }

                                var margin = '';
                                if (e.data.margin != 0) {
                                    margin = ' margin="' + e.data.margin + '"';
                                }

                                var customclass = '';
                                if (e.data.customclass) {
                                    customclass = ' class="' + e.data.customclass + '"';
                                }

                                var memauthor = '';
                                if (e.data.mememe_author) {
                                    memauthor = ' author="1"';
                                }

                                editor.insertContent( '[mememe-list' + cat + cols + perpage + thumbsize + margin + customclass + memauthor + ']');
                            }
                        });
                    }
                },
                {
                    text: MEMEMEadmin._templates,
                    onclick: function() {

                        editor.windowManager.open( {
                        	width : 570,
                        	height: 380,
                            title: MEMEMEadmin._list_templates,
                            body: [
                            {
			                    type   : 'listbox',
			                    name   : 'columns',
			                    label  : MEMEMEadmin._columns,
			                    values : mememe_columns,
			                    value : 0 // Sets the default
                            },
                            {
                                type   : 'textbox',
                                subtype: 'number',
                                name   : 'paginate',
                                label  : MEMEMEadmin._posts_per_page,
                                value  : 0
                            },
                            {
                                type   : 'listbox',
                                name   : 'thumbsize',
                                label  : MEMEMEadmin._thumbnail_size,
                                values : thumbsize_active,
                                value  : 'thumbnail'
                            },
                            {
                                type   : 'textbox',
                                subtype: 'number',
                                name   : 'margin',
                                label  : MEMEMEadmin._margin,
                                value  : 0
                            },
                            {
                                type   : 'checkbox',
                                name   : 'mememe_show_title',
                                id     : 'mememe_show_title',
                                label   : MEMEMEadmin._show_title,
                                // checked : true
                            },
                            {
                                type   : 'checkbox',
                                name   : 'random',
                                id     : 'random',
                                label   : MEMEMEadmin._random_templates,
                                // text   : 'Random',
                                // checked : true
                            },
                            {
                                type   : 'textbox',
                                name   : 'customclass',
                                label  : MEMEMEadmin._custom_class,
                            }
                            ],
                            onsubmit: function( e ) {

                            	var cols = '';
                            	if (e.data.columns) {
                            		cols = ' columns="' + e.data.columns + '"';
                            	}

                                var paginate = '';
                                if (e.data.paginate != 0) {
                                    paginate = ' paginate="' + e.data.paginate + '"';
                                }
                                
                                var thumbsize = '';
                                if (e.data.thumbsize != 'thumbnail') {
                                    thumbsize = ' thumbsize="' + e.data.thumbsize + '"';
                                }
                                
                                var margin = '';
                                if (e.data.margin != 0) {
                                    margin = ' margin="' + e.data.margin + '"';
                                }
                                
                                var customclass = '';
                                if (e.data.customclass) {
                                    customclass = ' class="' + e.data.customclass + '"';
                                }
                                
                                var showTitle = '';
                                if (e.data.mememe_show_title) {
                                    showTitle = ' title="1"';
                                }
                                
                                var random = '';
                                if (e.data.random) {
                                    random = ' random="1"';
                                }
                                editor.insertContent( '[mememe-templates' + cols + paginate + thumbsize + margin + customclass + showTitle + random + ']' );
                            }
                        });
                    }
                },
			]
        });
    });
})(jQuery);
