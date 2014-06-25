

Project page:
http://digitalnature.eu/projects/mystique

Licensed under GPL
http://www.opensource.org/licenses/gpl-license.php


                                    
CREDITS:

- digitalnature - http://digitalnature.eu (design and coding)
- WordPress - http://wordpress.org
- jQuery - http://jquery.com
- Fancybox by Janis Skarnelis - http://fancybox.net
- clearfield by Stijn Van Minnebruggen - http://www.donotfold.be
- jQuery Color picker plugin by Cory LaViska - http://abeautifulsite.net
- Justin Tadlock - http://justintadlock.com/archives/2009/05/09/using-shortcodes-to-show-members-only-content
- CodeMirror javascript library - http://marijn.haverbeke.nl/codemirror
- IP2Country by Omry Yadan - http://firestats.cc/wiki/ip2c
- IP2C database by Webhosting.info
- wp_pagenavi plugin - http://wordpress.org/extend/plugins/wp-pagenavi



REQUIREMENTS:

- PHP 5.2+
- WordPress 3.2+



CHANGE LOG:
 
  9, 5,2012,  3.3.2    - added back auto-generated excerpts to featured content media source
                       - disabled editing of ads in the Ad module (use the plugin)

  5, 5,2012,  3.3.1    - fixed an issue from 3.3 in the Ads module
                       - widget / tabs: fixed a bug in the show-more control
                       - widget / archives, calendar: fixed a problem with CPT queries that affected other plugins
                       - improved background color/image controls inside the live design preview

  30,4,2012,  3.3      - added featured posts source to "Photo Gallery", and renamed the gallery to "Featured Content"
                       - disabled meta section on all post types that have comments disabled, except "posts"
                       - added template files for image and generic attachments
                       - improved form field dependency API
                       - disabled the_content filter from post content requests outside the main loop to accommodate buggy plugins
                       - applied arrows to first level of the main menu as well
                       - only translation headers are now parsed on the welcome settings page to avoid insufficient memory issues
                       - disabled ajax navigation for the theme settings panel
                       - changed color picker, you can now deselect the background color :)
                       - fixed an issue with relative time on MU

 28,3,2012,  3.2.9.3   - widget / posts: fixed widget title not showing
                       - removed some PHP < 5 compat code which caused server errors on some 5.4 setups
                       - added Chinese and Japanese translations, tx to Laycher and yast

 23,2,2012,  3.2.9.2   - disabled the EXTEND feature (too many people getting server errors because of host restrictions);
                         if you want it, just create the child theme yourself...
                       - fixed an issue with multipart posts

  4,2,2012,  3.2.9.1   - widget / terms: fixed a code typo from 3.2.9

  4,2,2012,  3.2.9     - mod / ad blocks: fixed shortcode not working
                       - fixed a js error in toggleVisibility()
                       - added translations for page templates
                       - widget / posts: added y-thumbnail size option
                       - the_content is now only called once to prevent compatibility issues with bad plugins

 24,1,2012,  3.2.8     - removed all negative text-indent properties
                       - fixed a problem with the single pagination
                       - widget / login: fixed login issues with password that contain quotes
                       - fixed problem with some youtube movies not showing up (from 3.2.7)

 23,1,2012,  3.2.7     - removed old Chrome text-shadow/AA css fixes
                       - widget / posts: fixed wrong alpha order
                       - z-index fix for youtube iframe/movies
                       - added mousewheel support for the lightbox
                       - removed livequery from the front-end (replaced by the ajaxComplete event hook)
                       - fixed a problem with comment controls, introduced in 3.2.6

 11,1,2012,  3.2.6     - modules can now be called trough atom()->moduleName
                       - improved custom post type handling in getBreadcrumbs()
                       - added cache-flush trigger after theme options are changed
                       - mod / translate: fixed a problem with html character encoding
                       - mod / photo gallery: can now accept external image sources (trough photogallery->addSource)
                       - fixed a bug related to featured content added in 3.2.5
                       - widget / posts: added "show all" title links for custom post types that have archive pages

  2,1,2012,  3.2.5     - fixed a js bug in theme settings from 3.2.4 that affected other admin pages

  2,1,2012,  3.2.4     - page and category fallback menus now support arrows without the need of javascript
                       - fixed an issue related to disqus plugin
                       - added blog archive page template
                       - added user listing page template
                       - reversed icons in the social-media template
                       - widget / terms: replaced internal 'description_word_limit' option with the 'character_limit' option (defaults to 140)
                       - translation updates
                       - javascript improvements to widget controls added by Atom
                       - added getBreadcrumb method (use atom()->breadcrumbs if you want it in your templates)
                       - improved pagination (also changed getPageNavi with getPagination)
                       - some design changes, colors of links depend on the selected color scheme

20,12,2011,  3.2.3     - widget / posts: fixed issue where the character limit option would be ignored
                       - fixed duplicate output issue on plural translations
                       - deprecated some methods, including getSocialMediaLinks(), typically used in templates
                       - fixed comment submissions not working for registered site users

20,12,2011,  3.2.2     - fixed a bug in the media icons template
                       - fixed a CSS issue with active menus
                       - changed .pot catalog to include t() keywords
                       - fixed bbpress errors not showing
                       - added character limit option for post teasers (replaces numeric mode values)
                       - added password recovery template and updated user registration template to support blog registrations on MU

19,12,2011,  3.2.1     - fixed problems with 3rd party comment plugins, really :)
                       - fixed issue where 2nd sidebar was not properly styled
                       - blogs without mb_string active will now use PHP's default string functions

19,12,2011,  3.2       - updated the core to Atom 2.0 (theme options can now be almost fully configured trough dedicated API)
                       - moved template files into a sub-directory (child themes can still have them in both root/templates locations)
                       - fixed an issue where stylesheet caching would fail on servers without any fopen wrappers enabled
                       - widget / top commenters: added exclude option
                       - widget / users: added {ONLINE_STATUS} keyword
                       - widget / text: disabled visual editor until I manage to integrate the new wp 3.3 editor
                       - fixed some incompatibility issues with 3rd party comment plugins
                       - fixed ~1M memory leak in the theme settings (translation files were loaded inside all tabs, not just "Translate")
                       - changed sign-up / tag cloud internal templates with normal page templates; related widget links will show only
                         if there are pages that are using these templates
                       - improved content filtering speed (~300%), added Unicode support and "cutoff" argument (word/sentence/exact/custom)
                       - changed "word count" options across widgets with the "character count" option
                       - removed comment search form (useless for most blogs, and too slow on blogs with 5K+ comments/post)
                       - updated codemirror
                       - moved new theme file uploads (logo/header) to child theme directory, when available
                       - tabs no longer require javascript to work, but jQuery is used when available
                       - removed jQuery.FadeLinks() (replaced by CSS3 transitions, most browsers support them)
                       - fixed compatibility issue with wp-ecommerce, and probably other plugins that use unorthodox template loading methods

25, 8,2011,  3.1       - added bbPress support (basic)
                       - added "Tags" internal page
                       - disabled script concatenation, you can enable it by defining the ATOM_CONCATENATE_JS as true from your child theme
                       - widget / login: changed the register link to point to the "Sign-up" internal page
                       - new module: Translate
                       - removed en_US.po, and added a pot template
                       - improved language listing in the settings
                       - widget / twitter: corrected tweet date format
                       - fixed problem with sub-categories
                       - added Russian Translation, tx Philip
                       - mod / photo gallery: image links will now open inside the lightbox if the option is enabled
                       - mod / photo gallery: added location argument (theme action in which to show the gallery, default is 'before_main')

20, 8,2011,  3.0.9     - fixed localization issues with strings defined before the after_setup_theme hook
                       - translations can now be also dropped in the child theme "lang" folder (they will have higher priority)
                       - mod / featured gallery: completed (and renamed to "Photo gallery")

19, 8,2011,  3.0.8.1   - moved cached js in <head> so it doesn't break arbitrary javascript added by plugins

19, 8,2011,  3.0.8     - added Bengali translation, tx Shaikat77
                       - removed head.js, and made so scripts are compressed and concatenated
                         with Google's Closure Compiler service
                       - widget / twitter: changed cache option from select to input
                       - some SE optimizations (made the 'index' rel attribute point to relevant pages on archives,
                         single CPTs, custom blog page)
                       - fixed issue from 3.0.7 with clearFieldCheck (unfilled input fields not clearing on submit)
                       - fixed a CSS issue with splitters
                       - fixed small issue in theme settings / content options (media thumbnail size wasn't being displayed)
                       - added template argument to post->getTerms()
                       - fixed localization issue with role names and module variables set inside their init() method
                       - mod / social media icons: removed support for the WP SmushIt plugin

14, 8,2011,  3.0.7     - mod / social media icons: added persistent cache for label/uri fields
                       - fixed ugly bug introduced in 3.0.6 in get/add/setContextArgs()
                       - replaced non-standard rel attributes with html5 data attributes

14, 8,2011,  3.0.6     - reduced design image sizes by 3-4% and removed some images that weren't used
                       - fixed another bug in the [query] shortcode (template argument wasn't working correctly)
                       - updated French translation, tx Pierre
                       - added new module: Social-Media Icons (will replace old media links); @todo: add default labels/URLs
                       - fixed a graphic glitch in the search template

12, 8,2011,  3.0.5     - added French, Italian & Spanish translations, tx to Jonathan, Marco & Antonio
                       - added an internal page sample: user sign-up
                       - fixed pings not showing
                       - fixed a bug in the [query] shortcode
                       - tabs can be now activated by the location hash
                       - moved wp_footer() inside the #footer element because some plugins add HTML in it,
                         and it wasn't getting styled...
                       - made so "Home"-named custom menu items are styled with the home icon, like default menus
                       - widget / recent comments: fixed a bug with the comment date
                       - widget / posts: fixed wrong order-by-views
                       - widget / tabs: added "cat-ID" class for the Posts widget (category ID)
                       - mod / featured gallery: removed the "Tile Wave" gallery effect, not cool enough
                       - decreased spacing between menu entries
                       - temporarily disabled search query highlighting until I find a way to accurately handle any HTML

 8, 8,2011,  3.0.4     - added German and Dutch translations, tx to DotTobi, Blackstarwolf and Raymon
                       - added grey & red color schemes
                       - added error handler for the Design settings; if your site has errors you'll be notified on that page :)
                       - updates to the image uploader (GIFs are now allowed, but you should really avoid this format)
                       - fixed a notice in the [widget] shortcode
                       - fixed a bug in the [subs] shortcode
                       - removed 404 styles, it's now a normal page
                       - tabs are now sorted correctly in the front-end

 7, 8,2011,  3.0.3     - fixed a couple of small issues reported by theme users
                       - child theme style.css is now included after the parent styles

 5, 8,2011,  3.0.2     - fixed nasty bug in functions.php that caused full option reset
                         on any version change (should have been just < 3.0)

20, 5,2011,  3.0       - code rewrite, using Atom's codebase (beware - 2.x theme settings are reset)

...

3, 10.2009: First release (1.0)
