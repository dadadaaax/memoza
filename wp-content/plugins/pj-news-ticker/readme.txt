=== Plugin Name ===
Contributors: nicolasmontigny, pauljura
Tags: marquee, news ticker, jQuery news ticker, news headlines
Requires at least: 4.6
Tested up to: 5.5.1
Stable tag: trunk
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

PJ News Ticker is a small plugin that shows your most recent posts in a marquee style.

== Description ==

**This plugin is now maintained by the developers at Primitiv Media**

PJ News Ticker is a small plugin that shows your most recent posts in a marquee style.

You can embed the news ticker anywhere you like using shortcodes.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/pj-news-ticker` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->PJ News Ticker screen to configure the plugin defaults
4. Use the shortcode in your page, post, or widgets

Shortcode instructions:

Using default settings:
[pj-news-ticker]

Customise:

[pj-news-ticker
  * num_posts="5" - Defaults to showing 5 most recent posts, use "-1" for all matching posts
  * post_type="slug" - Choose the type of post to display, default to "post" for normal posts, or select a custom post type
  * post_cat="slug" - Choose the slug of a category to limit the posts, use a comma to separate multiple categories, use "0" for all categories (default)
  * show_label="true" - "true" or "false", to show a label for the News Ticker
  * label_text="Latest Posts" - If a label is shown, what text to use
  * label_text_colour="#ffffff" - If a label is shown, what colour is the text
  * label_bg_colour="#1e73be" - If a label is shown, what colour is the background
  * ticker_bg_colour="#ffffff" - What colour is the background for the ticker
  * no_content_text="No matching posts" - The text to display if no matching posts are found
  * show_excerpt="false" - "true" or "false", to show the excerpt for each post
  * speed="100" - The speed in pixels per second
  * size="100%" - The font size, can be in em, px, or %
  * target="_self" - The target for the links, can be _self or _blank
  * gap="true" - Choose whether to show a gap between cycles of the marquee content, defaults to "true" for classic marquee style, set to "false" for new infinite scrolling style marquee
  * hide_if_empty="false" - Choose whether to hide the plugin if no matching posts are found, defaults to "false"
  * override_posts_custom_colour= "True" or "false"; - Choose whether to use a custom color or not, defaults to "false"
  * override_posts_custom_text_colour="#cf2727" - If custom colour is true, what colour is the text
  * override_posts="false" - "true" or "false" - Choose whether to hide the posts and show custom text instead, defaults to "false"
  * override_posts_custom_text="Custom Text" - If custom text is shown, what text to use
  * override_posts_custom_text_colour="#cf2727" - If custom text is shown, what colour is the text
  * override_posts_custom_text_url="https://google.ca" - If custom text is shown, what url should it use. If left blank no URL will be added
  * google_font_choice="custom, predefined" - Chose to use a custom Google Font URL or a predefined one from our library, defaults to "Custom URL"
  * google_font_url= - Enter the Google font URL, example "https://fonts.googleapis.com/css?family=Open+Sans&display=swap", defaults to blank
  * google_font="Poppins, Roboto, Oswald, Open Sans, Lato, Montserrat" - Select the Google Font from our list
]

== Frequently Asked Questions ==

= How to add this to your site =

The easiest way is just to add a page and use the shortcode.

If you want to add this somewhere else on your site, like header, footer, sidebar, etc, then probably the easiest way is to go to Appearance, Widgets, and place a text widget somewhere in your layout, and use the shortcode in that. For this to work, you will need to use a plugin that will enable shortcodes in widgets. This is handy to have, not just for this plugin but for all your plugins. Do a google search, you will find plenty of resources explaining how to achieve this. Good luck!

== Screenshots ==

1. How it looks
2. Settings

== Changelog ==

= 1.8 =
* Tested with WordPress 5.5.1
* Changed how the custom color works. You can now apply a custom color regardless of the custom text being used or not.

= 1.7 =

* Added override posts with text options
* Added google fonts options
* Added options to use ticker as a banner
* Updated the look of the options page

= 1.6 =
* Added option to hide plugin if there are no posts to display

= 1.5.4 =
* Fixed unable to choose 'All' posts in settings

= 1.5.3 =
* Fix for wp_enqueue_scripts and wp_enqueue_style problem

= 1.5.2 =
* Fix for show_excerpt problem, again

= 1.5.1 =
* Fix for show_excerpt problem

= 1.5 =
* Can now choose multiple categories for posts

= 1.4 =
* Added option for custom post types
* Added option for no gap between cycles

= 1.3 =
* Added target option for links

= 1.2.1 =
* Fixed broken styles in admin area with RTL languages (Arabic)

= 1.2 =
* Added option to show excerpt

= 1.1.2 =
* Fixed showing all posts

= 1.1.1 =
* Fixed javascript problem causing conflicts with other plugins

= 1.1 =
* Added speed and font size options

= 1.0 =
* First release

== Upgrade Notice ==

= 1.6 =
* Added option to hide plugin if there are no posts to display

= 1.5.4 =
* Fixed unable to choose 'All' posts in settings

= 1.5.3 =
Fix for wp_enqueue_scripts and wp_enqueue_style problem

= 1.5.2 =
Fix for show_excerpt problem, again

= 1.5.1 =
Fix for show_excerpt problem

= 1.5 =
Can now choose multiple categories for posts

= 1.4 =
Added option for custom post types
Added option for no gap between cycles

= 1.3 =
Added target option for links

= 1.2 =
Added option to show excerpt

= 1.1 =
Added speed and font size options

= 1.0 =
First release
