=== Colorful Categories ===
Contributors: loyaltymanufaktur, madeinua
Tags: categories, widget, coloured, colourful categories
Requires at least: 4.9
Tested up to: 6.8
Stable tag: 2.0.21
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

New Categories widget in the awesome style! Bring colours to your categories widget - make every category in its colour.

== Description ==

New Categories widget in the awesome style! Bring colours to your categories widget - make every category in its colour.

All colours are stored as term meta variables.

<h4>Features:</h4>

<ul>
<li>custom taxonomies support</li>
<li>manually customize colours for each category</li>
<li>intelligent (automatic) colours selection</li>
<li>different themes</li>
<li>easy to customize</li>
<li>responsive and flexible widget</li>
</ul>

== Installation ==

1. Unzip the archive on your computer
2. Upload `colorful-categories` directory to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Add a widget to the sidebar on the page 'Widgets'. You can change widget title, taxonomy, theme and show/hide posts count
5. (optional) Customize colours on the page to manage categories (under the column 'Color')

== Frequently Asked Questions ==

= How to assign new colour? =

Open the page to edit tags or categories and change the colour in the column "Color".

= How does intelligent colour selection work? =

The plugin does check if the category name contains one of the keywords and sets the pre-defined colour for this category.
For example, a category that contains Facebook will be coloured to a dark blue colour.

= How to get a colour for a specific category? =

Use Wordpress default function: get_term_meta($term_id, 'cc_color', true);

== Screenshots ==

1. Categories widget in bubble theme.
2. Categories widget in box theme.
3. Page to manage categories with new column 'Color'.

== Changelog ==

= 2.0.21 =
* Tested up to Wordpress 6.8.

= 2.0.20 =
* Tested up to Wordpress 6.6.

= 2.0.19 =
* Tested up to Wordpress 6.4.

= 2.0.18 =
* WordPress 6.0 support.

= 2.0.17 =
* Fixed a problem with displaying.

= 2.0.16 =
* Improved plugin stability and security.

= 2.0.15 =
* Enhanced security.

= 2.0.14 =
* Minor changes.

= 2.0.13 =
* Fixed colors saving process.
* Wordpress 5.4 support added.

= 2.0.12 =
* Removed column "Color" from the private taxonomies.
* Small code refactoring.

= 2.0.11 =
* Wordpress 5.3 support added.

= 2.0.10 =
* Wordpress 5.2 support added.

= 2.0.9 =
* Updated file-header information and readme.txt.

= 2.0.8 =
* Added filter 'colorful_categories_term_text'.

= 2.0.7 =
* Added action 'colorful_categories_styles' to customize the output of theme styles.
* Small design improvements.

= 2.0.6 =
* Fixed functionality to handle translations.

= 2.0.5 =
* Small backend design improvements.

= 2.0.4 =
* Added class "current" to the categories that are currently selected.

= 2.0.3 =
* Updated functionality due to deprecated parameter in the function get_terms().

= 2.0.2 =
* Fixed compatibility with PHP 5.3-.

= 2.0.1 =
* Improved update script.

= 2.0.0 =
* Plugin completely updated to work with the WordPress term meta. All the stored data is moved from the options table to the terms meta table.

= 1.1.2 =
Fixed option "Show empty categories" in the widget settings.

= 1.1.1 =
* Compatibility with the latest version of WordPress.

= 1.1.0 =
* Minor improvements and optimization.

= 1.1.0 =
* Fixed issue with the browser cache.
* Fixed issue with an empty colour cell right after the adding of a new category/tag.
* Improved functionality: the colour will be removed from the database after removing the related term.
* Small minor improvements.

= 1.0.6 =
* Added a few new filters for better customization.
* A lot of small improvements, fixes and updates.

= 1.0.5 =
* Added field to limit categories to be displayed for the widget.
* Fixed field to select a theme.

= 1.0.4 =
* Added field to exclude categories from the widget.

= 1.0.3 =
* Fixed colours column.

= 1.0.2 =
* Fixed link to the term (category, tag, etc).

= 1.0.1 =
* Added support for non-hierarchical taxonomies.
* Added customizable message 'List is empty' in case there is nothing to display in the widget.

= 1.0 =
* Initial release.