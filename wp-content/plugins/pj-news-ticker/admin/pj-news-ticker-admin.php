<?php

if (!class_exists('PjNewsTickerAdmin'))
{
	/* just a change */
	class PjNewsTickerAdmin
	{
		private $options;

		public function __construct()
		{
			add_action('admin_init', array($this, 'initSettings'));
			add_action('admin_menu', array($this, 'addOptionsPage'));
			add_action('admin_enqueue_scripts', array($this, 'loadStyles'));
		}

		public function loadStyles()
		{
			wp_enqueue_script('wp-color-picker');
			wp_enqueue_style('wp-color-picker');
			wp_enqueue_script('pj-news-ticker-admin', plugins_url('js/pj-news-ticker-admin.js', __FILE__), array('wp-color-picker'), PjNewsTickerHelper::getVersion());
			wp_enqueue_style('pj-news-ticker-admin', plugins_url('css/pj-news-ticker-admin.css', __FILE__), array('wp-color-picker'), PjNewsTickerHelper::getVersion());
		}

		public function initSettings()
		{
			// array that holds all the options
			register_setting('pj-news-ticker-group', 'pj-news-ticker-options', array('PjNewsTickerHelper', 'sanitize'));

			// ticker content option section
			add_settings_section('pjnt-ticker-content-section', 'Ticker Content Options', array($this, 'printTickerContent_cb'), 'pj-news-ticker');
			add_settings_field('num_posts', 'Number of Posts', array($this, 'num_posts_cb'), 'pj-news-ticker', 'pjnt-ticker-content-section');
			add_settings_field('post_type', 'Post Type', array($this, 'post_type_cb'), 'pj-news-ticker', 'pjnt-ticker-content-section');
			add_settings_field('post_cat', 'Category of Posts', array($this, 'post_cat_cb'), 'pj-news-ticker', 'pjnt-ticker-content-section');
			add_settings_field('override_posts_custom_color', 'Use Custom Text Color', array($this, 'override_posts_custom_colour_cb'), 'pj-news-ticker', 'pjnt-ticker-content-section');
			add_settings_field('override_posts_custom_text_color', 'Custom Text Colour', array($this, 'override_posts_custom_text_colour_cb'), 'pj-news-ticker', 'pjnt-ticker-content-section');
			add_settings_field('override_posts', 'Show Custom Text', array($this, 'override_posts_cb'), 'pj-news-ticker', 'pjnt-ticker-content-section');
			add_settings_field('override_posts_custom_text', 'Custom Text', array($this, 'override_posts_custom_text_cb'), 'pj-news-ticker', 'pjnt-ticker-content-section');
			add_settings_field('override_posts_custom_text_url', 'Custom Text Link', array($this, 'override_posts_custom_text_url_cb'), 'pj-news-ticker', 'pjnt-ticker-content-section');
			add_settings_field('no_content_text', 'No Content Text', array($this, 'no_content_text_cb'), 'pj-news-ticker', 'pjnt-default-settings-section');
			add_settings_field('show_excerpt', 'Show Excerpt?', array($this, 'show_excerpt_cb'), 'pj-news-ticker', 'pjnt-default-settings-section');
			add_settings_field('target', 'Link Target', array($this, 'target_cb'), 'pj-news-ticker', 'pjnt-default-settings-section');


			// ticker display options section
			add_settings_section('pjnt-ticker-display-section', 'Ticker Display Options', array($this, 'printTickerStyle_cb'), 'pj-news-ticker');
			add_settings_field('show_label', 'Show Label?', array($this, 'show_label_cb'), 'pj-news-ticker', 'pjnt-ticker-display-section');
			add_settings_field('label_text', 'Label Text', array($this, 'label_text_cb'), 'pj-news-ticker', 'pjnt-ticker-display-section');
			add_settings_field('label_text_colour', 'Label Text Colour', array($this, 'label_text_colour_cb'), 'pj-news-ticker', 'pjnt-ticker-display-section');
			add_settings_field('label_bg_colour', 'Label Background Colour', array($this, 'label_bg_colour_cb'), 'pj-news-ticker', 'pjnt-ticker-display-section');
			add_settings_field('ticker_bg_colour', 'Ticker Background Colour', array($this, 'ticker_bg_colour_cb'), 'pj-news-ticker', 'pjnt-ticker-display-section');
			add_settings_field('speed', 'Speed', array($this, 'speed_cb'), 'pj-news-ticker', 'pjnt-ticker-display-section');
			add_settings_field('size', 'Font Size', array($this, 'size_cb'), 'pj-news-ticker', 'pjnt-ticker-display-section');
			add_settings_field('gap', 'Gap Between Cycles?', array($this, 'gap_cb'), 'pj-news-ticker', 'pjnt-ticker-display-section');
			add_settings_field('hide_if_empty', 'Hide If No Posts?', array($this, 'hide_if_empty_cb'), 'pj-news-ticker', 'pjnt-ticker-display-section');
			add_settings_field('top_banner', 'Show as Top Banner', array($this, 'top_banner_cb'), 'pj-news-ticker', 'pjnt-ticker-display-section');
			add_settings_field('top_banner_all_pages', 'Show on All Pages', array($this, 'top_banner_all_pages_cb'), 'pj-news-ticker', 'pjnt-ticker-display-section');
			add_settings_field('top_banner_flag', 'Select Condition', array($this, 'top_banner_flag_cb'), 'pj-news-ticker', 'pjnt-ticker-display-section');
			add_settings_field('top_banner_pages', 'Page ID, slug or title', array($this, 'top_banner_pages_cb'), 'pj-news-ticker', 'pjnt-ticker-display-section');
			add_settings_field('google_font_choice', 'Google Font Choice', array($this, 'google_font_choice_cb'), 'pj-news-ticker', 'pjnt-ticker-display-section');	
			add_settings_field('google_font_url', 'Google Font URL', array($this, 'google_font_url_cb'), 'pj-news-ticker', 'pjnt-ticker-display-section');	
			add_settings_field('google_font', 'Google Font', array($this, 'google_font_cb'), 'pj-news-ticker', 'pjnt-ticker-display-section');
		}

		public function printTickerContent_cb ()
		{
			echo '<p>What do we show in the ticker?</p>';
		}

		public function printTickerStyle_cb ()
		{
			echo '<p>How should the ticker be displayed?</p>';
		}

		public function printDefaultSettingsSectionInfo ()
		{
			echo '<p>A News Ticker created with a simple shortcode [pj-news-ticker] will use these default settings.</p>';
			echo '<p>To override individual tickers: <a href="#" id="pjnt-shortcode-help-show">View</a><a href="#" id="pjnt-shortcode-help-hide">Hide</a></p>';
			echo '<div id="pjnt-shortcode-help">';
			echo '[pj-news-ticker<br />';
			echo '&nbsp;&nbsp;num_posts=&quot;5&quot; - Defaults to showing 5 most recent posts, use &quot;-1&quot; for all matching posts<br />';
			echo '&nbsp;&nbsp;post_type=&quot;slug&quot; - Choose the type of post to display, default to &quot;post&quot; for normal posts, or select a custom post type<br />';
			echo '&nbsp;&nbsp;post_cat=&quot;slug&quot; - Choose the slug of a category to limit the posts, use a comma to separate multiple categories, use &quot;0&quot; for all categories (default)<br />';
			echo '&nbsp;&nbsp;show_label=&quot;true&quot; - &quot;true&quot; or &quot;false&quot;, to show a label for the News Ticker<br />';
			echo '&nbsp;&nbsp;label_text=&quot;Latest Posts&quot; - If a label is shown, what text to use<br />';
			echo '&nbsp;&nbsp;label_text_colour=&quot;#ffffff&quot; - If a label is shown, what colour is the text<br />';
			echo '&nbsp;&nbsp;label_bg_colour=&quot;#1e73be&quot; - If a label is shown, what colour is the background<br />';
			echo '&nbsp;&nbsp;ticker_bg_colour=&quot;#ffffff&quot; - What colour is the background for the ticker<br />';
			echo '&nbsp;&nbsp;no_content_text=&quot;No matching posts&quot; - The text to display if no matching posts are found<br />';
			echo '&nbsp;&nbsp;show_excerpt=&quot;false&quot; - &quot;true&quot; or &quot;false&quot;, to show the excerpt for each post<br />';
			echo '&nbsp;&nbsp;speed=&quot;100&quot; - The speed to scroll by, in pixels per second<br />';
			echo '&nbsp;&nbsp;size=&quot;100%&quot; - The size of the text, can be in px, or em, or %<br />';
			echo '&nbsp;&nbsp;target=&quot;_self&quot; - The target for the links, can be _self or _blank<br />';
			echo '&nbsp;&nbsp;gap=&quot;true&quot; - &quot;true&quot; or &quot;false&quot; - Choose whether to show a gap between cycles of the marquee content, defaults to &quot;true&quot; for classic marquee style, set to &quot;false&quot; for new infinite scrolling style marquee<br />';
			echo '&nbsp;&nbsp;hide_if_empty=&quot;true&quot; - &quot;true&quot; or &quot;false&quot; - Choose whether to hide the plugin if no matching posts are found, defaults to &quot;false&quot;<br />';
			echo '&nbsp;&nbsp;override_posts_custom_colour=&quot;true&quot; or &quot;false&quot; - Choose whether to use a custom color or not, defaults to &quot;false&quot;<br />';
			echo '&nbsp;&nbsp;override_posts_custom_text_colour=&quot;#cf2727&quot; - If custom colour is true, what colour is the text<br />';
			echo '&nbsp;&nbsp;override_posts=&quot;false&quot; - &quot;true&quot; or &quot;false&quot; - Choose whether to hide the posts and show custom text instead, defaults to &quot;false&quot;<br />';
			echo '&nbsp;&nbsp;override_posts_custom_text=&quot;Custom Text&quot; - If custom text is shown, what text to use<br />';
			echo '&nbsp;&nbsp;override_posts_custom_text_url=&quot;https://google.ca&quot; - If custom text is shown, what url should it use. If left blank no URL will be added<br />';
			echo '&nbsp;&nbsp;google_font_choice=&quot;custom, predefined&quot; - Chose to use a custom Google Font URL or a predefined one from our library, defaults to &quot;Custom URL&quot;<br />';
			echo '&nbsp;&nbsp;google_font_url= - Enter the Google font URL, example &quot;https://fonts.googleapis.com/css?family=Open+Sans&display=swap&quot;, defaults to blank<br />';
			echo '&nbsp;&nbsp;google_font=&quot;Poppins, Roboto, Oswald, Open Sans, Lato, Montserrat&quot; - Select the Google Font from our list<br />';
			echo ']';
			echo '</div>';
		}

		public function num_posts_cb()
		{
			$this->renderMenu('num_posts', array(-1 => 'All', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 10 => '10', 15 => '15', 20 => '20'));
		}

		public function target_cb()
		{
			$this->renderMenu('target', array('_self' => 'Same page (_self)', '_blank' => 'New page (_blank)'));
		}

		public function post_cat_cb()
		{
			$cats = array(0 => 'All');
			$wpCats = get_categories(array('orderby' => 'name', 'order' => 'ASC'));
			foreach ($wpCats as $wpCat)
			{
				$cats[$wpCat->slug] = $wpCat->name;
			}
			$this->renderMenu('post_cat', $cats, true);
		}

		public function post_type_cb()
		{
			$types = array('post' => 'Post');
			$wpTypes = get_post_types(array('public' => true, '_builtin' => false), 'objects', 'and');
			foreach ($wpTypes as $wpType)
			{
				$labels = get_post_type_labels($wpType);
				$types[$wpType->name] = $labels->name;
			}
			$this->renderMenu('post_type', $types);
		}

		public function show_label_cb()
		{
			$this->renderRadioButtons('show_label', array('true' => 'True', 'false' => 'False'));
		}

		public function show_excerpt_cb()
		{
			$this->renderRadioButtons('show_excerpt', array('true' => 'True', 'false' => 'False'));
		}

		public function gap_cb()
		{
			$this->renderRadioButtons('gap', array('true' => 'True', 'false' => 'False'));
		}

		public function hide_if_empty_cb()
		{
			$this->renderRadioButtons('hide_if_empty', array('true' => 'True', 'false' => 'False'));
		}

		public function override_posts_cb()
		{
			$this->renderRadioButtons('override_posts', array('true' => 'True', 'false' => 'False'));
		}

		public function override_posts_custom_colour_cb()
		{
			$this->renderRadioButtons('override_posts_custom_colour', array('true' => 'True', 'false' => 'False'));
		}

		public function top_banner_cb()
		{
			$this->renderRadioButtons('top_banner', array('true' => 'True', 'false' => 'False'));
		}

		public function top_banner_all_pages_cb()
		{
			$this->renderRadioButtons('top_banner_all_pages', array('true' => 'True', 'false' => 'False'));
		}

		public function top_banner_flag_cb()
		{
			$this->renderMenu('top_banner_flag', array('true' => 'Show on these pages', 'false' => 'Do not show on these pages'));
		}

		public function google_font_choice_cb()	
		{	
			$this->renderMenu('google_font_choice', array('custom' => 'Custom URL', 'predefined' => 'Choose from our library'));
		}	
		public function google_font_cb()	
		{	
			$this->renderMenu('google_font', array('Poppins' => 'Poppins', 'Roboto' => 'Roboto', 'Montserrat' => 'Montserrat', 'Open Sans' => 'Open Sans', 'Oswald' => 'Oswald', 'Lato' => 'Lato'));	
		}

		public function top_banner_pages_cb()
		{
			$this->renderTextField('top_banner_pages');
		}

		public function label_text_cb()
		{
			$this->renderTextField('label_text');
		}

		public function override_posts_custom_text_cb()
		{
			$this->renderTextField('override_posts_custom_text');
		}

		public function override_posts_custom_text_colour_cb()
		{
			$this->renderColourPicker('override_posts_custom_text_colour');
		}

		public function override_posts_custom_text_url_cb()
		{
			$this->renderTextField('override_posts_custom_text_url');
		}

		public function google_font_url_cb()	
		{	
			$this->renderTextField('google_font_url');	
		}

		public function size_cb()
		{
			$this->renderTextField('size');
		}

		public function speed_cb()
		{
			$this->renderTextField('speed');
		}

		public function label_text_colour_cb()
		{
			$this->renderColourPicker('label_text_colour');
		}

		public function label_bg_colour_cb()
		{
			$this->renderColourPicker('label_bg_colour');
		}

		public function ticker_bg_colour_cb()
		{
			$this->renderColourPicker('ticker_bg_colour');
		}

		public function no_content_text_cb()
		{
			$this->renderTextField('no_content_text');
		}

		public function renderMenu($id, $options, $multiple = false)
		{
			printf('<select name="pj-news-ticker-options[%s]%s" %s>', $id, ($multiple ? '[]' : ''), ($multiple ? 'multiple' : ''));
			if (isset($this->options[$id])) {
				if ($multiple) {
					$thisVal = explode(',', $this->options[$id]);
				} else {
					$thisVal = array($this->options[$id]);
				}
			} else {
				$thisVal = array();
			}
			foreach ($options as $value => $label)
			{
				printf('<option value="%s" %s/>%s</option>', $value, in_array((string)$value, $thisVal) ? 'selected="selected" ' : '', $label);
			}
			printf('</select>');
		}

		public function renderRadioButtons($id, $options)
		{
			foreach ($options as $value => $label)
			{
				printf('%s <input type="radio" name="pj-news-ticker-options[%s]" value="%s" %s/>', $label, $id, $value, (isset($this->options[$id]) && $this->options[$id] == $value) ? 'checked ' : '');
			}
		}

		public function renderTextField($id)
		{
			printf('<input type="text" name="pj-news-ticker-options[%s]" value="%s" />', $id, isset( $this->options[$id] ) ? esc_attr($this->options[$id]) : '');
		}

		public function renderColourPicker($id)
		{
			printf('<input type="text" name="pj-news-ticker-options[%s]" value="%s" class="color-field" />', $id, isset($this->options[$id]) ? esc_attr($this->options[$id]) : '' );
		}

		public function addOptionsPage()
		{
			add_submenu_page('options-general.php', 'PJ News Ticker Settings', 'PJ News Ticker', 'manage_options', 'pj-news-ticker', array($this, 'renderOptions') );
		}

		public function renderOptions()
		{
			// check user capabilities
			if (!current_user_can('manage_options'))
			{
				return;
			}

			// check if any settings have been updated
			if (isset($_GET['settings-updated']))
			{
				add_settings_error('pjnt_messages', 'pjnt_message', 'Settings Saved', 'updated');
			}

			// show the options form
			$this->options = array_replace(PjNewsTickerHelper::getDefaultOptions(), get_option('pj-news-ticker-options', array()));
			?>
			<div class="wrap pjnt-settings-wrap">
				<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
				<form action="options.php" method="post">
				<?php
					// This prints out all hidden setting fields
					$this->printDefaultSettingsSectionInfo();
					settings_fields('pj-news-ticker-group');
					do_settings_sections('pj-news-ticker');
					submit_button('Save Settings');
				?>
				</form>
			</div>
			<?php
		}
	}

	new PjNewsTickerAdmin();
}
