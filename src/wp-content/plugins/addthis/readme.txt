=== AddThis Sharing Buttons ===
Contributors: abramsm, srijith.v, vipinss, dnrahamim, jgrodel, bradaddthiscom, mkitzman, addthis_paul, addthis_matt, addthis_elsa, ribin_addthis, AddThis_Mike
Tags: AddThis, addtoany, bookmark, bookmarking, email, e-mail, sharing buttons, share, share this, facebook, google+, pinterest, instagram, linkedin, whatsapp, social tools, website tools, twitter, content marketing, recommended content, conversion tool, subscription button, conversion tools, email tools, ecommerce tools, social marketing, personalization tools
Requires at least: 3.0
Tested up to: 4.3
Stable tag: 5.1.1

AddThis provides the best sharing tools to help you make your website smarter.

== Description ==

The best sharing and following tools on the web are now available for your WordPress site. Promote your content easily by sharing to over 200 of the most popular social networking and bookmarking sites (like Facebook, Twitter, Pinterest, Google+, WhatsApp, LinkedIn and more). Clean, customizable and simple social buttons are unobtrusive, quick to load and recognized all over the web.

== Installation ==

For an automatic installation through WordPress:

1. Go to the 'Add New' plugins screen in your WordPress admin area
1. Search for 'AddThis'
1. Click 'Install Now' and activate the plugin

For a manual installation via FTP:

1. Upload the addthis folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' screen in your WordPress admin area

To upload the plugin through WordPress, instead of FTP:

1. Upload the downloaded zip file on the 'Add New' plugins screen (see the 'Upload' tab) in your WordPress admin area and activate.

== Frequently Asked Questions ==

= Is AddThis free? =

Many of our tools are free, but Pro users get the benefit of exclusive widgets, including mobile­ friendly tools
and retina icons, priority support and deeper analytics.

= Do I need to create an account? =

No, you do not need to create an account in order to control a limited number of AddThis sharing tools from within WordPress. In order to use more AddThis tools and see your site's analytics you will need to create an account with AddThis. It requires an email address and name, but that's it.

= Is JavaScript required? =

All of the options required through this plugin require javascript. JavaScript must be enabled. We load the actual interface via JavaScript at run-time, which allows us to upgrade the core functionality of the menu itself automatically everywhere.

= Why use AddThis? =
1. Ease of use. AddThis is easy to install, customize and localize. We've worked hard to make a suite of simple and beautiful website tools on the internet.
1. Performance. The AddThis menu code is tiny and fast. We constantly optimize its behavior and design to make sharing a snap.
1. Peace of mind. AddThis gathers the best services on the internet so you don't have to, and backs them up with industrial strength analytics, code caching, active tech support and a thriving developer community.
1. Flexibility. AddThis can be customized via API, and served securely via SSL. You can roll your own sharing toolbars with our toolbox. Share just about anything, anywhere ­­ your way.
1. Global reach. AddThis sends content to 200+ sharing services 60+ languages, to over 1.9 billion unique users in countries all over the world.

= What PHP version is required? =

This plugin requires PHP 5.2.4 or greater.

= Who else uses AddThis? =
Over 15,000,000 sites have installed AddThis. With over 1.9 billion unique users, AddThis is helping share content all over the world, in more than sixty languages.

= What services does AddThis support? =
We currently support over 200 services, from email and blogging platforms to social networks and news aggregators, and we add new services every month. Want to know if your favorite service is supported? This list is accurate up to the minute: <a href="http://www.addthis.com/services">http://www.addthis.com/services</a>.

= How do I remove AddThis from a page =
In the screen options you can enable the AddThis meta box. Check the box and save if you've already published that page or post to disable AddThis on that page or post.

== Screenshots ==

1. Sharing Tools tab on the plugin settings page (WordPress mode)
2. Sharing Tools tab on the plugin settings page (WordPress mode)
3. Advanced Options tab on the plugin settings page
4. Drag and dropable sharing buttons widget (WordPress mode)
4. Sharing Tools tab on the plugin settings page (AddThis mode)
5. Analytics on the AddThis Dashboard
6. Tool Gallery on the AddThis Dashboard
7. Customization options on the AddThis Dashboard

== Changelog ==

= 5.1.1 =
* PHP notice at addthis_social_widget.php:1337
* PHP error for older versions of PHP (< 5.3.0) at addthis_settings_functions.php:476
* Adding select configs and page info into addthis_plugin_info to aide <a href="http://support.addthis.com/">the AddThis Support Team</a> with troubleshooting

= 5.1.0 =
* New feature: set your own addthis.layers() paramaters to customize further using our API (in WordPress mode only)
* Fixing a bug with the AddThis Sharing Buttons meta box not showing up for users in AddThis mode when editing posts
* Fixing a bug with addthis_config where the JSON wasn't always checked before submitting the settings form
* Fixing a bug with the sharing sidebar, where the theme for the sidebar was used for all AddThis layers tools (in WordPress mode)
* Fixing a bug between AddThis Sharing Buttons and AddThis Follow Buttons, where saving changes in Follow Buttons would reset Sharing Buttons settings
* Support for WordPress 4.3

= 5.0.13 =
* Fixing XSS bug in the administrative panel

= 5.0.12 =
* Fixing a bug that resets settings to defaults during the upgrade for users in AddThis mode
* Reverting to pre-5.0.9 settings of plugin for upgrades from 5.0.9, 5.0.10 and 5.0.11

= 5.0.11 =
* Fixing overwrite of $addThisConfig global to resolve more PHP errors where a function is called from a non-object

= 5.0.10 =
* Fixing bug where all checkboxes get unchecked on first settings page save
* Fixing PHP error with getConfigs() on a non-object in addthis_social_widget.php on line 1086

= 5.0.9 =
* Updated troubleshooting information available to AddThis support to be more in line with other plugins, including providing the anonymous profile IDs to help in moving over statistics to registered profile IDs upon request.
* Improving the way variables are being shared to support global AddThis variables between AddThis plugins (not supported yet by any other plugin) and plugin specific settings. Specifically, this is to make sure things such as the profile ID or addthis_config settings are the same across all AddThis plugins.
* Refactored the filter used on wp_trim_excerpt in WordPress mode to better interact with other modules' and themes' filters on wp_trim_excerpt, excerpt_length and excerpt_more (such as mh-magazine-lite)
* Resolving issues with PHP errors for URLs with %s in them when using "Select Your Own" services
* Resolving services bug, where once a user selected their own for above or below content, the custom services where always used, even if the user went back to Auto Personalization
* Fixing bug with a PHP warning for undefined index below_chosen_list

= 5.0.8 =
* PHP notice fix for AddThisConfigs.php on line 204

= 5.0.7 =
* Changed the page/post edit screens AddThing sharing buttons checkbox to on/off radiobuttons
* Added a checkbox to the plugin's settings page's Advanced Options tab to enable/disable the per page/post sharing button configurations mentioned above

= 5.0.6 =
* Limiting when we filter content from get_the_excerpt because of issues with the manshet and backstreet themes
* Reducing PHP warnings and error messages
* Adding back in addthis_config.ignore_server_config JavaScript variable for WordPress model (for on page configurations)
* Adding data-cfasync="false" to all AddThis JavaScript to stop CloudFlare's RocketScript feature from breaking sharing buttons

= 5.0.5 =
* Adding all WordPress mode Advanced Options to AddThis mode Advanced Options. These are primarily options to edit the addthis_share and addthis_config JavaScript variables used by AddThis tools, as well as giving you the option to choose whether to load the AddThis script asyncronously.
* Removing conflicts with WordPress Login Box plugin
* Removing conflicts with AddThis Follow Widget plugin
* CSS to remove new lines from br tags between our buttons, because some themes add them and break stuff (such as Customizr)
* Exposing more objects for use with adding sharing buttons to troublesome themes

= 5.0.4 =
* PHP error fixes for strict modes of PHP

= 5.0.3 =
* Reduced the size of the plugin by removing unused images

= 5.0.2 =
* Fixes for WordPress instances running on PHP verions below 5.5

= 5.0.1 =
* Improved migration of checkbox settings from 4.0 plugin
* Improved settings page layout on mobile devices and small screens
* Improved CSS class naming around red/green enable sliders on the settings page, to reduce CSS conflicts
* Proactively removing all other CSS and JavaScript files from other plugins when on our settings page to reduce conflicts with other active plugins
* Removing unused CSS

= 5.0 =
* Two functional modes allowing users to choose whether to control sharing buttons from within the WordPress admin area (with limited features), or through the AddThis Dashboard at addthis.com.
* Sharing sidebar configuration available within WordPress (in addition to the previously available sharing buttons above and below content).
* Seperate preferences for what sharing buttons appear on what templates.
* Cleaned up plugin and removed deprecated AddThis config options.
* Added beauty.
* Removed the global configuration for custom service list.
* Removed support for themes that use the get_the_excerpt method because of inconsistent results across themes.

= 4.0.7 =
* Added node marker and loop cap to getFirstElderWithCondition to solve infinite loop
* Fixed the issue where characters *,+,- were printed in feeds

= 4.0.6 =
* Adds data-url and data-title attributes to get_the_excerpt toolbox divs

= 4.0.5 =
* Fixed js folder

= 4.0.4 =
* Fixed issues with certain themes using get_the_excerpt method
* Fixed issue with Share layer and Recommended layer showing up in wrong order
* Fixed issue with Recommended layer showing up page title instead of Recommended layer title
* Fixed PHP warnings in Divi theme

= 4.0.3 =
* Removed addthis initialization function (addthis.init()) to avoid javascript error in async mode.
* Added option to show inline sharing buttons on category and archive pages

= 4.0.2 =
* Control inline share buttons for each page/post
* Implemented asynchronous script loading
* Added option to add checkbox to show/hide Addthis sharing buttons in admin post add/edit page.
* Prevented buttons showing on 404 error pages
* CSS fixes
* Added css classes to prevent inline div from taking extra spaces

= 4.0.1 =
* Frictionless integration with AddThis Dashboard.
* Updated services list in description
* Added new snapshots to give more idea on the new plugin flow
* Minor text changes

= 4.0 =
* Integrated with AddThis Dashboard.
* Managed the plugin in such a way that, tool settings can be controlled from AddThis Dashboard
* Provided options to upgrade to new setup for the existing users

= 3.5.10 =
* Minor bug fix

= 3.5.9 =
* CURL bug fix

= 3.5.8 =
* Resolved conflict with another plugin

= 3.5.7 =
* Minor updates

= 3.5.6 =
* Fix for better analytics

= 3.5.5 =
* Minor bug fixes.

= 3.5.4 =
* Fixed JS errors in Options page.

= 3.5.3 =
* Fix for wrong url being shared while using Custom Button option

= 3.5.2 =
* Minor bug fixes.

= 3.5.1 =
* Reintroduced Custom Button Code box

= 3.5 =
* Drag and drop customization of buttons
* Better preview of buttons
* Bug fixes

= 3.1 =
* Better support for excerpts
* Internal issues with tw:screenname resolved
* Better JSON validation
* Bug fixes

= 3.0.5 =
* Bug fixes: Style conflicts resolved

= 3.0.4 =
* Bug fix: Style conflicts with some themes resolved
* Made friends with WPSupercache

= 3.0.3 =
* Bug fix: Style tags are now allowed in Custom Code box.

= 3.0.2 =
* Bug fix: Conflict with WpSuperCache resolved
* Wordpress 3.5 style conflicts resolved

= 3.0.1 =
* Bug fix: Issues with usernames/passwords with special characters

= 3.0 =
* UX changes in admin settings
* Credential verification in Admin settings. If AddThis account details are wrong it will be notified to user.
* Bug fixes

= 2.5.1 =
* Added hashtag support for Twitter
* Conflict removed with <a href="http://wordpress.org/extend/plugins/addthis-follow/">AddThis Follow</a> and <a href="http://wordpress.org/extend/plugins/addthis-welcome/">AddThis Welcome Bar</a> Wordpress plugins
* Bug fixes

= 2.5.0 =
* <a href="http://www.addthis.com/blog/2012/09/13/introducing-our-new-sharing-experience">New streamlined and simplified share menu</a>
* Added Instant Share for Facebook and Twitter
* Updated Pinterest support
* Bug fixes

= 2.4.1 =
* Bug fixes

= 2.4.0 =
* Refactor how we add code to pages which should eliminate how many times it is added
* Numerous Bug Fixes and a few new filters

= 2.3.2 =
* Add opt out for copy tracking

= 2.3.1 =
* Don't strip pintrest tags from custom buttons

= 2.3.0 =
* Add Google Analytics option
* Update settings interface

= 2.2.2 =
* Fix custom button whitespace saving issue

= 2.2.1 =
* Fix compatability with 3.2
* Fix over agressive regular expression

= 2.2.0 =
* More Customization Option
* optionally shorten urls with one bit.ly account

= 2.1.1 =
* Add +1

= 2.1.0 =
* Add Twitter Template Option
* Add Post Meta Box
* Add top shared/clicked URLS to dashboard
* More Filters

= 2.0.6 =
* define ADDTHIS_NO_NOTICES to prevent admin notices from displaying

= 2.0.5 =
* force service codes to be lowercase
* If opting out of clickback tracking, set config to force opting out

= 2.0.4 =
* Fix conflict with other plugins
* Prevent button js from appearing in feeds

= 2.0.3 =
* plugin should still work if theme doesn't have wp_head and wp_footer

= 2.0.2 =
* Bug Fixes
* set addthis_exclude custom field to 'true' to not display addthis on that post / page
* Added additional paramater to
* Ability to specify custom toolboxes for both above and below
* Added additional paramater to do_action('addthis_widget').  Paramaters are now:
* * url (use get_permalink() if you are calling it inside the loop)
* * title (use the_title() if calling inside the loop)
* * Style (specify the style to display) See $addthis_new_styles for the styles.  may also pass an arra (see addthis_custom_toolbox for array values to pass)

= 2.0.1 =
* Fix theme compatablity issues
* Fix excerpts bug
* Add option to not display on excerpts
* Restore option to customize services
* Add more filters

= 2.0.0 =
* Redesigned Settings page
* Added Share Counter option
* Redesigned Admin Dashboard widget
* Updated sharing widget options
* Updated sidebar widget to extend WP_Widget

= 1.6.7 =
* Using wp_register_sidebar_widget() in WordPress installs that support it

= 1.6.6 =
* Fixed argument bug in 1.6.5

= 1.6.5 =
* Added support for arbitrary URL and title in template tag as optional parameters
 * i.e., <?php do_action( 'addthis_widget', $url, $title); ?>
 * Can be called, for example, with get_permalink() and the_title() within a post loop, or some other URL if necessary

= 1.6.4 =
* Fixed parse bug with "static" menu option
* Fixed regression of brand option

= 1.6.3 =
* Added template tags. &lt;?php do_action( 'addthis_widget' ); ?&gt; in your template will print an AddThis button or toolbox, per your configuration.
* Added <a href="http://addthis.com/blog/2010/03/11/clickback-analytics-measure-traffic-back-to-your-site-from-addthis/">clickback</a> tracking.
* Added "Automatic" language option. We'll auto-translate the AddThis button and menu into our supported languages depending on your users' settings.
* Fixed script showing up in some trackback summaries.

= 1.6.2 =
Fixed name conflict with get_wp_version() (renamed to addthis_get_wp_version()), affecting users with the k2 theme.

= 1.6.1 =
Fixed nondeterministic bug with the_title(), causing the title to occasionally appear in posts.

= 1.6.0 =
* Added <a href="http://addthis.com/toolbox">toolbox</a> support
* Added WPMU support
* For WP 2.7+ only:
 * Added support for displaying basic sharing metrics in the WordPress dashboard
 * Updated settings management to use nonces


== Upgrade Notice ==

= 5.1.1 =
* PHP notice at addthis_social_widget.php:1337
* PHP error for older versions of PHP (< 5.3.0) at addthis_settings_functions.php:476
* Adding select configs and page info into addthis_plugin_info to aide <a href="http://support.addthis.com/">the AddThis Support Team</a> with troubleshooting

= 5.1.0 =
* New feature: set your own addthis.layers() paramaters to customize further using our API (in WordPress mode only)
* Fixing a bug with the AddThis Sharing Buttons meta box not showing up for users in AddThis mode when editing posts
* Fixing a bug with addthis_config where the JSON wasn't always checked before submitting the settings form
* Fixing a bug with the sharing sidebar, where the theme for the sidebar was used for all AddThis layers tools (in WordPress mode)
* Fixing a bug between AddThis Sharing Buttons and AddThis Follow Buttons, where saving changes in Follow Buttons would reset Sharing Buttons settings
* Support for WordPress 4.3

= 5.0.12 =
* Fixing a bug that resets settings to defaults during the upgrade for users in AddThis mode
* Reverting to pre-5.0.9 settings of plugin for upgrades from 5.0.9, 5.0.10 and 5.0.11

= 5.0.11 =
* Fixing overwrite of $addThisConfig global to resolve more PHP errors where a function is called from a non-object

= 5.0.10 =
* Fixing bug where all checkboxes get unchecked on first settings page save
* Fixing PHP error with getConfigs() on a non-object in addthis_social_widget.php on line 1086

= 5.0.9 =
* Updated troubleshooting information available to AddThis support to be more in line with other plugins, including providing the anonymous profile IDs to help in moving over statistics to registered profile IDs upon request.
* Improving the way variables are being shared to support global AddThis variables between AddThis plugins (not supported yet by any other plugin) and plugin specific settings. Specifically, this is to make sure things such as the profile ID or addthis_config settings are the same across all AddThis plugins.
* Refactored the filter used on wp_trim_excerpt in WordPress mode to better interact with other modules' and themes' filters on wp_trim_excerpt, excerpt_length and excerpt_more (such as mh-magazine-lite)
* Resolving issues with PHP errors for URLs with %s in them when using "Select Your Own" services
* Resolving services bug, where once a user selected their own for above or below content, the custom services where always used, even if the user went back to Auto Personalization
* Fixing bug with a PHP warning for undefined index below_chosen_list

= 5.0.8 =
* PHP notice fix for AddThisConfigs.php on line 204

= 5.0.7 =
* Changed the page/post edit screens AddThing sharing buttons checkbox to on/off radiobuttons
* Added a checkbox to the plugin's settings page's Advanced Options tab to enable/disable the per page/post sharing button configurations mentioned above

= 5.0.6 =
* Limiting when we filter content from get_the_excerpt because of issues with the manshet and backstreet themes
* Reducing PHP warnings and error messages
* Adding back in addthis_config.ignore_server_config JavaScript variable for WordPress model (for on page configurations)
* Adding data-cfasync="false" to all AddThis JavaScript to stop CloudFlare's RocketScript feature from breaking sharing buttons

= 5.0.5 =
* Adding all WordPress mode Advanced Options to AddThis mode Advanced Options. These are primarily options to edit the addthis_share and addthis_config JavaScript variables used by AddThis tools, as well as giving you the option to choose whether to load the AddThis script asyncronously.
* Removing conflicts with WordPress Login Box plugin
* Removing conflicts with AddThis Follow Widget plugin
* CSS to remove new lines from br tags between our buttons, because some themes add them and break stuff (such as Customizr)
* Exposing more objects for use with adding sharing buttons to troublesome themes

= 5.0.3 =
* Two functional modes allowing users to choose whether to control sharing buttons from within the WordPress admin area (with limited features), or through the AddThis Dashboard at addthis.com.
* Sharing sidebar configuration available within WordPress (in addition to the previously available sharing buttons above and below content).
* Seperate preferences for what sharing buttons appear on what templates.
* Cleaned up plugin and removed deprecated AddThis config options.
* Added beauty.
* Removed the global configuration for custom service list.
* Removed support for themes that use the get_the_excerpt method because of inconsistent results across themes.

= 4.0.7 =
* Added node marker and loop cap to getFirstElderWithCondition to solve infinite loop
* Fixed the issue where characters *,+,- were printed in feeds

= 4.0.6 =
* Adds data-url and data-title attributes to get_the_excerpt toolbox divs

= 4.0.5 =
* Fixed js folder

= 4.0.4 =
* Fixed issues with certain themes using get_the_excerpt method
* Fixed issue with Share layer and Recommended layer showing up in wrong order
* Fixed issue with Recommended layer showing up page title instead of Recommended layer title
* Fixed PHP warnings in Divi theme

= 4.0.3 =
* Removed addthis initialization function (addthis.init()) to avoid javascript error in async mode.
* Added option to show inline sharing buttons on category and archive pages

= 4.0.2 =
* Control inline share buttons for each page/post
* Implemented asynchronous script loading
* Added option to add checkbox to show/hide Addthis sharing buttons in admin post add/edit page.
* Prevented buttons showing on 404 error pages
* CSS fixes
* Added css classes to prevent inline div from taking extra spaces

= 4.0.1 =
* Frictionless integration with AddThis Dashboard.
* Updated services list in description
* Added new snapshots to give more idea on the new plugin flow
* Minor text changes

= 4.0 =
* Integrated with AddThis Dashboard.
* Managed the plugin in such a way that, tool settings can be controlled from AddThis Dashboard
* Provided options to upgrade to new setup for the existing users

= 3.5.10 =
* Minor bug fix

= 3.5.9 =
* CURL bug fix

= 3.5.8 =
Minor bug fix

= 3.5.7 =
Minor updates

= 3.5.6 =
Minor updates

= 3.5.5 =
Bug fixes

= 3.5.4 =
Bug fixes

= 3.5.3 =
Fix for wrong url being shared while using Custom Button option

= 3.5.2 =
Bug fixes.

= 3.5.1 =
Reintroduced Custom Button Code box

= 3.5 =
Drag and drop customization of buttons, better preview of buttons

= 3.1 =
Bug fixes, better validations

= 3.0.5 =
Bug fixes

= 3.0.4 =
Bug fixes, made WPSupercache friendly

= 3.0.3 =
Bug fixes.

= 3.0.2 =
Bug fixes.

= 3.0.1 =
Bug fixes.

= 3.0 =
Updated UI, AddThis account verification and bug fixes.

= 2.5.1 =
Bug fixes.

= 2.5.0 =
Updated share menu, Instant Share, Pinterest updates and bug fixes.

= 2.4.3 =
Fixed admin console bug for non-administrators.

= 2.4.1 =
Bug fixes.

= 2.4.0 =
Better performance and improved UI.

= 2.3.2 =
New option for opting out of copy text tracking.

= 2.3.0 =
Improve the Settings interface and add Google Analytics integration.

= 2.2.1 =
Add 3.2 compatability.

= 2.2.0 =
More customization options.

= 2.1.0 =
More features, more filters, more social goodness.

= 2.0.5 =
Force service codes to be lowercase and, if opting out of clickback tracking, set config to force opting out.

= 2.0.4 =
Fix conflict with other plugins and other bug fixes.

= 2.0.3 =
Still work in themes that don't have wp_head and wp_footer.

= 2.0.2 =
Bug fixes, enhanced customization.

= 2.0.1 =
Bug fixes, more filters, small tweak to options.

= 2.0.0 =
More and better options for sharing widgets.  Redesigned analytics dashboard widget and interface.

