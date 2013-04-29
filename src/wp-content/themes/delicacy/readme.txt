/*========= Delicacy =========*/

Author: Aleksandra Łączek
Tags: white, light blue, red, pink, orange, green, gold, theme-options, editor-style, two-columns, right-sidebar, custom-background, custom-menu,
Requires at least: WP 3.0
Tested up to: WP 3.3.1

/*========= Description =========*/

Culinary oriented two column WordPress theme, also perfect for personal blog.
Suppors widgets (with 4 custom widgets), custom menu, custom background, and multiple color schemes, image slider on home page.
Post edit screen includes additional set of meta boxes, to easily input culinary recipe.

Delicacy theme includes support for the following languages:
* English
* Polish

For questions, comments or bug reports, please go to http://webtuts.pl/delicacy-en/

/*========= Installation =========*/

You can install the theme through the WordPress installer under "Themes" > "Install themes" by searching for it.
Alternatively you can download the file, unzip it and move the unzipped contents to the "wp-content/themes" folder
of your WordPress installation. You will then be able to activate the theme.

/*========= Theme Update =========*/

It's hihgly recommended that you backup you database and files before attempting theme update.
Please keep in mind, that any changes made directly in theme files will be overwritten during automatic update.

/*========= Theme Features =========*/

* Multiple Color Schemes
* Custom Widgets
* Internationalized & localization
* Drop-down Menu
* Clean Code
* Cross-browser compatibility
* Threaded Comments
* Gravatar ready


/*========= Credits =========*/

Theme Delicacy uses:

* Titillium Font (http://www.campivisivi.net/archivio/_titillium/titillium2010/), licensed under OFL (http://scripts.sil.org/OFL)

/*========= Theme Setup =========*/

== Home Page Slider ==

In order to use slide on the home page, You need to activate it first in Theme Options. The slider rotates posts from a specific category, that You also set in Theme Options. slider dimensions are 588px x 289px. If You upload bigger images, proper size will be generated automatically. Keep in mind, that slider uses posts featured image. If You see an image that says "Image placeholder" in Your slider, it probably means, that the post doeasn't have featured image set.

== Home Page Layout ==

Delicacy offers two ways of displaying latest posts on the Home Page. Home Page layout is set in Theme Options. If You decide to display post excerpts with an image, keep in mind, that the image used for this purpose, is the post featured image.

== Drop Down Navigation ==

Delicacy supports multiple level drop down navigation. To set it up go to Appereance->Menus. Then create a menu, and in the box „Theme Locations” choose it's name from the drop down and click Save. Than just add items to this menu and again hit Save.

== Delicacy Header Widget ==

In addition to sidebar widgets, Delicacy has one more widget area in the header, just below the main navigation. This area has it's dedicated widget - "Delicacy: Featured Posts". This widget allows You to rotate in the header links to posts tagged with a specific tag. You can see it in action in the theme demo here: http://demo.webtuts.pl/

== Child Themes ==

It's highly recommended to create a child theme if you plan on making ANY changes to the theme. The easiest way to do it is via One Click Child Theme plugin.

== Translations ==
Delicacy is translation ready. To translate open delicacy.po (located in languages folder) with Poedit (http://www.poedit.net/). Translate the file and save in languages folder under name appropriate to your language (i.e. pl_PL for Polish).


== Changelog ==

= 1.2.8 - 24.02.2013 =
* Fixed small glitch with the slider
* Added Pinterest icon to social widget

= 1.2.7 - 17.02.2013 =
* Fixed Pink color scheme
* Fixed error in archive.php

= 1.2.6 - 15.02.2013 =
* Added Violet color scheme
* Options Framework update
* Nivo Slider update
* Added full width template
* Integrated Rich Snippets for recipes
* Chenged page width to 1000px
* Added new theme options
* CSS tweaks
* Updated .po file