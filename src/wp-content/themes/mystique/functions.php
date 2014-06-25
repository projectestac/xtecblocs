<?php
/**
 * Theme Setup.
 *
 * You can change any of the settings below from a child theme, by simply calling these functions inside the child theme's functions.php file.
 * To override all settings define the ATOM_OVERRIDE_CONFIG constant as "true"
 *
 * @package ATOM
 * @subpackage Template
 */



// theme unique ID, used as theme option name, text domain for translations, and possibly other identifiers
// make sure there are no spaces in it if you're setting your own ID inside a child theme
defined('ATOM') or define('ATOM', get_stylesheet());

// enable development mode? (loads local & uncompressed js, disables w3c validation in debug mode)
// for testing purposes only; always set this to false on a live site
defined('ATOM_DEV_MODE') or define('ATOM_DEV_MODE', (isset($_SERVER['SERVER_ADDR']) && strpos($_SERVER['SERVER_ADDR'], '127.0.0.1') !== false));

// attempt to automatically create and activate a child theme on 1st install or reset;
// if succesfull the user-functions field is enabled in the theme settings;
defined('ATOM_EXTEND') or define('ATOM_EXTEND', false);

// log post views (just like wp-post-views) ?
// on a very large site this can cause server load issues since the database is updated on each page view...
defined('ATOM_LOG_VIEWS') or define('ATOM_LOG_VIEWS', false);

// track user online status?
defined('ATOM_TRACK_USERS') or define('ATOM_TRACK_USERS', true);

// compress and concatenate js with Google's Closure Compiler?
// seems to fail on certain setups...
defined('ATOM_CONCATENATE_JS') or define('ATOM_CONCATENATE_JS', false);

// required for compatibility with Mystique < 3.2 child themes and modules
define('ATOM_COMPAT_MODE', true);


// the core; if you're reconfiguring Atom from a child theme, this line must be present as well
require_once TEMPLATEPATH.'/atom-core.php';

// and this function
if(!function_exists('atom')){
  function atom(){
    static $app;

    if(!($app instanceof Atom))
      $app = Atom::app();

    return $app;
  }
}



// any of the settings below can be overridden from a child theme if this constant is present and set to "true"
if(!defined('ATOM_OVERRIDE_CONFIG') || !ATOM_OVERRIDE_CONFIG){

  // available layout types -- @todo: finish this
  atom()->registerLayoutTypes('c1', 'c2left', 'c2right', 'c3', 'c3left', 'c3right');

  // menu locations
  atom()->registerMenus(array(
    'top'       => atom()->t('Page Top'),
    'primary'   => atom()->t('Below Header (Main)'),
    'footer'    => atom()->t('Above Footer')
  ));


  add_theme_support('post-thumbnails');
  add_theme_support('automatic-feed-links');
  add_theme_support('bbpress');
  add_theme_support('post-formats', array('aside', 'gallery'));

  // register ad locations for the AdManager plugin;
  // key names may not match what the label suggests, but they are correct because these are the template action tags
  // the ":index" prepended to the action tag suggests that this location requires the index field (the ad will be inserted after N action executions)
  if(class_exists('AdManager') && defined('AdManager::VERSION'))
    AdManager()->registerAdLocation(array(
      'atom_before_main'             => atom()->t('After header'),         // all pages
      'atom_before_primary'          => atom()->t('Before main'),          // all pages
      'atom_after_primary'           => atom()->t('After main'),           // all pages
      'atom_before_teaser:index'     => atom()->t('Before post teaser'),   // post listing pages
      'atom_after_teaser:index'      => atom()->t('After post teaser'),    // post listing pages
      'atom_before_comment:index'    => atom()->t('Before comment'),       // single pages
    ));

  atom()->registerDefaultOptions(array(
    'layout'                       => 'c2right',
    'page_width'                   => 'fixed',
    'page_width_max'               => 1200,
    'dimensions_fixed_c2left'      => '320',
    'dimensions_fixed_c2right'     => '640',
    'dimensions_fixed_c3'          => '240;720',
    'dimensions_fixed_c3left'      => '240;480',
    'dimensions_fixed_c3right'     => '480;720',
    'dimensions_fluid_c2left'      => '30',
    'dimensions_fluid_c2right'     => '70',
    'dimensions_fluid_c3'          => '25;75',
    'dimensions_fluid_c3left'      => '25;50',
    'dimensions_fluid_c3right'     => '50;75',
    'favicon'                      => atom()->getThemeURL().'/favicon.ico',
    'logo'                         => '',
    'color_scheme'                 => 'green',
    'background_image'             => '',
    'background_color'             => '',
    'background_image_selector'    => '#page',    // internal (only change if you alter the selector name in the html templates / css)
    'background_color_selector'    => 'body',     // internal, same...
    'footer_content'               => '<p> [credit] | [link rss] </p>',
    'post_title'                   => true,
    'post_date'                    => true,
    'post_date_mode'               => 'relative',
    'post_category'                => true,
    'post_tags'                    => true,
    'post_author'                  => true,
    'post_comments'                => true,
    'post_content'                 => true,
    'post_content_mode'            => 'f',
    'post_content_size'            => 600,
    'post_thumbs'                  => true,
    'post_thumbs_mode'             => 'left',
    'post_thumb_size'              => '90x90',
    'post_thumb_auto'              => true,
    'post_navi'                    => 'single',
    'single_links'                 => true,
    'single_meta'                  => true,
    'single_share'                 => true,
    'single_author'                => false,
    'single_related'               => true,
    'comment_karma'                => true,
    'comment_bury_limit'           => -5,
    'widget_icon_size'             => '42x38', // internal, each theme should define its own size
    'css'                          => '',
    'jquery'                       => true,
    'effects'                      => true,
    'optimize'                     => false,
    'generate_thumbs'              => true,
    'lightbox'                     => true,
    'debug'                        => false,
    'meta_description'             => true,
    'remove_settings'              => false,
  ));


  atom()->registerWidgetArea(array(
    'id'            => 'sidebar1',
    'name'          => atom()->t('Primary Sidebar'),
    'description'   => atom()->t('This is the default sidebar, active on 2 or 3 column layouts. If no widgets are visible, the page will fall back to a single column layout.'),
    'before_widget' => '<li class="block"><div class="block-content block-%2$s clear-block" id="instance-%1$s">',
    'after_widget'  => '</div></li>',
    'before_title'  => '<div class="title"><h3>',
    'after_title'   => '</h3><div class="bl"></div><div class="br"></div></div><div class="i"></div>',
  ));

  atom()->registerWidgetArea(array(
    'id'            => 'sidebar2',
    'name'          => atom()->t('Secondary Sidebar'),
    'description'   => atom()->t('This sidebar is active only on a 3 column setup, if at least one of its widgets is visible to the current user.'),
    'before_widget' => '<li class="block"><div class="block-content block-%2$s clear-block" id="instance-%1$s">',
    'after_widget'  => '</div></li>',
    'before_title'  => '<div class="title"><h3>',
    'after_title'   => '</h3><div class="bl"></div><div class="br"></div></div><div class="i"></div>',
  ));


  atom()->registerWidgetArea(array(
    'id'            => 'footer1',
    'name'          => atom()->t('Footer'),
    'description'   => atom()->t('Active only if at least one of its widgets is visible to the current user. You can add between 1 and 6 widgets here (3 or 4 are optimal). They will adjust their size based on the widget count.'),
    'before_widget' => '<li class="block block-%2$s" id="instance-%1$s"><div class="block-content clear-block">',
    'after_widget'  => '</div></li>',
    'before_title'  => '<h4 class="title">',
    'after_title'   => '</h4>'
  ));

  atom()->registerWidgetArea(array(
    'id'            => 'arbitrary',
    'name'          => atom()->t('Arbitrary Widgets'),
    'description'   => atom()->t('Widgets from this area can be grouped into tabs, or added into posts/pages using the %1$s or %2$s shortcodes.', array('[widget ID]', '[widget "Name"]')),
    'before_widget' => '<div class="block"><div class="block-content block-%2$s clear-block" id="instance-%1$s">',
    'after_widget'  => '</div></div>',
    'before_title'  => '<h3 class="title"><span>',
    'after_title'   => '</span></h3>',
  ));

  // theme settings interface
  if(is_admin()){

    // tabs and tab sub-sections
    atom()->interface->addSection('welcome',          atom()->t('Welcome'));
    atom()->interface->addSection('design',           atom()->t('Design'));
    atom()->interface->addSection('content',          atom()->t('Content options'));
    atom()->interface->addSection('content/post',     atom()->t('Post teasers'));
    atom()->interface->addSection('content/single',   atom()->t('Single pages'));
    atom()->interface->addSection('content/comment',  atom()->t('Comments'));
    atom()->interface->addSection('content/footer',   atom()->t('Footer'));
    atom()->interface->addSection('css',              atom()->t('CSS'));
    atom()->interface->addSection('advanced',         atom()->t('Advanced'));
    atom()->interface->addSection('modules',          atom()->t('Modules'));

    // options, as form fields
    atom()->interface->addControls(array(

      // content options: titles on post teasers
      'post_title'          => array(
                                 'location'    => 'content/post',
                                 'type'        => 'checkbox',
                                 'label'       => atom()->t('Title'),
                               ),

      // content options: content on post teasers
      'post_content'        => array(
                                 'location'    => 'content/post',
                                 'type'        => 'checkbox',
                                 'label'       => atom()->t('Content'),
                               ),

      // content options: content display mode
      'post_content_mode'   => array(
                                 'location'    => 'content/post/post_content',
                                 'depends_on'  => 'post_content',
                                 'type'        => 'select',
                                 'values'      => array(
                                   'user'        => atom()->t('User limit'),
                                   'f'           => atom()->t('Full post'),
                                   'ff'          => atom()->t('Full post, filtered'),
                                   'e'           => atom()->t('Excerpt'),
                                 ),
                               ),

      // content options: content limit
      'post_content_size'   => array(
                                 'location'    => 'content/post/post_content',
                                 'depends_on'  => 'post_content_mode:user',
                                 'type'        => 'text:5',
                                 'label'       => atom()->t('~ %s characters', '%post_content_size%'),
                               ),

      // content options: show category links on post teasers
      'post_category'       => array(
                                 'location'    => 'content/post',
                                 'type'        => 'checkbox',
                                 'label'       => atom()->t('Category'),
                               ),


      // content options: show date on post teasers
      'post_date'           => array(
                                 'location'    => 'content/post',
                                 'type'        => 'checkbox',
                                 'label'       => atom()->t('Date / Time'),
                               ),

      // content options: date display mode
      'post_date_mode'      => array(
                                 'location'    => 'content/post/post_date',
                                 'depends_on'  => 'post_date',
                                 'type'        => 'select',
                                 'values'      => array(
                                   'relative'    => atom()->t('Relative'),
                                   'absolute'    => atom()->t('Absolute'),
                                 ),
                               ),

      // content options: show author link on post teasers
      'post_author'         => array(
                                 'location'    => 'content/post',
                                 'type'        => 'checkbox',
                                 'label'       => atom()->t('Author'),
                               ),

      // content options: comments link on post teasers
      'post_comments'       => array(
                                 'location'    => 'content/post',
                                 'type'        => 'checkbox',
                                 'label'       => atom()->t('Comment Count'),
                               ),

      // content options: tag links on post teasers
      'post_tags'           => array(
                                 'location'    => 'content/post',
                                 'type'        => 'checkbox',
                                 'label'       => atom()->t('Tags'),
                               ),

      // content options: thumbnails on post teasers
      'post_thumbs'         => array(
                                 'location'    => 'content/post',
                                 'type'        => 'checkbox',
                                 'label'       => atom()->t('Thumbnails'),
                               ),

      // content options: alignment of on post thumbnails
      'post_thumbs_mode'    => array(
                                 'location'    => 'content/post/post_thumbs',
                                 'depends_on'  => 'post_thumbs',
                                 'type'        => 'select',
                                 'values'      => array(
                                   'left'        => atom()->t('Left Aligned'),
                                   'right'       => atom()->t('Right Aligned'),
                                 ),
                               ),

      // content options: size of post thumbnails
      'post_thumb_size'     => array(
                                 'location'    => 'content/post',
                                 'depends_on'  => 'post_thumbs',
                                 'type'        => 'select',
                                 'label'       => atom()->t('Thumbnail Size'),
                                 'description' => atom()->t('Note that for the new sized thumbnails to take effect, all existing images must be resized. See the Advanced page.'),
                                 'values'      => array(
                                   '60x60'       => atom()->t('Very Small, 60 x 60'),
                                   '90x90'       => atom()->t('Small, 90 x 90'),
                                   '120x120'     => atom()->t('Medium, 120 x 120'),
                                   '140x140'     => atom()->t('Large, 140 x 140'),
                                   '180x180'     => atom()->t('Larger, 180 x 180'),
                                   '210x210'     => atom()->t('Very large, 210 x 210'),
                                   'media'       => atom()->t('Default media setting: %d x %d', array(get_option('thumbnail_size_w'), get_option('thumbnail_size_h'))),
                                 ),
                               ),

      // content options: automatically select thumbnails from first image
      'post_thumb_auto'     => array(
                                 'location'    => 'content/post',
                                 'type'        => 'checkbox',
                                 'label'       => atom()->t('Use first attachment as thumbnail, if none is set'),
                               ),

      // content options: display mode of navigation links
      'post_navi'           => array(
                                 'location'    => 'content/post',
                                 'type'        => 'select',
                                 'label'       => atom()->t('Navigation'),
                                 'values'      => array(
                                   'single'      => atom()->t('Single link for older posts'),
                                   'prevnext'    => atom()->t('Previous / Next links'),
                                   'numbers'     => atom()->t('Page numbers'),
                                 ),
                               ),

      // comments: enable/disable karma ratings
      'comment_karma'       => array(
                                 'location'    => 'content/comment',
                                 'type'        => 'checkbox',
                                 'label'       => atom()->t('Allow Comment Ratings (Karma)'),
                               ),

      // comments: hide or show low-karma comments
      'comment_bury_limit'  => array(
                                 'location'    => 'content/comment',
                                 'type'        => 'text:5',
                                 'depends_on'  => 'comment_karma',
                                 'label'       => atom()->t('Hide comments with %s karma and below', array('%comment_bury_limit%')),
                               ),

      // single: post navigation for single pages
      'single_links'        => array(
                                 'location'    => 'content/single',
                                 'type'        => 'checkbox',
                                 'label'       => atom()->t('Previous / Next links'),
                               ),

      // single: share links on single pages
      'single_share'        => array(
                                 'location'    => 'content/single',
                                 'type'        => 'checkbox',
                                 'label'       => atom()->t('Share Links'),
                               ),

      // single: meta info on single pages
      'single_meta'         => array(
                                 'location'    => 'content/single',
                                 'type'        => 'checkbox',
                                 'label'       => atom()->t('Meta Information'),
                               ),

      // single: about the author info on single pages
      'single_author'       => array(
                                 'location'    => 'content/single',
                                 'type'        => 'checkbox',
                                 'label'       => atom()->t('About the Author'),
                               ),

      // single: related posts
      'single_related'      => array(
                                 'location'    => 'content/single',
                                 'type'        => 'checkbox',
                                 'label'       => atom()->t('Related Posts'),
                               ),

      // footer: contents of the footer (last block area)
      'footer_content'      => array(
                                 'location'    => 'content/footer',
                                 'type'        => 'text/code/html',
                                 'description' => atom()->t('Use %s for convenient adjustments', array(sprintf('<code>[%s]</code>', atom()->t('shortcodes')))),
                               ),

      // advanced: jquery
      'jquery'              => array(
                                 'location'    => 'advanced',
                                 'type'        => 'checkbox',
                                 'label'       => atom()->t('Use jQuery'),
                                 'description' => atom()->t("Only uncheck if you know what you're doing. Your site will load faster without jQuery, but features that depend on it will be disabled."),
                               ),

      // advanced: effects
      'effects'             => array(
                                 'location'    => 'advanced',
                                 'type'        => 'checkbox',
                                 'depends_on'  => 'jquery',
                                 'label'       => atom()->t('Animate content'),
                                 'description' => atom()->t('Enable jQuery effects such as fading or sliding. Effects can render the site slightly slower on less powerful PCs'),
                               ),

      // advanced: optimize
      'optimize'            => array(
                                 'location'    => 'advanced',
                                 'type'        => 'checkbox',
                                 'cap'         => 'edit_themes', // this option is slightly sensitive, so we only enable editing for users who can access the editor (usually super-admins)
                                 'label'       => atom()->t('Optimize website for faster loading'),
                                 'description' => atom()->t("Compresses and concatenates stylesheets and javascript files (using %s). Leave this unchecked if you're experiencing conflicts with other plugins, or if you're using a cache plugin that handles such functions"),
                               ),

      // advanced: auto (re-)generate missing thumbnails
      'generate_thumbs'     => array(
                                 'location'    => 'advanced',
                                 'type'        => 'checkbox',
                                 'depends_on'  => 'jquery',
                                 'label'       => atom()->t('Automatically create missing thumbnail sizes'),
                                 'description' => atom()->t('Asynchronously update thumbnails if needed. You can also use the %s plugin to process all missing thumbnail sizes manually, in a single pass', array('<a href="http://wordpress.org/extend/plugins/regenerate-thumbnails/" target="_blank">Regenerate Thumbnails</a>')),
                               ),

      // advanced: use built-in lightbox
      'lightbox'            => array(
                                 'location'    => 'advanced',
                                 'type'        => 'checkbox',
                                 'depends_on'  => 'jquery',
                                 'label'       => atom()->t('Use theme built-in lightbox on all image links'),
                                 'description' => atom()->t('Uncheck if you prefer a lightbox plugin'),
                               ),

      // advanced: auto-generate meta descriptions
      'meta_description'    => array(
                                 'location'    => 'advanced',
                                 'type'        => 'checkbox',
                                 'label'       => atom()->t('Automatically generate meta descriptions'),
                                 'description' => atom()->t("Uncheck if you're using a SEO plugin, otherwise you'll get duplicate fields! Note that you don't need such plugins, %s is heavily optimized for search engines", array(atom()->getThemeName())),
                               ),

      // advanced: debug info
      'debug'               => array(
                                 'location'    => 'advanced',
                                 'type'        => 'checkbox',
                                 'label'       => atom()->t('Display debug messages (english only)'),
                                 'description' => atom()->t('Status notifications about the theme setup (only visible to administrators). Only enable this when you need to, because it can significantly slow down page load for admins'),
                               ),

      // advanced: auto-uninstall
      'remove_settings'     => array(
                                 'location'    => 'advanced',
                                 'type'        => 'checkbox',
                                 'label'       => atom()->t('Theme auto-uninstall'),
                                 'description' => atom()->t('Check to remove all %s settings from the database after you switch to a different theme (Featured post records are preserved).', array(atom()->getThemeName())),
                               ),


    ));

  }



  /*** Mystique-specific settings / hooks ***/

  // append arrow pointers inside primary menu items
  atom()->addContextArgs('primary_menu', array('link_after' => '<span class="p"></span>'));



  atom()->add('sync_options', 'mystique_sync_old_options', 10, 3);

  function mystique_sync_old_options($old_version, $defaults, $old_options){

    // completely reset settings from the database if version is older than 3.0,
    // because almost none of the older settings are relevant in 3+
    if(version_compare($old_version, '3.0', '<')){
      atom()->reset();

    // changes in 3.3
    }elseif(version_compare($old_version, '3.3', '<')){

      // background color option changed in 3.3
      // black doesn't meant "no color" anymore
      if($old_options['background_color'] == '000000' || empty($old_options['background_color'])){
        $old_options['background_color'] = '';
        atom()->setOptions($old_options);
      }

    }

    /*
    // some of the 3.2+ option names are different
    // we need to safely update them -- not now...
    elseif(version_compare($old_version, '3.1', '<')){
      // we're changing the option ID
      $old_id = get_stylesheet();
      $old_options = get_option($old_id);

      if($old_id !== 'mystique'){
        atom()->setOptions($old_options);
        delete_option($old_id);
      }

    }

    */
  }

}

