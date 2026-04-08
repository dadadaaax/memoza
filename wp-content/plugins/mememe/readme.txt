=== MeMeMe plugin ===
Author: Nicola Franchini
Version: 1.9.0
Tested up to: 5.8
Stable tag: 1.9
Requires at least: 3.5
Requires PHP: 5.3
Text Domain: mememe
Author URI: http://www.veno.es
Plugin URI: http://veno.es/mememe
License: Exclusvely sold on CodeCanyon
License URI: https://codecanyon.net/item/mememe-ultimate-meme-generator-wp-plugin/20518209
Tags: meme, gag, meme generator

Advanced meme generator

== Description ==

Easy to use and full featured WordPress plugin to quickly turn your WordPress into a Meme portal, or to simply offer a funny tool for your users.

### Features

* **Responsive**
* **Touch enabled UI**
* **Free drawing with custom stroke sizes**
* **Text Blocks with custom outline size**
* **RTL support**
* **800+ Fonts available from Google Fonts**
* **Custom Fonts**
* **Custom color for text and pencil**
* **Custom color for application buttons**
* **3 Widgets available**
* **3 shortcodes**
* **List available templates**
* **List generated memes (saved as custom post types)**
* **"Re-caption this" option**
* **Masonry galleries**
* **Filter memes by category**
* **Filter templates by Tag**
* **Gallery custom options: columns, thumbnail size, margins, corners, shadows, style**
* **Meme social share links**
* **Meme rating system**
* **Report abuse link**

= Shortcodes =

**Meme generator**

[mememe]

**List Memes**

[mememe-list]

**List Templates**

[mememe-tempates]

== Installation ==

**Via WP**
Go under Plugins > Add New, click on Upload Plugin and select the file mememe.zip.

**Via FTP**
Upload the plugin folder /mememe/ to the /wp-content/plugins/ directory.

Activate the plugin through the Plugins menu in WordPress.

Go to Settings->MeMeMe and save default settings for the first time (or edit them).

== Changelog ==

= 1.9.0 =
* Update: disable lazy-loading for WP-Optimize
* Update: disable lazy-loading for WP Rocket plugin
* Update: disable lazy-loading for Smush plugin
* Update: disable lazy-loading forr Lazy Loader plugin
* Update: disable lazy-loading for LiteSpeed Cache Loader plugin
* Update: CMB2
* New: Polish language

= 1.8.9 =
* Fix: open keyboard after textblock select with Chrome/Android

= 1.8.8 =
* Fix: load carousel
* Fix: correct coordinates for new text boxes if latest is big
* Fix: empty all text boxes with clear button
* Fix: avoid set cursor to end for empty text boxes

= 1.8.7 =
* New: Option to hide submission message
* Fix: Load default text
* Fix: RTL support

= 1.8.6 =
* New: custom meme category slug
* New: custom template tag slug

= 1.8.5 =
* Update: Set category and tag slugs according to custom post slug

= 1.8.4 =
* Fix: Spacer update

= 1.8.3 =
* Update: include attachments to search
* Update: recaption button below attachments

= 1.8.2 =
* Fix: Show first line of text inside title

= 1.8.1 =
* Fix: Show first line of text on Chrome and Safari

= 1.8.0 =
* Fix: Generator carousel css

= 1.7.9 =
* Update: filter custom font families with quotes
* Update: set [...] between text blocks in post title
* Fix: minor css adjustments

= 1.7.8 =
* New: multiline text blocks on enter key
* New: text alignment
* New: option autoplay template carousel
* New: Reddit, Tumblr, Buffer, WhatsApp social share links
* Update: set default category also without choice for users
* Fix: No url encode permalink inside sharing text field
* Fix: save default text stroke if 0
* Fix: show mememe icon on classic editor

= 1.7.7 =
* New: option Post description for social sharing
* New: selective tags for template gallery widget
* New: optional Filters for meme galleries block and widget
* New: optional Filters for template galleries widget
* Fix: generator carousel with specific template tags
* Fix: remove template from selection if attachment has been removed from media library
* Fix: specific naming for get template link and post rating

= 1.7.6 =
* New: option Select default meme category
* New: option Default shadow / outline color
* Update: select text outline or shadow
* Update: cut Meme title and slug to 90 chars
* Update: separate color picker for text and pencil
* Update: darken text shadow
* Fix: clean bold text borders
* Fix: error saving only one template
* Fix: error updating posts per page number inside mememe-gallery block
* Fix: empty default meme title
* Minor fixes

= 1.7.5 =
* Update: set text shadow instead of borders
* Update: improved web font loading
* Update: preview mode (hide text box handles)

= 1.7.4 =
* Update: use meme title as image name
* Update: skip lazy load for gallery images

= 1.7.3 =
* Update: register scripts on init
* Update: better performance for backend template management

= 1.7.2

* Fix: Default background color without template
* Fix: reset spacer position on change template

= 1.7.1 =
* Fix: image orientation via EXIF data
* Fix: reset spacer to none

= 1.7.0 =
* Fix: List categories if available
* Fix: css share buttons color priority
* Fix: load generated image using CORS

= 1.6.9 =
* Fix: Hidden generator title field
* Update: Remove lenght limit to meme title

= 1.6.8 =
* New: Rotatable text blocks
* New: Optional spacer above and below the image
* Update: unique ID for multiple generator forms
* Update: code refactoring, generator process improved
* Update: minor css fixes

= 1.6.7 =
* New: Social share buttons
* Update: set focus() only to explicitly added text boxes
* Update: new style to rating buttons
* Update: load script only on request
* Fix: Product search with WooCommerce active
* Fix: Gutenberg blocks options UI

= 1.6.6 =
* Update: fix Elementor widget hook

= 1.6.5 =
* Update: custom generated image name taken from meme slug

= 1.6.4 =
* Update: gutenberg block compatible with WP 5.3

= 1.6.3 =
* New: take title from text blocks if empty
* New: option hide title above the generator
* New: option select / checkbox pills categories above the generator
* Fix: load default fonts if empty selection
* Update: plugin update checker

= 1.6.2 =
* New: option [mememe template="..."] for default template on generator
* New: option load custom fonts
* Update: fadein template on load image inside generator
* Fix: js error with only 1 template selected
* Fix: load more templates
* Fix: css buttons

= 1.6.1 =
* Fix: templates widget
* Update: Load template by slug
* Update: create meme button below template attachment page
* Update: default template limit = post_per_page
* Update: full support for widgets inside Beaver Builder and Elementor live editors

= 1.6.0 =
* Fix: re-caption this meme

= 1.5.9 =
* Update: Gutenberg `deprecated` option for the migration to the new blocks

= 1.5.8 =
* New: selective tags for templates gallery `[mememe-templates tags=""]`
* New: selective tags for generator's templates  `[mememe tags=""]`
* New: option filters to hide templates gallery filters `[mememe-templates filters="1"]`
* Update: remove button to text boxes
* Update: add new text box on enter key
* Update: meme_tag to main query

= 1.5.7 =
* Update: place watermark image at the canvas edges

= 1.5.6 =
* Update: now available all the Google font variants (weight, style)
* Fix: best positioning for the text redraw
* Update: new `center-top`, `center-bottom`, `left-center`, `right-center` positions for the watermark
* Update: backend libraries

= 1.5.5 =
* New option: custom post slug
* New option: report abuse
* Update: Get exif data and adjust the image orientation
* Update: download attribute to image link. (force file download)
* Update: security improvements
* Fix: normalize tools size

= 1.5.4 =
* Update: stronger input sanitize + cut long titles

= 1.5.3 =
* Fix: Security improvement, avoid external templates

= 1.5.2 =
* New: Meme Rating system
* Update: Skip Jetpack Lazy Images
* Update: enable Gutenberg on mememe CPT
* Update: og:tags improved for mememe CPT
* Update: remove JS 3s timeout
* Fix: [mememe-list category=""]

= 1.5.1 =
* Update: transform old mememe shortcodes to new gutenberg blocks

= 1.5.0 =
* New: Gutenberg Blocks
* Fix: Columns mispelling
* Fix: show title setting for template galleries
* New: menu icon

= 1.4.9 =
* Update: script loading optimization
* Update: gallery layout fix for jetpack lazy images

= 1.4.8 =
* New option: Initial Text (pre-filled text boxes)
* Fix: reset stage size after user upload
* Fix: save tall images from Safari
* Update: jquery minicolors 2.3.1
* Update: owl carousel v2.3.4
* Update: Isotope v3.0.6

= 1.4.7 =
* New: Light and dark color options
* Update: Available pages and posts as recaption destination
* Fix: Hide recaption button if template is not availabe
* Fix: Bug generating memes with YOAST and some themes

= 1.4.6 =
* New option: mandatory title
* Update: First category selected
* Update: Meme title as img alt
* Fix: add mememe search to main query
* minicolors Update: RTL support for color picker

= 1.4.5 =
* Fix: plugin updater bug raised in version 1.4.4

= 1.4.4 =
* New: set custom initial width to text boxes
* New: view meme thumbnails in admin listing
* New: Hide or display inline the tools
* Update: Delete post image when removing a meme (delete permanently)

= 1.4.3 =
* New: set more than one default text box
* Update: set custom /mememe directory inside /uploads/
* Update: support for PHP 7.2
* Update: prepend text field if overflow y
* Update: cross-origin="Anonymous" for img toDataURL()
* Fix:  Notice: Undefined index: thumb_h on activation

= 1.4.2 =
* Fix: relayout galleries

= 1.4.1 =
* Update: Packery layout for galleries
* Fix: update css for RTL mode
* Update: support for jetpack lazy load images

= 1.4.0 =
* Update: author option for [mememe-list]
* Update: RTL support
* Fix: [meme-list] per_page option
* Fix list all pages to assign recaption destination

= 1.3.0 =
* New: Image watermark over generated memes
* New: Automatic updates
* Update: Plugin details under plugins admin panel
* Update: Backward compatibility with Aruna theme and other themes / plugins using old versions of Isotope
* Update: show only images under media manager

= 1.2.0 =
* New: Masonry layouts with filter option for all the galleries
* New: `thumbsize`, `paginate` and `margin` options for memes and templates list
* New: `random` and `title` for templates lists
* New: Random option on Meme widget lists
* New: multiple thumbnail size option for memes and templates lists
* New: `class` attribute to customize `.mememe-wrap-gallery`, on memes and templates lists
* New: Pre compiled classes for multiple gallery styles
* New: Category filter for the generated Memes
* New: Attachments tags for templates filtering
* New: Load more button on tempates and memes lists
* New: Added /mememe/ posts to search results 
* New: `og:tags` for sharing single memes with image preview
* Update: recaption link inside meme list
* Update: download image link below single meme
* Update: translations
