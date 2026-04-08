jQuery(function($){
	/*
	 * Select/Upload image(s) event
	 */
	var custom_uploader = wp.media({
		title: 'Insert image',
		library : {
			type : 'image'
		},
		button: {
			text: 'Use this image' // button label text
		},
		multiple: 'add' // false || 'add' || 'toggle'
	});

	$('body').on('click', '.mememe_upload_template', function(e){
		e.preventDefault();
		custom_uploader.open();
	});

	custom_uploader.on('select', function() { // it also has "open" and "close" events 

		var container = $('.mememe-templates-container');
		var current_page = container.data('page');
		var per_page = parseInt(container.data('perpage'));
		var attachments = custom_uploader.state().get('selection');

		container.html('');

		var thumb;
		var attachment_ids = [];
		var last_selection = {};

		attachments.each(function(attachment) {
			attachment_ids.push(attachment.id); 
			thumb = attachment.attributes.url;
			if ( attachment.attributes.sizes ) {
				if ( attachment.attributes.sizes.thumbnail ) {
					if ( attachment.attributes.sizes.thumbnail.url ) {
						thumb = attachment.attributes.sizes.thumbnail.url;
					}
				}
			}
			last_selection[attachment.id] = thumb;
		});

  		var last_page = attachment_ids.slice( -per_page );
  		var last_items = last_page.reverse();

		$('.mememe_all_templates').val(attachment_ids);
		$('.mememe-template-counter').html(attachment_ids.length);
		$('.mememe-template-pagination').hide();

		last_items.forEach(function(index) {
			$('.mememe-templates-container').append('<div class="mememe-template-wrap" data-id="' + index + '"><div class="mememe-thumb-wrap mememe_upload_template"><img src="'+ last_selection[index] +'"></div><a href="#" class="mememe_remove_template"><svg width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16 8A8 8 0 110 8a8 8 0 0116 0zm-4.146-3.146a.5.5 0 00-.708-.708L8 7.293 4.854 4.146a.5.5 0 10-.708.708L7.293 8l-3.147 3.146a.5.5 0 00.708.708L8 8.707l3.146 3.147a.5.5 0 00.708-.708L8.707 8l3.147-3.146z" clip-rule="evenodd"/></svg></a></div>');
		});
	});

	custom_uploader.on('open',function() {
		var selection = custom_uploader.state().get('selection');
		var ids_value = $('.mememe_all_templates').val();

		if (ids_value.length > 0) {
			var ids = ids_value.split(',');

			ids.forEach(function(id) {
				attachment = wp.media.attachment(id);
				// attachment.fetch();
				selection.add(attachment ? [attachment] : []);
			});
		}
	});
 
	/*
	 * Remove template
	 */
	$('body').on('click', '.mememe_remove_template', function(){
		var id = $(this).parent().data('id').toString();
		var alltemp = $('.mememe_all_templates').val();
		var alltemparray = alltemp.split(',');
		var alltempupdate = removeItemOnce(alltemparray, id);
		$('.mememe-template-pagination').hide();
		$('.mememe_all_templates').val(alltempupdate);
		$('.mememe-template-counter').html(alltempupdate.length);

		$(this).parent().remove();

		return false;
	});

	function removeItemOnce(arr, value) { 
		var index = arr.indexOf(value);
		if (index > -1) {
			arr.splice(index, 1);
		}
		return arr;
	} 
});
