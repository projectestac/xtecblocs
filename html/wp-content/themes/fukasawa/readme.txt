=== Fukasawa ===
Contributors: Anlino
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=anders%40andersnoren%2ese&lc=US&item_name=Free%20WordPress%20Themes%20from%20Anders%20Noren&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Requires at least: 4.6
Requires PHP: 5.6
Tested up to: 6.7
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html


== Installation ==

1. Upload the theme
2. Activate the theme

All theme specific options are handled through the WordPress Customizer.


== Licenses ==

Lato
License: SIL Open Font License, 1.1 
Source: https://fonts.google.com/specimen/Lato

Genericon font icon set
License: GNU GPL 2.0
Source: http://genericons.com

screenshot.png images
License: CC0 Public Domain 
Source: http://www.unsplash.com

Flexslider jQuery Slider
License: GNU GPL 2.0
Source: http://flexslider.woothemes.com


== Frequently Asked Questions ==


== Use the gallery post format 

1. Go to Admin > Posts > Add New.
2. Select the "Gallery" post format in the Post Attributes box.
3. Click "Add Media" and upload the images you wish to display in the gallery.
4. Close the Media window and publish/update the post.
5. The images you uploaded should now be displayed in the post gallery.


== Use the video post format

1. Create a new post.
2. Select "Video" in the Format window to the right.
3. In the post content, enter the full url to the video you want to include.
4. Directly after the url, add the <!--more--> tag (or "More" block in the Block Editor) followed by the rest of the content. Example:

https://www.youtube.com/watch?v=iszwuX1AK6A
<!--more-->
The rest of the content...

5. Publish.
6. The video will now be displayed in the featured image position, above the title of your post.


== Changelog ==

Version 2.1.3 (2025-02-23)
-------------------------
- Wrapped widget registration in `widgets_init` action, fixing notice for `_load_textdomain_just_in_time()`, added in 6.7.
- Removed `load_theme_textdomain()` call, since it's no longer required for WordPress.org themes.
- Removed Flickr widget, since the API it was using is no longer available.
- Updated theme description to reflect the removed Flickr widget.
- Bumped "Requires at least" to 4.6.
- Bumped "Tested up to" to 6.7.

Version 2.1.1 (2022-06-30)
-------------------------
- Fixed the screenshot.

Version 2.1 (2022-06-29)
-------------------------
- Switched from the Google Fonts CDN to font files included in the theme folder.
- Bumped "Tested up to" to 6.0.
- Removed the www prefix from andersnoren.se URLs.

Version 2.0.5 (2020-08-23)
-------------------------
- Removed the Dribbble widget, since Dribbble no longer supports the type of feed used to retrieve images.
- Removing the widget also removed the use of `create_function()`, which is deprecated starting in PHP 7.2.
- Added "Tested up to" and "Requires PHP" to style.css, and added "Requires PHP" to readme.txt.

Version 2.0.4 (2020-05-26)
-------------------------
- Updated the CSS targeting of the `post` element on archive pages to play nicer with custom post types.

Version 2.0.3 (2020-05-04)
-------------------------
- Fixed the author name being misaligned on author archives.

Version 2.0.2 (2020-04-29)
-------------------------
- Fixed list styles being unintentionally added to some elements (a story in two acts).

Version 2.0.1 (2020-04-29)
-------------------------
- Fixed lists missing a list style (bullets/numbers).

Version 2.0.0 (2020-04-27)
-------------------------
- Updated the theme folder structure with an `/assets/` folder, and moved assets to it.
- Renamed the editor style files.
- Removed Genericons font files no longer needed to support modern browsers (IE10+).
- Removed the license.txt file.
- Removed output of "Comments are closed" when comments are closed.
- Updated conditional for comment output accordingly.
- Updated `template-archive.php` to use `singular.php` for output, with the archive list included in a separate file.
- Replaced `<div class="clear"></div>` elements with pseudo clearing determined by a class on the wrapper.
- Updated templates to use more semantic HTML5 elements.
- Updated the title on attachment pages to use a H1 heading.
- Updated conditional for when to use a H1 element for the site title.
- Added output of the site title as screen reader text to the header site logo.
- Removed unneccessary title attributes from links.
- Removed obsolete vendor prefixes.
- Removed font antialiasing.
- Added output of the widget ID attributes to the widget area.
- Added support for the core custom_logo setting, and updated the old fukasawa_logo setting to only be displayed if you already have a fukasawa_logo image set (thanks, @poena).
- Bumped the "Requires at least" tag to 4.5, since Fukasawa now uses custom_logo.
- Removed Customizer live preview.
- Increased the color contrast of light gray colors.
- Fixed a mismatch between the block editor palette settings and the actual block editor colors.
- Renamed the "Regular" block editor font size slug to "normal", to make it the default choice in the block editor.
- Added output of an edit link after the content on post types other than posts.
- Simplified the header structure on mobile.
- Restructured and simplified global.js, and made the Masonry function more reliable.
- Added theme version to enqueues.
- Social block margin fix.
- Updated "Tested up to" to 5.4.
- Fixed potential notice in custom widgets.
- Reduced specificity of ul/ol styles in the post content, fixing conflicts with core block styles.
- Added styles for the new calendar widget footer nav in 5.4.
- Cleaned up the CSS, removed unused classes.
- Moved the archive pagination to `pagination.php`.
- Updated the `$name` parameter of `get_template_part()` in the main posts loop to allow for custom templates for custom post types.
- Adjusted block editor styles.
- Added base vertical margins for core blocks.
- Changed the file type of the screenshot from `png` to `jpg`, reducing theme footprint by 300 KB.
- Fixed the incorrect comment number being displayed on comments with pagination.
- Moved the `Fukasawa_Customize` class from `functions.php` to `/inc/classes/class-fukasawa-customize.php`.

Version 1.28 (2019-07-21)
-------------------------
- Fixed password protection issue

Version 1.27 (2019-07-21)
-------------------------
- Added content to search pages without results
- Added skip link
- Don't show comments if the post is password protected
- Don't show the post thumbnail if the post is password protected
- Fixed font issues in the block editor styles

Version 1.26 (2019-07-01)
-------------------------
- Changed the incorrect "block-style" theme tag to the correct "block-styles"

Version 1.25 (2019-07-01)
-------------------------
- Updated "Tested up to" to 5.2
- Fixed the margin of the last gallery item in Block: Gallery
- Added theme tags for "block-style" and "wide-blocks"
- Expanded a ternary to be full-length
- Added output of `get_the_archive_description()` to index.php

Version 1.24 (2019-04-07)
-------------------------
- Updated the readme to describe the new method of adding featured videos (new = four years ago)
- Fixed a potential notice for the video format
- Added the new wp_body_open() function, along with a function_exists check

Version 1.23 (2019-04-07)
-------------------------
- Fixed a misplaced conditional that prevented wp_link_pages() from showing up on pages

Version 1.22 (2019-02-02)
-------------------------
- Updated Genericons enqueue to work better with child themes
- Update output of menus in the header to only call wp_nav_menu() once
- Fixed small images in centered figures to being centered within the figure

Version 1.21 (2018-12-31)
-------------------------
- If the .grid-sizer element doesn't exist, add it to .posts before initializing Masonry

Version 1.20 (2018-12-15)
-------------------------
- Fixed elements being incorrectly displayed on pages

Version 1.19 (2018-12-15)
-------------------------
- Implemented Images Loaded, to prevent the Masonry layout from getting messed up
- Turned the navigation toggle into a button
- Removed the removal of outline on link focus
- Structure updates:
	- Unified index.php, archive.php and search.php into index.php
	- Unified all post formats into content.php
	- Unified single.php and page.php into singular.php
	- Removed custom searchform.php

Version 1.18 (2018-12-07)
-------------------------
- Fixed Gutenberg style changes required due to changes in the block editor CSS and classes
- Fixed the Classic Block TinyMCE buttons being set to the wrong font

Version 1.17 (2018-11-30)
-------------------------
- Fixed Gutenberg editor styles font being overwritten

Version 1.16 (2018-10-20)
-------------------------
- Updated theme description

Version 1.15 (2018-10-20)
-------------------------
- Fixed the accent color not being updated
- Fixed issue with items misalignment in the gallery block

Version 1.14 (2018-10-20)
-------------------------
- Updated with Gutenberg support
	- Gutenberg editor styles
	- Styling of Gutenberg blocks
	- Custom Fukasawa Gutenberg palette
	- Custom Fukasawa Gutenberg typography styles
- Added option to disable Google Fonts with a translateable string
- Updated theme description
- Improved compatibility with < PHP 5.5
- Replaced minified flexslider with non-minified version
- Removed imagesloaded.pkgd.js, since a) it's bundled with WordPress, and b) it wasn't being used anyway
- Fixed the archive template date output
- Removed the languages sub folder, since that is handled by WordPress.org
- Set flexslider to a fixed height, so it doesn't break the Masonry layout on archive pages

Version 1.13 (2018-05-24)
-------------------------
- Fixed output of cookie checkbox in comments

Version 1.12 (2017-12-03)
-------------------------
- Replaced ternany shorthands will full-length version, to retain support for PHP pre 5.3 

Version 1.11 (2017-11-26)
-------------------------
- Updated to the new readme.txt format, with changelog.txt incorporated into it
- Added a demo link to the stylesheet theme description
- Removed specific post types for add_theme_support( 'post-thumbnails' );
- Added a deliberate dependency order to the stylesheet enqueueing
- Same for scripts enqueues
- Made all functions in functions.php pluggable
- Replaced a new WP_Query in widgets/recent-posts.php with a get_posts()
- Fixed genericons path
- Made a text string in single.php translateable
- Fixed notice in comments.php
- Changed closing element comment structure
- General code cleanup, improvements in readability
- Removed duplicate comment-reply enqueueing from the header (already in functions)
- SEO improvements (title structure, mostly)
- Better handling of edge cases (missing title, missing content)
- Restructured query on the archive page template


Version 1.10 (2016-06-28)
-------------------------
- Added the new theme directory tags

Version 1.09 (2016-04-02)
-------------------------
- Fixed respond input margins with new order of inputs

Version 1.08 (2016-03-12)
-------------------------
- Fixed Flickr widget styling issue
- Removed extra semicolon in Flickr widget input label

Version 1.07 (2016-03-12)
-------------------------
- Fixed automated theme scanning error (.ds_store)

Version 1.06 (2016-03-12)
-------------------------
- Fixed an issue with empty titles

Version 1.05 (2015-08-25)
-------------------------
- Fixed an issue with overflowing images
- Added the .screen-reader-text class

Version 1.04 (2015-08-11)
-------------------------
- Removed a superfluous closing <p> tag
- Replaced a custom title function with support for title-tag
- Added comment-reply to enqueued js files
- Removed the custom post meta boxes for content-video from functions.php
- Added sanitize callback for custom accent color
- Removed do_shortcode() function from functions.php
- Modified theme widgets to use __construct() in prep for WP 4.3
- Removed superfluous code from single.php (leftovers from previously supported post formats)
- Changed post title elements on singular from h2 to h1 for SEO reasons
- Updated the Swedish translation

Version 1.03 (2014-09-22)
-------------------------
- Fixed an issue with the read more image in the post editor being too big

Version 1.02 (2014-09-13)
-------------------------
- Added missing function prefix in functions.php
- Added esc_url to the video urls
- Fixed a bug in the flexslider function

Version 1.01 (2014-09-10)
-------------------------
- Added missing function prefixes in functions.php
- Globalized $content_width and moved it to the theme initialization

Version 1.0 (2014-08-04)
------------------------- 