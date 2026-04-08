<?php
/*
Plugin Name: PJ News Ticker
Plugin URI:  https://wordpress.org/plugins/pj-news-ticker/
Description: PJ News Ticker is a small plugin that shows your most recent posts in a marquee style.
Version:     1.8
Author:      Paul Jura & Nicolas Montigny
Author URI:  https://primitiv.media/
License:     GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: pj-news-ticker
Domain Path: /languages

PJ News Ticker - A basic news ticker for Wordpress
Copyright (C) 2020  Paul Jura & Nicolas Montigny

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

define('PJNT_VERSION', '1.8');

/**
 * Static class available for content and admin pages
 */
if (!class_exists('PjNewsTickerHelper'))
{
	class PjNewsTickerHelper
	{
		public static function getVersion()
		{
			return PJNT_VERSION;
		}

		public static function getDefaultOptions()
		{
			return array(
				'num_posts' => 5,
				'post_cat' => '0',
				'post_type' => 'post',
				'show_label' => 'true',
				'label_text' => 'Latest Posts',
				'label_text_colour' => '#ffffff',
				'label_bg_colour' => '#1e73be',
				'ticker_bg_colour' => '#ffffff',
				'no_content_text' => 'There are no matching posts to display...',
				'speed' => 100,
				'size' => '100%',
				'show_excerpt' => 'false',
				'target' => '_self',
				'gap' => 'true',
				'hide_if_empty' => 'false',
				'override_posts' => 'false',
				'override_posts_custom_colour' => 'false',
				'override_posts_custom_text' => 'Custom Text',
				'override_posts_custom_text_colour' => '#cf2727',
				'override_posts_custom_text_url' => 'https://google.ca',
				'top_banner' => 'false',
				'top_banner_all_pages' => 'true',
				'top_banner_flag' => 'only_on',
				'top_banner_pages' => '',
				'google_font_url' => '',
				'google_font' => 'Poppins',
				'google_font_choice' => 'custom'
			);
		}

		public static function sanitize($input)
		{
			$newInput = self::getDefaultOptions();
			if (isset($input['num_posts'])) $newInput['num_posts'] = intval($input['num_posts']);
			if (isset($input['post_type'])) $newInput['post_type'] = sanitize_text_field($input['post_type']);
			if (isset($input['post_cat'])) {
				if (is_array($input['post_cat'])) {
					$newInput['post_cat'] = array_map('sanitize_text_field', $input['post_cat']);
					if (in_array('0', $newInput['post_cat'])) {
						$newInput['post_cat'] = '0';
					} else {
						$newInput['post_cat'] = join(',', $newInput['post_cat']);
					}
				} else {
					$newInput['post_cat'] = sanitize_text_field($input['post_cat']);
				}
			}
			if (isset($input['show_label']) && in_array($input['show_label'], array('true', 'false'))) $newInput['show_label'] = $input['show_label'];
			if (isset($input['label_text'])) $newInput['label_text'] = sanitize_text_field($input['label_text']);
			if (isset($input['label_text_colour'])) $newInput['label_text_colour'] = self::validateHtmlColour($input['label_text_colour']);
			if (isset($input['label_bg_colour'])) $newInput['label_bg_colour'] = self::validateHtmlColour($input['label_bg_colour']);
			if (isset($input['ticker_bg_colour'])) $newInput['ticker_bg_colour'] = self::validateHtmlColour($input['ticker_bg_colour']);
			if (isset($input['no_content_text'])) $newInput['no_content_text'] = sanitize_text_field($input['no_content_text']);
			if (isset($input['speed'])) $newInput['speed'] = abs(intval($input['speed']));
			if (isset($input['size'])) $newInput['size'] = sanitize_text_field($input['size']);
			if (isset($input['show_excerpt']) && in_array($input['show_excerpt'], array('true', 'false'))) $newInput['show_excerpt'] = $input['show_excerpt'];
			if (isset($input['target']) && in_array($input['target'], array('_self', '_blank'))) $newInput['target'] = sanitize_text_field($input['target']);
			if (isset($input['gap']) && in_array($input['gap'], array('true', 'false'))) $newInput['gap'] = sanitize_text_field($input['gap']);
			if (isset($input['hide_if_empty']) && in_array($input['hide_if_empty'], array('true', 'false'))) $newInput['hide_if_empty'] = sanitize_text_field($input['hide_if_empty']);
			if (isset($input['override_posts']) && in_array($input['override_posts'], array('true', 'false'))) $newInput['override_posts'] = sanitize_text_field($input['override_posts']);
			if (isset($input['override_posts_custom_colour']) && in_array($input['override_posts_custom_colour'], array('true', 'false'))) $newInput['override_posts_custom_colour'] = sanitize_text_field($input['override_posts_custom_colour']);
			if (isset($input['override_posts_custom_text'])) $newInput['override_posts_custom_text'] = sanitize_text_field($input['override_posts_custom_text']);
			if (isset($input['override_posts_custom_text_colour'])) $newInput['override_posts_custom_text_colour'] = self::validateHtmlColour($input['override_posts_custom_text_colour']);
			if (isset($input['override_posts_custom_text_url'])) $newInput['override_posts_custom_text_url'] = self::validateUrl($input['override_posts_custom_text_url']);
			if (isset($input['top_banner']) && in_array($input['top_banner'], array('true', 'false'))) $newInput['top_banner'] = sanitize_text_field($input['top_banner']);
			if (isset($input['top_banner_all_pages']) && in_array($input['top_banner_all_pages'], array('true', 'false'))) $newInput['top_banner_all_pages'] = sanitize_text_field($input['top_banner_all_pages']);
			if (isset($input['top_banner_flag']) && in_array($input['top_banner_flag'], array('true', 'false'))) $newInput['top_banner_flag'] = sanitize_text_field($input['top_banner_flag']);
			if (isset($input['top_banner_pages'])) $newInput['top_banner_pages'] = sanitize_text_field($input['top_banner_pages']);
			if (isset($input['google_font_url'])) $newInput['google_font_url'] = self::validateUrlTwo($input['google_font_url']);
			if (isset($input['google_font_choice']) && in_array($input['google_font_choice'], array('custom', 'predefined'))) $newInput['google_font_choice'] = sanitize_text_field($input['google_font_choice']);
			if (isset($input['google_font']) && in_array($input['google_font'], array('Poppins', 'Roboto', 'Montserrat', 'Open Sans', 'Oswald', 'Lato'))) $newInput['google_font'] = sanitize_text_field($input['google_font']);

			return $newInput;
		}

		public static function validateHtmlColour($input)
		{
			if (preg_match('/^#[a-f0-9]{6}$/i', $input))
			{
				return $input;
			}
			else
			{
				return '#000000';
			}
		}

		public static function validateUrl($input)
		{
			// Remove all illegal characters from a url
			$input = filter_var($input, FILTER_SANITIZE_URL);
			// Validate url
			if (filter_var($input, FILTER_VALIDATE_URL)) {
				return $input;
			} else {
				return 'Invalid URL';
			}

		}

		public static function validateUrlTwo($input)
		{
			// Remove all illegal characters from a url
			$input = filter_var($input, FILTER_SANITIZE_URL);
			// Validate url
			if (filter_var($input, FILTER_VALIDATE_URL) && strpos($input, '|') == false && strpos($input, ',') == false) {
				return $input;
			} else {
				return 'Invalid URL';
			}

		}

	}
}

if (is_admin())
{
	require_once(dirname(__FILE__).'/admin/pj-news-ticker-admin.php');
}

if (!class_exists('PjNewsTicker') && !is_admin())
{
	class PjNewsTicker
	{
		private $defaultOptions;

		public function __construct()
		{
			$this->defaultOptions = array_replace(PjNewsTickerHelper::getDefaultOptions(), get_option('pj-news-ticker-options', array()));
			$this->addShortcode();
			add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
			add_action('wp_body_open', array($this,'custom_content_after_body_open_tag'));
		}

		protected function addShortCode()
		{
			add_shortcode('pj-news-ticker', array($this, 'renderNewsTicker'));
		}

		public static function enqueueScripts( $atts = array(), $content = null ) {
			// news ticker js
			wp_enqueue_script( 'pj-news-ticker', plugins_url( 'public/js/pj-news-ticker.js', __FILE__ ), array( 'jquery' ), PjNewsTickerHelper::getVersion() );
			// css
			wp_enqueue_style( 'pj-news-ticker', plugins_url( 'public/css/pj-news-ticker.css', __FILE__ ), array(), PjNewsTickerHelper::getVersion() );

			//enqueue google font
			$atts         = array_change_key_case( (array) $atts, CASE_LOWER );
			$options      = shortcode_atts( array_replace( PjNewsTickerHelper::getDefaultOptions(), get_option( 'pj-news-ticker-options', array() ) ), $atts );
			$page_id      = get_queried_object_id();
			$page_content = get_post( $page_id )->post_content;
			$array_of_pages = explode(",", $options['top_banner_pages']);

			function add_google_fonts($page_content,$options) {
				if ( $options['google_font_choice'] === 'custom' && $options['google_font_url'] != '' && $options['google_font_url'] !== "Invalid URL" ) {
					wp_enqueue_style( 'pj-news-ticker-google-fonts', $options['google_font_url'], false );
				} else {
					wp_enqueue_style( 'pj-news-ticker-google-fonts', 'https://fonts.googleapis.com/css?family=' . $options['google_font'], false );
				}

				if ( preg_match_all( '/google_font=["\'](.*?)["\']/', $page_content, $google_fonts ) ) {
					$counter = 0;
					foreach ( $google_fonts[1] as $google_font ) {
						$counter ++;
						wp_enqueue_style( 'pj-news-ticker-google-fonts-' . $counter, 'https://fonts.googleapis.com/css?family=' . $google_font, false );
					}
				}

				if ( preg_match_all( '/google_font_url=["\'](.*?)["\']/', $page_content, $google_fonts_url ) ) {
					$counter = 0;
					foreach ( $google_fonts_url[1] as $google_font_url ) {
						$counter ++;
						wp_enqueue_style( 'pj-news-ticker-google-fonts-' . $counter, $google_font_url, false );
					}
				}
			}

			if ( has_shortcode( $page_content, 'pj-news-ticker' ) || $options['top_banner'] === 'true') {
				if ( $options['top_banner_all_pages'] === 'false' ) {
					if ( $options['top_banner_flag'] === 'true' ) {
						if ( is_page( $array_of_pages ) ) {
							add_google_fonts($page_content, $options);
						}
					} else {
						if ( !is_page( $array_of_pages ) ) {
							add_google_fonts($page_content, $options);
						}
					}
				} else {
					add_google_fonts($page_content, $options);
				}
			}
		}



		public function custom_content_after_body_open_tag($atts = array(), $content = null) {
			// Override default options with the shortcode options
			$atts = array_change_key_case((array)$atts, CASE_LOWER);
			$options = shortcode_atts($this->defaultOptions, $atts);

			$array_of_pages = explode(",", $options['top_banner_pages']);

			if($options['top_banner'] === 'true') {
				if ( $options['top_banner_all_pages'] === 'false' ) {
					if ( $options['top_banner_flag'] === 'true' ) {
						if ( is_page( $array_of_pages ) ) {
							echo do_shortcode( '[pj-news-ticker]' );
						}
					} else {
						if ( !is_page( $array_of_pages ) ) {
							echo do_shortcode( '[pj-news-ticker]' );
						}
					}
				} else {
					echo do_shortcode( '[pj-news-ticker]' );
				}
			}
		}

		public function renderNewsTicker($atts = array(), $content = null)
		{
			// Override default options with the shortcode options
			$atts = array_change_key_case((array)$atts, CASE_LOWER);
			$options = shortcode_atts($this->defaultOptions, $atts);



			// Get the posts
			$postOptions = array(
				'numberposts'	=> $options['num_posts'],
				'category'		=> 0,
				'orderyby'		=> 'date',
				'order'			=> 'DESC',
				'post_type'     => $options['post_type'],
			);
			if (!empty($options['post_cat']))
			{
				$postOptions['category_name'] = $options['post_cat'];
			}
			$posts = get_posts($postOptions);

			// return early if no posts and option to hide is set
			if (count($posts) == 0 && $options['hide_if_empty'] == 'true') {
				return $content;
			}

			if($options['google_font_choice'] === 'custom') {
				$font_family = $options['google_font_url'];
				if ( strpos( $font_family, '&' ) !== false ) {
					preg_match( '~=(.*?)&~', $font_family, $font_family );
					$font_family = str_replace( "+", " ", $font_family[1] );
					if ( strpos( $font_family, ':' ) !== false ) {
						$font_family = strstr( $font_family, ':', true );
					}
				} else {
					$font_family = substr( $font_family, strpos( $font_family, "=" ) + 1 );
					if ( strpos( $font_family, ':' ) !== false ) {
						$font_family = strstr( $font_family, ':', true );
					}
					if ( strpos( $font_family, "+" ) !== false ) {
						$font_family = str_replace( "+", " ", $font_family );
					}
				}
			} else {
				$font_family = $options['google_font'];
			}

			// Render the marquee
			if ($options['show_label'] == 'true')
			{
				$content .= '<div class="pjnt-border" style="font-family: '. $font_family .', sans-serif; background-color: '.$options['label_bg_colour'].'; border-color: '.$options['label_bg_colour'].';">';
				$content .= '<div class="pjnt-label" style="color: '.$options['label_text_colour'].'; font-size: '.$options['size'].';">'.$options['label_text'].'</div>';
			}
			$content .= '<div class="pjnt-box" style="background-color: '.$options['ticker_bg_colour'].';">';
			$content .= '<div class="pjnt-content" data-gap="'.$options['gap'].'" data-speed="'.$options['speed'].'" style="font-size: '.$options['size'].';">';
			if (count($posts) == 0)
			{
				$content .= '<span class="pjnt-item">';
				$content .= $options['no_content_text'];
				$content .= '</span>';
			}
			else
			{
				if($options['override_posts'] == 'false') {
					$isFirst = true;
					foreach ( $posts as $post ) {
						$content .= '<span class="pjnt-item">';
						if($options['override_posts_custom_colour'] == 'false') {
							$content .= '<a target="' . $options['target'] . '" href="' . get_permalink( $post ) . '">' . get_the_title( $post );
							}
							else {
								$content .= '<a target="' . $options['target'] . '" style="color: ' . $options['override_posts_custom_text_colour'] . ';" href="' . get_permalink( $post ) . '">' . get_the_title( $post );
							}
							if ( $options['show_excerpt'] == 'true' ) {
							setup_postdata( $post );
							$content .= ' - ' . get_the_excerpt( $post );
						}
						$content .= '</a>';
						$content .= '</span>';
					}
				} else {
					if($options['override_posts_custom_text_url'] !== '' && $options['override_posts_custom_text_url'] !== 'Invalid URL') {
						$content .= '<span class="pjnt-item">';
						if($options['override_posts_custom_colour'] == 'false'){
							$content .= '<a target="' . $options['target'] . '" href="' . $options['override_posts_custom_text_url'] . '">' . $options['override_posts_custom_text'];
						} else {
							$content .= '<a target="' . $options['target'] . '" style="color: ' . $options['override_posts_custom_text_colour'] . ';" href="' . $options['override_posts_custom_text_url'] . '">' . $options['override_posts_custom_text'];
						}
						$content .= '</a>';
						$content .= '</span>';
					} else {
						if($options['override_posts_custom_colour'] == 'false'){
							$content .= '<span class="pjnt-item">';
						} else {
							$content .= '<span class="pjnt-item" style="color: ' . $options['override_posts_custom_text_colour'] . ';">';
						}
						$content .= $options['override_posts_custom_text'];
						$content .= '</span>';
					}
				}
			}
			$content .= '</div>'; // end content
			$content .= '</div>'; // end marquee box
			if ($options['show_label'] == 'true')
			{
				$content .= '</div>'; // end border container
			}
			return $content;
		}
	}

	new PjNewsTicker();
}
