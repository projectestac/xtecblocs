<?php
/*
 * Atom filters and actions that change WP's behaviour.
 *
 * Read the documentation for more info: http://digitalnature.eu/docs/
 *
 * @revised   February 4, 2012
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */



/**
 * Check WordPress version and register out hooks if everything is OK
 *
 * @since 1.7
 */
function atom_register_hooks(){
  global $wp_version;

  // if the current wp version is lower than the minimum required version display a error message and don't go further
  if($wp_version < Atom::REQ_WP_VERSION){
    add_action('admin_notices',  'atom_unsupported_wp_version');
    add_action('wp',             'atom_unsupported_wp_version');

    // stop, don't register hooks...
    return false;
  }

  // admin-only hooks
  if(is_admin()){
    add_action('admin_notices',  'atom_editor_warning');
    add_action('save_post',      'atom_save_post', 10, 2);
    add_action('add_meta_boxes', 'atom_meta_boxes', 10, 2);

    // theme update check
    if(Atom::THEME_UPDATE_URI)
      add_filter('pre_set_site_transient_update_themes', 'atom_transient_update_themes');

    // atom css/js in the administration area
    add_action('admin_enqueue_scripts',         'atom_admin_assets');

    // featured columns
    add_filter('manage_edit-post_columns',      'atom_featured_column_title');
    add_filter('manage_media_columns',          'atom_featured_column_title');
    add_filter('manage_posts_custom_column',    'atom_posts_column_content', 10, 2);
    add_filter('manage_media_custom_column',    'atom_media_column_content', 10, 2);
    add_action('post_submitbox_misc_actions',   'atom_publish_action_featured');

    add_action('wp_ajax_process_featured',      'atom_process_featured');

    add_action('delete_post',                   'clean_featured_post_record');

  // front-end-only hooks
  }else{

    // css class adjustments
    add_filter('body_class',         'atom_body_class');
    add_filter('post_class',         'atom_post_class');
    add_filter('comment_class',      'atom_comment_class');
    add_filter('nav_menu_css_class', 'atom_menu_css_classes', 10, 2);

    // meta info in document head
    add_filter('get_the_generator_xhtml', 'atom_meta_generator', 10, 2);
    add_filter('index_rel_link',          'atom_link_rel_index');

    // remove post relational links
    remove_action('wp_head', 'start_post_rel_link', 10);
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);

    // javascript and css
    add_action('wp_enqueue_scripts', 'atom_assets');
    add_action('wp_head',            'atom_inline_css', 420);

    // compress and concatenate js/css
    if(ATOM_CONCATENATE_JS)
      add_action('wp_print_scripts', 'atom_print_scripts', 999);

    add_action('wp_print_styles',    'atom_print_styles', 999);

    add_action('atom_before_page',   'atom_check_js');

    add_action('template_redirect',   'atom_redirect', -999);
    add_filter('get_search_form',     'atom_get_search_form');

    // these templates are found in the "templates" directory by default
    add_filter('index_template',      'atom_wp_templates', 999);
    add_filter('404_template',        'atom_wp_templates', 999);
    add_filter('archive_template',    'atom_wp_templates', 999);
    add_filter('author_template',     'atom_wp_templates', 999);
    add_filter('category_template',   'atom_wp_templates', 999);
    add_filter('tag_template',        'atom_wp_templates', 999);
    add_filter('taxonomy_template',   'atom_wp_templates', 999);
    add_filter('date_template',       'atom_wp_templates', 999);
    add_filter('home_template',       'atom_wp_templates', 999);
    add_filter('front_page_template', 'atom_wp_templates', 999);
    add_filter('page_template',       'atom_wp_templates', 999);
    add_filter('paged_template',      'atom_wp_templates', 999);
    add_filter('search_template',     'atom_wp_templates', 999);
    add_filter('single_template',     'atom_wp_templates', 999);
    add_filter('attachment_template', 'atom_wp_templates', 999);

    // bbp compat
    add_filter('bbp_get_profile_template',        'atom_bbp_templates', 999);
    add_filter('bbp_get_profile_edit_template',   'atom_bbp_templates', 999);
    add_filter('bbp_get_single_view_template',    'atom_bbp_templates', 999);
    add_filter('bbp_get_topic_edit_template',     'atom_bbp_templates', 999);
    add_filter('bbp_get_topic_split_template',    'atom_bbp_templates', 999);
    add_filter('bbp_get_topic_merge_template',    'atom_bbp_templates', 999);
    add_filter('bbp_get_reply_edit_template',     'atom_bbp_templates', 999);
    add_filter('bbp_get_topic_tag_template',      'atom_bbp_templates', 999);
    add_filter('bbp_get_topic_tag_edit_template', 'atom_bbp_templates', 999);
    add_filter('bbp_get_theme_compat_templates',  'atom_bbp_templates', 999);

    // remove default bbp error handling on template_notices, and add ours (we display form-inline errors)
    remove_action('bbp_template_notices',         'bbp_template_notices');
    add_action('bbp_template_notices',            'atom_bbp_template_notices');
  }

  // universal hooks
  add_action('init', 'atom_set_thumb_sizes');
  add_action('init', 'atom_signup_redirect');

  // set up widgets
  add_action('widgets_init',                             'atom_widgets_init');
  add_filter('widget_display_callback',                  'atom_visibility_check');
  add_filter('widget_update_callback',                   'atom_widget_visibility_update', 10, 3);
  add_action('in_widget_form',                           'atom_widget_visibility_options', 10, 3);
  add_action('wp_ajax_widget_visibility_options_fields', 'atom_widget_visibility_options_fields');


  // ajax/get requests -- should be on "init" but some requests might need the post data to be set up
  add_action('wp',                  'atom_requests');

  if(ATOM_TRACK_USERS)
    add_action('wp',                  'atom_update_online_users_status');

  // change the wpmu sign-up page, if we have a page that uses the "sign-up" page template
  add_filter('wp_signup_location',  'atom_wp_signup_location');

  // wp cron job, deletes expired transients from the db
  add_action('wp_scheduled_delete', 'atom_delete_expired_transients');

  // post query
  add_filter('query_vars',          'atom_query_vars');
  add_filter('pre_get_posts',       'atom_main_query_args');

  // wp applies the_content filter even if outside the loop,
  // and stupid wp plugins don't bother to handle this...
  remove_filter('get_the_excerpt', 'wp_trim_excerpt');
  add_filter('get_the_excerpt',     'atom_trim_excerpt', -10);


  Atom::action('init');
  return true;
}



/**
 * register the "atom" query variable
 *
 * @since 1.0
 */
function atom_query_vars($public_query_vars){
  $public_query_vars[] = 'atom';
  return $public_query_vars;
}



/**
 * Change main query arguments
 *
 * @since 1.7
 *
 * @param object $query
 *
 * @return object
 */
function atom_main_query_args($query){
  global $wp_the_query;

  // @todo: use is_main_query() when we drop support for wp < 3.3
  if(!is_admin() && ($wp_the_query === $query) && ($custom_query_args = atom()->getContextArgs('main_query')))
    foreach($custom_query_args as $arg => $value) $query->set($arg, $value);

  return $query;
}



/**
 * Template redirect
 *
 * @since 1.3
 */
function atom_redirect(){

  // check for internal pages
  if(get_query_var('pagename')){
    $page = get_query_var('pagename');

    if($page && ($template = atom()->findTemplate("internal-{$page}"))){

      global $wp_query;
      // a 404 code will not be returned in the HTTP headers if the page does not exist in the db
      status_header(200);
      $wp_query->is_404 = false;

      // used to set a body class
      atom()->is_internal_page = $page;

      // include the corresponding template
      atom()->load($template);
      exit;
    }

  }

  // redirect (only on single posts or pages if custom field exists)
  if(is_single() || is_page()){
    global $post;
        
    if($url = get_post_meta($post->ID, 'redirect', true)){
      wp_redirect(esc_url($url), 301); // @todo: add redirect type cf.
      exit;
    }
  } 
}



/**
 * Append the Atom version in the generator meta field
 *
 * @since 1.0
 *
 * @param string $generator
 * @param string $type
 */
function atom_meta_generator($generator, $type){
  return '<meta name="generator" content="WordPress '.get_bloginfo('version').', ATOM '.ATOM::VERSION.'" />';
}



/**
 * Changes the default <index rel="index" /> value depending on the current page context
 *
 * @since 1.7
 *
 * @param string $link Current link
 *
 * @return string new value
 */
function atom_link_rel_index($link){

  // defaults
  $title = esc_attr(get_bloginfo('name', 'display'));
  $url = esc_url(user_trailingslashit(get_bloginfo('url', 'display')));

  // single page/post/cpt
  if(is_singular()){
    $post_type = get_post_type();

    // @todo - maybe - handle hierarchy?
    if(!in_array($post_type, array('post', 'page'))){
      $title = post_type_archive_title($post_type);
      $post_type_object = get_post_type_object($post_type);
      $title = $post_type_object->labels->name;
      $url = get_post_type_archive_link($post_type);

    }elseif($post_type === 'post'){

      // if the blog is not the index page, then make the index the blog page
      if(get_option('show_on_front') === 'page' && get_option('page_on_front')){
        $blog = get_option('page_for_posts'); // get blog page ID
        $title = get_the_title($blog);
        $url = get_page_link($blog);
      }

    }

  // archives, including category/date/author etc.
  }elseif(is_archive()){

    // handle hierarchy, set the index to parent term
    $term = get_queried_object();
    if(!is_wp_error($term) && !empty($term->parent)){
      $tax = get_taxonomy($term->taxonomy);
      $title = $tax->labels->name;
      $url = get_term_link((int)$term->parent, $term->taxonomy);

    }else{

      // root-term, let the index be the homepage, unless...

      // ...the blog is not the index page, then make the index the blog page
      if((is_category() || is_tag() || is_date()) && get_option('show_on_front') === 'page' && get_option('page_on_front')){
        $blog = get_option('page_for_posts'); // get blog page ID
        $title = get_the_title($blog);
        $url = get_page_link($blog);
      }
    }

  }

  return '<link rel="index" title="'.$title.'" href="'.$url.'" />'."\n";
}



/**
 * Generates semantic classes for BODY element (mostly based on the sandbox theme)
 *
 * @since 1.0
 * @global $wp_query object
 *
 * @param array $classes
 */
function atom_body_class($classes){
  global $wp_query, $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

  // Special classes for BODY element when a single post
  if(is_single()){
    $postname = $wp_query->post->post_name;
    the_post();

    // post title
    $classes[] = "title-{$postname}";

    // Adds category classes for each category on single posts
    if($cats = get_the_category())
      foreach($cats as $cat) $classes[] = "category-{$cat->slug}";

    // Adds tag classes for each tags on single posts
    if($tags = get_the_tags())
      foreach($tags as $tag) $classes[] = "tag-{$tag->slug}";

    // Adds author class for the post author
    $classes[] = "author-".sanitize_html_class(strtolower(get_the_author_meta('user_login')));
    rewind_posts();

  }elseif(is_page()){ 	// Page author for BODY on 'pages'
    $pagename = $wp_query->post->post_name;
    the_post();
    $classes[] = "page-{$pagename}";
    $classes[] = "author-".sanitize_html_class(strtolower(get_the_author_meta('user_login')));
    rewind_posts();

  }elseif(!empty(atom()->is_internal_page)){
    $classes[] = 'int';
    $classes[] = 'int-'.atom()->is_internal_page;
  }

  // generate layout related classes
  if(atom()->options('layout')){
    $classes[] = atom()->getLayoutType();
    $classes[] = atom()->options('page_width'); // fluid/fixed
  }

  if(atom()->options('background_image'))
    $classes[] = 'cbgi';

  if(atom()->options('background_color'))
    $classes[] = 'cbgc';

  // determine if meta is present (comments)
  // some themes (like "digitalnature v5" may want to style the site differently in this case
  if(is_singular()){
    $comments_disabled = (post_password_required() || (!comments_open() && !is_single('post') && atom()->post->getCommentCount() < 1));
    if(!$comments_disabled)
      $classes[] = 'with-meta';
  }

  // detect browser
  if($is_lynx) $browser = 'lynx';
  elseif($is_gecko) $browser = 'gecko';
  elseif($is_opera) $browser = 'opera';
  elseif($is_NS4) $browser = 'ns4';
  elseif($is_safari) $browser = 'safari';
  elseif($is_chrome) $browser = 'chrome';
  elseif($is_IE) $browser = 'ie';
  else $browser = 'unknown';

  // iphone?
  if($is_iphone) $browser .= '-iphone';

  $classes[] = "browser-{$browser}";

  return atom()->getContextArgs('body_class', $classes);
}



/**
 * Generates semantic classes for posts
 *
 * @since 1.0
 * @global $wp_query object
 *
 * @param array $classes
 */
function atom_post_class($classes){
  global $wp_query;

  $current_post = $wp_query->current_post + 1;

  // post alt
  $classes[] = "count-{$current_post}";
  $classes[] = ($current_post % 2) ? 'odd' : 'even alt';

  // thumbnail classes
  if(!in_array('thumb-only', $classes) && (atom()->options('post_thumbs', 'post_thumbs_mode') === TRUE) && !is_singular())
    $classes[] = 'thumb-'.atom()->options('post_thumbs_mode');

  // author
  $classes[] = 'author-'.sanitize_html_class(strtolower(get_the_author_meta('user_nicename')), get_the_author_meta('ID'));

  // password-protected?
  if(post_password_required())
    $classes[] = 'protected';

  // first/last class ("first" and "count-1" are the same)
  if($current_post === 1) $classes[] = 'first'; elseif($current_post === $wp_query->post_count) $classes[] = 'last';

  return atom()->getContextArgs('post_class', $classes);
}



/**
 * Generates semantic classes for comments
 *
 * @since 1.0
 * @global $post object
 * @global $comment object
 *
 * @param array $classes
 */
function atom_comment_class($classes) {
  global $post, $comment;

  // avatars enabled?
  if(get_option('show_avatars'))
     $classes[] = 'with-avatars';

  // user roles
  if($comment->user_id > 0){
    $user = new WP_User($comment->user_id);

    if(is_array($user->roles))
      foreach ($user->roles as $role) $classes[] = 'role-'.$role;
    $classes[] = 'user-'.sanitize_html_class(strtolower($user->user_nicename), $user->ID);

  }else{
    $classes[] = 'reader name-'.sanitize_html_class(strtolower(get_comment_author()));
  }

  // needs moderation?
  if($comment->comment_approved == '0') $classes[] = 'awaiting-moderation';

  // karma-related
  if(atom()->comment->isBuried()) $classes[] = 'buried';
  if(atom()->options('comment_karma'))
    if((int)$comment->comment_karma < 0) $classes[] = 'karma-negative'; elseif((int)$comment->comment_karma > 0) $classes[] = 'karma-positive';

  return atom()->getContextArgs('comment_class', $classes);
}



/**
 * Adjust classes added by the menu walker
 *
 * @since 1.0
 *
 * @param array $classes CSS classes
 * @param object $item Current menu item
 *
 * @return array Updated classses
 */
function atom_menu_css_classes($old_classes, $item){
  // remove useless classes like id-xxx and only keep the object title
  $classes = array('menu-'.sanitize_html_class(strtolower($item->title)));

  // we are going to replace old classes with 'active' or 'active-parent'
  $allowed_classes = array('current-menu-item', 'current-menu-parent', 'current-menu-ancestor', 'extends');
  $new_classes = array('active', 'active-parent', 'active-parent', 'extends');

  foreach($old_classes as $class)
    if(in_array($class, $allowed_classes))
      $classes[] = str_replace($allowed_classes, $new_classes, $class);

  return $classes;
}



/**
 * Sets up widgets
 *
 * @since 1.0
 */
function atom_widgets_init(){
  global $pagenow;

  // sidebars
  $widget_areas = atom()->get('widget_areas');

  // default widgets (used if the theme is installed for the 1st time and if they are not already present in the sidebar, see below)
  $widget_fallbacks = array();

  if(!empty($widget_areas))
    foreach($widget_areas as $area){
      if(isset($area['default_widgets'])){
        $widget_fallbacks[$area['id']] = $area['default_widgets'];
        unset($area['default_widgets']);
      }

      register_sidebar($area);
    }

  // register widgets
  $widgets = atom()->getContextArgs('active_widgets', array(
    'Archives',
    'Blogs',
    'Calendar',
    'Links',
    'Login',
    'Menu',
    'Pages',
    'Posts',
    'RecentComments',
    'Search',
    'Splitter',
    'Tabs',
    'TagCloud',
    'Terms',
    'Text',
    'TopCommenters',
    'Twitter',
    'Users',
  ));

  $root_site = is_multisite() && is_main_site();

  if(!empty($widgets))
    foreach((array)$widgets as $widget)
      if((!$root_site && $widget !== 'Blogs') || $root_site){

        // unregister default widget correspondent
        if($widget === 'Menu')
          unregister_widget('WP_Nav_Menu_Widget');

        elseif($widget === 'Meta')
          unregister_widget('WP_Widget_Meta');

        elseif($widget === 'Posts')
          unregister_widget('WP_Widget_Recent_Posts');

        elseif($widget === 'Search')
          unregister_widget('WP_Widget_Search');

        elseif($widget === 'Terms')
          unregister_widget('WP_Widget_Categories');

        else
          unregister_widget('WP_Widget_'.preg_replace('/\B([A-Z])/', '_$1', $widget));

        register_widget("AtomWidget{$widget}");
      }

  // first run and no active widgets?
  // add a few then, since a lot of people don't know about widgets.
  // this way they may find out about them by trying to remove these ones :)
  ///
  // @note: this code only runs when the theme is activated, and not on theme options reset
  if(apply_filters('atom_widget_fallback_condition', defined('INSTALLING_ATOM') && !empty($widget_fallbacks))){

    $sidebars_widgets = get_option('sidebars_widgets');
    $have_tabs = false;
    foreach($widget_fallbacks as $sidebar_id => $sidebar_widgets){
      $active_widgets = array();

      // only proceed if this sidebar is empty
      if(empty($sidebars_widgets[$sidebar_id])){
        foreach($sidebar_widgets as $widget => $instance){

          // not a associative array? assume the value is the widget ID base
          if(is_numeric($widget)){
            $widget = $instance;
            $instance = array();
          }

          $widget_options = get_option("widget_{$widget}"); // options for all instances

          atom()->log("A default widget was added to the &lt;{$sidebar_id}&gt; area: {$widget}");

          $widget_options[] = $instance;
          end($widget_options);

          // widget instance ID, eg. atom-posts-2
          $instance_id = key($widget_options);
          $active_widgets[] = $widget.'-'.$instance_id;

          if(!isset($widget_options['_multiwidget'])) $widget_options['_multiwidget'] = 1;

          update_option("widget_{$widget}", $widget_options);

          if($widget === 'atom-tabs') $have_tabs = $instance_id;

        }

        $sidebars_widgets[$sidebar_id] = $active_widgets;
        update_option('sidebars_widgets', $sidebars_widgets);

      }
    }

    // set up first tab widget instance to use all widgets from the 'arbitrary' area
    // -- only if it was found above & if it's "widgets" option is empty
    if($have_tabs){
      $widget_options = get_option('widget_atom-tabs');
      if(empty($widget_options[$have_tabs]['widgets'])){
        $widget_options[$have_tabs]['widgets'] = $sidebars_widgets['arbitrary'];
        update_option('widget_atom-tabs', $widget_options);
      }
    }

  }

  // keep only the last 10 inactive widgets (faster widgets.php load)
  // http://wordpress.stackexchange.com/questions/23915/limit-the-number-of-inactive-widgets
  if(is_admin() && $pagenow === 'widgets.php'){
    $sidebars = wp_get_sidebars_widgets();
    if(count($sidebars['wp_inactive_widgets']) > 10){
      $new_inactive = array_slice($sidebars['wp_inactive_widgets'], -10, 10);

      // remove the dead widget options
      $dead_inactive = array_slice($sidebars['wp_inactive_widgets'], 0, count($sidebars['wp_inactive_widgets']) - 10);
      foreach($dead_inactive as $dead){
        $pos = strpos($dead, '-');
        $widget_name = substr($dead, 0, $pos);
        $widget_number = substr($dead, $pos + 1);
        $options = get_option("widget_{$widget_name}");
        unset($options[$widget_number]);
        update_option("widget_{$widget_name}", $options);
      }

      // save our new widget setup
      $sidebars['wp_inactive_widgets'] = $new_inactive;
      wp_set_sidebars_widgets($sidebars);
    }
  }
}



/**
 * Hide widget/item based on the current context.
 * The check is not strict, ie. if a certain page ID or role is not present in the $instance array (and has a false value),
 * then the item will be considered visible on that page ID / role etc.
 *
 * Important: always use identical operators to check what this functions returns, like === or !===
 * because in some cases $instance might be a empty array (ie. widget doesn't have options set)
 *
 * @since 1.0
 *
 * @param array $instance Instance options
 * @return array|bool returns instance options if widget should be visible on the current page, false otherwise
 */
function atom_visibility_check($instance){
  global $current_user;

  if(is_404() || !is_array($instance)) return false;

  $restricted_post_types = $restricted_taxonomies = $restricted_roles = array();

  // generic pages
  if(is_home())
    $show = isset($instance['page-home']) ? ($instance['page-home']) : true;
  elseif(is_author())
    $show = isset($instance['page-author']) ? ($instance['page-author']) : true;
  elseif(is_date())
    $show = isset($instance['page-date']) ? ($instance['page-date']) : true;
  elseif(is_search())
    $show = isset($instance['page-search']) ? ($instance['page-search']) : true;
  elseif(is_category())
    $show = isset($instance['page-category']) ? ($instance['page-category']) : true;
  elseif(is_tag())
    $show = isset($instance['page-tag']) ? ($instance['page-tag']) : true;
  else $show = true;

  // check single post pages
  foreach($instance as $key => $enabled)
    if(strpos($key, 'page-singular-') === 0 && !$enabled) $restricted_post_types[] = substr($key, 14);

  if(!empty($restricted_post_types))
    foreach($restricted_post_types as $post_type)
      if(is_singular($post_type)) $show = false;


  // check taxonomies
  foreach($instance as $key => $enabled)
    if(strpos($key, 'page-tax-') === 0 && !$enabled) $restricted_taxonomies[] = substr($key, 9);

  if(!empty($restricted_taxonomies))
    foreach($restricted_taxonomies as $tax)
      if(is_tax($tax)) $show = false; // surprisingly category/tags are not considered taxonomies by is_tax(); hopefully this won't change in the future...


  // check pages
  if(is_page()){
    global $wp_query;
    $post_id = $wp_query->get_queried_object_id();
    $show = isset($instance["page-{$post_id}"]) ? ($instance["page-{$post_id}"]) : true;
  }


  // check user roles
  if(is_user_logged_in()){

    foreach($instance as $key => $enabled)
      // we're checking for all keys starting with "role-" and disable the widget only if the current user role matches a disabled role from $instance
      if((strpos($key, 'role-') === 0) && !$enabled) $restricted_roles[] = substr($key, 5);

    $has_not_role = true;
    foreach($restricted_roles as $role)
      if(in_array($role, $current_user->roles) && $has_not_role) $show = $has_not_role = false;

  }else{
    $show = (isset($instance['user-visitor']) && !$instance['user-visitor']) ? false : $show;
  }

  return $show ? $instance : false;
}



/**
 * Same as the above, but will perform a strict check,
 * meaning that it will only return true if the current page/taxonomy/post_type/role is present within the $instance array (and has a true value)
 *
 * @since 1.7
 *
 * @param array $instance Instance options
 * @return array|bool returns instance options if widget should be visible on the current page, false otherwise
 */
function atom_strict_visibility_check($instance){
  global $current_user;

  if(is_404() || !is_array($instance)) return false;

  // assume hidden
  $show = false;
  $allowed_post_types = $allowed_taxonomies = $allowed_roles = array();

  // generic pages
  if(is_home())
    $show = isset($instance['page-home']) && $instance['page-home'];
  elseif(is_author())
    $show = isset($instance['page-author']) && $instance['page-author'];
  elseif(is_date())
    $show = isset($instance['page-date']) && $instance['page-date'];
  elseif(is_search())
    $show = isset($instance['page-search']) && $instance['page-search'];
  elseif(is_category())
    $show = isset($instance['page-category']) && $instance['page-category'];
  elseif(is_tag())
    $show = isset($instance['page-tag']) && $instance['page-tag'];

  // check single post pages
  foreach($instance as $key => $enabled)
    if(strpos($key, 'page-singular-') === 0 && $enabled) $allowed_post_types[] = substr($key, 14);

  if(!empty($allowed_post_types))
    foreach($allowed_post_types as $post_type)
      if(is_singular($post_type)) $show = true;


  // check taxonomies
  foreach($instance as $key => $enabled)
    if(strpos($key, 'page-tax-') === 0 && $enabled) $allowed_taxonomies[] = substr($key, 9);

  if(!empty($allowed_taxonomies))
    foreach($allowed_taxonomies as $tax)
      if(is_tax($tax)) $show = true; // surprisingly category/tags are not considered taxonomies by is_tax(); hopefully this won't change in the future...


  // check pages
  if(is_page()){
    global $wp_query;
    $post_id = $wp_query->get_queried_object_id();
    $show = isset($instance["page-{$post_id}"]) && $instance["page-{$post_id}"];
  }


  // check user roles
  if(is_user_logged_in()){
    foreach($instance as $key => $enabled)
      // we're checking for all keys starting with "role-" and disable the widget only if the current user role matches a disabled role from $instance
      if((strpos($key, 'role-') === 0) && $enabled) $allowed_roles[] = substr($key, 5);

    $has_role = false;
    foreach($allowed_roles as $role)
      if(in_array($role, $current_user->roles) && $has_role){
        $has_role = true;
        $show = $show && $has_role;
      }

  }else{
    $show = isset($instance['user-visitor']) && $instance['user-visitor'] && $show;
  }

  return $show ? $instance : false;
}



/**
 * Check if jQuery is enabled and removes the "no-js" class from body
 * This function should be called just after <body> to avoid visual flickers
 *
 * @since 1.3
 */
function atom_check_js(){
  // "no js" means "no jquery" too so we leave the class if js is on, but jquery is disabled for some reason
  if(atom()->options('jquery'))
    echo "<script> document.body.className = document.body.className.replace('no-js',''); </script>";
}



/*
 * JavaScript and CSS used by Atom in the administration area
 *
 * @since   2.0
 * @param   string $page   Current page
 */
function atom_admin_assets($page){

  $app = atom();

  wp_enqueue_style(ATOM.'-settings', $app->get('theme_url').'/css/admin.css');

  // only load these on specific pages -- maybe we should load them on all pages?
  if(in_array($page, array('post.php', 'post-new.php', 'widgets.php', 'edit.php', 'upload.php', 'appearance_page_'.ATOM))){

    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');

    // we need to prepend our prefix to codemirror because the one used by plugins (if such a plugin is present) will most likely not work here
    wp_enqueue_script('atom-codemirror', $app->jsURL('codemirror'), $app->getThemeVersion(), true);
    wp_enqueue_script('atom-preview', $app->jsURL('theme-preview'), array('jquery'), $app->getThemeVersion(), true);
    wp_enqueue_script('atom-interface', $app->jsURL('admin'), array('jquery', 'jquery-ui-core', 'atom-codemirror'), $app->getThemeVersion(), true);

    $atom_config = array(
      'id'                        => ATOM,
      'preview_mode'              => false,
      'label_loading'             => $app->t('Loading...'),
      'label_visibility_show'     => $app->t('Show Page Visibility Options'),
      'label_visibility_hide'     => $app->t('Hide Page Visibility Options'),
      'label_checking'            => $app->t('Checking...'),
      'label_design_panel_error'  => $app->t('It appears that your home page has javascript errors on it. Deactivate all plugins, then activate them back one by one to find out which one is causing errors.'),
      'label_uploading'           => $app->t('Uploading'),
      'label_try_another'         => $app->t('Try Another Image?'),
      'label_change_image'        => $app->t('Change Image'),
      'label_upload_image'        => $app->t('Upload Image'),
    );

    wp_localize_script('atom-interface', 'atom_config', $app->getContextArgs('atom_config_js', $atom_config));

    // extra assets, registered by modules etc.
    $assets = $app->getContextArgs('interface_assets');

    if(isset($assets['script']))
      foreach($assets['script'] as $args)
        call_user_func_array('wp_enqueue_script', $args);

    if(isset($assets['style']))
      foreach($assets['style'] as $args)
        call_user_func_array('wp_enqueue_style', $args);

    $app->action('admin_js');
  }
}



/**
 * Load jQuery & related .js
 *
 * @since 1.3
 * @todo load locales in order (eg. one of the js files from the Symposium plugin requires a locale loaded before it)
 */
function atom_print_scripts(){
  global $wp_scripts, $wp_filesystem;

  if(!($wp_scripts instanceof WP_Scripts) || !atom()->options('optimize') || atom()->previewMode()) return;

  // remove admin-bar js if jquery is enabled
  //wp_dequeue_script('admin-bar');

  $scripts = $locales = array();
  $queue = $wp_scripts->queue;
  $wp_scripts->all_deps($queue);   // arrange the list of scripts based on dependencies

  foreach($wp_scripts->to_do as $key => $handle){

    if($wp_scripts->registered[$handle]->ver === null) $ver = '';
    else $ver = $wp_scripts->registered[$handle]->ver ? $wp_scripts->registered[$handle]->ver : $wp_scripts->default_version;

    if(isset($wp_scripts->args[$handle]))
      $ver = $ver ? $ver.'&amp;'. $wp_scripts->args[$handle] : $wp_scripts->args[$handle];

    $src = $wp_scripts->registered[$handle]->src;
    if($locale = $wp_scripts->print_scripts_l10n($handle, false)) $locales[] = $locale;
//    if($locale = $wp_scripts->print_scripts_l10n($handle, false)) continue;

    if(!preg_match('|^https?://|', $src) && ! ($wp_scripts->content_url && 0 === strpos($src, $wp_scripts->content_url)))
      $src = $wp_scripts->base_url.$src;

    if(!empty($ver))
      $src = add_query_arg('ver', $ver, $src);

    $src = esc_url_raw(apply_filters('script_loader_src', $src, $handle));

    $scripts[$handle] = $src;
//    unset($wp_scripts->to_do[$key]);
//    $wp_scripts->done[] = $handle;

  }

  if(empty($scripts)) return;

  if(($cache = get_transient('atom_js_cache')) === false) $cache = array();

  // wp-content/uploads
  $upload_dir = wp_upload_dir();

  $handles = implode(', ', array_keys($scripts));
  $cache_name = ATOM.'-'.md5($handles);
  $cache_file_path = "{$upload_dir['basedir']}/{$cache_name}.js";
  $cache_file_url = "{$upload_dir['baseurl']}/{$cache_name}.js";

  // calculate modified tag
  $hash = 0;
  foreach($scripts as $handle => $script){
    $parts = parse_url($script);
    $file_path = str_replace(site_url('/'), ABSPATH, $parts['scheme'].'://'.$parts['host'].$parts['path']);
    $hash += @filemtime($file_path);
  }

  // decide whether to build cache or not
  if(!isset($cache[$cache_name]) || ($cache[$cache_name] !== $hash) || !is_readable($cache_file_path)){

    require_once(ABSPATH.'wp-admin/includes/file.php');
    if(!WP_Filesystem())  // return and load them normally
      return atom()->log("Failed to initialize WPFS for javascript caching. Your host's \"security settings\" prevent direct file writes...", 1);

    $script_urls = array_values($scripts);
    $url = 'http://closure-compiler.appspot.com/compile?code_url='.array_shift($script_urls);
    foreach($script_urls as $script_url)
      $url .= '&code_url='.urlencode($script_url);

    $compression_options = array(
      'timeout' => 20,
      'body' => array(
        'compilation_level' => 'SIMPLE_OPTIMIZATIONS',
        'output_format'     => 'json',
        'output_info'       => 'compiled_code',
        //'output_file_name'  => 'atom_js',
    ));

    $raw_response = wp_remote_post($url, $compression_options);

    if(!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
      $response = json_decode($raw_response['body']);

    if(!empty($response->compiledCode) && empty($response->serverErrors) &&  empty($response->errors)){
      $compressed_js = $response->compiledCode;

      // save file
      if($wp_filesystem->is_writable($upload_dir['basedir']) && $wp_filesystem->put_contents($cache_file_path, "/*\nCache: {$handles}\n*/\n{$compressed_js}", FS_CHMOD_FILE)){
        $saved = true;
        $cache[$cache_name] = $hash;
        set_transient('atom_js_cache', $cache, 60*60*24*30); // 30 day cache
        atom()->log("Javascript cache rebuilt ({$cache_file_url})");

      }else{ // return and load them normally
        return atom()->log("Failed to create javascript cache ({$cache_file_url}). Try reloading again or disable the 'optimize' option to avoid slowdowns.", 1);
      }

    }else{ // return and load them normally
      $error_msg = empty($response->serverErrors[0]->error) ? '' : esc_attr(strip_tags($response->serverErrors[0]->error));
      return atom()->log("Failed to create javascript cache: the Google Closure Compiler service returned error(s): {$error_msg}. Please disable the 'optimize' option to avoid slowdowns.", 1);
    }

  }

  foreach($scripts as $id => $url)
    foreach($wp_scripts->to_do as $key => $handle)
      if($id == $handle){
        unset($wp_scripts->to_do[$key]);
        $wp_scripts->done[] = $handle;
      }

  echo '<script src="'.$cache_file_url.'"></script>'."\n";

  if(!empty($locales)){
    echo "<script>\n";
    foreach($locales as $locale){
      echo "{$locale}\n";
    }
    echo "</script>\n";
  }


//  atom()->addContextArgs('footer', array('js_cache' => $cache_file_url));
//  wp_enqueue_script(ATOM.'-js-cache', $cache_file_url, array(), false, true);

//  $wp_scripts->reset();
//  return $wp_scripts->done;
}



/**
 * Load stylesheets.
 * Will compress and concatenate them all into a single file, if "optimize" is checked.
 * Slightly based on "JS & CSS Script Optimizer" by evgenniy - http://4coder.info/en/
 *
 * @since 1.3
 *
 * @todo test rtl
 */
function atom_print_styles(){
  global $wp_styles, $wp_filesystem;
  if(!($wp_styles instanceof WP_Styles) || !atom()->options('optimize') || atom()->previewMode()) return;

  $styles = array();

  $queue = $wp_styles->queue;
  $wp_styles->all_deps($queue);
  $queue_unset = array();
  $home = site_url('/');
  foreach($wp_styles->to_do as $key => $handle){
    if($wp_styles->registered[$handle]->ver === null) $ver = '';
    else $ver = $wp_styles->registered[$handle]->ver ? $wp_styles->registered[$handle]->ver : $wp_styles->default_version;

    if(isset($wp_styles->args[$handle]))
      $ver = $ver ? $ver.'&amp;'.$wp_styles->args[$handle] : $wp_styles->args[$handle];

    if(isset($wp_styles->registered[$handle]->args)) $media = esc_attr($wp_styles->registered[$handle]->args); else $media = 'all';

    $href = $wp_styles->_css_href($wp_styles->registered[$handle]->src, $ver, $handle);

    // rtl?
    if('rtl' === $wp_styles->text_direction && isset($wp_styles->registered[$handle]->extra['rtl']) && $wp_styles->registered[$handle]->extra['rtl'])
      if(is_bool( $wp_styles->registered[$handle]->extra['rtl'])){
        $suffix = isset($wp_styles->registered[$handle]->extra['suffix']) ? $wp_styles->registered[$handle]->extra['suffix'] : '';
        $href = str_replace("{$suffix}.css", "-rtl{$suffix}.css", $wp_styles->_css_href($wp_styles->registered[$handle]->src , $ver, "$handle-rtl"));
      }else{
        $href = $wp_styles->_css_href($wp_styles->registered[$handle]->extra['rtl'], $ver, "$handle-rtl");
      }

    // ignore external styles
    $external = false;
    if((strpos($href, 'http', 0) !== false) && (strpos($href, $home, 0) === false)) $external = true;
    if($external) continue;

    $styles[$media][$handle] = $href;
  }

  if(($cache = get_transient('atom_css_cache')) === false) $cache = array();

  // wp-content/uploads
  $upload_dir = wp_upload_dir();

  foreach($styles as $media => $items){
    $handles = array_keys($items);
    $handles = implode(', ', $handles);
    $cache_name = ATOM.'-'.md5($handles);
    $cache_file_path = "{$upload_dir['basedir']}/{$cache_name}.css";
    $cache_file_url = "{$upload_dir['baseurl']}/{$cache_name}.css";

    // calculate modified tag
    $hash = 0;
    foreach($items as $handle => $style){
      $parts = parse_url($style);
      $file_path = str_replace(site_url('/'), ABSPATH, $parts['scheme'].'://'.$parts['host'].$parts['path']);
      $hash += @filemtime($file_path);
    }

    if(!isset($cache[$media][$cache_name]) || ($cache[$media][$cache_name] !== $hash) || !is_readable($cache_file_path)){ // build cache

      require_once(ABSPATH.'wp-admin/includes/file.php');
      if(!WP_Filesystem())  // return and load them normally
        return atom()->log("Failed to initialize WPFS for stylesheet caching. Your host's \"security settings\" prevent direct file writes...", 1);

      $css = '';
      foreach($items as $handle => $style){
        $css .= "/* $handle: ($style) */\n";
        
        $local_path = str_replace(site_url('/'), ABSPATH, $style);
        $local_path = substr($local_path, 0, strpos($local_path, '?')); // remove version info
        $content = $wp_filesystem->get_contents($local_path);

        // compress CSS (remove spaces & comments)
        $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);
        $content = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $content);
        $dir = dirname($style).'/'; // url()
        $content = preg_replace('|url\(\'?"?([a-zA-Z0-9\-_\s\./]*)\'?"?\)|', "url(\"$dir$1\")", $content);

        $css .= "{$content}\n";
      }

      // save file
      if($wp_filesystem->is_writable($upload_dir['basedir']) && $wp_filesystem->put_contents($cache_file_path, "/*\nCache: {$handles}\n*/\n{$css}", FS_CHMOD_FILE)){
        $saved = true;
        $cache[$media][$cache_name] = $hash;
        set_transient('atom_css_cache', $cache, 60*60*24*7); // 1 week cache
        atom()->log("Stylesheet cache rebuilt ({$cache_file_url}), media: {$media}");

      }else{ // return and load them normally
        return atom()->log("Failed to create stylesheet cache ({$cache_file_url}), media: {$media}", 1);
      }

    }

    echo "\n<link rel=\"stylesheet\" href=\"{$cache_file_url}\" type=\"text/css\" media=\"{$media}\" />\n";
  }

  foreach($wp_styles->to_do as $key => $handle){
    unset($wp_styles->to_do[$key]);
    $wp_styles->done[] = $handle;
  }
  
}



/**
 * Inline styles (layout dimensions, custom bg image etc.)
 *
 * @since 1.3
 */
function atom_inline_css(){

  // get the theme settings
  $s = atom()->options();

  $css = array();

  if(atom()->OptionExists('layout')){
    $layout = atom()->getLayoutType();

    // column dimensions
    $w = $s['page_width'];
    $unit = ($w !== 'fluid') ? 'px' : '%';
    $gs = ($w !== 'fluid') ? 960: 100;

    if($layout !== 'c1'){
      $sb = explode(';', $s["dimensions_{$w}_{$layout}"]);
      switch($layout){
        case 'c2left':
          $css[] = '.'.$w.'.c2left #primary-content{width:'.($gs-$sb[0]).$unit.';left:'.$gs.$unit.'}';
          $css[] = '.'.$w.'.c2left #sidebar{width:'.$sb[0].$unit.';left:0}';
          $css[] = '.'.$w.'.c2left #mask-1{right:'.($gs-$sb[0]).$unit.'}';
          break;
        case 'c2right':
          $css[] = '.'.$w.'.c2right #primary-content{width:'.($gs-($gs-$sb[0])).$unit.';left:'.($gs-$sb[0]).$unit.'}';
          $css[] = '.'.$w.'.c2right #sidebar{width:'.($gs-$sb[0]).$unit.';left:'.($gs-$sb[0]).$unit.'}';
          $css[] = '.'.$w.'.c2right #mask-1{right:'.($gs-$sb[0]).$unit.'}';
          break;
        case 'c3':
          $css[] = '.'.$w.'.c3 #primary-content{width:'.($gs-$sb[0]-($gs-$sb[1])).$unit.';left:'.$gs.$unit.'}';
          $css[] = '.'.$w.'.c3 #sidebar{width:'.$sb[0].$unit.';left:'.($gs-$sb[1]).$unit.'}';
          $css[] = '.'.$w.'.c3 #sidebar2{width:'.($gs-$sb[1]).$unit.';left:'.($gs-$sb[0]).$unit.'}';
          $css[] = '.'.$w.'.c3 #mask-2{right:'.($gs-$sb[1]).$unit.'}';
          $css[] = '.'.$w.'.c3 #mask-1{right:'.($gs-$sb[0]-($gs-$sb[1])).$unit.'}';
          break;
        case 'c3left':
          $css[] = '.'.$w.'.c3left #primary-content{width:'.($gs-$sb[1]).$unit.';left:'.($gs+($sb[1]-$sb[0])).$unit.'}';
          $css[] = '.'.$w.'.c3left #sidebar{width:'.$sb[0].$unit.';left:'.($sb[1]-$sb[0]).$unit.'}';
          $css[] = '.'.$w.'.c3left #sidebar2{width:'.($sb[1]-$sb[0]).$unit.';left:'.($sb[1]-$sb[0]).$unit.'}';
          $css[] = '.'.$w.'.c3left #mask-2{right:'.($gs-$sb[1]).$unit.'}';
          $css[] = '.'.$w.'.c3left #mask-1{right:'.($sb[1]-$sb[0]).$unit.'}';
          break;
        case 'c3right':
          $css[] = '.'.$w.'.c3right #primary-content{width:'.$sb[0].$unit.';left:'.(($gs-$sb[0]-($gs-$sb[1]))+($gs-$sb[1])).$unit.'}';
          $css[] = '.'.$w.'.c3right #sidebar{width:'.($gs-$sb[0]-($gs-$sb[1])).$unit.';left:'.($gs-$sb[0]).$unit.'}';
          $css[] = '.'.$w.'.c3right #sidebar2{width:'.($gs-$sb[1]).$unit.';left:'.($gs-$sb[0]).$unit.'}';
          $css[] = '.'.$w.'.c3right #mask-2{right:'.($gs-$sb[1]).$unit.'}';
          $css[] = '.'.$w.'.c3right #mask-1{right:'.($sb[1]-$sb[0]).$unit.'}';
          break;
      }
    }

    // page width
    if(($w !== 'fixed'))
      $css[] = '.page-content{max-width:'.$s['page_width_max'].'px;}';

  }

  // background image
  if(atom()->OptionExists('background_image') && $s['background_image']){
    $selector = $s['background_image_selector'] === 'body' ? '' : $s['background_image_selector'];
    $css[] = 'body.cbgi '.$selector.'{background-image:url("'.$s['background_image'].'");}';
  }

  // background color
  if(atom()->OptionExists('background_color') && ($s['background_color'])){
    $selector = $s['background_color_selector'] === 'body' ? '' : $s['background_color_selector'];
    $css[] = 'body.cbgc '.$selector.'{background-color:#'.$s['background_color'].';}';
  }

  // extra css may be added by plugins etc.
  $css = apply_filters('atom_inline_css', implode("\n", $css));

  // user css (use template system)
  if($s['css']) $css .= atom()->getBlockTemplate($s['css'], array(
    'THEME_URL'       => atom()->get('theme_url'),
    'CHILD_THEME_URL' => atom()->get('child_theme_url'),
    'BLOG_URL'        => home_url(),
  ));

  // search for CSS custom field
  if(is_single() || is_page()){
    $meta_css = atom()->post->getMeta('css', true);
    if(!empty($meta_css))
      $css .= atom()->getBlockTemplate($s['css'], array(
        'THEME_URL'       => atom()->get('theme_url'),
        'CHILD_THEME_URL' => atom()->get('child_theme_url'),
        'BLOG_URL'        => home_url(),
      ));
  }

  $css = trim($css);

  if(!empty($css))
    echo "<style>\n{$css}\n</style>\n";
}



/*
 * Register JavaSscript and stylesheets used by ATOM in the front-end
 *
 * @since 2.0
 */
function atom_assets(){

  $theme = ATOM;
  $app = atom();

  $app->action('js');
  $app->action('css');

  if($app->options('jquery')){

    // load google's jquery, should be faster theoretically
    if(!ATOM_DEV_MODE && $app->options('optimize')){
      wp_deregister_script('jquery');
      wp_register_script('jquery', ('https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'));
    }

    // jquery
    wp_enqueue_script('jquery');

    // jquery plugins
    wp_enqueue_script($theme, $app->jsURL('jquery.atom'), array('jquery'), $app->getThemeVersion(), true);

    if($app->previewMode())
      wp_enqueue_script('theme-preview', $app->jsURL('theme-preview'), array('jquery', $theme));

    // send relevant option configuration to atom.js
    $js_options = array('effects', 'lightbox', 'generate_thumbs');

    foreach($js_options as $index => $option)
      if(!$app->options($option))
        unset($js_options[$index]);

    // get search query, if any
    // -- @todo, handle inline <script>'s and document.write conflicts...
    $referer = isset($_SERVER['HTTP_REFERER']) ? urldecode($_SERVER['HTTP_REFERER']) : '';
    $search_query = preg_match('@^http://(.*)?\.?(google|yahoo|lycos).*@i', $referer) ? esc_attr(preg_replace('/^.*(&q|query|p)=([^&]+)&?.*$/i','$2', $referer)) : get_search_query();

    // determine page context
    foreach(array('404', 'page', 'single', 'search', 'archive', 'home') as $context){
      $is_context = "is_{$context}";

      // $context will hold our context
      if($is_context()) break;
    }

    // wp_localize_script should escape any js characters
    $args = array(
      'id'           => ATOM,
      'blog_url'     => home_url('/'),
      'theme_url'    => $app->getThemeURL(),
      'context'      => $context,
      'preview_mode' => $app->previewMode(),
      'search_query' => $search_query,
      'options'      => implode('|', $js_options),
    );

    wp_localize_script($theme, 'atom_config', apply_filters('atom_config_js', $args));
  }

  // determine if we have a color scheme, and enqueue it
  if($app->optionExists('color_scheme')){

    // custom field -- highest priority
    if(is_singular())
      $style = $app->post->getMeta('style');

    // theme settings - lowest priority
    if(empty($style))
      $style = $app->options('color_scheme');

    if($style)
      wp_enqueue_style("{$theme}-style", $app->get('theme_url').'/css/style-'.$style.'.css', array("{$theme}-core"), $app->getThemeVersion());

  }

  // enqueue the core if we don't have a color scheme option, or if we have it, but it's set to blank (which suggests the user wants custom css)
  if(($app->optionExists('color_scheme') && !empty($style)) || !$app->optionExists('color_scheme'))
    wp_enqueue_style("{$theme}-core", $app->get('theme_url').'/css/core.css', array(), $app->getThemeVersion());

  // enqueue child theme css, if this is a child theme
  if(is_child_theme())
    wp_enqueue_style($theme, get_stylesheet_uri(), array(), $app->getThemeVersion());

}



/**
 * Set up post thumbnail sizes (must run before the ajax)
 *
 * @since 1.0
 */
function atom_set_thumb_sizes(){

  if(!atom()->options('post_thumbs')) return;
  $post_thumb = (atom()->options('post_thumb_size') === 'media') ? array(get_option('thumbnail_size_w'), get_option('thumbnail_size_h')) : explode('x', atom()->options('post_thumb_size'));

  list($w, $h) = $post_thumb;

  set_post_thumbnail_size($w, $h, true); // same as add_image_size('post-thumbnail' ...);

  //if(atom()->options('featured_post_thumb_size')){
  //  list($w, $h) = explode('x', atom()->options('featured_post_thumb_size'));
  //  add_image_size('featured-thumbnail', $w, $h, true);
  //}

  Atom::action('thumb_sizes');
}




/**
 * Removes expired transients from the database
 * http://wordpress.stackexchange.com/questions/6602/are-transients-garbage-collected
 * This should be integrated in the WP core...
 *
 * @since `1.3
 */
function atom_delete_expired_transients(){
  global $wpdb, $_wp_using_ext_object_cache;

  if($_wp_using_ext_object_cache) return;

  $time = isset($_SERVER['REQUEST_TIME']) ? (int)$_SERVER['REQUEST_TIME'] : time();
  $expired = $wpdb->get_col($wpdb->prepare("SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout%' AND option_value < %d", $time));

  foreach($expired as $transient)
    delete_transient(str_replace('_transient_timeout_', '', $transient));

  atom()->log('Deleted '.count($expired).' expired transients.'); // probably never shown because this runs in a separate request
}



/**
 * GET/POST requests etc.
 *
 * @since 1.0
 */
function atom_requests(){

  if(!defined('DOING_AJAX') && is_single())
    atom()->post->incrementViews();

  // retrieve avatar image when email address field changes (inside the comment form)
  if(atom()->request('get_avatar')){
    atom()->ajaxHeader();
    $email = isset($_GET['email']) ? sanitize_email($_GET['email']) : '';
    $size = empty($_GET['size']) ? 48 : (int)$_GET['size'];
    atom()->Avatar($email, $size, false);
    exit;
  }

  // update thumbnails (regenerateThumbnail will do the check if regeneration is needed)
  elseif(atom()->request('update_thumb')){
    atom()->ajaxHeader();

    $size = esc_attr(strip_tags((string)$_GET['attachment_size']));

    if(AtomObjectPost::regenerateThumbnail(abs((int)$_GET['thumb_id']), $size)){
      atom()->post = abs((int)$_GET['post_id']);
      atom()->post->Thumbnail($size);
    }
    exit;
  }

  // echo full post content on 'read more' click -- @todo more testing
  elseif(atom()->request('read_more')){
    atom()->ajaxHeader();
    $post_id = isset($_GET['post_id']) ? abs((int)$_GET['post_id']) : false;
    if($post_id){
      $query = new WP_Query(array('p' => $post_id));
      if($query->have_posts()){
        $query->the_post();
        atom()->post->Content(atom()->options('post_content_mode'), array('limit' => 0));
      }
    }
    exit;
  }

  // echo full post content on 'read more' click -- @todo more testing
  elseif(atom()->request('more_related_posts')){
    atom()->ajaxHeader();
    $post_id = isset($_GET['post_id']) ? abs((int)$_GET['post_id']) : false;
    $offset = isset($_GET['offset']) ? abs((int)$_GET['offset']) : false;
    if($post_id && $offset){

      atom()->setCurrentPost($post_id);
      atom()->post->setRelatedPostsQuery(array(
        'offset'          => $offset,
        'posts_per_page'  => 10000,  // all remaining posts from $offset -
      ));

      atom()->template('related-posts');
    }
    exit;
  }

  // comment karma rating
  elseif(atom()->request('comment_karma') && is_user_logged_in() && atom()->options('comment_karma')){
    atom()->ajaxHeader();
    global $user_ID;

    list($rate, $comment_id) = explode('/', $_GET['karma']);

    $comment_id = abs((int)$comment_id);
    $comment = get_comment($comment_id);
    $karma = $comment->comment_karma;
    if($comment->user_id == $user_ID) // the user shouldn't see these messages unless they hack the html or something
      die(atom()->t("You can't vote your own comment :("));

    $ratings = get_user_meta($user_ID, 'comment_ratings', true);
    $ratings = empty($ratings) ? array() : explode(',', $ratings);
    if(in_array($comment_id, $ratings)) die(atom()->t('You can only vote once :)'));

    $unit = 1; // + $unit
    if($rate !== '+' && $rate !== '-')
      atom()->te('Invalid vote');

    $karma = ($rate !== '+') ? ($karma - $unit) : ($karma + $unit);

    //$query = $wpdb->query("UPDATE {$wpdb->comments} SET comment_karma = comment_karma {$rate} {$unit} WHERE comment_ID = {$comment_id} LIMIT 1");
    //$wpdb->update($wpdb->comments, array('comment_karma' => $karma), array('comment_ID' => $comment_id));
    $comment->comment_karma = $karma;
    if(!wp_update_comment((array)$comment)) die(atom()->t('Failed.'));

    $ratings[] = $comment_id;
    $ratings = implode(',', $ratings); // we could also send it as a array, which will automatically be serialized... maybe explode() is faster?
    update_user_meta($user_ID, 'comment_ratings', $ratings);

    if($comment->user_id){
      $user_karma = (int)get_user_meta($comment->user_id, 'karma', true);
      $new_karma = ($rate == '+') ? ($user_karma + $unit) : ($user_karma - $unit);
      if($new_karma < 0) $new_karma = 0; // no negative karma
      update_user_meta($comment->user_id, 'karma', $new_karma);
    }
    die(($karma != 0) ? str_replace('-', '&#8722;', $karma) : '');
  }

  // get a single comment
  elseif(atom()->request('get_comment')){
    atom()->ajaxHeader();
    define('RETRIEVING_BURIED_COMMENT', true);
    $comment_id = abs((int)$_GET['comment_id']);
    $comment = get_comment($comment_id);
    atom()->setCurrentComment($comment);
    atom()->template('comment');
    die();
  }

  // debug info -- @todo
  elseif(atom()->request('debug') && atom()->options('debug')){

    require_once ABSPATH.'wp-admin/includes/file.php';
    require_once ABSPATH.'wp-admin/includes/admin.php';

    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
    <head>
    	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    	<title><?php echo atom()->get('theme_name'); ?> debug info</title>
    	<?php wp_admin_css('install', true); ?>
    </head>
    <body>
    <h1 id="logo"><img alt="WordPress" src="<?php echo admin_url(); ?>/images/wordpress-logo.png" /></h1>
    <h1><?php atom()->te('%s debug info', atom()->get('theme_name')); ?></h1>
    <p>(Red text indicates possible problems)</p>
    <h3>Environment</h3>
    <?php
      $verify_ext = array('mbstring', 'simplexml', 'gd', 'curl');
      foreach(get_loaded_extensions() as $ext)
        if(in_array(strtolower($ext), $verify_ext))
          unset($verify_ext[array_search(strtolower($ext), $verify_ext)]);

      $missing_ext = '';
      if(!empty($verify_ext))
         $missing_ext = '(missing <span style="color: #FF0000">'.implode(', ', $verify_ext).'</span>)';

      printf('PHP %1$s %2$s / WordPress %3$s / %4$s (%5$s %6$s)',
         PHP_VERSION,
         $missing_ext,
         $GLOBALS['wp_version'],
         ATOM,
         atom()->get('theme_name'),
         atom()->get('theme_version')
      );
    ?>

    <h3>Preferred transports</h3>
    <?php
      $temp_file = wp_tempnam();
      $fs_transport_status = (getmyuid() === fileowner($temp_file));
      unlink($temp_file);
      $fs_transport_status = $fs_transport_status ? ' <span style="color: #00CC00;">OK</span>' : ' <span style="color: #FF0000">FAILED</span>';
      printf('FS: %1$s %2$s', get_filesystem_method(get_option('ftp_credentials')), $fs_transport_status);
    ?>

    <h3>Active plugins</h3>
    <ul>
    <?php
      foreach(get_plugins() as $path => $plugin)
        if(is_plugin_active($path))
          echo '<li><a href="'.$plugin['PluginURI'].'" target="_blank">'.$plugin['Name'].'</a> '.$plugin['Version'].'</li>';
    ?>
    </ul>
    <?php

    exit;
  }

  atom()->action('requests');
}



/*
 * Displays an error message telling the user that he needs to upgrade his WordPress installation
 *
 * @since   1.0
 * @global  $wp_version   WordPress version
 */
function atom_unsupported_wp_version(){
  global $wp_version;

  // message for all users when inside dashboard, and only for blog admin if outside
  if(current_user_can('edit_theme_options') || is_admin()){
    $message = atom()->t('Your site is running on %1$s. %2$s requires at least %3$s. Please update your site even if you\'re not going to use this theme, because outdated applications expose you to security risks',
       'WordPress '.$wp_version,
       '<strong>'.atom()->get('theme_name').'</strong>',
       '<a href="http://codex.wordpress.org/Upgrading_WordPress/">WordPress '.Atom::REQ_WP_VERSION.'</a>'
    );

  // message for normal visitors
  }else{
    $message = atom()->t('The site is temporarily down for maintainance. Come back in a few minutes, or login in <a%s>here</a> if you\'re the site admin', ' href="'.get_admin_url().'"');

  }

  // extra message for logged-in blog admins, if outside the dashboard
  if(current_user_can('edit_theme_options') && !is_admin())
    $message .= ' '.atom()->t('Go to your %s', '<a href="'.get_admin_url().'">'.atom()->t('Dashboard').'</a>');

  // not in the admin area, show message and stop script
  if(!is_admin())
    wp_die($message);

  // in the admin area, display the message as a notice
  echo '<div class="error"><p>'.$message.'</p></div>';
}



/*
 * Displays a warning message telling the user he shouldn't edit the theme files :)
 *
 * @since 1.0
 */
function atom_editor_warning(){
  global $current_screen;
  if($current_screen->id !== 'theme-editor' || is_child_theme()) return; // only on editor pages, if parent theme is active

  $message = atom()->t('To make small CSS customizations you can use the %1$s option from the theme settings. For extensive customizations activate and edit the %2$s theme if available, or create your own child theme. Read the %3$s for more information.',
    '<a href="'.admin_url('themes.php?page='.ATOM.'&section=css').'">'.atom()->t('User CSS').'</a>',
    '"'.atom()->get('theme_name').' - Extend"',
    '<a href="'.Atom::THEME_DOC_URI.'">'.atom()->t('documentation').'</a>');
  ?>
  <div class="error fade">
    <p><strong><?php atom()->te('Do not edit core theme files because you will loose any changes you make here when you update!'); ?></strong></p>
    <p><em><?php echo $message; ?></em></p>
  </div>
  <?php
}






function atom_trim_excerpt($text){
  if(empty($text)){
    $text = get_the_content();
    $text = strip_shortcodes($text);

    if(in_the_loop())
      $text = apply_filters('the_content', $text);

    $text = str_replace(']]>', ']]&gt;', $text);
    $text = atom()->generateExcerpt($text, atom()->getContextArgs('auto_excerpt', array(
       'limit'         => 300,      // apply_filters('excerpt_length', 55)
       'cutoff'        => 'word',
       'allowed_tags'  => array(),
       'shortcodes'    => true,     // we remove them above
       'more'          => apply_filters('excerpt_more', '[...]'),
    )));
  }
  return $text;
}






//add_filter('comment_id_fields', 'atom_antispam_honeypots');

function atom_antispam_honeypots($fields){
  $fields .= '<input type="hidden" name="_ltime" value="'.current_time('timestamp').'" />';
  $fields .= '<input type="text" name="_hpot" style="display:none;" value="" />';

  return $fields;
}


//add_action('pre_comment_on_post',      'atom_check_for_automated_spam');
//add_action('bbp_new_topic_pre_extras', 'atom_check_for_automated_spam');
//add_action('bbp_new_reply_pre_extras', 'atom_check_for_automated_spam');


/**
 * Simple anti-spam routine.
 * Checks if the page load time minus the submit time is lower than a certain value
 *
 * @since 1.3
 */
function atom_check_for_automated_spam($object_id){

  // only do this for non-logged in users
  if(is_user_logged_in()){
    $page_load_time = isset($_POST['_ltime']) ? (int)$_POST['_ltime'] : current_time('timestamp');
    $diff = current_time('timestamp') - $page_load_time;

    if(!empty($_POST['_hpot']) || ($diff < 15))
      wp_die(atom()->t('Stop spamming dude!'));
  }
}



/**
 * Makes our template loader compatible with get_search_form.
 *
 * @since 2.0
 */
function atom_get_search_form($form){

  $located = atom()->findTemplate('searchform');

  if($located){
    ob_start();
    atom()->load($located);
    $form = ob_get_clean();
    return $form;
  }

  return $form;
}



/**
 * Overrides for WP's template system.
 * We're changing the default location of the templates and we're adding a few extra templates
 *
 * @since 1.2
 */
function atom_wp_templates(){

  $templates = array();

  // build a list of possible templates for the current context
  // the first one that is found is loaded
  if(is_404()){
    $templates[] = '404';
  }

  if(is_search()){
    $post_type = get_query_var('post_type');

    if($post_type)
      $templates[] = "search-{$post_type}";

    $templates[] = 'search';
  }

  if(is_tax()){
    $term = get_queried_object();

    $templates[] = "taxonomy-{$term->taxonomy}-{$term->slug}";
    $templates[] = "taxonomy-{$term->taxonomy}";
    $templates[] = 'taxonomy';
  }

  if(is_front_page()){
    $templates[] = 'front-page';
  }

  if(is_home()){
    $templates[] = 'home';
    $templates[] = 'index';
  }

  if(is_attachment()){
    list($type, $sub_type) = explode('/', get_post_mime_type());
    $templates[] = $type;
    $templates[] = $sub_type;
    $templates[] = "{$type}-{$sub_type}"; // maybe this should be first?
    $templates[] = 'attachment';
    remove_filter('the_content', 'prepend_attachment');
  }

  if(is_single()){
    $post = get_queried_object();
    //$templates[] = "single-{$post->ID}";
    $templates[] = "single-{$post->post_type}";
    $templates[] = 'single';
  }

  if(is_page()){
    $page_id = get_queried_object_id();
    $page_template = get_post_meta($page_id, '_wp_page_template', true); // template attribute for the "page" post type
    $page_name = get_query_var('pagename');

    if(!$page_name && $page_id > 0){
      // If a static page is set as the front page, $page_name will not be set. Retrieve it from the queried object
      $post = get_queried_object();
      $page_name = $post->post_name;
    }

    if(!empty($page_template) && ($page_template != 'default') && !validate_file($page_template))
      $templates[] = basename($page_template, '.php');

    if($page_name)
      $templates[] = "page-{$page_name}";

    if($page_id)
      $templates[] = "page-{$page_id}";

    $templates[] = 'page';
  }

  if(is_category()){
    $category = get_queried_object();
    $templates[] = "category-{$category->slug}";
    $templates[] = "category-{$category->term_id}";
    $templates[] = 'category';
  }

  if(is_tag()){
    $tag = get_queried_object();
    $templates[] = "tag-{$tag->slug}";
    $templates[] = "tag-{$tag->term_id}";
    $templates[] = 'tag';
  }

  if(is_author()){
    $author = atom()->user(get_queried_object());
    $templates[] = 'author-'.$author->getSlug();
    $templates[] = 'author-'.$author->getID();
    $templates[] = 'author-'.$author->getRole('slug');
    $templates[] = 'author';
  }

  if(is_date()){
    $templates[] = 'date';
  }

  if(is_archive()){
    $post_type = get_query_var('post_type');

    if($post_type)
      $templates[] = "archive-{$post_type}";

    $templates[] = 'archive';
  }

  // @todo, maybe - add support for is_comments_popup?

  if(is_paged()){
    $templates[] = 'paged';  // <-- never reaches this point?
  }

  if(!empty($templates))
    atom()->log('Possible page templates to search (in this order): '.implode(', ', $templates).'.');

  $located = atom()->findTemplate($templates);

  if($located)
    atom()->log('Using the '.basename($located).' template.');

  return $located;
}



/**
 * Makes our template loader compatible with bbpress.
 *
 * @since 2.0
 */
function atom_bbp_templates($templates){

  $atom_templates = array();

  foreach($templates as $template)
    $atom_templates[] = "templates/{$template}";

  $templates = array_merge($atom_templates, $templates);

  if(atom()->options('debug')){
    $template_info = array();
    foreach($templates as $template)
      $template_info[] = basename($template, '.php');

    atom()->log('Possible page templates to search (in this order): '.implode(', ', array_unique($template_info)).'.');
  }

  return $templates;
}



/**
 * Meta boxes to register.
 *
 * @since 2.0
 */
function atom_meta_boxes($post_type, $post){

  // unregister the default page attributes meta box and register our copy of it
  // unfortunately we need to do this because page template listings are not hookable in 3.2...
  if(post_type_supports($post_type, 'page-attributes')){
    remove_meta_box('pageparentdiv', $post_type, 'side');
    add_meta_box('pageparentdiv', ($post_type === 'page') ? __('Page Attributes') : __('Attributes'), 'atom_page_attributes_meta_box', $post_type, 'side');
  }

}




/**
 * Our page attributes meta box, almost the same as the default one.
 * The only difference is that we're listing page templates from the 'templates' directory too
 *
 * @since 2.0
 */
function atom_page_attributes_meta_box($post) {

  $post_type_object = get_post_type_object($post->post_type);

  // parent
  if($post_type_object->hierarchical){
    $pages = wp_dropdown_pages(array(
       'post_type'         => $post->post_type,
       'exclude_tree'      => $post->ID,
       'selected'          => $post->post_parent,
       'name'              => 'parent_id',
       'show_option_none'  => __('(no parent)'),
       'sort_column'       => 'menu_order, post_title',
       'echo'              => 0,
    ));

    if(!empty($pages)):  ?>
      <p><strong><?php _e('Parent'); ?></strong></p>
      <label class="screen-reader-text" for="parent_id"><?php _e('Parent') ?></label>
      <?php echo $pages; ?>
      <?php
    endif;

  }

  // template
  if($post->post_type === 'page'){

    // get page templates
    $themes = get_themes();
    $theme = get_current_theme();
    $templates = $themes[$theme]['Template Files'];
    $page_templates = array();

    if(is_array($templates)){

      $base = array(TEMPLATEPATH.'/templates/');

      if(is_child_theme())
        $base[] = STYLESHEETPATH.'/';

      foreach($templates as $template){
        $basename = str_replace($base, '', $template);

        // ingnore
        if($basename === 'functions.php') continue;

        // don't allow template files in other than root and 'templates' sub-directory
        if((strpos($basename, 'templates/') !== false) || (strpos($basename, '/') === false)){

          $template_data = implode('', file($template));
          $name = '';
          $prefix_tag = 'Template Name';

          if(preg_match('|'.$prefix_tag.':(.*)$|mi', $template_data, $name))
            $name = _cleanup_header_comment($name[1]);

          if(!empty($name))
            $page_templates[trim($name)] = basename($basename); // cut off sub-dir

        }
      }
    }

    // we're using atom_page_template instead of page_template, so the default template check doesn't fire.
    if(count($page_templates) > 0):
      ksort($page_templates);
      $current_template = !empty($post->page_template) ? $post->page_template : '';  ?>
      <p><strong><?php _e('Template'); ?></strong></p>
      <label class="screen-reader-text" for="atom_page_template"><?php _e('Page Template') ?></label>
      <select name="atom_page_template" id="atom_page_template">
        <option value="default"><?php _e('Default Template'); ?></option>
        <?php foreach(array_keys($page_templates) as $template): ?>
        <option value="<?php echo $page_templates[$template]; ?>" <?php selected($current_template, $page_templates[$template]); ?>> <?php echo translate($template, ATOM); ?></option>
        <?php endforeach; ?>
      </select> <?php
    endif;
  }
  ?>

  <p><strong><?php _e('Order'); ?></strong></p>
  <p>
    <label class="screen-reader-text" for="menu_order"><?php _e('Order'); ?></label>
    <input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo esc_attr($post->menu_order); ?>" />
  </p>
  <?php if($post->post_type == 'page'): ?><p> <?php _e('Need help? Use the Help tab in the upper right of your screen.'); ?></p><?php endif; ?>
  <?php
}



/**
 * Post save handler.
 *
 * @since 2.0
 */
function atom_save_post($post_id, $post){

  // update page template
  $page_template = isset($_POST['atom_page_template']) ? $_POST['atom_page_template'] : '';
  if(!empty($page_template) && ($post->post_type === 'page'))
    update_post_meta($post_id, '_wp_page_template', $page_template);
}


function atom_wp_signup_location($url){

  if($page_template = atom()->getPageByTemplate('sign-up')){
    $page_template = atom()->getPost($page_template);
    atom()->resetCurrentPost();
    $url = $page_template->getURL();
  }

  return $url;
}


function atom_signup_redirect($url){
  global $pagenow;

  if(($pagenow === 'wp-signup.php') && $page_template = atom()->getPageByTemplate('sign-up')){
    $page_template = atom()->getPost($page_template);
    atom()->resetCurrentPost();
    wp_redirect($page_template->getURL(), 301);
    exit;

  }
}


function atom_bbp_template_notices(){
  global $bbp;

  // Bail if no notices or errors
  if(bbp_has_errors())
    foreach($bbp->errors->get_error_messages('generic') as $generic_error)
      printf('<div class="message error">%s</div>', $generic_error);

}






/*
 * Update widget visibility options.
 *
 * @since    1.0
 * @param    array $instance        Instance options
 * @param    array $new_instance    New instance
 * @param    array $old_instance    Old instance
 * @return   array                  Final options
 */
function atom_widget_visibility_update($instance, $new_instance, $old_instance){

  // page visibility box status (1/0)
  $instance['visibility'] = (int)$new_instance['visibility'];

  // we're checking if the 'user_changed_vis' field is present
  // because we don't want to change any of the below widgets options without user intervention...
  if(isset($new_instance['user_changed_vis'])){

    // generic pages
    foreach(array('home', 'single', 'category', 'tag', 'author', 'date', 'search') as $page)
      $instance["page-{$page}"] = !isset($new_instance["page-{$page}"]) ? 1 : (int)$new_instance["page-{$page}"];

    // pages as in post type
    $pages = get_pages();
    foreach($pages as $page)
      $instance["page-{$page->ID}"] = !isset($new_instance["page-{$page->ID}"]) ? 1 : (int)$new_instance["page-{$page->ID}"];

    // singural-type pages
    foreach(get_post_types(array('public' => true)) as $post_type)
      $instance["page-singular-{$post_type}"] = !isset($new_instance["page-singular-{$post_type}"]) ? 1 : (int)$new_instance["page-singular-{$post_type}"];

    // tax / archives
    foreach(get_taxonomies(array('public' => true)) as $taxonomy)
      $instance["page-tax-{$taxonomy}"] = !isset($new_instance["page-tax-{$taxonomy}"]) ? 1 : (int)$new_instance["page-tax-{$taxonomy}"];

    // user access
    $wp_roles = new WP_Roles();
    foreach($wp_roles->role_names as $role => $label)
      $instance["role-{$role}"] = !isset($new_instance["role-{$role}"]) ? 1 : (int)$new_instance["role-{$role}"];

    $instance['user-visitor'] = !isset($new_instance['user-visitor']) ? 1 : (int)$new_instance['user-visitor'];
  }

  // we don't want this set, this is not a real option
  if(isset($instance['user_changed_vis']))
    unset($instance['user_changed_vis']);

  return $instance;
}



/*
 * Add widget visibility option fields in the widget settings form.
 * Inspired by "Display Widgets" plugin by Stephanie Wells - http://blog.strategy11.com/display-widgets
 *
 * @since   1.0
 * @param   object $widget    Widget object
 * @param   string $return    widget form (HTML output)
 * @param   array $instance   Instance options
 */
function atom_widget_visibility_options($widget, $return, $instance){

  // we don't want vis. options on splitters (or do we?)
  if($widget instanceof AtomWidgetSplitter) return;

  $show_options = isset($instance['visibility']) ? (int)$instance['visibility'] : 0;
  $nonce = md5($widget->id);
  $nonce = wp_create_nonce("vis_{$nonce}");
  ?>
  <div class="atom-block <?php if(!is_numeric($widget->number) && ($widget instanceof AtomWidget)) echo 'hidden'; ?>">

    <input <?php disabled(!is_numeric($widget->number)); ?> type="button" class="button-visibility" value="<?php $show_options ? atom()->te('Hide Page Visibility Options') : atom()->te('Show Page Visibility Options'); ?>" />

    <input class="visibility" type="hidden" name="<?php echo $widget->get_field_name('visibility'); ?>" value="<?php echo $show_options; ?>" data-widget="<?php echo $widget->id; ?>" data-nonce="<?php echo $nonce; ?>" />

    <div class="visibility-options">
      <?php if($show_options) atom_widget_visibility_options_fields($widget->id, $instance); ?>
    </div>
  </div>
  <?php
}



/*
 * The widget visibility fields added in the widget options.
 * By default, this function is fired trough ajax to save bandwidth; It will be called normally within the function above only when the user manually unhides these options
 *
 * @since    1.0
 * @global   $wp_registered_widgets    Stored registered widgets.
 * @param    string $widget_id         Widget ID
 * @param    array $instance           Widget Instance
 */
function atom_widget_visibility_options_fields($widget_id = false, $instance = false){
  global $wp_registered_widgets;

  if(!$widget_id){
    $ajax = true;
    $widget_id = esc_attr($_POST['widget_id']);
    $nonce_part = md5($widget_id);
    check_ajax_referer("vis_{$nonce_part}");

  }else{
    $ajax = false;

  }

  $wp_page_types = array(
    'home'     => atom()->t('Blog Homepage'),
    'search'   => atom()->t('Search Results'),
    'author'   => atom()->t('Author Archives'),
    'date'     => atom()->t('Date-based Archives'),
    'tag'      => atom()->t('Tag Archives'),
    'category' => atom()->t('Category Archives'),
  );

  $widget = AtomWidget::getObject($widget_id);

  if(!($widget instanceof WP_Widget))
    return -1;

  $widget_settings  = get_option($widget->option_name);
  $instance         = $instance ? $instance : $widget_settings[$widget->number];
  $widget_width     = (isset($widget->control_options['width'])) ? $widget->control_options['width'] : 0;

  // get the active widgets from all sidebars
  $sidebars_widgets = wp_get_sidebars_widgets();

  // prepare matches
  $matches = array();
  foreach($wp_registered_widgets as $i => $w)
    if($w['name'] === $widget->name)
      $matches[] = $w['id'];

  /*/ exclude widgets from the "inactive widgets" area? -- @todo, maybe
  $is_inactive = false;
  if(!empty($sidebars_widgets['wp_inactive_widgets']))
    foreach($sidebars_widgets['wp_inactive_widgets'] as $i => $value)
      if($value == $widget->id) $is_inactive = true;

  // stop, if it is (we don't add these options in inactive widgets to save bandwidth)
  if($is_inactive) return;
  //*/

  // find out if the widget is in the arbitrary area, and it's position (number)
  $number = 0;
  $is_arbitrary = false;
  if(!empty($sidebars_widgets['arbitrary']))
    foreach($sidebars_widgets['arbitrary'] as $i => $value):
      if(in_array($value, $matches) && !$is_arbitrary) $number++;
      if($value === $widget->id) $is_arbitrary = true;
    endforeach;
  ?>

  <?php if(!$is_arbitrary): ?>
  <p><strong><em><?php atom()->te('Show this widget on:'); ?></em></strong></p>
  <p>
    <?php foreach($wp_page_types as $key => $label): ?>

    <?php if(!isset($instance["page-{$key}"])) $instance["page-{$key}"] = 1; ?>
    <input type="hidden" name="<?php echo $widget->get_field_name("page-{$key}"); ?>" value="0" />
    <input type="checkbox" value="1" <?php checked($instance["page-{$key}"]); ?> id="<?php echo $widget->get_field_id("page-{$key}"); ?>" name="<?php echo $widget->get_field_name("page-{$key}"); ?>" />
    <label for="<?php echo $widget->get_field_id("page-{$key}"); ?>"><?php echo $label; ?></label>
    <br />
    <?php endforeach; ?>

    <?php foreach(get_post_types(array('public' => true)) as $post_type):
      $object = get_post_type_object($post_type);
      if(empty($object->labels->name) || $post_type == 'page') continue; // we handle pages separately
      if(!isset($instance["page-singular-{$post_type}"])) $instance["page-singular-{$post_type}"] = 1; ?>
      <input type="hidden" name="<?php echo $widget->get_field_name("page-singular-{$post_type}"); ?>" value="0" />
      <input type="checkbox" value="1" <?php checked($instance["page-singular-{$post_type}"]); ?> id="<?php echo $widget->get_field_id("page-singular-{$post_type}"); ?>" name="<?php echo $widget->get_field_name("page-singular-{$post_type}"); ?>" />
      <label for="<?php echo $widget->get_field_id("page-singular-{$post_type}"); ?>"><?php atom()->te('Single: %s', $object->labels->name); ?></label>
      <br />
    <?php endforeach; ?>

    <?php foreach(get_taxonomies(array('public' => true, '_builtin' => false)) as $taxonomy):
      $object = get_taxonomy($taxonomy);
      if(empty($object->labels->name)) continue;
      if(!isset($instance["page-tax-{$taxonomy}"])) $instance["page-tax-{$taxonomy}"] = 1; ?>
      <input type="hidden" name="<?php echo $widget->get_field_name("page-tax-{$taxonomy}"); ?>" value="0" />
      <input type="checkbox" value="1" <?php checked($instance["page-tax-{$taxonomy}"]); ?> id="<?php echo $widget->get_field_id("page-tax-{$taxonomy}"); ?>" name="<?php echo $widget->get_field_name("page-tax-{$taxonomy}"); ?>" />
      <label for="<?php echo $widget->get_field_id("page-tax-{$taxonomy}"); ?>"><?php atom()->te('Tax Archive: %s', $object->labels->name); ?></label>
      <br />
    <?php endforeach; ?>

  </p>
  <?php
    $pages = get_pages();
    if($pages):
      echo '<p>';
      foreach($pages as $page):
        // determine if widget is visible on selected page; we consider it visible if the visibility option is not set
        $instance["page-{$page->ID}"] = isset($instance["page-{$page->ID}"]) ? ($instance["page-{$page->ID}"] ? true : false) : true;
        if(!isset($instance["page-{$page->ID}"])) $instance["page-{$page->ID}"] = 1; ?>
        <input type="hidden" name="<?php echo $widget->get_field_name("page-{$page->ID}"); ?>" value="0">
        <input type="checkbox" <?php checked($instance["page-{$page->ID}"]) ?> id="<?php echo $widget->get_field_id("page-{$page->ID}"); ?>" name="<?php echo $widget->get_field_name("page-{$page->ID}"); ?>" value="1" />
        <label for="<?php echo $widget->get_field_id("page-{$page->ID}"); ?>">
        <?php atom()->te('Page: %s', '<a href="'.get_permalink($page).'" target="_blank"><strong title="'.$page->post_title.'">'.((strlen($page->post_title) > 16 && $widget_width < 500) ? substr($page->post_title, 0, 16)."..." : $page->post_title).'</strong></a>'); ?>
        </label>
        <br />
      <?php endforeach;
      echo '</p>';
    endif;
  ?>

  <?php endif; ?>
  <p><strong><em><?php atom()->te('To:'); ?></em></strong></p>
  <p>
    <?php if(!isset($instance["user-visitor"])) $instance["user-visitor"] = 1; ?>
    <input type="hidden" name="<?php echo $widget->get_field_name("user-visitor"); ?>" value="0" />
    <input type="checkbox" value="1" <?php checked('1', $instance['user-visitor']); ?> id="<?php echo $widget->get_field_id('user-visitor'); ?>" name="<?php echo $widget->get_field_name('user-visitor'); ?>" />
    <label for="<?php echo $widget->get_field_id('user-visitor'); ?>"><?php atom()->te('Unregistered user (Visitor)'); ?></label>

    <?php
     $wp_roles = new WP_Roles();
     $names = $wp_roles->get_names();
     foreach($names as $role => $label):
       if(!isset($instance["role-{$role}"])) $instance["role-{$role}"] = 1;
       ?>
       <br />
       <input type="hidden" name="<?php echo $widget->get_field_name("role-{$role}"); ?>" value="0" />
       <input type="checkbox" value="1" <?php checked($instance["role-{$role}"]); ?> id="<?php echo $widget->get_field_id("role-{$role}"); ?>" name="<?php echo $widget->get_field_name("role-{$role}"); ?>" />
       <label for="<?php echo $widget->get_field_id("role-{$role}"); ?>"><?php echo translate_user_role($label); ?></label>
     <?php endforeach; ?>
  </p>

  <?php if($is_arbitrary):
   // output the [widget] shortcode info if we're on the arbitrary widget area
   // ID = generated by hashing the instance ID (we shorten it to 8 chars)
   // Name = the widget name
   // Number = the widget position in the sidebar (counting widgets from the same class)
  ?>
  <div class="info-block">
    <?php
      atom()->te('To include this widget into your posts or pages use one of the following shortcodes: %s',
                '<span><code>[widget '.substr(md5($widget->id), 0, 8).']</code></span><span><code>[widget "'.$widget->name.'"'.(($number > 1) ? ' number='.$number : '').']</code></span>');
    ?>
  </div>
  <?php endif; ?>

  <input type="hidden" name="<?php echo $widget->get_field_name('user_changed_vis'); ?>" value="1" />

  <?php
  if($ajax) exit;
}



/*
 * Check for a new theme versions
 *
 * @since 1.2
 */
function atom_transient_update_themes($checked_data) {
  global $wp_version;

  // make sure we get the parent theme name
  $theme = get_theme_data(TEMPLATEPATH.'/style.css');

  if(empty($checked_data->checked))
    return $checked_data;

  //$theme_base = basename(dirname(dirname(__FILE__)));
  $theme_base = get_template();

  // Start checking for an update
  $send_for_check = array(
    'body' => array(
      'action'  => 'theme_update',
      'request'  => serialize(array(
        'slug' => $theme_base,
        'version' => $checked_data->checked[$theme_base],
      )),

      // @todo: add username/password check for commercial themes
    ),
    'user-agent' => 'WordPress '.$wp_version.' / PHP '.PHP_VERSION.' / '.$theme['Name'].' '.$theme['Version'].'; '.get_bloginfo('name')
  );

  /* @todo: maybe...
  if(atom()->options('anon_stats')){

    $active_widgets = array();
    foreach(wp_get_sidebars_widgets() as $area)
      if($area !== 'wp_inactive_widgets')
        foreach($area as $widget) $active_widgets[] = $widget;

    $send_for_check['body']['stats'] = serialize(array(
      'php_ver'   => PHP_VERSION,
      'wp_ver'    => $wp_version,
      'widgets'   => $active_widgets,

      'options'   => array(
         'optimize'         => (int)atom()->options('optimize'),
         'jquery'           => (int)atom()->options('jquery'),
         'effects'          => (int)atom()->options('effects'),
         'lightbox'         => (int)atom()->options('lightbox'),
         'comment_karma'    => (int)atom()->options('comment_karma'),
         'post_navi'        => atom()->options('post_navi'),
         'color_scheme'     => atom()->options('color_scheme'),
         'layout'           => atom()->options('layout'),
      ),

      'actions' => array(
         'css_cache'        => (get_transient('atom_css_cache') != false),
         'child_theme'      => is_child_theme(),
      ),

    ));
  }
  */


  $raw_response = wp_remote_post(Atom::THEME_UPDATE_URI, $send_for_check);

  if(!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
    $response = unserialize($raw_response['body']);

  // feed the update data into WP updater
  if(!empty($response))
    $checked_data->response[$theme_base] = $response;

  return $checked_data;
}








function atom_update_online_users_status(){

  if(is_user_logged_in()){

    // get the online users list
    if(($logged_in_users = get_transient('users_online')) === false) $logged_in_users = array();

    $current_user = atom()->user->getID();
    $current_time = current_time('timestamp');

    if(!isset($logged_in_users[$current_user]) || ($logged_in_users[$current_user] < ($current_time - (15 * 60)))){
      $logged_in_users[$current_user] = $current_time;
      set_transient('users_online', $logged_in_users, 30 * 60);
    }

  }
}



// @todo: make proper versions of these missing functions
if(!defined('MB_OVERLOAD_STRING')){

  if(!function_exists('mb_substr')){
    function mb_substr($string, $start, $length){
      return substr($string, $start, $length);
    }
  }

  if(!function_exists('mb_strlen')){
    function mb_strlen($string){
      return strlen($string);
    }
  }


}









/**
 * Process the featured content actions (AJAX)
 *
 * @since 1.0
 * @todo Add User Role for managing Featured Posts
 */
function atom_process_featured(){

  // @todo: add nonce check?

  // proceed if the user has post editing rights
  if(current_user_can('edit_posts')){

    // read submitted info
    $id       = (int)$_GET['id'];
    $is_on    = (bool)$_GET['isOn'];
    $what     = ($_GET['what'] !== 'post') ? 'featured_media' : 'featured_posts';

    // get current featured array
    $featured = get_option($what) ? wp_parse_id_list(get_option($what)) : array();

    // add / or remove requested item from array, depending on current selection
    // "is_on" means that the item was selected and is now being unchecked, so we're removing it from the list...
    if(!$is_on && !in_array($id, $featured)) array_push($featured, $id); elseif($is_on && in_array($id, $featured)) unset($featured[array_search($id, $featured)]);

    // newer items (higher IDs) first
    rsort($featured);

    update_option($what, $featured);

    atom()->action($what !== 'post' ? 'feature_galleries' : 'feature_posts');

    // reverse classes
    echo $is_on ? 'off' : 'on';
    exit;
  }
}



/**
 * Add "Featured" column title to the appropriate page (this function is hooked to posts/media column title)
 *
 * @since 1.3
 *
 * @param array $defaults default columns
 */
function atom_featured_column_title($defaults){
  $defaults['featured'] = atom()->t('Featured'); // featured posts
  return $defaults;
}



/**
 * Setup featured column content for Posts (edit.php) page
 *
 * @since 1.0
 *
 * @param string $column_name Current Column
 * @param string $id Post ID
 */
function atom_posts_column_content($column_name, $id){
  // not our column
  if($column_name !== 'featured') return;

  $posts = wp_parse_id_list(get_option('featured_posts'));
  ?>
  <a href="#" data-type="post" data-post="<?php echo $id; ?>" class="feature <?php echo in_array($id, $posts) ? 'on' : 'off'; ?>"></a>
  <?php
}



/**
 * Setup featured column content for Media Library (upload.php) page
 *
 * @since 1.3
 *
 * @param string $column_name Current Column
 * @param string $id Post ID
 */
function atom_media_column_content($column_name, $id){
  // not our column / current item not an image
  if($column_name !== 'featured' || strpos(get_post_mime_type($id), 'image/') === false) return;
  $posts = wp_parse_id_list(get_option('featured_media'));
  ?>
  <a href="#" data-type="attachment" data-post="<?php echo $id; ?>" class="feature <?php echo in_array($id, $posts) ? 'on' : 'off'; ?>"></a>
  <?php
}



/**
 * Featured post status / publish action
 *
 * @since 2.0
 */
function atom_publish_action_featured(){

  if(get_post_type() !== 'post') return;

  $featured = get_option('featured_posts');
  $featured = $featured ? wp_parse_id_list($featured) : array();

  ?>
  <div class="feature-section misc-pub-section curtime misc-pub-section-last">
    <a class="feature <?php echo in_array(get_the_ID(), $featured) ? 'on' : 'off'; ?>" data-type="post" data-post="<?php echo get_the_ID(); ?>">
      <span></span><?php atom()->te('Make this post featured'); ?>
    </a>
  </div>

  <?php
}



/**
 * Remove feature post entry if a post is deleted
 *
 * @since 1.4
 */
function clean_featured_post_record($post_id){

  if($post_id){
    $featured = get_option('featured_posts') ? wp_parse_id_list(get_option('featured_posts')) : array();
    unset($featured[array_search($post_id, $featured)]);
    update_option('featured_posts', $featured);
  }

  return $post_id;
}



/**
 * First theme install notification message
 *
 * @since 2.0
 */
function atom_theme_install_notification(){ ?>
  <div class="updated fade">
    <p><?php atom()->te('You can customize your %1$s theme from the <%2$s>theme settings</a> page.', atom()->getThemeName(), 'a href="'.admin_url('admin.php?page='.ATOM).'"'); ?></p>
  </div>
  <?php
}




