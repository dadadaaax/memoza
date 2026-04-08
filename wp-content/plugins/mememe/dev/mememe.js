/*
 * MeMeMe - jQuery Plugin
 * version: 1.8
 * @requires jQuery >= 1.7.0
 * Depends:
 *  jquery.ui.mouse.js
 *  jquery.ui.widget.js
 *  jquery.ui.touch-punch.js
 *  jquery.ui.draggable.js
 *  jquery.ui.resizable.js
 *  jquery.ui.rotatable.js
 *  jQuery MiniColors
 *  WebFont Loader
 *  fitMememe
 *  
 * Copyright 2017-2020 Nicola Franchini - @nicolafranchini
 */

/* global WebFont, jQuery */
(function($){
    'use strict';
    $.fn.extend({
        //plugin name - mememe
        mememe: function(options) {
            // default options
            var defaults = {
                baseimg : '.mememe-placeholder img',
                bgcolor : '#EBEEEE', // default canvas bg color
                color : '#F2FFFF', // default text color
                outcolor : '#000000', // default shadow / outline
                drawcolor : '#F2FFFF', // default pencil color
                finalwidth : 800,
                fonts : ['Arial, Helvetica, sans-serif**'], // available fonts
                init : true, // if false don't init, and use the method mememe.init() later
                mode: 'hand', // default app mode: 'hand' | 'text'
                onInit : function(){},
                onImageUpdate : function(){},
                onSave : function(){},
                placeholder: 'Click here to enter text', // text box placeholder
                textposition : 'top-center', // default text box position: 'top-left' | 'top-center' | 'top-right' | 'center-left' | 'center-center' | 'center-right' | 'bottom-left' | 'bottom-center' | 'bottom-right'
                watermark : 'MeMeMe', // watermark text
                watermarkposition : 'bottom-right', // 'top-left' | 'top-right' | 'bottom-left' | 'bottom-right'
                uploader : 'on',
                labels : {
                    color :'Change color',
                    mode :'Mode', 
                    upload :'Upload image',
                    tools :'Tools',
                    reset :'Reset',
                    save :'Save',
                    select_color :'Select color',
                    stroke :'Stroke',
                    font :'Select Font',
                    new_text_box :'Add new text box',
                    spacer :'Spacer',
                    none :'None',
                    top :'Top',
                    bottom :'Bottom',
                    topbottom :'Top-Bottom',
                    white :'White',
                    black :'Black',
                    default :'Default',
                    textalign : 'Text alignment'
                },
                outlines : [ 0, 1, 2, 3, 4 ], // available text outlines
                outline : 2, // text outline 0 | 1 | 2 | 3 | 4
                strokes : [ 2, 4, 6, 8, 12, 16 ], // available strokes
                stroke : 4, // pencil stroke 2, 4, 6, 8, 12
                direction : 'LTR',
                text_box_num : 1,
                show_tools : 0,
                textinit : false,
                spacer : ''
            };

            var option = $.extend(defaults, options);

            var plugin = this;
            var core = $('<div class="wrapmememe"><div class="mememe-stage"></div></div>');
            var $wrapdrag = $('<div class="mememe-drag"></div>');
            var $wrapdraw = $('<canvas class="mememe-draw"></canvas>');
            var scaledCanvas = document.createElement('canvas');
            var ctscale = scaledCanvas.getContext('2d');
            var pixelratio = 1;
            var totaltext = '';

            /**
             * Save images in HI resoultion (with monitors retina)
             */
            if (window.devicePixelRatio) {
                pixelratio = window.devicePixelRatio;
            }

            return this.each(function() {

                var obj = $(this);

                var ctx, ctf, font, stage, canvas, finalcanvas, image, ratio,
                isDrawing = false,
                points = [],
                actualX = 0,
                actualY = 0,
                stroke = option.stroke,
                outline = option.outline,
                color = option.color,
                outcolor = option.outcolor,
                drawcolor = option.drawcolor,
                mode = option.mode;

                /**
                 * methods to be used outside the plugin
                 */
                plugin.resize = function() {
                    resizeMememe();
                };

                plugin.save = function() {
                    saveImageRemote();
                };

                plugin.init = function() {
                    init();
                };

                plugin.updateImg = function( url ) {
                    updateImg(url);
                };

                if (option.init === true) {
                    init();
                }

                function updateImg(url) {
                    var placeimg = obj.find(option.baseimg);
                    placeimg.one('load', function() {
                        resizeMememe();
                        placeimg.addClass('mememe-animate-in');
                    }).removeClass('mememe-animate-in').attr('src', url);
                    obj.find('.original-mememe-temp').remove();
                    // reset spacer selection
                    obj.find('.mememe-spacer-position option[value="none"]').prop('selected', true);
                }

                /**
                 * Init app
                 */
                function init(){

                    obj.css({ 'position': 'relative', 'z-index': '1' }).append(core).addClass('mememe-init');

                    stage = obj.find('.mememe-stage');
                    stage.append($wrapdraw);

                    // set container
                    if (obj.find('.mememe-drag').length < 1){
                      stage.append($wrapdrag);
                    }

                    canvas = obj.find('.mememe-draw')[0];
                    finalcanvas = document.createElement('canvas');

                    if (option.direction === 'RTL') {
                        finalcanvas.classList.add("hidden-mememe-canvas");
                        finalcanvas.dir = "rtl";
                        stage.append(finalcanvas);
                    }

                    if (!obj.find(option.baseimg).length) {
                        // or create a blank placeholder before init
                        var place = defaultImage();
                        obj.find('.wrapmememe').append('<div class="mememe-placeholder"></div>');
                        obj.find('.mememe-placeholder').append(place);

                        place.onload = function() {
                            image = obj.find(option.baseimg);
                            ratio = image.outerWidth() / image.outerHeight();
                            start();
                            place.onload = null;
                            place = null;
                        };
                    } else {
                        image = obj.find(option.baseimg);
                        ratio = image.outerWidth() / image.outerHeight();
                        start();
                    }
                }

                /**
                 * Start app
                 */
                function start() {
                    // Prevent double initialization
                    if (obj.data('mememe')) {
                        return true;
                    }

                    if (canvas.getContext && finalcanvas.getContext) {
                        ctx = canvas.getContext('2d');
                        ctf = finalcanvas.getContext('2d');
                    }

                    if (ctx) {
                        resizeMememe();
                        toolsMenu();

                        // INIT
                        resetImage(function(){
                            if (option.mode === 'text') {
                                initText();
                            } else {
                                initDraw();
                            }
                            // callback 'onInit'
                            if ($.isFunction(option.onInit)) {
                                option.onInit.call();
                            }
                        });
                    }
                    obj.data('mememe', true);
                }

                /**
                 * Manage text input
                 */
                function initText(){

                    obj.find('.mememe-drag').removeClass('mememe-disabled-text');

                    obj.find('.mememe-protools-draw').hide();
                    obj.find('.mememe-protools-text').show();

                    canvas.removeEventListener('mousedown', drawMouseDown, false);
                    canvas.removeEventListener('mousemove', drawMouseMove, false);
                    window.removeEventListener('mouseup', drawMouseUp, false);

                    // // React to touch events on the canvas
                    canvas.removeEventListener('touchstart', drawTouch, false);
                    canvas.removeEventListener('touchmove', drawTouch, false);
                    canvas.removeEventListener('touchend', drawTouchEnd, false);

                    if (obj.find('.dragmememe').length < 1){
                        for (var i = 0; i < option.text_box_num; i++) {
                            addText();
                        }
                    }
                    loadCustomFont();
                }

                /**
                 * Insert text box
                 */
                function addText( focusme = 0 ){

                    var dragstage = obj.find('.mememe-drag');
                    var dragmelen = obj.find('.dragmememe').length;

                    // Prepare position
                    var lastX = '0', lastY = '15px';
                    var topbottom = 'top';
                    var rightleft = 'left';

                    // Get last item percent position
                    if (dragmelen > 0){
                        var lastdrag = obj.find('.dragmememe:last');

                        var percX = lastdrag.position().left / dragstage.width() * 100;
                        var maxH = (lastdrag.position().top + lastdrag.outerHeight()*2);
                        var percY = 0;

                        if (maxH < dragstage.outerHeight()) {
                            percY = (lastdrag.position().top + lastdrag.outerHeight()) / dragstage.height() * 100;
                            // lastY = lastdrag.position().top + lastdrag.outerHeight() + 15;
                        } else {
                            percY = (lastdrag.position().top - lastdrag.outerHeight()) / dragstage.height() * 100;
                            // lastY = lastdrag.position().top - lastdrag.outerHeight() - 15;
                        }

                        if (percX < 0) {
                            percX = 0;
                        }
                        if (percY < 0) {
                            percY = 0;
                        }

                        lastX = percX + '%';
                        lastY = percY + '%';

                    } else {
                        // Get plugin options to position first box
                        var txtpos = option.textposition.split('-');
                        topbottom = txtpos[0];
                        rightleft = txtpos[1];
                    }

                    // Prepare the box
                    var dragID = dragmelen;

                    var rtlClass = '';
                    var rtlDir = '';

                    if (option.direction === 'RTL') {
                        rtlClass = ' rtl';
                        rtlDir = ' dir="rtl"';
                    }

                    var dragMeDelete = '';
                    if (dragmelen > 0){
                        dragMeDelete = '<div class="mememe-del-text"><svg width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16 8A8 8 0 110 8a8 8 0 0116 0zm-4.146-3.146a.5.5 0 00-.708-.708L8 7.293 4.854 4.146a.5.5 0 10-.708.708L7.293 8l-3.147 3.146a.5.5 0 00.708.708L8 8.707l3.146 3.147a.5.5 0 00.708-.708L8.707 8l3.147-3.146z" clip-rule="evenodd"/></svg></div>';
                    }

                    var $dragme = $('<div class="dragmememe dragme-'+dragID+' mememe-active-drag" data-fit="fit-'+dragID+'">'+dragMeDelete+'</div>');
                    var $input = $('<div class="rotatemememe"><div class="mememe-editme'+rtlClass+'" contenteditable="true" data-rows="0" data-placeholder="'+option.placeholder+'"'+rtlDir+'></div></div>');

                    // Add the box
                    $dragme.append($input);

                    $wrapdrag.append($dragme);

                    dragmelen = obj.find('.dragmememe').length;
                    
                    var dragMeThis = obj.find('.dragme-' + dragID);
                    var inputinput = dragMeThis.find('.mememe-editme');
                    var rotatemememe = dragMeThis.find('.rotatemememe');

                    var thiswidth = dragMeThis.outerWidth();
                    var thisheight = dragMeThis.outerHeight();
                    var parentwidth = $wrapdrag.outerWidth();
                    var parentheight = $wrapdrag.outerHeight();

                    var currentalign = $('.mememe-switch-textalign.active').data('align');

                    // Update text style
                    inputinput.css({'color': color, 'text-align': currentalign});
                    updateOutline();
                    setFont();

                    // Adjust position for centered options
                    if (dragmelen === 1){
                        if (rightleft === 'center'){
                            lastX = (parentwidth/2 - thiswidth/2)+'px';
                            rightleft = 'left';
                        }
                        if (topbottom === 'center'){
                            lastY = (parentheight/2 - thisheight/2)+'px';
                            topbottom = 'top';
                        }
                    }

                    // Set position and size in px
                    dragMeThis.css(rightleft, lastX);
                    dragMeThis.css(topbottom, lastY);

                    dragMeThis.css({'width': thiswidth, 'height': thisheight});

                    // call fitMememe plugin
                    var fitThisText = inputinput.fitMememe();

                    var dragMeThisPosition = dragMeThis.position();

                    // Resize text block if is going offset the stage.
                    if (rightleft === 'left' && (dragMeThisPosition.left + thiswidth) > parentwidth) {
                        dragMeThis.width(parentwidth - dragMeThisPosition.left);
                    }

                    // Convert position in %.
                    convertPos(dragMeThis, dragMeThisPosition.top, dragMeThisPosition.left);

                    // Resize & Drag.
                    dragMeThis.resizable({
                        handles: 'ne, se, sw', // 'n, e, s, w, ne, se, sw, nw',
                        containment: 'parent',
                        minHeight: 40,
                        minWidth: 100,
                        // maxHeight: 140,
                        // aspectRatio: true,
                        resize: function(e, ui) {
                            fitThisText.resize();
                            convertPos(ui.helper, ui.position.top, ui.position.left);
                        },
                        // stop: function(e, ui) {
                        //     fitThisText.resize();
                        // }
                    }).draggable({ 
                        cancel: '', 
                        containment: 'document', // parent, document, window
                        stop : function(e, ui) {
                            convertPos(ui.helper, ui.position.top, ui.position.left);
                        }
                    });
                    // Rotate.
                    rotatemememe.rotatable({
                        wheel: false,
                        start: function (e, ui) { 
                            $(e.target).find('.mememe-editme').focus().addClass('mememe-input-highlight');
                        },
                        stop: function (e, ui) { 
                            $(e.target).find('.mememe-editme').removeClass('mememe-input-highlight');
                        }
                    });

                    // Enable writing inside text block
                    dragMeThis.on('click', function(){
                        $('.dragmememe').css('z-index', 1);
                        $(this).css('z-index', 90);
                        $(this).find('.mememe-editme').focus();
                    });

                    var deleteme = dragMeThis.find('.mememe-del-text');

                    inputinput.on('focus', function(){
                        dragstage.find('.dragmememe').removeClass('mememe-active-drag');
                        dragstage.find('.mememe-del-text').css('display', 'none');
                        deleteme.css('display', 'block');
                        dragMeThis.addClass('mememe-active-drag');
                        setCursorToEnd($(this).get(0));
                    });

                    deleteme.on('click', function(){
                        dragMeThis.remove();
                    });

                    inputinput.on('touchstart', function(){
                        $(this).focus();
                    });

                    // Enable save button.
                    inputinput.on('input', function(){
                        if ($(this).text().length) {
                            obj.find('.save-mememe.disabled').removeClass('disabled').prop('disabled', false);
                        }
                    });

                    // Set pre-filled text
                    if (option.textinit) {
                        inputinput.text(option.textinit);
                        setCursorToEnd(inputinput.get(0));
                    }

                    // Set focus.
                    if ( focusme == 1) {
                        inputinput.focus();
                    }
                }
                
                function setCursorToEnd(ele) {
                    if (ele.textContent.length > 0) {
                        var range = document.createRange();
                        var sel = window.getSelection();
                        range.setStart(ele, 1);
                        range.collapse(true);
                        sel.removeAllRanges();
                        sel.addRange(range);
                    }
                    // ele.focus();
                }
                /**
                 * Convert position in percent.
                 */
                function convertPos(select, postop, posleft){
                    var percX = posleft / select.parent().width() * 100;
                    var percY = postop / select.parent().height() * 100;
                    select.css({
                        'left': percX + '%',
                        'top': percY + '%',
                    });
                }

                /**
                 * Reset image.
                 */
                function resetImage(callback){
                    // remove text boxes
                    var dragmelist = obj.find('.dragmememe');

                    if (dragmelist.length) {
                        if (dragmelist.length > 1) {
                            obj.find('.dragmememe:not(:first)').remove();
                        }
                        obj.find('.dragmememe').each(function(){
                            $(this).find('.mememe-editme').html('').focus().blur();
                            // inputFit($(this));
                        });
                    }
                    // clear drawings.
                    ctx.clearRect(0, 0, canvas.width/pixelratio, canvas.height/pixelratio);
                    // Disable save button.
                    obj.find('.save-mememe').addClass('disabled').prop('disabled', true);

                    if ($.isFunction(callback)) {
                        callback();
                    }
                }

                /**
                 * Draw background image on canvas.
                 */
                function drawMyBack(callback){

                    var img = new Image();
                    img.crossOrigin = "Anonymous";

                    $(img).one('load', function() {

                        ctf.drawImage(img, 0, 0, canvas.width/pixelratio, canvas.height/pixelratio);

                        if ($.isFunction(callback)) {
                          callback();
                        }
                    });

                    img.src = image.attr('src');
                }

                /**
                 * Save image data.
                 */
                function saveImageRemote() {
                    var dragz = obj.find('.mememe-drag').css('z-index');
                    var drawz = obj.find('.mememe-draw').css('z-index');

                    // draw background
                    drawMyBack( function(){

                        if (drawz > dragz) {
                            // lines over text
                            drawText();
                            ctf.drawImage(canvas, 0, 0, canvas.width/pixelratio, canvas.height/pixelratio);
                        } else {
                            // text over lines
                            ctf.drawImage(canvas, 0, 0, canvas.width/pixelratio, canvas.height/pixelratio);
                            drawText();
                        }

                        // Resize before saving.
                        ratio = canvas.width/canvas.height;
                        scaledCanvas.width = option.finalwidth;
                        scaledCanvas.height = option.finalwidth/ratio; //compared to original canvas
                        ctscale.drawImage(finalcanvas, 0, 0, scaledCanvas.width, scaledCanvas.height);

                        // draw watermark
                        setWatermark();

                        // generate data
                        var image_data = scaledCanvas.toDataURL('image/png');

                        if ($.isFunction(option.onSave)) {
                            option.onSave.call(this, image_data);
                            drawMyBack();
                        }
                    });
                }

                /**
                 * Add watermark
                 */
                function setWatermark(){

                    // Get plugin options to position watermark
                    var waterpos = option.watermarkposition.split('-');

                    // Set watermark image
                    if ($('#mememe-watermark').length) {

                        var waterimg = document.getElementById('mememe-watermark');

                        var waterimgoriz = 0;
                        var waterimgvert = 0;

                        if (waterpos[1] === 'right') {
                            waterimgoriz = scaledCanvas.width-waterimg.width;
                        }

                        if (waterpos[1] === 'center') {
                            waterimgoriz = (scaledCanvas.width/2)-(waterimg.width/2);
                        }

                        if (waterpos[0] === 'bottom') {
                            waterimgvert = scaledCanvas.height-waterimg.height;
                        }

                        if (waterpos[0] === 'center') {
                            waterimgvert = (scaledCanvas.height/2)-(waterimg.height/2);
                        }
                        ctscale.drawImage(waterimg, waterimgoriz, waterimgvert, waterimg.width, waterimg.height);

                    } else {

                        if (option.watermark.length) {

                            var wateroriz = 10;
                            var watervert = 14;

                            if (waterpos[1] === 'right') {
                              ctscale.textAlign = 'end';
                              wateroriz = (scaledCanvas.width-10);
                            } 

                            if (waterpos[1] === 'center') {
                              ctscale.textAlign = 'center';
                              wateroriz = (scaledCanvas.width/2);
                            } 

                            if (waterpos[0] === 'bottom') {
                              watervert = (scaledCanvas.height-10);
                            }

                            if (waterpos[0] === 'center') {
                              watervert = (scaledCanvas.height/2);
                            }

                            ctscale.font = '11px sans-serif';
                            ctscale.fillStyle = '#ffffff';
                            ctscale.shadowColor = '#000000';
                            ctscale.shadowBlur = 3;
                            ctscale.fillText(option.watermark, wateroriz, watervert);
                        }
                    }
                }

                /**
                 * Get text rotation
                 */
                function getRotationDegrees(obj) {
                    var matrix = obj.css("-webkit-transform") ||
                    obj.css("-moz-transform")    ||
                    obj.css("-ms-transform")     ||
                    obj.css("-o-transform")      ||
                    obj.css("transform");
                    if(matrix !== 'none') {
                        var values = matrix.split('(')[1].split(')')[0].split(',');
                        var a = values[0];
                        var b = values[1];
                        var angle = Math.round(Math.atan2(b, a) * (180/Math.PI));
                    } else { var angle = 0; }
                    return angle;
                }

                /**
                 * Write text over image
                 */
                function drawText() {

                    obj.find('.dragmememe .mememe-editme').each(function(){

                        var thisfield = $(this);
                        var fontsize = parseInt(thisfield.css('font-size'));
                        var fontstyle = thisfield.css('font-style') + ' ' + thisfield.css('font-weight') + ' ' + fontsize + 'px ' + thisfield.css('font-family');
                        var textalign = thisfield.css('text-align');
                        var strokeWidth = outline*pixelratio;

                        ctf.textBaseline = "top"; 
                        // ctf.textBaseline = "middle"; 
                        ctf.font = fontstyle;
                        ctf.textAlign = 'center';
                        ctf.fillStyle = thisfield.css('color');
                        ctf.lineWidth = strokeWidth;

                        var text = thisfield.text();
                        // var lineHeight = parseInt(thisfield.css('line-height'));
                        var stoRotate = thisfield.parent('.rotatemememe');
                        var stoDrag = stoRotate.parent('.dragmememe');
                        var initY = stoDrag.height()/2;
                        var pos = stoDrag.position();
                        var posy = pos.top + initY;
                        var posx = pos.left + stoDrag.width()/2;

                        var rotation = getRotationDegrees(stoRotate);
                        var radians = (rotation * Math.PI / 180);

                        var lines = [], line = '', currentY = 0, currentX = 0, firstrowtext = '', singleline, multilines, metrics, actualHeight;

                        var multidivs = thisfield.find('div');
                        var multip = thisfield.find('p');

                        firstrowtext = thisfield.text();

                        if (multidivs.length) {
                            firstrowtext = thisfield.text().replace(multidivs.text(), '');
                        }

                        if (multip.length) {
                            firstrowtext = thisfield.text().replace(multip.text(), '');
                        }

                        var allines = [];

                        if (firstrowtext.length) {
                            allines.push({ text: firstrowtext });
                        }

                        if (multidivs.length) {
                            multidivs.each(function(index){
                                allines.push({ text: $(this).text() });
                            });
                        }
                        if (multip.length) {
                            multip.each(function(index){
                                allines.push({ text: $(this).text() });
                            });
                        }

                        for (var i = 0, len = allines.length; i < len; i++) {
                            line = allines[i].text;
                            metrics = ctf.measureText(line);
                            currentY = i * fontsize - initY + fontsize*0.1; // <- weird adjustment :\

                            // Adjust text position if aligned left or right
                            if (textalign == 'left' || textalign == 'right') {
                                currentX = stoDrag.width()/2 - metrics.width/2;
                                if (textalign == 'left') {
                                    currentX = currentX*-1;
                                }
                            }
                            lines.push({ text: line, starty: currentY, startx: currentX });
                        }

                        // Save canvas before moving center and rotate
                        ctf.save();
                        ctf.translate(posx, posy);
                        ctf.rotate(radians);

                        for (var i = 0, len = lines.length; i < len; i++) {

                            // Write text and outline
                            if (outline > 0) {

                                var strokeshadow = obj.find('.mememe-select-strokeshadow').val();

                                if (strokeshadow == 'shadow') {
                                    ctf.shadowColor = outcolor;
                                    ctf.shadowBlur = strokeWidth;
                                    ctf.fillText(lines[i].text, lines[i].startx, lines[i].starty);
                                    ctf.fillText(lines[i].text, lines[i].startx, lines[i].starty);
                                } else {
                                    //ctf.lineJoin = 'bevel';
                                    ctf.lineJoin = 'round';
                                    ctf.strokeStyle = outcolor;
                                    ctf.strokeText(lines[i].text, lines[i].startx, lines[i].starty);
                                }
                            }
                            ctf.fillText(lines[i].text, lines[i].startx, lines[i].starty);

                            // ctf.beginPath();
                            // ctf.moveTo(0, lines[i].starty);
                            // ctf.lineTo(300, lines[i].starty);
                            // ctf.stroke();
                        }
                        // Restore canvas original pos
                        ctf.restore();
                    });
                }

                /**
                 * Free hand drawing
                 */
                function drawLine(){
                    ctx.lineWidth = stroke;
                    ctx.strokeStyle = drawcolor;
                    ctx.lineJoin = ctx.lineCap = 'round';
                    ctx.beginPath();

                    for (var i = 1; i < points.length; i++) {
                      ctx.lineTo(points[i].x, points[i].y);
                    }
                    ctx.stroke();

                    // Enable save button.
                    obj.find('.save-mememe.disabled').removeClass('disabled').prop('disabled', false);
                }

                /**
                 * Start mouse drawing
                 */
                function drawMouseDown(){
                    isDrawing = true;
                }

                /**
                 * Mouse drawing
                 */
                function drawMouseMove(e){
                    if (!isDrawing) {
                      return;
                    }
                    getMousePos(e);
                    points.push({ x: actualX, y: actualY });
                    drawLine();
                }

                /**
                 * Stop mouse drawing
                 */
                function drawMouseUp(){
                    isDrawing = false;
                    points.length = 0;
                }

                /**
                 * Get the current mouse position
                 */
                function getMousePos(e) {
                    if (!e) {
                        e = event;
                    }
                    var winTop = $(window).scrollTop();
                    var winLeft = $(window).scrollLeft();
                    actualX = e.clientX-stage.offset().left+winLeft;
                    actualY = e.clientY-stage.offset().top+winTop;
                 }

                /**
                 * Get the current touch position
                 */
                function getTouchPos(e) {
                    if (!e) {
                        e = event;
                    }
                    if(e.touches) {
                        if (e.touches.length === 1) { // Only deal with one finger
                            var touch = e.touches[0]; // Get the information for finger #1
                            actualX=touch.pageX-stage.offset().left;
                            actualY=touch.pageY-stage.offset().top;
                        }
                    }
                }

                // Draw something when a touch start is detected
                function drawTouch(e) {
                    getTouchPos(e);
                    points.push({ x: actualX, y: actualY });
                    drawLine();
                    event.preventDefault();
                }

                // Called when touch is lifted from the screen of the device.
                function drawTouchEnd() { 
                    points.length = 0;
                }

                /**
                 * Init free hand drawing
                 */
                function initDraw(){

                    obj.find('.mememe-drag').addClass('mememe-disabled-text');

                    obj.find('.mememe-protools-draw').show();
                    obj.find('.mememe-protools-text').hide();

                    // // React to mouse events on the canvas, and mouseup on the entire document
                    canvas.addEventListener('mousedown', drawMouseDown, false);
                    canvas.addEventListener('mousemove', drawMouseMove, false);
                    window.addEventListener('mouseup', drawMouseUp, false);

                    // // React to touch events on the canvas
                    canvas.addEventListener('touchstart', drawTouch, false);
                    canvas.addEventListener('touchmove', drawTouch, false);
                    canvas.addEventListener('touchend', drawTouchEnd, false);
                }

                /**
                 * Create a default placeholder
                 */
                function defaultImage() {
                    var canvasplaceholder = document.createElement('canvas');
                    canvasplaceholder.width = 4;
                    canvasplaceholder.height = 3;

                    var ctp = canvasplaceholder.getContext('2d');

                    ctp.fillStyle = option.bgcolor;
                    ctp.fillRect(0,0,4,3); 

                    var imgData = ctp.getImageData(0, 0, 4, 3);

                    ctp.putImageData(imgData, 0, 0);

                    var placeholder_image = new Image();
                    placeholder_image.crossOrigin = "Anonymous";
                    placeholder_image.src = canvasplaceholder.toDataURL();
                    return placeholder_image;
                }

                /**
                 * Create a spacer
                 */
                function spacerImage(spacerpos, spacerPercent, spacercolor) {
                    if ( ! obj.find('.original-mememe-temp').length ) {
                        var image = obj.find(option.baseimg);
                        core.append('<img class="original-mememe-temp" src="'+image.attr('src')+'">')
                    } else {
                        var image = obj.find('.original-mememe-temp');
                    }

                    $('.mememe-placeholder img').addClass('mememe-animate-in');

                    var imageWidth = image.outerWidth();
                    var imageHeight = image.outerHeight();

                    var spacerH = imageHeight / spacerPercent;

                    if (spacerpos == 'none') {
                        spacerH = 0;
                    }

                    var canvasWidth = imageWidth;
                    var canvasHeight = (imageHeight + spacerH);
                    if (spacerpos == 'topbottom') {
                        canvasHeight = (imageHeight + spacerH *  2);
                    }

                    var canvasimage = document.createElement('canvas');
                    canvasimage.width = canvasWidth;
                    canvasimage.height = canvasHeight;

                    var ctp = canvasimage.getContext('2d');

                     ctp.fillStyle = spacercolor;
                    // Spacer above
                    if (spacerpos == 'top') {
                        ctp.drawImage(image[0], 0, spacerH, imageWidth, imageHeight);
                        ctp.fillRect(0, 0, imageWidth, spacerH);
                    }
                    // Spacer below
                    if (spacerpos == 'bottom') {
                        ctp.drawImage(image[0], 0, 0, imageWidth, imageHeight);
                        ctp.fillRect(0, imageHeight, imageWidth, spacerH); 
                    }
                    // topbopttom
                    if (spacerpos == 'topbottom') {
                        ctp.fillRect(0, 0, imageWidth, spacerH);
                        ctp.drawImage(image[0], 0, spacerH, imageWidth, imageHeight);
                        ctp.fillRect(0, (imageHeight+spacerH), imageWidth, spacerH); 
                    }
                    // none
                    if (spacerpos == 'none') {
                        ctp.drawImage(image[0], 0, spacerH, imageWidth, imageHeight);
                    }

                    var dataURL = canvasimage.toDataURL();

                    var spacer_image = new Image();
                    spacer_image.crossOrigin = "Anonymous";
                    spacer_image.src = dataURL;

                    spacer_image.onload = function() {
                        obj.find(option.baseimg).attr('src', dataURL);
                        resizeMememe();
                    };
                }

                /**
                 * Tools menu
                 */
                function toolsMenu(){

                    if (!obj.find('.mememe-menu').length) {
                       var $memememenu = $('<div class="mememe-menu"></div>');
                        obj.prepend($memememenu);
                    }
                    var vmenu = obj.find('.mememe-menu');

                    /**
                    * Mode menu
                    */
                    var modes = [
                      'text',
                      'hand'
                    ];

                    var selectmode = '<div class="mememe-group mmm-tooltip" data-title="'+option.labels.mode+'" >';
                    $.each( modes, function( key, value ) {
                        var icon = '<i class="immm immm-draw-mode"></i>';
                        if (value === 'text') {
                          icon = '<i class="immm immm-text-mode"></i>';
                        }
                        
                        selectmode += '<button class="mememe-switch-mode mememe-btn';

                        if (value === mode) {
                            selectmode += ' active';
                        }
                        selectmode += '" data-mode="'+value+'">'+icon+'</button>';                    
                    });

                    selectmode += '</div>';
                    vmenu.append(selectmode);

                    obj.find('.mememe-switch-mode').on('click', function(){
                        obj.find('.mememe-switch-mode').removeClass('active');
                        $(this).addClass('active');
                        mode = $(this).data('mode');
                        if (mode === 'hand') {
                          initDraw();
                        } else {
                          initText();
                        }
                    });

                    /**
                     * Uploader
                     */
                    if (option.uploader === 'on') {
                        var $uploader = $('<div class="mememe-group"><button class="mememe-btn mememe-fake-up mmm-tooltip" data-title="'+option.labels.upload+'"><i class="immm immm-photo"></i></button><input type="file" class="mememe-up"></div>');
                        vmenu.append($uploader);

                        obj.find('.mememe-up').on('change', function(){
                            
                            var input = this;
                            var file = input.files[0];
                            var imageType = /image.*/;

                            if (file.type.match(imageType)) {

                                obj.find(option.baseimg).parent().css('height', 'auto');

                                var reader = new FileReader();

                                reader.onload = function(e) {

                                    var imageup = new Image();
                                    imageup.crossOrigin = "Anonymous";
                                    imageup.src = e.target.result;

                                    imageup.onload = function() {

                                        var stasrc = this.src;
                                        /*
                                        * Check image EXIF orientation
                                        */
                                        // window.EXIF.getData(imageup, function() {
                                            
                                        //     var orientation = EXIF.getTag(this, "Orientation");

                                        //     if (orientation > 1) {
                                        //         var width = imageup.width,
                                        //             height = imageup.height,
                                        //             transcanvas = document.createElement('canvas'),
                                        //             ctrans = transcanvas.getContext("2d");
                                        //         // set proper canvas dimensions before transform & export
                                        //         if (4 < orientation && orientation < 9) {
                                        //           transcanvas.width = height;
                                        //           transcanvas.height = width;
                                        //         } else {
                                        //           transcanvas.width = width;
                                        //           transcanvas.height = height;
                                        //         }
                                        //         // transform context before drawing image
                                        //         switch (orientation) {
                                        //           case 2: ctrans.transform(-1, 0, 0, 1, width, 0); break;
                                        //           case 3: ctrans.transform(-1, 0, 0, -1, width, height); break;
                                        //           case 4: ctrans.transform(1, 0, 0, -1, 0, height); break;
                                        //           case 5: ctrans.transform(0, 1, 1, 0, 0, 0); break;
                                        //           case 6: ctrans.transform(0, 1, -1, 0, height, 0); break;
                                        //           case 7: ctrans.transform(0, -1, -1, 0, height, width); break;
                                        //           case 8: ctrans.transform(0, -1, 1, 0, 0, width); break;
                                        //           default: break;
                                        //         }
                                        //         // draw image
                                        //         ctrans.drawImage(imageup, 0, 0);
                                        //         updateImg(transcanvas.toDataURL());
                                        //         transcanvas = null;
                                        //     } else {
                                        //         updateImg(stasrc);
                                        //     }
                                        // });
                                        updateImg(stasrc);

                                        // callback 'onImageUpdate'
                                        if ($.isFunction(option.onImageUpdate)) {
                                            option.onImageUpdate.call();
                                        }
                                    };
                                };
                                reader.readAsDataURL(file);
                            }
                        });
                        obj.find('.mememe-fake-up').on('click', function(){
                            obj.find('.mememe-up').trigger('click');
                        });
                    }

                    /**
                     * add text areas
                     */
                    var textbutton = '<div class="mememe-group mememe-text-group"><button class="mememe-btn mememe-add-text mmm-tooltip" data-title="'+option.labels.new_text_box+'">'+'<i class="immm immm-text-box"></i></button></div>';

                    vmenu.append(textbutton);

                    /**
                     * Refresh buttons
                     */
                    var refreshbutton = '<button class="mememe-btn mememe-clear-canvas mmm-tooltip mememe-left" data-title="'+option.labels.reset+'"><i class="immm immm-cover-up"></i></button>';
                    vmenu.append('<div class="mememe-group">' + refreshbutton + '</div>');

                    /**
                     * Preview mode
                     */
                    var textPreview = '<label data-title="'+option.labels.preview+'" class="mememe-group mememe-preview-mode mememe-icon-checkbox mmm-tooltip"><input type="checkbox"><span class="checkmark mememe-btn"><svg width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/><path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/></svg></span></label>';
                    vmenu.append(textPreview);

                    obj.find('.mememe-preview-mode input').on('change', function(){
                        if(this.checked) {
                            $(this).next('.mememe-btn').addClass('active');
                            obj.find('.mememe-drag').addClass('mememe-hidden-textbox');
                        } else {
                            $(this).next('.mememe-btn').removeClass('active');
                            obj.find('.mememe-drag').removeClass('mememe-hidden-textbox');
                        }
                    });

                    /**
                     * PRO tools
                     */
                    var proclass = 'mememe-protools';
                    if ( option.show_tools == false) {
                        proclass += ' mememe-dropdown';
                        var tools = '<div class="mememe-group dropdown"><button class="mememe-btn mmm-tooltip mememe-toggle" data-target=".mememe-protools" data-title="'+option.labels.tools+'"><i class="immm immm-tools"></i></button></div>';
                        vmenu.append(tools);
                    }
                    
                    var protools = $('<div class="' + proclass + '"></div>');
                    vmenu.append(protools);

                    /**
                     * Color picker
                     */
                    // var colormenu = '<div class="mememe-group mememe-drawcolor-group"><div class="mememe-picker mmm-tooltip" data-title="'+option.labels.color+'">'+ '<i class="immm immm-colorize"></i> '+'<input name="mememe-set-color" type="hidden" class="minicolors" value="' + drawcolor + '"/>'+'</div></div>';
                    var colormenu = '<div class="mememe-group mememe-drawcolor-group mememe-protools-draw"><div class="mememe-picker mmm-tooltip" data-title="'+option.labels.color+'"><input name="mememe-set-color" type="hidden" class="minicolors" value="' + drawcolor + '"/></div></div>';

                    protools.append(colormenu);
                    
                    obj.find('.minicolors').minicolors({
                      position: 'bottom left',
                        change: function(value) {
                          drawcolor = value;
                        }
                    });

                    /**
                     * Color picker Text
                     */
                    var colormenutext = '<div class="mememe-group mememe-textcolor-group mememe-protools-text"><div class="mememe-picker mmm-tooltip" data-title="'+option.labels.color+'"><input name="mememe-set-color" type="hidden" class="textminicolors" value="' + color + '"/></div></div>';

                    protools.append(colormenutext);
                    
                    obj.find('.textminicolors').minicolors({
                      position: 'bottom left',
                        change: function(value) {
                          color = value;
                          // if (mode === 'text') {
                            obj.find('.dragmememe .mememe-editme').css('color', color);
                          // }
                        }
                    });

                    /**
                     * Font selector
                     */
                    function isJSON(str) {
                        try {
                            JSON.parse(str);
                        } catch (e) {
                            return false;
                        }
                        return true;
                    }

                    var parsed;
                    if (isJSON(option.fonts)) {
                        parsed = $.parseJSON(option.fonts);
                    } else {
                        parsed = option.fonts;
                    }

                    if (parsed.length > 0) {
                        font = parsed[0];
                    }

                    if (parsed.length > 1) {

                      var selectfont = '<div class="mememe-font-group mmm-tooltip mememe-left mememe-group mememe-protools-text" data-title="'+option.labels.font+'">'+'<select class="mememe-select-font mememe-select">';
                      
                        $.each(parsed, function(key, value){

                            selectfont += '<option value="'+value+'"';
                            if (value === mode) {
                              selectfont += ' selected';
                            }
                            selectfont += '>'+value+'</option>';
                        });

                        selectfont += '</select></div>';
                        
                        protools.append(selectfont);

                        // get font
                        if (obj.find('.mememe-select-font option:selected').length < 1) {
                            font = obj.find('.mememe-select-font').find('option:first-child').val();
                        } else {
                            font = obj.find('.mememe-select-font option:selected').val();
                        }

                        obj.find('.mememe-select-font').on('change', function(){
                            font = $(this).val();
                            loadCustomFont();
                        });
                    }

                    var texttools = $('<div class="mememe-protools-text mememe-protools-text-group"></div>');
                    protools.append(texttools);

                    /**
                     * Stroke/Shadow select
                     */
                    var selectstrokeshadow = '<div class="mememe-strokeshadow-group mmm-tooltip mememe-left mememe-group" data-title="'+option.labels.outlineshadow+'">'+'<select class="mememe-select mememe-select-strokeshadow">';
                    selectstrokeshadow += '<option value="stroke">'+option.labels.outline+'</option>';
                    selectstrokeshadow += '<option value="shadow">'+option.labels.shadow+'</option>';
                    selectstrokeshadow += '</select></div>';
                    texttools.append(selectstrokeshadow);

                    obj.find('.mememe-select-strokeshadow').on('change', function(){
                        updateOutline();
                    });

                    /**
                     * Outline / shadow Color picker
                     */
                    var strokecolormenu = '<div class="mememe-group mememe-strokecolor-group"><div class="mememe-picker mmm-tooltip" data-title="'+option.labels.outline_color+'"><input name="mememe-set-color" type="hidden" class="strokeminicolors" value="' + outcolor + '"/></div></div>';

                    texttools.append(strokecolormenu);
                    
                    obj.find('.strokeminicolors').minicolors({
                      position: 'bottom left',
                        change: function(value) {
                          outcolor = value;
                          if (mode === 'text') {
                            updateOutline();
                          }
                        }
                    });

                    /**
                     * Outline menu
                     */
                    var $strokesmenu = $('<div class="mememe-group mememe-strokemenu mmm-tooltip" data-title="'+option.labels.size+'"></div>');
                    texttools.append($strokesmenu);

                    /**
                     * Text shadow
                     */
                    var shadowinput = '<div class="mememe-group"><input type="number" class="mememe-set-shadow" min="0" max="9" value="' + outline + '"></div>';

                    texttools.append(shadowinput);
                    obj.find('.mememe-set-shadow').on('input', function(){

                        var max = parseInt($(this).attr('max'));
                        var min = parseInt($(this).attr('min'));
                        if ($(this).val() > max) {
                            $(this).val(max);
                        } else if ($(this).val() < min) {
                            $(this).val(min);
                        }
                        outline = $(this).val();
                        updateOutline();
                    });

                   /**
                    * text alignment menu
                    */
                    var textalignmodes = [
                      'left',
                      'center',
                      'right'
                    ];

                    var textalignmode = '<div class="mememe-group mmm-tooltip" data-title="'+option.labels.textalign+'" >';
                    var align = 'center';
                    $.each( textalignmodes, function( key, value ) {
                        var icon = '';
                        if (value === 'left') {
                            icon = '<svg width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M2 12.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/></svg>';
                        }
                        if (value === 'center') {
                          icon = '<svg width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4 12.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/></svg>';
                        }
                        if (value === 'right') {
                          icon = '<svg width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-4-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm4-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-4-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/></svg>';
                        }
                        textalignmode += '<button class="mememe-switch-textalign mememe-btn';

                        if (value === align) {
                            textalignmode += ' active';
                        }
                        textalignmode += '" data-align="'+value+'">'+icon+'</button>';                    
                    });
                    textalignmode += '</div>';
                    texttools.append(textalignmode);

                    obj.find('.mememe-switch-textalign').on('click', function(){
                        obj.find('.mememe-switch-textalign').removeClass('active');
                        $(this).addClass('active');
                        align = $(this).data('align');
                        obj.find('.mememe-active-drag .mememe-editme').css('text-align', align);
                    });

                    /**
                     * Brush strokes menu
                     */
                    if ( option.strokes.length > 1 ) {
                        $.each( option.strokes, function( key, value ) {
                            var strokeinput = '<button class="mememe-set-stroke mememe-btn mememe-protools-draw';
                            if (value === option.stroke) {
                                strokeinput += ' active';
                            }
                            strokeinput += '" data-stroke="' + value + '"><div class="mememe-brush"><div class="mememe-dot" style="width:' + value + 'px; height:' + value + 'px"></div></div></button>';
                            protools.append(strokeinput);
                        });

                        obj.find('.mememe-set-stroke').on('click', function(){
                            obj.find('.mememe-set-stroke').removeClass('active');
                            $(this).addClass('active');
                            stroke = $(this).data('stroke');
                        });
                    }

                    obj.find('.mememe-add-text').on('click', function(){
                        addText(1);
                    });

                    /**
                     * add spacer areas
                     */
                    if (option.spacer === 'on') {
                        var spacertools = '<div class="mememe-spacer-tools mememe-inputs"><div class="mememe-add-spacer mememe-spacer-group"><i class="immm immm-spacer"></i> ' + option.labels.spacer + '</div>';
                        spacertools += '<div class="mememe-spacer-group"><select class="mememe-select mememe-spacer-position"><option value="none">' + option.labels.none + '</otion><option value="top">' + option.labels.top + '</otion><option value="bottom">' + option.labels.bottom + '</otion><option value="topbottom">' + option.labels.topbottom + '</otion></select></div>';
                        spacertools += '<div class="mememe-spacer-group"><select class="mememe-select mememe-spacer-height"><option value="10">10%</otion><option value="5">20%</otion><option value="4" selected>25%</otion><option value="2">50%</otion><option value="1">100%</otion></select></div>';
                        spacertools += '<div class="mememe-spacer-group"><select class="mememe-select mememe-spacer-color"><option value="#fff">' + option.labels.white + '</otion><option value="#000">' + option.labels.black + '</otion><option value="' + option.bgcolor + '">' + option.labels.default + '</otion></select></div>';
                        spacertools += '</div>';

                        vmenu.append(spacertools);

                        obj.find('.mememe-spacer-tools select').on('change', function(){
                            var spacerpos = obj.find('.mememe-spacer-position').val();
                            var spacerh = obj.find('.mememe-spacer-height').val();
                            var spacercolor = obj.find('.mememe-spacer-color').val();
                            spacerImage(spacerpos, spacerh, spacercolor);
                            // if (spacerpos !== 'none') {
                            //     spacerImage(spacerpos, spacerh, spacercolor);
                            // }
                        });
                    }

                    /**
                     * Save button
                     */
                    var savebutton = '<button class="mememe-btn save-mememe disabled" disabled><i class="immm immm-check"></i> '+option.labels.save+'</button>';                    
                    obj.append(savebutton);
                    
                    obj.find('.save-mememe').on('click', function(e){
                        e.preventDefault();
                        if ( ! $(this).hasClass('disabled')) {
                            saveImageRemote();
                        }
                    });

                    obj.find('.mememe-clear-canvas').on('click', function(){
                        resetImage();
                    });
                      
                    obj.find('.mememe-toggle').on('click', function(e){
                        var target = $(this).data('target');
                        $(this).closest('.mememe-menu').find(target).fadeToggle('fast').addClass('open');
                        e.stopPropagation();
                    });

                    $('html').click(function(e) {
                        var target = e.target;
                        if ( ! $(target).is('.mememe-toggle') && ! $(target).parents('.mememe-protools').length ) {
                            obj.find('.mememe-menu .open').hide().removeClass('open');
                        }
                    });
                }

                function updateOutline(){

                    var stringshadow = 'none';
                    var stringstroke = 'unset';
                    var multiply;

                    if (outline > 0) {

                        var strokeshadow = obj.find('.mememe-select-strokeshadow').val();

                        if (strokeshadow == 'stroke') {
                            multiply = outline * 3; // used to simulate outline with text-shadow.
                            // stringstroke = outline * pixelratio + 'px '+ outcolor;

                        // // Old Stroke shadow css method
                        //     // var ii = 0;
                        //     // stringshadow = '';
                        //     // for (var i = 0; i <= outline; i++) {
                        //     //       ii = i/pixelratio;
                        //     //       stringshadow += ' -' + ii + 'px -' + ii + 'px 0 #000, '+ ii + 'px ' + ii + 'px 0 #000, '+ '-' + ii + 'px ' + ii + 'px 0 #000, '+ ii + 'px -' + ii + 'px 0 #000, '+ '-' + outline + 'px ' + ii + 'px 0 #000, '+ outline + 'px -' + ii + 'px 0 #000,';
                        //     //     }
                        //     // stringshadow = stringshadow.slice(0, -1);

                        } else {
                            multiply = 2;
                        }

                        stringshadow = '';
                        for (var i = 0; i <= multiply; i++) {
                            stringshadow += '0 0 ' + outline + 'px ' + outcolor;
                            if (i < multiply) {
                                stringshadow += ', ';
                            }
                        }
                    }

                    obj.find('.dragmememe .mememe-editme').css({
                        'text-shadow': stringshadow,
                        // '-webkit-text-stroke': stringstroke,
                        // 'text-stroke': stringstroke,
                        // 'paint-order': 'stroke fill'
                    });
                }

                /**
                 * Add google web font
                 */
                function loadCustomFont(){

                    var asterix = /[*]/g;
                    var customfont = font.search(asterix);

                    if (customfont == -1) {
                        var loadfont = font.split('+').join(' ');
                        WebFont.load({
                            google: {
                              families: [loadfont]
                            },
                            active: function(){
                                setFont();
                            },
                            fontinactive: function(familyName) {
                                console.log('Failed loading Google font: ' + familyName);
                                setFont();
                            }
                        });
                    } else {
                        setFont(); 
                    }
                }

                /**
                 * Update font to text fields
                 */                
                function setFont(){
                    var cleanfont = font;
                    var asterix = /[*]/g;
                    var customfont = font.search(asterix);
                    if (customfont != -1) {
                        cleanfont = font.replace(asterix, "");
                    }
                    var loadfont = cleanfont.split('+').join(' ');
                    var fontID = cleanfont.split(':')[0];
                    var fontVariants = cleanfont.split(':')[1];
                    var fontVariant = "normal";
                    var fontStyle = "normal";
                    if (fontVariants) {
                        fontVariant = fontVariants.replace("regular", "normal");
                        var italic = fontVariants.search("italic");
                        if (italic != -1) {
                            fontVariant = fontVariants.replace("italic", "");
                            fontStyle = 'italic';
                        }
                    }
                    var fontFamily = fontID.split('+').join(' ');
                    var dragmeinput = obj.find('.dragmememe .mememe-editme');

                    if (customfont != -1) {
                        dragmeinput.css({'font-family': fontFamily});
                    } else {
                        dragmeinput.css({'font-family': '\''+fontFamily+'\''});
                    }
                    dragmeinput.css({'font-weight': fontVariant, 'font-style': fontStyle}).trigger('input');
                }

                /**
                 * Resize canvases
                 */
                function resizeMememe(){

                    image = obj.find(option.baseimg);

                    ratio = image.outerWidth() / image.outerHeight();

                    var canvasWidth = image.outerWidth();
                    var canvasHeight = image.outerHeight();

                    obj.find('.mememe-draw').attr({'width': canvasWidth * pixelratio, 'height': canvasHeight * pixelratio});
                    obj.find('.mememe-draw').css({'width': canvasWidth+'px', 'height': canvasHeight+'px'});
                    obj.find('.mememe-drag').css({'width': canvasWidth+'px', 'height': canvasHeight+'px'});

                    finalcanvas.width = canvasWidth * pixelratio;  //size of new canvas, make sure they are proportional
                    finalcanvas.height = canvasHeight * pixelratio; //compared to original canvas

                    canvas.width = canvasWidth * pixelratio;  //size of new canvas, make sure they are proportional
                    canvas.height = canvasHeight * pixelratio; //compared to original canvas

                    ctx.scale(pixelratio, pixelratio);
                    ctf.scale(pixelratio, pixelratio);
                }

                /**
                 * Window resize
                 */
                var windowWidth = $(window).width();
                $(window).resize(function(e) {
                    if (e.target === window) {
                        if ($(this).width() !== windowWidth) {
                            windowWidth = $(this).width();
                            obj.find(option.baseimg).parent().css('height', 'auto');

                            resizeMememe();
                        }
                    }
                });

            }); // each
        } // mememe
    }); // extend
})(jQuery);

/*
 * fitMememe - jQuery Plugin
 * version: 1.0
 * Copyright 2020 Nicola Franchini - @nicolafranchini
 */
(function($){
    'use strict';
    $.fn.extend({
        //plugin name - fitMememe
        fitMememe: function(options) {
            var plugin = this;
            // default options
            var defaults = {
                minFontSize : 1,
                maxFontSize : 200
            };
            var option = $.extend(defaults, options);
            
            return this.each(function() {
                // Store the object
                var el = $(this);

                function isOverflown(element) {
                    return element[0].scrollHeight > element[0].clientHeight || element[0].scrollWidth > element[0].clientWidth;
                }

                function resizeText(element){

                    if (element.text().length) {
                        var fontSize = option.maxFontSize;
                        element.css('font-size', fontSize + "px");

                        for (var i = fontSize; i >= 0; i--) {
                            var overflow = isOverflown(el);
                            if (overflow) {
                             fontSize--;
                             element.css('font-size', fontSize + "px");
                            }
                        }
                    }
                }

                // Prevent double initialization
                if (el.data('fitMememe')) {
                    return true;
                }

                // method to be used outside the plugin
                plugin.resize = function() {
                    resizeText(el);
                };

                el.on('input', function(){
                    resizeText($(this));
                });

                // resizeText(el);
                el.data('fitMememe', true);
            }); // each
        } // fitMememe
    }); // extend
})(jQuery);
