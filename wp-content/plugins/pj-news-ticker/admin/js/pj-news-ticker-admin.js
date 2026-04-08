
jQuery(document).ready(function($){
    $('.color-field').wpColorPicker();

	$("#pjnt-shortcode-help").hide();
	$("#pjnt-shortcode-help-hide").hide();
	$("#pjnt-shortcode-help-show").click(function(){
	    $("#pjnt-shortcode-help").show();
	    $("#pjnt-shortcode-help-show").hide();
	    $("#pjnt-shortcode-help-hide").show();
	});
	$("#pjnt-shortcode-help-hide").click(function(){
	    $("#pjnt-shortcode-help").hide();
	    $("#pjnt-shortcode-help-show").show();
	    $("#pjnt-shortcode-help-hide").hide();
	});

	// show & hide google custom font url text field on select
	var google_font_choice = $("select[name='pj-news-ticker-options[google_font_choice]'] option:selected").val();
	if(google_font_choice === 'custom') {
		$("select[name='pj-news-ticker-options[google_font]']").hide();
		$("select[name='pj-news-ticker-options[google_font]']").parent().prev().hide();
		$("select[name='pj-news-ticker-options[google_font]']").parent().parent().hide();
	} else {
		$("input[name='pj-news-ticker-options[google_font_url]']").hide();
		$("input[name='pj-news-ticker-options[google_font_url]']").parent().prev().hide();
		$("input[name='pj-news-ticker-options[google_font_url]']").parent().parent().hide();
	}
	$("select[name='pj-news-ticker-options[google_font_choice]']").change(function() {
		if($(this).children("option:selected").val() === 'custom'){
			$("input[name='pj-news-ticker-options[google_font_url]']").show();
			$("input[name='pj-news-ticker-options[google_font_url]']").parent().prev().show();
			$("input[name='pj-news-ticker-options[google_font_url]']").parent().parent().show();
			$("select[name='pj-news-ticker-options[google_font]']").hide();
			$("select[name='pj-news-ticker-options[google_font]']").parent().prev().hide();
			$("select[name='pj-news-ticker-options[google_font]']").parent().parent().hide();
		} else {
			$("select[name='pj-news-ticker-options[google_font]']").show();
			$("select[name='pj-news-ticker-options[google_font]']").parent().prev().show();
			$("select[name='pj-news-ticker-options[google_font]']").parent().parent().show();
			$("input[name='pj-news-ticker-options[google_font_url]']").hide();
			$("input[name='pj-news-ticker-options[google_font_url]']").parent().prev().hide();
			$("input[name='pj-news-ticker-options[google_font_url]']").parent().parent().hide();
		}
	});
});
