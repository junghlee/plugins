=== WP Category Images ===
Contributors: elCHAVALdelaWEB
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DNZ7D68MBS6KN
Tags: categories, thumbnails, images, taxonomies
Requires at least: 3.0
Tested up to: 3.4
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple plugin that permits to images to Categories, Tags and Custom Taxonomies.

== Description ==

This simple plugin permits you to upload images to categories, tags and custom taxonomies just like to posts.
This feature is very useful when you want to show unique image in header tor every category or even some slideshow of images.
Also you can show a list of categories/subcategories with a little thumbnail am much more.

By the way, this plugin just uses wordpress native functionality - no aditional tables and nothing like that.
Also it uses wordpress images so all sizes are generated automatically (custom sizes included).

== Installation ==

1. Upload `wp_cat_images` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place `<?php cat_image(<term_id>); ?>` in your templates to show category thumbnail

== Frequently Asked Questions ==

= How to get other image sizes =
`
	cat_image(
		<term_id>, 				// ID of category/taxonomy to show thumbnail from
		[echo],					// return(false) or print(true) the thumbnail code (default: TRUE)
		[thumbnail_size]		// any registered sizes are accepted 'thumbnail', 'medium', 'large','original' or any custom registered size (default: "thumbnail")
	)
`
= How to get all images attached to category =
`
	$cat_images = get_cat_images(
		<term_id>,				// ID of category/taxonomy to get thumbnails from
		[limit]					// Maximum images count to return or -1 to return all of them (default: -1)
	)
`

== Screenshots ==

http://elchavaldelaweb.com/wp-content/uploads/2013/08/WP_Category_Images_screenshot.png

== Changelog ==

First release

== Upgrade Notice ==

First release