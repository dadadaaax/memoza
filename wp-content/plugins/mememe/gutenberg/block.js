( function( blocks, element, blockEditor, components ) {
    var el = element.createElement;
    const registerBlockType = blocks.registerBlockType;
    const InspectorControls = blockEditor.InspectorControls;
        
    const TextControl = components.TextControl,
        PanelBody = components.PanelBody,
        SelectControl = components.SelectControl,
        CheckboxControl = components.CheckboxControl,
        ToggleControl = components.ToggleControl,
        Placeholder = components.Placeholder,
        FormTokenField = components.FormTokenField;

    const iconSvg = "M0,0v20h20V0H0z M17.8,17.8H2.2V2.2h15.5V17.8z M16.4,4H3.6v1h12.8V4z M4.6,13.5l2,2l4-4l-2-2 L4.6,13.5z M4.1,14l-0.5,2.5L6.1,16L4.1,14z M9.1,8.9l2,2l1-1l-2-2L9.1,8.9z";
    const iconEl = el('svg', { width: 20, height: 20, fill: '#555d66' },
            el('path', { d: iconSvg })
        );

    const iconSvglist = "M0,0v20h20V0H0z M17.8,17.8H2.2V2.2h15.5V17.8z M15,10.4c0,2.8-2.2,5-5,5s-5-2.2-5-5h1 c0,2.2,1.8,4,4,4s4-1.8,4-4H15z M8.4,7.3c0,0.7-0.6,1.3-1.3,1.3S5.8,8,5.8,7.3S6.4,6,7.1,6S8.4,6.6,8.4,7.3z M14.2,7.3 c0,0.7-0.6,1.3-1.3,1.3S11.6,8,11.6,7.3S12.2,6,12.9,6S14.2,6.6,14.2,7.3z";
    const iconList = el('svg', { width: 20, height: 20, fill: '#555d66' },
            el('path', { d: iconSvglist })
        );

    const iconSvgtemplate = "M0,0h5v7H0V0z M0,14h5V9H0V14z M0,20h5v-4H0V20z M7,12h6V6H7V12z M7,4h6V0H7V4z M7,20h6v-6H7V20z M15,20h5v-2h-5V20z M15,16h5V9h-5V16z M15,0v7h5V0H15z";
    const iconTemplate = el('svg', { width: 20, height: 20, fill: '#555d66' },
            el('path', { d: iconSvgtemplate })
        );

    var thumbsize_active = [];
    var thumbsize = JSON.parse( MEMEMEadmin.thumbsize );

    Object.keys(thumbsize).forEach(function(index) {
        thumbsize_active.push({ label: thumbsize[index], value : index });
    });

    const mememe_columns = [
        { label: MEMEMEadmin._responsive, value: 0 },
        { label: '1', value: 1 },
        { label: '2', value: 2 },
        { label: '3', value: 3 },
        { label: '4', value: 4 },
        { label: '5', value: 5 },
        { label: '6', value: 6 },
        { label: '7', value: 7 },
        { label: '8', value: 8 },
        { label: '9', value: 9 }
    ];

    const mememe_order = [
        { label: 'DESC', value: 0 },
        { label: 'ASC', value: 'ASC' }
    ];

    const mememe_orderby = [
        { label: 'Date', value: 0 },
        { label: 'Title', value: 'title' },
        { label: 'Random', value: 'rand' }
    ];

    /**
     * *****************************************************************************
     *                                  MEME GENERATOR
     * *****************************************************************************
    */

    var templates_active = [];
    var templates_active = [ { label: '--', value : '' } ];

    var templates = JSON.parse( MEMEMEadmin.templates );

    Object.keys(templates).forEach(function(index) {
        templates_active.push({ label: templates[index], value : index });
    });

    registerBlockType( 'mememe/mememe-block', {
        title: 'MeMeMe ' + MEMEMEadmin._generator,
        icon: iconEl,
        category: 'widgets', // common, formatting, layout, widgets
        // Remove to make block editable in HTML mode.
        supportHTML: false,

        attributes: {
            content: {
                type: 'array',
                source: 'children',
                selector: 'div',
            },
            nocarousel: { type: 'integer', default: 0 },
            random: { type: 'integer', default: 0 },
            autoplay: { type: 'integer', default: 0 },
            limit: { type: 'string', default: 0 },
            tags: { type: 'array', default: [] },
            template: { type: 'string', default: '' },
        },

        /**
         * Called when Gutenberg initially loads the block.
         */
        edit: function( props ) {
            
            var mememe_nocarousel = props.attributes.nocarousel || 0,
            tags = props.attributes.tags || [],
            mememe_random_carousel = props.attributes.random || 0,
            mememe_autoplay_carousel = props.attributes.autoplay || 0,
            mememe_carousel_limit = props.attributes.limit || 0,
            mememe_template_init = props.attributes.template || 0;
            return [

                el(
                    Placeholder, 
                    {
                        key: 'placeholder',
                        icon: el('span', { className: 'dashicon' }, iconEl ),
                        label: "MeMeMe " + MEMEMEadmin._generator
                    },
                ),

                el(
                    InspectorControls,
                    {key: 'mememe-block-controls'},
    
                    el(
                        PanelBody, {},
                        // {
                        //  title: 'Panel Title',
                        //  initialOpen: true
                        // },
                        // el('hr'),
                        el(
                            ToggleControl,
                            {
                                label: MEMEMEadmin._hide_carousel,
                                help: ' ',
                                checked: mememe_nocarousel,
                                onChange: function(mememe_nocarousel) {
                                    var state = mememe_nocarousel ? 1 : 0;
                                    props.setAttributes({nocarousel: state});
                                }
                            }
                        ),
                        el(
                            ToggleControl,
                            {
                                label: MEMEMEadmin._random_templates,
                                help: ' ',
                                checked: mememe_random_carousel,
                                onChange: function(mememe_random_carousel) {
                                    var state = mememe_random_carousel ? 1 : 0;
                                    props.setAttributes({random: state});
                                }
                            }
                        ),
                        el(
                            TextControl,
                            {
                                label: MEMEMEadmin._max_templates,
                                type: 'number',
                                value: mememe_carousel_limit,
                                onChange: function(limit) {
                                    var state = limit ? limit : 0;
                                    props.setAttributes({limit: state});
                                }
                            }
                        ),
                        el(
                            ToggleControl,
                            {
                                label: MEMEMEadmin._autoplay_carousel,
                                help: ' ',
                                checked: mememe_autoplay_carousel,
                                onChange: function(mememe_autoplay_carousel) {
                                    var state = mememe_autoplay_carousel ? 1 : 0;
                                    props.setAttributes({autoplay: state});
                                }
                            }
                        ),
                        el(
                            FormTokenField,
                            {
                                label: MEMEMEadmin._tags_help,
                                value: tags,
                                onChange: function(tags) {
                                    var state = tags ? tags : [];
                                    props.setAttributes({tags: state});
                                },
                                suggestions: MEMEMEadmin.available_tags
                            }
                        ),
                        el(
                            SelectControl,
                            {
                                label: MEMEMEadmin._default_template,
                                help : ' ',
                                options: templates_active,
                                value: mememe_template_init,
                                onChange: function(template) {
                                    var state = template ? template : 0;
                                    props.setAttributes({template: state});
                                }
                            }
                        )
                    ) // PanelBody
                ) // InspectorControls
            ]; // return 
        }, // edit

        transforms: {
            from: [
                {
                    type: 'shortcode',
                    // Shortcode tag can also be an array of shortcode aliases
                    tag: 'mememe',
                    attributes: {
                        nocarousel: { 
                            type: 'integer', 
                            shortcode: function( obj ) {
                                var nocarousel = obj.named.nocarousel ? obj.named.nocarousel : 0;
                                return nocarousel;
                            }
                        },
                        random: { 
                            type: 'integer', 
                            shortcode: function( obj ) {
                                var random = obj.named.random ? obj.named.random : 0;
                                return random;
                            }
                        },
                        limit: { 
                            type: 'string', 
                            shortcode: function( obj ) {
                                var limit = obj.named.limit ? obj.named.limit : 0;
                                return limit;
                            }
                        },
                        autoplay: { 
                            type: 'integer', 
                            shortcode: function( obj ) {
                                var autoplay = obj.named.autoplay ? obj.named.autoplay : 0;
                                return autoplay;
                            }
                        },
                        tags: { 
                            type: 'array', 
                            shortcode: function( obj ) {
                                var tags = obj.named.tags ? obj.named.tags : [];
                                return tags;
                            }
                        },
                        template: { 
                            type: 'string', 
                            shortcode: function( obj ) {
                                var template = obj.named.template ? obj.named.template : 0;
                                return template;
                            }
                        },
                    }
                }
            ]
        },

        /**
         * Called when Gutenberg "saves" the block to post_content
         */
        save: function (props) {

            var mememe_nocarousel = props.attributes.nocarousel || 0,
            tags = props.attributes.tags || '',
            mememe_random_carousel = props.attributes.random || 0,
            mememe_autoplay_carousel = props.attributes.autoplay || 0,
            mememe_template_init = props.attributes.template || 0,
            mememe_carousel_limit = props.attributes.limit || 0;

            return el('div', {
            }, '[mememe nocarousel="'+mememe_nocarousel
            +'" random="'+mememe_random_carousel
            +'" autoplay="'+mememe_autoplay_carousel
            +'" template="'+mememe_template_init
            +'" tags="'+tags
            +'" limit="'+mememe_carousel_limit+'"]' )
        },

        deprecated: [
            {
                attributes: {
                    content: {
                        type: 'array',
                        source: 'children',
                        selector: 'p',
                    },
                    nocarousel: { type: 'integer', default: 0 },
                    random: { type: 'integer', default: 0 },
                    limit: { type: 'string', default: 0 }
                },

                save: function (props) {

                    var mememe_nocarousel = props.attributes.nocarousel || 0,
                    mememe_random_carousel = props.attributes.random || 0,
                    mememe_carousel_limit = props.attributes.limit || 0;

                    return el('div', {
                    }, '[mememe nocarousel="'+mememe_nocarousel
                    +'" random="'+mememe_random_carousel
                    +'" limit="'+mememe_carousel_limit+'"]' )
                },
            },
            {
                attributes: {
                    content: {
                        type: 'array',
                        source: 'children',
                        selector: 'div',
                    },
                    nocarousel: { type: 'integer', default: 0 },
                    random: { type: 'integer', default: 0 },
                    tags: { type: 'array', default: [] },
                    limit: { type: 'string', default: 0 }
                },

                save: function (props) {

                    var mememe_nocarousel = props.attributes.nocarousel || 0,
                    tags = props.attributes.tags || '',
                    mememe_random_carousel = props.attributes.random || 0,
                    mememe_carousel_limit = props.attributes.limit || 0;

                    return el('div', {
                    }, '[mememe nocarousel="'+mememe_nocarousel
                    +'" random="'+mememe_random_carousel
                    +'" tags="'+tags
                    +'" limit="'+mememe_carousel_limit+'"]' )
                },
            },
            {
                attributes: {
                    content: {
                        type: 'array',
                        source: 'children',
                        selector: 'div',
                    },
                    nocarousel: { type: 'integer', default: 0 },
                    random: { type: 'integer', default: 0 },
                    limit: { type: 'string', default: 0 },
                    tags: { type: 'array', default: [] },
                    template: { type: 'string', default: '' },
                },

                save: function (props) {

                    var mememe_nocarousel = props.attributes.nocarousel || 0,
                    tags = props.attributes.tags || '',
                    mememe_random_carousel = props.attributes.random || 0,
                    mememe_template_init = props.attributes.template || 0,
                    mememe_carousel_limit = props.attributes.limit || 0;

                    return el('div', {
                    }, '[mememe nocarousel="'+mememe_nocarousel
                    +'" random="'+mememe_random_carousel
                    +'" template="'+mememe_template_init
                    +'" tags="'+tags
                    +'" limit="'+mememe_carousel_limit+'"]' )
                },
            }
        ]
    });

    /**
     * *****************************************************************************
     *                                  MEME GALLERY mememe-list
     * *****************************************************************************
    */
    var cats_active = [ { label: MEMEMEadmin._all_categories, value : 0 } ];    
    var cats = JSON.parse( MEMEMEadmin.categories );

    Object.keys(cats).forEach(function(index) {
        cats_active.push({ label: cats[index], value : index });
    });


    var thumbsize_active = [];
    var thumbsize = JSON.parse( MEMEMEadmin.thumbsize );

    Object.keys(thumbsize).forEach(function(index) {
        thumbsize_active.push({ label: thumbsize[index], value : index });
    });

    registerBlockType( 'mememe/mememe-list-block', {
        title: 'MeMeMe Gallery',
        icon: iconList,
        // icon: 'smiley',

        category: 'widgets', // common, formatting, layout, widgets

        // Remove to make block editable in HTML mode.
        supportHTML: false,

        attributes: {
            content: {
                type: 'array',
                source: 'children',
                selector: 'div',
            },

            category: { type: 'string', default: 0 },
            thumbsize: { type: 'string', default: 'mememe-thumb' },
            per_page: { type: 'string', default: MEMEMEadmin.per_page_default },
            margin: { type: 'string', default: 0 },
            columns: { type: 'string', default: 0 },
            mememe_author: { type: 'integer', default: 0 },
            // random: { type: 'integer', default: 0 },
            filters: { type: 'integer', default: 0 },
            customclass: { type: 'string', default: '' },
            style_shadow: { type: 'boolean', default: false },
            style_rounded: { type: 'boolean', default: false },
            style_dark: { type: 'boolean', default: false },
            style_frame: { type: 'boolean', default: false },
            style_card: { type: 'boolean', default: false },
            theclass: { type: 'array', default: [] }
        },

        /**
         * Called when Gutenberg initially loads the block.
         */
        edit: function( props ) {
            
            var category = props.attributes.category || 0,
            thumbsize = props.attributes.thumbsize || 'mememe-thumb',
            margin = props.attributes.margin || 0,
            per_page = props.attributes.per_page || MEMEMEadmin.per_page_default,
            columns = props.attributes.columns || 0,
            mememe_author = props.attributes.mememe_author || 0,
            // random = props.attributes.random || 0,
            filters = props.attributes.filters || 0,
            customclass = props.attributes.customclass || '',
            style_shadow = props.attributes.style_shadow || false,
            style_rounded = props.attributes.style_rounded || false,
            style_dark = props.attributes.style_dark || false,
            style_frame = props.attributes.style_frame || false,
            style_card = props.attributes.style_card || false,
            theclass = customclass.split(' ');
            theclass = theclass.filter(entry => entry.trim() != '');

            function onChangelist_col(argument) {
                props.setAttributes({columns: argument});
            }
            function onChangelist_cat(argument) {
                props.setAttributes({category: argument});
            }
            function onChangelist_thumb(argument) {
                props.setAttributes({thumbsize: argument});
            }
            function onChangepaginate(argument) {
                props.setAttributes({per_page: argument});
            }
            function onChangemargin(argument) {
                props.setAttributes({margin: argument});
            }

            function updateStyle(update, classe, remove) {
                // Find and remove item from an array
                var i = theclass.indexOf(classe);
                if (i != -1) {
                    theclass.splice(i, 1);
                }

                if (update) {
                    if (remove) {
                        var j = theclass.indexOf(remove);
                        if (j != -1) {
                            theclass.splice(j, 1);
                        }
                    }
                    theclass.push(classe); 
                }
                props.setAttributes({customclass: theclass.join(" ")});
            }

            return [
                el(
                    Placeholder, 
                    {
                        key: 'placeholder',
                        icon: el('span', { className: 'dashicon' }, iconList),
                        label: MEMEMEadmin._list_memes
                    },
                ),
                el(
                    InspectorControls,
                    {key: 'mememe-list-block-controls'},
                    el(
                        PanelBody, {},
                        el(
                            TextControl,
                            {
                                label: MEMEMEadmin._posts_per_page,
                                help : ' ',
                                type: 'number',
                                value: per_page,
                                onChange: onChangepaginate
                            }
                        ),
                        el(
                            SelectControl,
                            {
                                label: MEMEMEadmin._columns,
                                help : ' ',
                                value: columns,
                                options: mememe_columns,
                                onChange: onChangelist_col
                            }
                        ),
                        el(
                            SelectControl,
                            {
                                label: MEMEMEadmin._thumbnail_size,
                                help : ' ',
                                value: thumbsize,
                                options: thumbsize_active,
                                onChange: onChangelist_thumb
                            }
                        ),
                        el(
                            TextControl,
                            {
                                label: MEMEMEadmin._margin,
                                type: 'number',
                                help : ' ',
                                value: margin,
                                onChange: onChangemargin,
                            }
                        ),
                        el(
                            ToggleControl,
                            {
                                label: MEMEMEadmin._filters,
                                help: ' ',
                                checked: filters,
                                onChange: function(filters) {
                                    var state = filters ? 1 : 0;
                                    props.setAttributes({filters: state});
                                }
                            }
                        ),
                        el(
                            SelectControl,
                            {
                                label: MEMEMEadmin._category,
                                help : ' ',
                                value : category,
                                options: cats_active,
                                onChange: onChangelist_cat
                            }
                        ),
                        // el(
                        //  ToggleControl,
                        //  {
                        //      label: MEMEMEadmin._random_memes,
                        //      help: ' ',
                        //      checked: random,
                        //      onChange: function(random) {
                        //          var state = random ? 1 : 0;
                        //          props.setAttributes({random: state});
                        //      }
                        //  }
                        // ),
                        el(
                            ToggleControl,
                            {
                                label: MEMEMEadmin._author,
                                help: ' ',
                                checked: mememe_author,
                                onChange: function(mememe_author) {
                                    var state = mememe_author ? 1 : 0;
                                    props.setAttributes({mememe_author: state});
                                }
                            }
                        ),
                        el('p', {}, MEMEMEadmin._style ),
                        el(
                            CheckboxControl,
                            {
                                label: 'Card',
                                checked: style_card,
                                onChange: function(style_card) {
                                    updateStyle(style_card, 'mmm-card', 'mmm-frame');
                                    if (style_card) {
                                        props.setAttributes({style_frame: false});
                                    }
                                    props.setAttributes({style_card: style_card});
                                }
                            }
                        ),
                        el(
                            CheckboxControl,
                            {
                                label: 'Frame',
                                checked: style_frame,
                                onChange: function(style_frame) {
                                    updateStyle(style_frame, 'mmm-frame', 'mmm-card');
                                    if (style_frame) {
                                        props.setAttributes({style_card: false});
                                    }
                                    props.setAttributes({style_frame: style_frame});
                                }
                            }
                        ),
                        el(
                            CheckboxControl,
                            {
                                label: 'Dark',
                                checked: style_dark,
                                onChange: function(style_dark) {
                                    updateStyle(style_dark, 'mmm-dark');
                                    props.setAttributes({style_dark: style_dark});
                                }
                            }
                        ),
                        el(
                            CheckboxControl,
                            {
                                label: 'Rounded',
                                checked: style_rounded,
                                onChange: function(style_rounded) {
                                    updateStyle(style_rounded, 'mmm-rounded');
                                    props.setAttributes({style_rounded: style_rounded});
                                }
                            }
                        ),
                        el(
                            CheckboxControl,
                            {
                                label: 'Shadow',
                                checked: style_shadow,
                                onChange: function(style_shadow) {
                                    updateStyle(style_shadow, 'mmm-shadow');
                                    props.setAttributes({style_shadow: style_shadow});
                                }
                            }
                        )
                    ) // PanelBody
                ) // InspectorControls
            ]; // return 
        }, // edit

        transforms: {
            from: [
                {
                    type: 'shortcode',
                    tag: 'mememe-list',
                    attributes: {
                        category: { 
                            type: 'string', 
                            shortcode: function( obj ) {
                                var category = obj.named.category ? obj.named.category : 0;
                                return category;
                            }
                        },
                        thumbsize: { 
                            type: 'string', 
                            shortcode: function( obj ) {
                                var thumbsize = obj.named.thumbsize ? obj.named.thumbsize : 'mememe-thumb';
                                return thumbsize;
                            }
                        },
                        per_page: { 
                            type: 'string', 
                            shortcode: function( obj ) {
                                var per_page = obj.named.per_page ? obj.named.per_page : MEMEMEadmin.per_page_default;
                                return per_page;
                            }
                        },
                        margin: { 
                            type: 'string', 
                            shortcode: function( obj ) {
                                var margin = obj.named.margin ? obj.named.margin : 0;
                                return margin;
                            }
                        },
                        columns: { 
                            type: 'string', 
                            shortcode: function( obj ) {
                                var columns = obj.named.columns ? obj.named.columns : 0;
                                return columns;
                            }
                        },
                        mememe_author: { 
                            type: 'integer', 
                            shortcode: function( obj ) {
                                var mememe_author = obj.named.author ? obj.named.author : 0;
                                return mememe_author;
                            }
                        },
                        // random: { 
                        //     type: 'integer', 
                        //     shortcode: function( obj ) {
                        //         var mememe_author = obj.named.random ? obj.named.random : 0;
                        //         return random;
                        //     }
                        // },
                        filters: { 
                            type: 'integer', 
                            shortcode: function( obj ) {
                                var filters = obj.named.filters ? obj.named.filters : 0;
                                return filters;
                            }
                        },
                        customclass: { 
                            type: 'string', 
                            shortcode: function( obj ) {
                                var customclass = obj.named.class ? obj.named.class : '';
                                return customclass;
                            }
                        },
                        style_card: { 
                            type: 'boolean', 
                            shortcode: function( obj ) {
                                var style_card = (obj.named.class && obj.named.class.indexOf('mmm-card') !== -1) ? 1 : 0;
                                return style_card;
                            }
                        },
                        style_dark: { 
                            type: 'boolean', 
                            shortcode: function( obj ) {
                                var style_dark = (obj.named.class && obj.named.class.indexOf('mmm-dark') !== -1) ? 1 : 0;
                                return style_dark;
                            }
                        },
                        style_frame: { 
                            type: 'boolean', 
                            shortcode: function( obj ) {
                                var style_frame = (obj.named.class && obj.named.class.indexOf('mmm-frame') !== -1) ? 1 : 0;
                                return style_frame;
                            }
                        },
                        style_rounded: { 
                            type: 'boolean', 
                            shortcode: function( obj ) {
                                var style_rounded = (obj.named.class && obj.named.class.indexOf('mmm-rounded') !== -1) ? 1 : 0;
                                return style_rounded;
                            }
                        },
                        style_shadow: { 
                            type: 'boolean', 
                            shortcode: function( obj ) {
                                var style_shadow = (obj.named.class && obj.named.class.indexOf('mmm-shadow') !== -1) ? 1 : 0;
                                return style_shadow;
                            }
                        }
                    },
                },
            ]
        },

        /**
         * Called when Gutenberg "saves" the block to post_content
         */
        save: function (props) {

            var category = props.attributes.category,
            thumbsize = props.attributes.thumbsize,
            margin = props.attributes.margin,
            per_page = props.attributes.per_page,
            columns = props.attributes.columns,
            mememe_author = props.attributes.mememe_author,
            // random = props.attributes.random,
            filters = props.attributes.filters,
            customclass = props.attributes.customclass;
            // var setrandom = '';
            // if (random) {
            //     setrandom = '" random="'+random;
            // }
            var setfilters = '';
            if (filters) {
                setfilters = '" filters="'+filters;
            }
            return el('div', {
            }, '[mememe-list category="'+category
            +'" thumbsize="'+thumbsize
            +'" margin="'+margin
            +'" per_page="'+per_page
            +'" columns="'+columns
            +'" author="'+mememe_author
            // +setrandom
            +setfilters
            +'" class="'+customclass+'"]' )
        }

    });

    /**
     * *****************************************************************************
     *                                  MEME TEMPLATES [mememe-templates] 
     * *****************************************************************************
    */

    registerBlockType( 'mememe/mememe-templates-block', {
        title: 'MeMeMe Templates',
        icon: iconTemplate,

        category: 'widgets', // common, formatting, layout, widgets

        // Remove to make block editable in HTML mode.
        supportHTML: false,

        attributes: {
            content: {
                type: 'array',
                source: 'children',
                selector: 'p',
            },

            columns: { type: 'string', default: 0 },
            paginate: { type: 'string', default: MEMEMEadmin.per_page_default },
            thumbsize: { type: 'string', default: 'mememe-thumb' },
            margin: { type: 'string', default: 0 },
            mememe_show_title: { type: 'integer', default: 0 },
            random: { type: 'integer', default: 0 },
            customclass: { type: 'string', default: '' },
            style_shadow: { type: 'boolean', default: false },
            style_rounded: { type: 'boolean', default: false },
            style_dark: { type: 'boolean', default: false },
            style_frame: { type: 'boolean', default: false },
            theclass: { type: 'array', default: [] },
            filters: { type: 'boolean', default: 1 },
            tags: { type: 'array', default: [] }
        },

        /**
         * Called when Gutenberg initially loads the block.
         */
        edit: function( props ) {
            // console.log(MEMEMEadmin.available_tags);
            var random = props.attributes.random || false,
            thumbsize = props.attributes.thumbsize || 'mememe-thumb',
            margin = props.attributes.margin || 0,
            paginate = props.attributes.paginate || MEMEMEadmin.per_page_default,
            columns = props.attributes.columns || 0,
            mememe_show_title = props.attributes.mememe_show_title || 0,
            style_shadow = props.attributes.style_shadow || false,
            style_rounded = props.attributes.style_rounded || false,
            style_dark = props.attributes.style_dark || false,
            style_frame = props.attributes.style_frame || false,
            tags = props.attributes.tags || [],
            filters = props.attributes.filters || false,
            customclass = props.attributes.customclass || '',
            theclass = customclass.split(' ');
            theclass = theclass.filter(entry => entry.trim() != '');

            function onChangelist_col(argument) {
                props.setAttributes({columns: argument});
            }
            function onChangepaginate(argument) {
                props.setAttributes({paginate: argument});
            }
            function onChangelist_thumb(argument) {
                props.setAttributes({thumbsize: argument});
            }
            function onChangemargin(argument) {
                props.setAttributes({margin: argument});
            }

            function updateStyle(update, classe) {
                // Find and remove item from an array
                var i = theclass.indexOf(classe);
                if (i != -1) {
                    theclass.splice(i, 1);
                }
                if (update) {
                    theclass.push(classe); 
                }
                props.setAttributes({customclass: theclass.join(" ")});
            }

            return [
                el(
                    Placeholder, 
                    {
                        key: 'placeholder',
                        icon: el('span', { className: 'dashicon' }, iconTemplate),
                        label: MEMEMEadmin._list_templates
                    },
                ),
                el(
                    InspectorControls,
                    {key: 'mememe-templates-block-controls'},

                    el(
                        PanelBody, {},
                        el(
                            TextControl,
                            {
                                label: MEMEMEadmin._posts_per_page,
                                type: 'number',
                                help : ' ',
                                value: paginate,
                                onChange: onChangepaginate,
                            }
                        ),
                        el(
                            SelectControl,
                            {
                                label: MEMEMEadmin._columns,
                                help : ' ',
                                value: columns,
                                options: mememe_columns,
                                onChange: onChangelist_col
                            }
                        ),
                        el(
                            SelectControl,
                            {
                                label: MEMEMEadmin._thumbnail_size,
                                help : ' ',
                                value: thumbsize,
                                options: thumbsize_active,
                                onChange: onChangelist_thumb
                            }
                        ),
                        el(
                            TextControl,
                            {
                                label: MEMEMEadmin._margin + ' ( px )',
                                type: 'number',
                                help : ' ',
                                value: margin,
                                onChange: onChangemargin,
                            }
                        ),
                        el(
                            ToggleControl,
                            {
                                label: MEMEMEadmin._filters,
                                help: ' ',
                                checked: filters,
                                className: props.className,
                                onChange: function(filters) {
                                    var state = filters ? 1 : 0;
                                    props.setAttributes({filters: state});
                                }
                            }
                        ),
                        el(
                            FormTokenField,
                            {
                                label: MEMEMEadmin._tags,
                                value: tags,
                                onChange: function(tags) {
                                    var state = tags ? tags : [];
                                    props.setAttributes({tags: state});
                                },
                                suggestions: MEMEMEadmin.available_tags
                            }
                        ),
                        el(
                            'p',
                            {},
                            MEMEMEadmin._tags_help
                        ),
                        el(
                            ToggleControl,
                            {
                                label: MEMEMEadmin._random_templates,
                                help: ' ',
                                checked: random,
                                onChange: function(random) {
                                    var state = random ? 1 : 0;
                                    props.setAttributes({random: state});
                                }
                            }
                        ),
                        el(
                            ToggleControl,
                            {
                                label: MEMEMEadmin._show_title,
                                help: ' ',
                                checked: mememe_show_title,
                                onChange: function(mememe_show_title) {
                                    var state = mememe_show_title ? 1 : 0;
                                    props.setAttributes({mememe_show_title: state});
                                }
                            }
                        ),
                        el('p', {}, MEMEMEadmin._style ),
                        el(
                            CheckboxControl,
                            {
                                label: 'Dark',
                                checked: style_dark,
                                onChange: function(style_dark) {
                                    updateStyle(style_dark, 'mmm-dark');
                                    props.setAttributes({style_dark: style_dark});
                                }
                            }
                        ),
                        el(
                            CheckboxControl,
                            {
                                label: 'Frame',
                                checked: style_frame,
                                onChange: function(style_frame) {
                                    updateStyle(style_frame, 'mmm-frame');
                                    props.setAttributes({style_frame: style_frame});
                                }
                            }
                        ),
                        el(
                            CheckboxControl,
                            {
                                label: 'Rounded',
                                checked: style_rounded,
                                onChange: function(style_rounded) {
                                    updateStyle(style_rounded, 'mmm-rounded');
                                    props.setAttributes({style_rounded: style_rounded});
                                }
                            }
                        ),
                        el(
                            CheckboxControl,
                            {
                                label: 'Shadow',
                                checked: style_shadow,
                                onChange: function(style_shadow) {
                                    updateStyle(style_shadow, 'mmm-shadow');
                                    props.setAttributes({style_shadow: style_shadow});
                                }
                            }
                        )
                    ) // panelBody
                ) // InspectorControls
            ]; // return 
        }, // edit

        transforms: {
            from: [
                {
                    type: 'shortcode',
                    tag: 'mememe-templates',
                    attributes: {

                        random: { 
                            type: 'integer', 
                            shortcode: function( obj ) {
                                var random = obj.named.random ? obj.named.random : 0;
                                return random;
                            }
                        },

                        thumbsize: { 
                            type: 'string', 
                            shortcode: function( obj ) {
                                var thumbsize = obj.named.thumbsize ? obj.named.thumbsize : 'mememe-thumb';
                                return thumbsize;
                            }
                        },
                        margin: { 
                            type: 'string', 
                            shortcode: function( obj ) {
                                var margin = obj.named.margin ? obj.named.margin : 0;
                                return margin;
                            }
                        },
                        paginate: { 
                            type: 'string', 
                            shortcode: function( obj ) {
                                var paginate = obj.named.paginate ? obj.named.paginate : MEMEMEadmin.per_page_default;
                                return paginate;
                            }
                        },
                        columns: { 
                            type: 'string', 
                            shortcode: function( obj ) {
                                var columns = obj.named.columns ? obj.named.columns : 0;
                                return columns;
                            }
                        },
                        title: { 
                            type: 'integer', 
                            shortcode: function( obj ) {
                                var title = obj.named.title ? obj.named.title : 0;
                                return title;
                            }
                        },
                        customclass: { 
                            type: 'string', 
                            shortcode: function( obj ) {
                                var customclass = obj.named.class ? obj.named.class : '';
                                return customclass;
                            }
                        },
                        style_dark: { 
                            type: 'boolean', 
                            shortcode: function( obj ) {
                                var style_dark = (obj.named.class && obj.named.class.indexOf('mmm-dark') !== -1) ? 1 : 0;
                                return style_dark;
                            }
                        },
                        style_frame: { 
                            type: 'boolean', 
                            shortcode: function( obj ) {
                                var style_frame = (obj.named.class && obj.named.class.indexOf('mmm-frame') !== -1) ? 1 : 0;
                                return style_frame;
                            }
                        },
                        style_rounded: { 
                            type: 'boolean', 
                            shortcode: function( obj ) {
                                var style_rounded = (obj.named.class && obj.named.class.indexOf('mmm-rounded') !== -1) ? 1 : 0;
                                return style_rounded;
                            }
                        },
                        style_shadow: { 
                            type: 'boolean', 
                            shortcode: function( obj ) {
                                var style_shadow = (obj.named.class && obj.named.class.indexOf('mmm-shadow') !== -1) ? 1 : 0;
                                return style_shadow;
                            }
                        },
                        filters: { 
                            type: 'boolean', 
                            shortcode: function( obj ) {
                                var filters = obj.named.filters ? 1 : 0;
                                return filters;
                            }
                        },
                        tags: { 
                            type: 'array', 
                            shortcode: function( obj ) {
                                var tags = obj.named.tags ? obj.named.tags : [];
                                return tags;
                            }
                        }
                    },
                },
            ]
        },

        /**
         * Called when Gutenberg "saves" the block to post_content
         */
        save: function (props) {
            var random = props.attributes.random || false,
            thumbsize = props.attributes.thumbsize || 'mememe-thumb',
            margin = props.attributes.margin || 0,
            paginate = props.attributes.paginate || MEMEMEadmin.per_page_default,
            columns = props.attributes.columns || 0,
            mememe_show_title = props.attributes.mememe_show_title || 0,
            customclass = props.attributes.customclass || '',
            filters = props.attributes.filters,
            tags = props.attributes.tags;
            return el('div', {
            }, '[mememe-templates random="'+random
            +'" thumbsize="'+thumbsize
            +'" margin="'+margin
            +'" paginate="'+paginate
            +'" columns="'+columns
            +'" title="'+mememe_show_title
            +'" filters="'+filters
            +'" tags="'+tags
            +'" class="'+customclass+'"]' )
        },

        deprecated: [
            {
                attributes: {
                    content: {
                        type: 'array',
                        source: 'children',
                        selector: 'p',
                    },
                    columns: { type: 'string', default: 0 },
                    paginate: { type: 'string', default: MEMEMEadmin.per_page_default },
                    thumbsize: { type: 'string', default: 'mememe-thumb' },
                    margin: { type: 'string', default: 0 },
                    mememe_show_title: { type: 'integer', default: 0 },
                    random: { type: 'integer', default: 0 },
                    customclass: { type: 'string', default: '' },
                    style_shadow: { type: 'boolean', default: false },
                    style_rounded: { type: 'boolean', default: false },
                    style_dark: { type: 'boolean', default: false },
                    style_frame: { type: 'boolean', default: false },
                    theclass: { type: 'array', default: [] }
                },

                save: function (props) {
                    var random = props.attributes.random || false,
                    thumbsize = props.attributes.thumbsize || 'mememe-thumb',
                    margin = props.attributes.margin || 0,
                    paginate = props.attributes.paginate || MEMEMEadmin.per_page_default,
                    columns = props.attributes.columns || 0,
                    mememe_show_title = props.attributes.mememe_show_title || 0,
                    customclass = props.attributes.customclass || '';
                    return el('div', {
                    }, '[mememe-templates random="'+random
                    +'" thumbsize="'+thumbsize
                    +'" margin="'+margin
                    +'" paginate="'+paginate
                    +'" columns="'+columns
                    +'" title="'+mememe_show_title
                    +'" class="'+customclass+'"]' )
                }
            }
        ]
    });
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components
);
