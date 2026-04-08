  jQuery(function($){
    $('.btn-toolbar,.fea-btn').hide();
    $('.la-title').attr('contenteditable', 'false'); 
    $('.deactive,.hid').hide();
    $('.eneditor').removeClass('editor');
    $('#la-save').hide();
    function initToolbarBootstrapBindings() {
      var fonts = ['Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 
            'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Lucida Sans', 'Tahoma', 'Times',
            'Times New Roman', 'Verdana'],
            fontTarget = $('[title=Font]').siblings('.dropdown-menu');
      $.each(fonts, function (idx, fontName) {
          fontTarget.append($('<li><a data-edit="fontName ' + fontName +'" style="font-family:\''+ fontName +'\'">'+fontName + '</a></li>'));
      });
      $('a[title]').tooltip({container:'body'});
    	$('.dropdown-menu input').click(function() {return false;})
		    .change(function () {$(this).parent('.dropdown-menu').siblings('.dropdown-toggle').dropdown('toggle');})
        .keydown('esc', function () {this.value='';$(this).change();}); 

      $('[data-role=magic-overlay]').each(function () { 
        var overlay = $(this), target = $(overlay.data('target')); 
        overlay.css('opacity', 0).css('position', 'absolute').offset(target.offset()).width(target.outerWidth()).height(target.outerHeight());
      });
      if ("onwebkitspeechchange"  in document.createElement("input")) {
        var editorOffset = $('#editor').offset();
        $('#voiceBtn').css('position','absolute').offset({top: editorOffset.top, left: editorOffset.left+$('#editor').innerWidth()-35});
      } else {
        $('#voiceBtn').hide();
      }
	};
	function showErrorAlert (reason, detail) {
		var msg='';
		if (reason==='unsupported-file-type') { msg = "Unsupported format " +detail; }
		else {
			console.log("error uploading file", reason, detail);
		}
		$('<div class="alert"> <button type="button" class="close" data-dismiss="alert">&times;</button>'+ 
		 '<strong>File upload error</strong> '+msg+' </div>').prependTo('#alerts');
	};

    $('.activep').click(function(event) {
      $('.btn-toolbar,.fea-btn,.hid').show();
      $('.wdo-outer').css('border', '1px solid #e8e8e8').css('padding', '10px 20px');
       initToolbarBootstrapBindings();
      window.prettyPrint && prettyPrint();
      $(this).hide();
      $('.deactive').show();
      $('.la-title').attr('contenteditable', 'true');
      $('.eneditor').addClass('editor').attr('contenteditable', 'true');
      $('.editor').wysiwyg({
        fileUploadError: showErrorAlert
      });
      $('#la-save').show();
    });


    $('.deactive').click(function(event) {
      $('.btn-toolbar,.fea-btn,.hid').hide();
      $(this).hide();
      $('.deactive').hide();
      $('.la-title').attr('contenteditable', 'false');
      $('.activep').show();
      $('.eneditor').removeClass('editor').attr('contenteditable', 'false');
      $('#la-save').hide();
    });



  jQuery('.loader').hide();
  $('#la-save').click(function(event) {
      jQuery('.loader').show();
      event.preventDefault();
      var title = $('.la-title').html();
      var content = $('.eneditor').html();
      var image = $('.post-thumbnail img,.wp-post-image,.wp-post-image').attr('src');
      var data = {
        action: 'save_content_front',
        content: content,
        title: title,
        image: image,
        imageid: $(".post-thumbnail img,.wp-post-image,.wp-post-image").attr("id"),
        id: $('#la-id').data('laid'),
        imagesrc : image,
      };
      $.post(laAjax.url, data, function(resp) {
        // console.log(resp);
        window.location.reload(true);
        jQuery('.loader').hide();
      });
    });

    $('#upload-btn').click(function(e) {
      e.preventDefault();
      var image = wp.media({
        title: 'Upload Image',

        multiple: false
      }).open()
        .on('select', function(e) {

          var uploaded_image = image.state().get('selection').first();

          console.log(uploaded_image);
          var image_url = uploaded_image.toJSON().url;

          $(".eneditor").prepend('<img src="' + image_url + '">');


        });
    });


    $('.fea-btn').on("click", ".add", function(e) {
      e.preventDefault();
      var image = wp.media({
        title: 'Upload Image',

        multiple: false
      }).open()
        .on('select', function(e) {

          var uploaded_image = image.state().get('selection').first();

          var image_url = uploaded_image.toJSON().url;
          var imageid = uploaded_image.toJSON().id;
          $(".post-fea").append('<img style="width:100%;" src="' + image_url + '" id="' + imageid + '" class="wp-post-image">');

        });

    });

        $('.fea-btn').on("click", ".fea", function(e) {
      e.preventDefault();
      var image = wp.media({
        title: 'Upload Image',

        multiple: false
      }).open()
        .on('select', function(e) {

          var uploaded_image = image.state().get('selection').first();

          var image_url = uploaded_image.toJSON().url;
          var imageid = uploaded_image.toJSON().id;

          $(".post-thumbnail img,.wp-post-image").attr("src", image_url);
          $(".post-thumbnail img,.wp-post-image").attr("srcset", image_url);
          $(".post-thumbnail img,.wp-post-image").attr("id", imageid);



        });

    });

    $('.fea-btn ').on('click', '.remo', function(event) {
      event.preventDefault();
      // $('.wp-post-image').remove();
      $('.post-thumbnail img,.wp-post-image').remove();

    });
    
  });