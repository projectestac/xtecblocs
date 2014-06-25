<?php
/*
 * Framework API.
 *
 * Read the documentation for more info: http://digitalnature.eu/docs/
 *
 * @revised   February 4, 2012
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */



defined('ATOM') or die("You're doing it wrong...");



// filters and action hooks
require TEMPLATEPATH.'/atom-hooks.php';

// shortcode functions
require TEMPLATEPATH.'/atom-shortcodes.php';

// administration interface
if(is_admin())
  require TEMPLATEPATH.'/atom-interface.php';

// widgets
require TEMPLATEPATH.'/atom-widgets.php';

// deprecated stuff, it's here for compatibility with older versions
if(defined('ATOM_COMPAT_MODE') && ATOM_COMPAT_MODE)
  require TEMPLATEPATH.'/atom-deprecated.php';

//if(ATOM_DEV_MODE)
//  require 'timer.php';



/*
 * Atom main class.
 * This is essentially a WordPress abstraction layer. Provides the template author quick & easy access to most of WP's functionality.
 *
 * A few notes on magic methods:
 * - atom()->getCurrentProperty can be also accessed as atom()->property (same with set*)
 * - we can use atom()->something() to directly output the return value of atom()->getSomething()
 *
 * @since 1.0
 */
class Atom{



  const

    // framework version
    VERSION             = '2.1.2',

    // required WordPress version to run this theme (3.1 is the minimum for Atom-based themes)
    // the check is made during the 'after_setup_theme' hook, so everything executed before this action
    // should be backwards compatible as much as possible with older version to avoid breaking the website
    REQ_WP_VERSION      = '3.2',

    // theme documentation URI
    THEME_DOC_URI       = 'http://digitalnature.eu/docs/',

    // theme update checks will be performed here;
    // the target site needs to have the necessary functions to handle update checks...
    THEME_UPDATE_URI    = 'http://digitalnature.eu/',

    // yahoo query language uri;
    // used by some components to get external data
    YQL_URI             = 'http://query.yahooapis.com/v1/public/yql?q=',

    // warning message; used by debug info
    WARNING             = 1,

    // notification message
    NOTICE              = 2,

    // list of tags used by wigets to filter content
    SAFE_INLINE_TAGS    = 'abbr,acronym,b,cite,code,del,dfn,em,i,ins,q,strong,sub,sup';



  // our Atom instance
  protected static $instance;



  protected
    $current_theme_options  = array(),    // current theme options (the ones stored in the db)
    $default_theme_options  = array(),    // default theme options
    $user_functions         = false,      // only if a child theme is used; this is the path to functions-user.php
    $theme_name             = '',         // parent theme name (from style.css)
    $theme_version          = 0,          // parent theme version
    $theme_author           = '',         // parent theme author string
    $theme_uri              = '',         // parent theme URI string, ie. digitalnature.eu
    $theme_url              = '',         // local URL to the parent theme, eg. yoursite.com/wp-content/themes/atom/
    $child_theme_url        = '',         // local URL to the child theme (if no child theme is used, this is the same as the parent theme URL)
    $swc                    = false,      // site-wide content plugin installed & MU parent site?
    $log_messages           = array(),    // stores Atom's debug messages
    $widget_areas           = array(),    // widget areas (sidebars); this will also contain the sidebar contents
    $widget_splitter        = false,      // stores the splitter widget state (true = open, false = closed)
    $menus                  = array(),    // custom menu locations
    $layout_types           = array(),    // active layout types
    $current_post           = null,       // current post (should be the same as the $post global)
    $current_term           = null,       // current term
    $current_user           = null,       // current logged-in user
    $current_author         = null,       // current author, valid only on author archives; maybe we should remove this and use $app->post->author instead?
    $current_comment        = null,       // current comment
    $current_commenter      = null,       // registered user or returning visitor
    $current_page_title     = '',         // can hold a custom title for the current page
    $current_layout         = false,      // will store the current page layout
    $page_requests          = array(),    // caches requested page URLs
    $page_urls              = array(),    // caches requested page URLs
    $context_config         = array(),    // stores custom configuration for various methods, like pagination, post_content etc. (array)
    $modules                = array(),    // active modules
    $interface              = false;      // default administration interface (the theme settings)



  public
    $is_internal_page       = false;          // true if using a "internal" page template



 /*
  * This will instantiate the class if needed, and return the only class instance if not...
  *
  * @since     1.0
  * @param     $class    Class to use (we're trying to immitate late static binding for php < 5.3)
  * @return    object    Atom instance
  */
  final public static function app($ext_class = ''){

    if(!(self::$instance instanceof self)){ // first run?

      // @note: in php 5.3 we could use late static binding ("static" instead of "self")
      self::$instance = $ext_class ? new $ext_class() : new self();

      // get theme meta info from style.css; this is always the parent theme
      $theme = get_theme_data(TEMPLATEPATH.'/style.css');

      // set version info
      self::app()->theme_version                          = trim($theme['Version']);
      self::app()->default_theme_options['theme_version'] = self::app()->theme_version;

      self::app()->theme_name        = $theme['Name'];
      self::app()->theme_author      = $theme['Author'];
      self::app()->theme_uri         = $theme['URI'];
      self::app()->theme_url         = get_template_directory_uri();
      self::app()->child_theme_url   = get_stylesheet_directory_uri();
      self::app()->swc               = is_multisite() && is_main_site() && defined('ATOM_SWC_ACTIVE');  // site-wide content plugin installed ?
      self::app()->user_functions    = is_child_theme() ? STYLESHEETPATH.'/functions-user.php' : false;

      add_action('after_setup_theme', array(self::$instance, 'setup'));

      // localize the theme
      $locale = get_locale();

      // child theme translations have priority
      if(is_child_theme()){
        $locale_file = STYLESHEETPATH."/lang/{$locale}.php";
        if(is_readable($locale_file))
          require_once($locale_file);

        load_theme_textdomain(ATOM, STYLESHEETPATH.'/lang');
      }

      // parent theme
      load_theme_textdomain(ATOM, TEMPLATEPATH.'/lang');

      // set encoding for mb_* functions
      if(defined('MB_OVERLOAD_STRING'))
        mb_internal_encoding(get_option('blog_charset'));
    }


    return self::$instance;
  }



 /*
  * do_action wrapper (we're only prepending the ATOM_ prefix to the tag)
  *
  * @since   1.2
  * @param   string $tag   Tag name
  * @param   string $args  Arguments
  */
  public static function action($tag){
    $args = func_get_args();
    array_shift($args);
    array_unshift($args, "atom_{$tag}");
    func_num_args() ? call_user_func_array('do_action', $args) : do_action("atom_{$tag}");
  }



 /*
  * add_filter() / add_action() wrapper (same behaviour, just prepends the ATOM_ prefix to the tag)
  *
  * @since   1.2
  * @param   string $tag               Tag name
  * @param   string $function_to_add   Function name
  * @param   string $priority          Priority
  * @param   string $accepted_args     # of arguments accepted
  * @return  mixed                     Filtered value
  */
  public static function add($tag, $function_to_add, $priority = 10, $accepted_args = 1){
    return add_filter("atom_{$tag}", $function_to_add, $priority, $accepted_args);
  }



 /*
  * Get the translation of a string
  *
  * @since   2.0
  * @param   string $message    Message to translate
  * @param   mixed $args        Additional arguments will format the string
  * @return  string             Translated message
  */
  public static function t($message){

    $args = func_get_args();
    array_shift($args);

    if($args && is_array($args[0]))
      $args = $args[0];

    $translation = translate($message, ATOM);

    return empty($args) ? $translation : vsprintf($translation, $args);
  }



 /*
  * Output the translation of a string
  *
  * @since   2.0
  * @param   string $message    Message to translate
  * @param   mixed $args        Additional arguments will format the string
  */
  public static function te($message){

    $args = func_get_args();
    array_shift($args);

    if($args && is_array($args[0]))
      $args = $args[0];

    echo atom()->t($message, $args);
  }



 /*
  * Get the plural translation of a string
  *
  * @since   2.0
  * @param   string $message    Singular message
  * @param   string $plural     Plural message
  * @param   int $number        Number
  * @param   mixed $args        Additional arguments will format the string
  * @return  string             Translated message
  */
  public static function nt($message, $plural, $number){

    $args = func_get_args();
    $args = array_slice($args, 3);

    if($args && is_array($args[0]))
      $args = $args[0];

    $translations = &get_translations_for_domain(ATOM);
    $translation = $translations->translate_plural($message, $plural, $number);
    $translation = apply_filters('ngettext', $translation, $message, $plural, $number, ATOM);

    return empty($args) ? $translation : vsprintf($translation, $args);
  }



 /*
  * Echo the plural translation of a string
  *
  * @since   2.0
  * @param   string $message    Singular message
  * @param   string $plural     Plural message
  * @param   int $number        Number
  * @param   mixed $args        Additional arguments will format the string
  * @return  string             Translated message
  */
  public static function nte($message, $plural, $number){

    $args = func_get_args();
    $args = array_slice($args, 3);

    if(is_array($args[0]))
      $args = $args[0];

    echo atom()->nt($message, $plural, $number, $args);
  }



 /*
  * A single instance only
  *
  * @since 1.0
  */
  final protected function __construct(){}



 /*
  * No cloning
  *
  * @since 1.0
  */
  final protected function __clone(){}



  public function getThemeName(){
    return $this->theme_name;
  }

  public function getThemeURL(){
    return $this->theme_url;
  }

  public function getChildThemeURL(){
    return $this->child_theme_url;
  }

  public function getThemeVersion(){
    return $this->theme_version;
  }



  /**
   * Handles undefined method calls (this is a magic method).
   * First check if getMethod exists, and output it's return value to the screen if it does.
   * Otherwise check if the method is deprecated and throw an error...
   *
   * @since 1.0
   */
  public function __call($name, $args){

    $caller = "__call{$name}";
    $arg_count = count($args);

    if(method_exists($this, $caller))
      return $arg_count ? call_user_func_array(array($this, $caller), $args) : $this->$caller();

    $caller = "get{$name}";
    if(method_exists($this, $caller)){
      echo $arg_count ? call_user_func_array(array($this, $caller), $args) : $this->$caller();
      return;
    }

    if(defined('ATOM_COMPAT_MODE')){
      // method doesn't exist, check if it has been deprecated
      $deprecated_name = '__'.__CLASS__.$name;
      if(function_exists($deprecated_name))
        return $arg_count ? call_user_func_array($deprecated_name, $args) : call_user_func($deprecated_name); // call the compat function
    }

    // not deprecated, throw the error
    throw new Exception("Method {$name} is not defined");
  }



  /**
   * Only usable for getCurrent* methods and modules, eg. post, comment etc.
   *
   * @since 1.0
   */
  public function __get($name){

    $getter = "getCurrent{$name}";
    if(method_exists($this, $getter))
      return $this->$getter();

    // check modules
    if(($module = strtolower($name)) && isset($this->modules[$module]))
      return $this->modules[$module];

    // method doesn't exist, check if it has been deprecated
    if(defined('ATOM_COMPAT_MODE')){
      $deprecated_name = '__'.__CLASS__.$name;
      if(function_exists($deprecated_name))
        return $deprecated_name(); // call the compat function
    }

    throw new Exception("Method {$getter} is not defined.");  }



  /*
   * Only usable for setCurrent* methods
   *
   * @since 1.0
   */
  public function __set($name, $value){

    $setter = "setCurrent{$name}";
    if(method_exists($this, $setter))
      return $this->$setter($value);

    if(defined('ATOM_COMPAT_MODE')){
      // method doesn't exist, check if it has been deprecated
      $deprecated_name = '__'.__CLASS__.$name;
      if(function_exists($deprecated_name))
        return $deprecated_name($value); // call the compat function
    }

    throw new Exception("Method {$setter} is not defined.");

  }



  /**
   * Get the value of a protected variable
   *
   * @param mixed $var variable to get
   *
   * @since 1.0
   */
  public function get($var){
    return $this->$var;
  }



  /**
   * Get current post
   *
   * @since 1.0
   */
  public function getCurrentPost(){

    if(!isset($this->current_post))
      $this->current_post = new AtomObjectPost();

    return $this->current_post;
  }



  /**
   * Get current term
   *
   * @since 1.0
   */
  public function getCurrentTerm(){

    if(!isset($this->current_term))
      $this->current_term = new AtomObjectTerm();

    return $this->current_term;
  }


  /**
   * Set current post
   * - if $post is ommited the current post will take the value of WP's $post global
   * - if $post is a number (ID), the post will be fetched from the db and post data set up
   * - if $post is a object, the post data is set up...
   *
   * @param array|int|bool $post $post data / ID / nothing
   *
   * @since 1.0
   */
  public function setCurrentPost($post){
    $this->current_post = new AtomObjectPost($post);
  }



  /**
   * Resets the current post with the one from the main query
   *
   * @since 1.0
   */
  public function resetCurrentPost(){
    wp_reset_postdata();
    $this->setCurrentPost(false);
  }



 /*
  * Get a specific post as an Atom object
  *
  * @since     2.0
  * @param     $post              Post ID or object (if the value is empty the $post global is used)
  * @param     $setup_postdata    Set up the stupid WP $post global variable, false by default
  * @return    object             Atom post object
  */
  public static function __callPost($post, $setup_postdata = false){
    // we won't set up the post data here by default;
    // getContent will fail if used on this object...
    return new AtomObjectPost($post, $setup_postdata);
  }



 /*
  * Get a specific term as an Atom object
  *
  * @since     2.0
  * @param     $post              Term ID or object
  * @param     $taxonomy          Term taxonomy
  * @return    object             Atom term object
  */
  public static function __callTerm($term, $taxonomy){
    return new AtomObjectTerm($term, $taxonomy);
  }



 /*
  * Get a specific user as an Atom object
  *
  * @since     2.0
  * @param     $user              User ID or WP_User object
  * @return    object             Atom user object
  */
  public static function __callUser($user){
    return new AtomObjectUser($user);
  }



 /*
  * Get a specific comment as an Atom object
  *
  * @since     2.0
  * @param     $user              Comment ID or object
  * @return    object             Atom comment object
  */
  public static function __callComment($comment){
    return new AtomObjectComment($comment);
  }



  /**
   * Get current user,
   * Always check if there is a logged in user before using chained methods
   *
   * @since 2.0
   */
  public function getCurrentUser(){
    if(!isset($this->current_user)){

      if(is_user_logged_in()){
        $user = wp_get_current_user();
        $this->current_user = new AtomObjectUser($user);

      }else{
        $this->current_user = false;
      }

    }

    return $this->current_user;
  }



  /**
   * Get current author
   *
   * @since 1.0
   */
  public function getCurrentAuthor(){
    if(!isset($this->current_author))
      $this->current_author = new AtomObjectUser(get_query_var('author'));

    return $this->current_author;
  }



  /**
   * Get the admin interface object
   *
   * @since 2.0
   */
  public function getCurrentInterface(){
    if(!($this->interface instanceof AtomInterface))
      $this->interface = new AtomInterface('theme_settings', atom()->t('%s settings', $this->getThemeName()));

    return $this->interface;
  }



  /**
   * Sets the current layout type.
   * Note that this method will force the layout to this mode, regardless if an area is active or not
   * Should be called before get_header...
   *
   * @param string layout type (eg. c1, c2left, c2right, c3 etc)
   *
   * @since 2.0
   */
  public function setCurrentLayout($layout){
    $this->current_layout = $layout;
    define('ATOM_FORCE_LAYOUT', true);
  }



  /**
   * Compute the current layout type.
   * setCurrentLayout can override this
   *
   *
   * @since 2.0
   */
  public function getCurrentLayout(){
    global $post;

    if(!$this->current_layout){

      if(is_404()){
        $this->current_layout = 'c1';

      }else{

        // the "layout" custom field - highest priority
        if(isset($post->ID))
          $this->current_layout = get_post_meta($post->ID, 'layout', true);

        // custom page templates - lower priority
        if(empty($this->current_layout) && (is_single() || is_page())){
          $page_template = sanitize_html_class(str_replace(array('page-', '.php'), '', get_post_meta($post->ID, '_wp_page_template', true)));
          if($page_template && in_array($page_template, $this->layout_types))
            $this->current_layout = $page_template;
        }

        // if no template is defined so far, revert to the global layout option from the theme settings - lowest priority
        if(empty($this->current_layout))
          $this->current_layout = $this->options('layout');

      }
    }

    return $this->current_layout;
  }



  /**
   * Sets up current comment (and all the stupid WP globals etc)
   * - if $comment is a ID the comment will be fetched from the db
   *
   * @param array|int $comment comment data / ID
   *
   * @since 1.0
   */
  public function setCurrentComment($comment){
    $this->current_comment = new AtomObjectComment($comment);
  }



  /**
   * Get current comment
   *
   * @since 1.0
   */
  public function getCurrentComment(){
    if(!$this->current_comment)
      $this->current_comment = new AtomObjectComment();
      
    return $this->current_comment;
  }



  /**
   * Get current commenter info
   *
   * @since 1.0
   */
  public function getCurrentCommenter(){
    if(!isset($this->current_commenter))
      $this->current_commenter = wp_get_current_commenter();
      
    return $this->current_commenter;
  }



  /**
   * Updates the theme options.
   *
   * @since   1.0
   * @param   array $options   Theme options to set
   * @return  bool             Operation status
   */
  public function setOptions($options){
    $this->current_theme_options = $options;
    return update_option(ATOM, $options);
  }



 /*
  * Checks one or more theme options.
  * - can check multiple boolean-type options if multiple arguments are passed
  * - if no arguments are provided, all options are returned as an array
  *
  * @since    1.0
  * @param    string $option    Optional theme option(s) :)
  * @return   mixed             Single option value, array with all options or true / false / -1 (mixed) for multiple option checks
  */
  public function options(){

    // check if this is the 1st run, and load the theme settings if necessary
    if(empty($this->current_theme_options)){
      $this->current_theme_options = get_option(ATOM);

      // nothing in the database, install the defaults; reset() will set up the current_theme_options property
      if(empty($this->current_theme_options))
        $this->reset();
    }

    $args = func_get_args();
    $options = array_values($args);

    // no arguments, return the theme options array
    if(empty($options))
      return $this->current_theme_options;

    // single argument? return the value if the option exists, false otherwise...
    if(count($options) === 1)
      return isset($this->current_theme_options[$options[0]]) ? $this->current_theme_options[$options[0]] : false;

    // multiple arguments? (return true if all of them are enabled and have a true value, or false if all of them have a false value)
    $results = array();
    foreach($options as $option)
      if(isset($this->current_theme_options[$option]))
        $results[$option] = (bool)$this->current_theme_options[$option];

    // all are true, or all are false
    if(count(array_unique($results)) === 1)
      return array_shift($results);

    // mixed values
    return -1;
  }



 /*
  * Get the default theme options (the ones set with the function below)
  *
  * @since    1.0
  * @return   array    Default Theme Options
  */
  public function getDefaultOptions(){
    return $this->default_theme_options;
  }



 /*
  * Registers theme options.
  * (replaces addDefaultOptions from Atom 1.x)
  *
  * @since   2.0
  * @param   array $options    Theme options to register
  * @return  array             All currently registered options
  */
  public function registerDefaultOptions($options){
    $this->default_theme_options = array_merge($this->default_theme_options, $options);
    return $this->default_theme_options;
  }



 /*
  * Get widget areas (sidebars)
  *
  * @since    1.0
  * @return   array    Widget areas
  */
  public function widgetAreas(){
    return $this->widget_areas;
  }



 /*
  * Registers a widget area (sidebar).
  * Can process multiple arguments (each parameter represents a sidebar)
  *
  * @since   2.0
  * @param   array   Widget area(s) to register
  * @return  array   All currently registered widget areas
  */
  public function registerWidgetArea(){
    $args = func_get_args();

    foreach($args as $area)
      $this->widget_areas[] = $area;

    return $this->widget_areas;
  }



 /**
   * Get up the splitter wiget state
   *
   * @return bool True if active & open, false otherwise
   * @since 1.0
   */
  public function getWidgetSplitter(){
    return $this->widget_splitter;
  }



  /**
   * Set up the splitter wiget state
   *
   * @param bool True if active & open, false otherwise
   * @since 1.0
   */
  public function setWidgetSplitter($state){
    $this->widget_splitter = $state;
  }



 /*
  * Set up custom menu locations
  *
  * @since   1.0
  * @param   array $menus   Menus to register (array of IDs => labels)
  * @return  array          All currently registered menus
  */
  public function registerMenus($menus){

    $this->menus = array_merge($this->menus, $menus);

    return $this->menus;
  }



 /*
  * Registers up layout types (eg. col-1, col-2-left etc)
  *
  * @since   1.0
  * @param   string    Layout types (each is one argument)
  * @return  array     All currently registered layout types
  */
  public function registerLayoutTypes(){
    $args = func_get_args();

    if($args)
      $this->layout_types = array_merge($this->layout_types, array_values($args));

    return $this->layout_types;
  }



 /*
  * (Re)sets the theme settings.
  * Unless manually triggered, this should only run once (after the theme is activated for the 1st time)
  *
  * @since 1.0
  */
  public function reset(){
    global $wp_filesystem;

    define('INSTALLING_ATOM', true);

    $this->uninstall($reset_only = true);

    // attempt to create a child theme in the wp-themes directory and activate it
    // @todo: auto-network-activate the theme on MU, if the parent theme is network-activated
    if(ATOM_EXTEND && !is_child_theme()){

      require_once(ABSPATH.'wp-admin/includes/file.php');

      if(!WP_Filesystem()){
        $this->log('Failed to initialize WPFS...', 1); // do not return, we still need to set up the settings

      }else{
        $parent = get_theme_data(TEMPLATEPATH.'/style.css');
        $name = ATOM.'-extend';
        $destination = trailingslashit($wp_filesystem->wp_themes_dir()).$name;

        $style = "/*\n"
                ."Theme Name: {$parent['Name']} - Extend\n"
                ."Theme URI: {$parent['URI']}\n"
                ."Description: Auto-generated child theme of {$parent['Name']}. Please leave this one activated for proper customizations to {$parent['Name']}.\n"
                ."Version: 1.0\n"
                ."Author: {$parent['Author']}\n"
                ."Author URI: {$parent['AuthorURI']}\n"
                ."Template: ".get_template()."\n"
                ."*/\n\n"
                ."/* You can safely edit this file, but keep the Template tag above unchanged! */\n";


        // test wpfs, we need direct access
        $temp_file = wp_tempnam();
        $fs_transport_status = (getmyuid() === fileowner($temp_file));
        @unlink($temp_file);

        if($fs_transport_status && !$wp_filesystem->is_dir($destination)){
          if($wp_filesystem->mkdir($destination, FS_CHMOD_DIR) && $wp_filesystem->put_contents($destination.'/style.css', $style, FS_CHMOD_FILE)){

            // copy screenshot and license.txt
            $wp_filesystem->copy(TEMPLATEPATH.'/screenshot.png', $destination.'/screenshot.png');
            $wp_filesystem->copy(TEMPLATEPATH.'/license.txt', $destination.'/license.txt');

            // create "images" folder
            $wp_filesystem->mkdir($destination.'/images', FS_CHMOD_DIR);

            // will never show :)
            $this->log("Created {$name} child theme. Switching...");

            switch_theme(get_template(), $name);

            // unfortunately in Mystique the ATOM constant takes the value of the theme name by default, so we need to check for it
            $new_atom = strtolower($this->theme_name !== 'mystique') ? ATOM : get_stylesheet();

            // redirect and stop execution of the current page
            $redirect_url = is_admin() ? admin_url("themes.php?page={$new_atom}") : home_url();
            wp_redirect($redirect_url);

            exit;

          }else{
            $this->log('Failed to create child theme.', 1);

          }
        }
      }
    }

    // update the db with the default settings
    $this->default_theme_options['theme_version'] = $this->theme_version;

    $this->setOptions($this->default_theme_options);

    // update permalink structure later, after posts are loaded (some modules might change the permalink structure ;)
    add_action('wp', 'flush_rewrite_rules');

    add_action('admin_notices', 'atom_theme_install_notification');

    // action hook, probably useless
    $this->action('setup_options');

    $this->log('New theme install. Default settings were installed');
  }



 /*
  * Get the relative date of a UNIX timestamp
  *
  * @since    1.0
  * @param    string $older_date    Date in UNIX time format
  * @param    string $newer_date    Optional. Use a custom newer date instead of the current time returned by the server
  * @return   string                The relative date string
  */
  public static function getTimeSince($older_date, $newer_date = false){

    $chunks = array(
      'year'   => 31536000, // 60 * 60 * 24 * 365
      'month'  => 2592000,  // 60 * 60 * 24 * 30
      'week'   => 604800,   // 60 * 60 * 24 * 7
      'day'    => 86400,    // 60 * 60 * 24
      'hour'   => 3600,     // 60 * 60
      'minute' => 60,
      'second' => 1,
    );

    $newer_date = ($newer_date !== false) ? $newer_date : current_time('timestamp');

    foreach($chunks as $key => $seconds)
      if(($count = floor(($newer_date - $older_date) / $seconds)) != 0) break;

    $messages = array(
      'year'   => atom()->nt('%s year ago', '%s years ago', $count),
      'month'  => atom()->nt('%s month ago', '%s months ago', $count),
      'week'   => atom()->nt('%s week ago', '%s weeks ago', $count),
      'day'    => atom()->nt('%s day ago', '%s days ago', $count),
      'hour'   => atom()->nt('%s hour ago', '%s hours ago', $count),
      'minute' => atom()->nt('%s minute ago', '%s minutes ago', $count),
      'second' => atom()->nt('%s second ago', '%s seconds ago', $count),
    );
    return sprintf($messages[$key], $count);
  }



 /*
  * Filters and truncates a HTML string, and appends a "read more" link if needed. Supports Unicode text
  * Partly based on truncate() from CakePHP
  *
  * @since    2.0
  * @param    string $text   Text to filter / truncate
  * @param    array $args    Optional arguments, see below
  * @return   string
  */
  public static function generateExcerpt($text, $args = array()) {

    $args = wp_parse_args($args, array(
      'limit'                    => 0,          // character limit, disabled by default
      'shortcodes'               => false,      // keep shortcodes? normally there's no need to, because this filter is applied after shortcodes are processed...
      'more'                     => false,      // more tag format, disabled by default
      'more_inline'              => true,       // attempt to insert the "more" tag before the last closing <p>, <li> or <dd> tag
      'cutoff'                   => 'word',     // cut off type: relative (between words, sentences or a specific character / set/array of characters); or exact (false)
      'disallowed_tags_content'  => true,       // strip content between non-allowed tags

      // by default only allow safe inline tags + <a> + a few block tags
      'allowed_tags'             => Atom::SAFE_INLINE_TAGS.'<a><p><ul><ol><dl><dt><dd><li><blockquote>',
    ));

    extract($args);

    // strip shortcodes?
    if(!$shortcodes)
      $text = strip_shortcodes($text);

    if(!is_array($allowed_tags)){

      if(strpos($allowed_tags, ',') !== false)
        $allowed_tags = explode(',', $allowed_tags);

      else
        $allowed_tags = explode('><', trim(preg_replace('/\s+/', '', $allowed_tags), '<>'));

    }

    // strip all tags that were not mentioned above, unless "ALL" is present in the list
    if(!in_array('ALL', $allowed_tags)){

      // only strip tags
      if($disallowed_tags_content)
        $text = strip_tags($text, '<'.implode('><', $allowed_tags).'>');

      // strip tags and their contents
      else
        $text = preg_replace('@<(?!(?:'.implode('|', $allowed_tags).')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
    }

    // do nothing if no limit was given, or if the text length is smaller than the limit value
    if(empty($limit) || mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $limit) return $text;

    // truncate Unicode text -- stolen from CakePHP :)
    // https://github.com/cakephp/cakephp/blob/2.0/lib/Cake/View/Helper/TextHelper.php#L197

    $total_length = 0;
    $open_tags = array();
    $excerpt = '';

    preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);

    foreach($tags as $tag){
      if(!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])){
        if(preg_match('/<[\w]+[^>]*>/s', $tag[0])){
          array_unshift($open_tags, $tag[2]);

        }elseif(preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $close_tag)){
          $pos = array_search($close_tag[1], $open_tags);
          if($pos !== false)
            array_splice($open_tags, $pos, 1);

        }
      }

      $excerpt .= $tag[1];
      $content_length = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));

      if($content_length + $total_length > $limit){
        $left = $limit - $total_length;
        $entities_length = 0;

        if(preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE))
          foreach($entities[0] as $entity)
            if($entity[1] + 1 - $entities_length <= $left){
              $left--;
              $entities_length += mb_strlen($entity[0]);

            }else{
              break;
            }

        $excerpt .= mb_substr($tag[3], 0 , $left + $entities_length);
        break;

      }else{
        $excerpt .= $tag[3];
        $total_length += $content_length;
      }

      if($total_length >= $limit) break;
    }


    // cut off text position at specified character(s) ?
    if($cutoff && $cutoff !== 'exact'){

      // we got an array of possible stop characters
      if(is_array($cutoff))
        $stop_chars = $cutoff;

      // 'word' argument means the stop character is space
      elseif($cutoff === 'word')
        $stop_chars = array(' ');

      // 'sentence' argument means that we have multiple stop characters
      elseif($cutoff === 'sentence')
        $stop_chars = array('.', '!', '?', ';');

      // we got a custom character, turn it into an array -- @todo: allow full strings as stop positions...
      else
        $stop_chars = (array)$cutoff[0];

      $in_tag = $stop_pos = false;

      // split string into an array, the utf8 friendly way
      $chars = preg_split('//u', $excerpt, -1, PREG_SPLIT_NO_EMPTY);

      // iterate and find the nearest stop character that's not inside a tag
      foreach($chars as $index => $char){

        if(!$in_tag && in_array($char, $stop_chars))
          $stop_pos = $index + 1;

        elseif(!$in_tag && ($char === '<'))
          $in_tag = true;

        elseif($in_tag && ($char === '>'))
          $in_tag = false;
      }

      // if we have a valid cut position, cut the string and drop unclosed tags
      if($stop_pos !== false){

        $bits = mb_substr($excerpt, $stop_pos);
        preg_match_all('/<\/([a-z]+)>/', $bits, $dropped_tags, PREG_SET_ORDER);
        if(!empty($dropped_tags))
          foreach($dropped_tags as $closing_tag)
            if(!in_array($closing_tag[1], $open_tags)) array_unshift($open_tags, $closing_tag[1]);

        $excerpt = mb_substr($excerpt, 0, $stop_pos);
      }
    }

    // process "more" text
    if($more){

      $did_more = false;

      // insert it inline?
      if($more_inline){

        // get the last one of the block level elements inside which we can append our "more" text
        foreach($open_tags as $index => $tag)
          if(in_array($tag, array('p', 'li', 'dd'))) $last_block_tag_index = $index;

        // close open tags
        foreach($open_tags as $index => $tag){

          // if this is the last block tag, append the "more" text before we close it
          if(isset($last_block_tag_index) && ($index === $last_block_tag_index)){
            $excerpt .= ' '.$more;
            $did_more = true;
          }

          // close tag
          $excerpt .= '</'.$tag.'>';
        }

      }

      // append our "more" text if we didn't already
      if(!$did_more)
        $excerpt .= ' '.$more;

    }

    return $excerpt;
  }



  /**
   * Check if we're on a certain page / or output the current page slug
   *
   * @param string $page Page to check
   * @since 1.0
   */
  public function isPage($page){
    // internal page handled by a template
    if(get_query_var('pagename') === $page) return true;

    // not a internal page, normal page then?
    return is_page($page);
  }



 /*
  * Find the page that is using a specific page template.
  * If multiple pages are using the page template, only the 1st one is returned
  *
  * @since    2.0
  * @param    string $page_template     Page template to search
  * @return   int|bool                  Page ID on success, false if not page was found using that template
  */
  public function getPageByTemplate($page_template){
    global $wp_rewrite, $wpdb;

    // already requested?
    if(!isset($this->page_requests[$page_template])){

      $got_id = false;

      $template = "page-{$page_template}.php";
      $query = $wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_page_template' AND meta_value = %s", $template);

      // check cache
      $key = md5($query);
      $cache = wp_cache_get('get_page_template', 'atom');

      if(!isset($cache[$key])){
        $got_id = $wpdb->get_var($query);
        $cache[$key] = $got_id;
        wp_cache_set('get_page_template', $cache, ATOM);

      }else{
        $got_id = $cache[$key];
      }

      $this->page_requests[$page_template] = $got_id ? (int)$got_id : false;
    }

    return $this->page_requests[$page_template];
  }



 /*
  * Get a page's URL
  *
  * @param string $page Page name, template name or internal template name to get
  * @param bool $check_internal Check internal pages (templates)
  * @param bool $check_templates Check page templates
  * @since 1.0
  */
  public function getPageURL($page, $check_internal = true, $check_templates = true){
    global $wp_rewrite, $wpdb;

    // already requested?
    if(isset($this->page_urls[$page]))
      return $this->page_urls[$page]['url'];

    $url = false;

    if($check_internal && $this->findTemplate("internal-{$page}")){

      // @todo: fix 404 when perma structure != default
      $url = ($wp_rewrite->using_permalinks()) ?  home_url('/').$page.'/' : add_query_arg(array('pagename' => $page), home_url('/'));

    // internal page doesn't exist or not request, check page templates?
    }else{

      $page_id = false;

      if($check_templates){
        $template = "page-{$page}.php";
        $query = $wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_page_template' AND meta_value = %s", $template);
        // check cache
        $key = md5($query);
        $cache = wp_cache_get('get_page_url', 'atom');
        if(!isset($cache[$key])){
          $page_id = $wpdb->get_var($query);
          $cache[$key] = $page_id;
          wp_cache_set('get_page_url', $cache, ATOM);
        }else{
          $page_id = $cache[$key];
        }
      }

      $page_obj = $page_id ? get_page($page_id) : get_page_by_path($page);

      if(!empty($page_obj->ID))
        $url = get_page_link($page_obj->ID);
    }

    if($url !== false){
      $this->page_urls[$page]['url'] = $url;
      $this->page_urls[$page]['id'] = isset($page_obj) ? (int)$page_obj->ID : 0;
    }

    return $url;
  }



 /*
  * Output the favicon meta
  *
  * @param string $override Optional, override favicon path...
  * @since 1.0
  */
  public function getFavIcon($override = ''){

    $favicon = $override ? $override : $this->options('favicon');

    if($favicon)
      return '<link rel="shortcut icon" href="'.$favicon.'" />';
  }



  /**
   * Force a custom title for the current page. "Paged" parts are detected automatically.
   * Obviously must be called before getDocumentTitle() below...
   *
   * @param string $title Title to set. If multiple arguments are given, they are combined as "parts" into an array
   * @since 1.7
   */
  public function setDocumentTitle($title){
    $args = func_get_args();
    $parts = array_values($args);
    $this->current_page_title = (count($parts) > 1) ? $parts : array_shift($parts);
  }



 /*
  * Formats and outputs the document title
  *
  * @since   1.0
  * @param   string $separator    Title separator. eg. |, -, &laquo; etc...
  * @param   bool $reverse        Reverse title parts?
  * @return  string               Title
  */
  public function getDocumentTitle($separator = '&raquo;', $reverse = false){
    global $wp_query;

    // extra config, defaults -- @todo expand this, add more options...
    $config = array(
      'separator'              => $separator,
      'reverse'                => $reverse,
      'singular_post_parents'  => array(), // array of post types for which to append parent post titles
      'singular_object_labels' => array(), // array of post types for which to append object labels
    );

    extract($this->getContextArgs('document_title', $config));

    $title = array();

    $title[] = get_bloginfo('name');
    $description = get_bloginfo('description');

    // allow developers so set a custom page title using the setDocumentTitle() method (useful theme-internal pages)
    if(!empty($this->current_page_title)){

      if(is_array($this->current_page_title))
        foreach($this->current_page_title as $part) $title[] = $part;

      elseif(!empty($this->current_page_title))
        $title[] = $this->current_page_title;

    // home page / front page
    }elseif(is_front_page() && is_home() && !empty($description)){
      $title[] = $description;

    // home page or single post page
    }elseif(is_singular()){

      if($meta = get_post_meta($this->post->getID(), 'title', true)){
        $title[] = $meta;

      }else{

        if(in_array($this->post->getType(), (array)$singular_object_labels))
          $title[] = get_post_type_object($this->post->getType())->labels->name;

        if(in_array($this->post->getType(), (array)$singular_post_parents)){ // appends parent post titles, if any
          $parent = (int)$this->post->getParent();
          while($parent !== 0){
            $title[] = get_the_title($parent);
            $parent = (int)get_post_field('post_parent', $parent);
          }
        }

        // current post title
        $title[] = $this->post->getTitle();
      }

    // archives
    }elseif(is_archive()){
      // taxonomy archives
      if(is_category() || is_tag() || is_tax()){
        $term = $wp_query->get_queried_object();
        $title[] = $term->name;

      // author archives
      }elseif(is_author()){
        $title[] = get_the_author_meta('display_name', get_query_var('author'));

      // date archives
      }elseif(is_date()){
        // day
        if(is_day())
          $title[] = atom()->t('Archive for %s', $this->post->getDate(apply_filters('doc_title_archive_day_format', 'F jS, Y')));

        // week
        elseif(get_query_var('w'))
          $title[] = atom()->t('Archive for week %1$s of %2$s', $this->post->getDate('W'), $this->post->getDate('Y'));

        // month
        elseif(is_month())
          $title[] = atom()->t('Archive for %s', single_month_title(' ', false));

        // year
        elseif(is_year())
          $title[] = atom()->t('Archive for year %s', $this->post->getDate('Y'));

      // other archive types
      }else{
        $title[] = post_type_archive_title('', false);

      }

    // search
    }elseif(is_search()){
      $title[] = atom()->t('Search results for %s', sprintf('&quot;%s&quot;', get_search_query()));

    // 404
    }elseif(is_404()){
      $title[] = atom()->t('404 Not Found');

    }

    // paged?
    if((($page = $wp_query->get('paged')) || ($page = $wp_query->get('page'))) && $page > 1 && !is_404())
      $title[] = atom()->t('Page %s', $page);

    // comment page?
    if(get_query_var('cpage'))
      $title[] = atom()->t('Comment Page %s', get_query_var('cpage'));

    // reverse parts?
    if($reverse) $title = array_reverse($title);

    // apply the wp_title filters so we're compatible with plugins
    return apply_filters('wp_title', implode(" {$separator} ", $title), $separator, $reverse);
  }



 /*
  * Generates and outputs the current page meta description field
  *
  * @since   1.0
  * @return  string
  */
  public function getMetaDescription(){
    if(!$this->options('meta_description')) return;

    // blog description on the homepage
    if(is_home()){
      $description = get_bloginfo('description');

    // single pages?
    }elseif(is_singular()){

      // custom field, if present
      $description = atom()->post->getMeta('description');

      // if not, and we're on a static homepage use the blog description
      if(empty($description) && is_front_page())
        $description = get_bloginfo('description');

      // otherwise use the post excerpt
      if(empty($description)){

        // some stupid WP plugins force us to call get_the_content only once (inside the loop)
        $description = atom()->generateExcerpt(trim(get_the_content()), array(
          'limit'                    => 300,
          'cutoff'                   => 'sentence',
          'shortcodes'               => false,
          'more'                     => '',
          'disallowed_tags_content'  => false,
          'allowed_tags'             => array(), // no html!
        ));
      }


    // archive pages?
    }elseif(is_archive()){

     // author bio on author pages
     if(is_author())
       $description = get_the_author_meta('description', get_query_var('author'));

     // taxonomy term description on category/tags/etc...
     elseif(is_category() || is_tag() || is_tax())
       $description = term_description('', get_query_var('taxonomy'));

    }

    if(!empty($description)){

      // remove html formatting
      $description = str_replace(array("\r", "\n", "\t"), '', esc_attr(strip_tags($description)));

      // output (and allow user to change the content trough a filter hook)
      return '<meta name="description" content="'.apply_filters('meta_description', $description).'" />'."\n";
    }
  }



 /*
  * Output the site logo HTML
  *
  * @since    1.0
  * @param    string $image_url  Override 'logo' setting
  * @param    string|bool $tag   Tag to wrap the logo between
  * @return   string             Logo
  */
  public function getLogo($image_url = '', $tag = false){

    if(!$image_url)
      $image_url = $this->options('logo');

    $title = get_bloginfo('name');

    // determine length of the title, theme might want to handle font size of the logo
    $standard_lengths = array(
      3  => 'xs',
      6  => 's',
      12 => 'm',
      24 => 'l',
    );
    $title_length = mb_strlen($title);
    $font_size = 'xl';
    foreach($standard_lengths as $length => $size)
      if($title_length < $length){
         $font_size = $size;
         break;
      }

    // <h1> only on the front/home page, for seo reasons
    if(!$tag)
      $tag = (is_home() || is_front_page()) ? 'h1' : 'div';

    $output = '<'.$tag.' id="logo" class="size-'.$font_size.'">';

    if($image_url){ // logo image?

      // determine image size -- @todo
      $image_size = '';
      /*
      $upload_dir = wp_upload_dir();
      echo $upload_dir['path'];
      echo basename($image_url);
      $image_path = str_replace(site_url('/'), ABSPATH, $parts['scheme'].'://'.$parts['host'].$parts['path']);
      list($width, $height) = getimagesize($image_path);
      $image_size = (empty($width) || empty($height)) ? '' : 'width="'.$width.'" height="'.$height.'"';
      */

      $output .= '<a href="'.home_url('/').'"><img src="'.$image_url.'" title="'.$title.'" '.$image_size.' alt="'.$title.'" /></a>';

    }else{ // text?

      // we get a special treat if the logo is made out of 2 or 3 words
      $words = explode(' ', $title);
      if(!empty($words[1]) && empty($words[3])){
        $words[1] = '<span class="alt">'.$words[1].'</span>';

        // leave the space here and remove it trough css to avoid seo problems
        $title = implode(' ', $words);
      }

      $output .= '<a href="'.home_url('/').'">'.$title.'</a>';

    }

    $output .= '</'.$tag.'>';
    return apply_filters('atom_logo', $output, $title);
  }



 /*
  * has_nav_menu wrapper
  * Check if a menu location exists
  *
  * @since       1.8
  * @param       string $location    Menu location
  * @return      bool
  */
  public function MenuExists($location){

    // location not set
    if(!isset($this->menus[$location])) return false;

    // check if the menu has items
    if(($location !== 'primary') && !has_nav_menu($location)) return false;

    // primary menu always exists because it has a fallback set by default
    return true;
  }



 /*
  * wp_nav_menu wrapper
  * @todo: (maybe) create our own walker and add menu entry template?
  *
  * @since    1.0
  * @param    string|array $location   Menu location (string), or an array of arguments
  * @param    string $user_classes     Extra classes to add to the menu list
  * @param    string $fallback         Fallback menu
  * @return   string                   menu HTML
  */
  public function getMenu($location, $user_classes = '', $fallback = ''){

    $args = array();

    // our walker; if a custom walker is passed, it must be a child of ours
    $walker = new AtomWalkerNavMenu();

    // check if wp_nav_menu arguments were given
    if(is_array($location)){

      $args = $location;

      if(!isset($args['theme_location']) || !array_key_exists($args['theme_location'], $this->menus))
        throw new Exception('getMenu(): theme_location argument not given or invalid.');

      $location = $args['theme_location'];

      // override menu_class
      if(isset($args['menu_class']))
        $user_classes = $args['menu_class'];

      // override fallback_cb
      if(isset($args['fallback_cb']))
        $fallback = $args['fallback_cb'];

      if(isset($args['walker']) && ($args['walker'] instanceof AtomWalkerNavMenu))
        $walker = $args['walker'];

    }

    if($fallback && method_exists($this, "get{$fallback}"))
      $fallback = array($this, "get{$fallback}");

    $args = $this->getContextArgs("{$location}_menu",  $args, array(
      'echo'           => false,
      'container'      => false,
      'menu_class'     => "menu {$user_classes} clear-block",
      'theme_location' => $location,
      'fallback_cb'    => $fallback,
      'walker'         => $walker,
    ));

    // check if this menu can be seen by the current user
    if(!empty($args['user_visibility'])){

      $roles = (array)$args['user_visibility'];
      $can_see_menu = false;

      foreach($roles as $role)
        if(current_user_can($role)){
          $can_see_menu = true;
          break;
        }

      if(!$can_see_menu) return '';
    }

    return wp_nav_menu($args);
  }



 /*
  * Default navigation, only displayed if no custom menu is set as the primary navigation.
  * Contents are the home link and a list of pages (and sub-pages if there are any).
  * This function is a replacement wp_page_menu, which produces HTML mark-up that's inconsistent with wp_nav_menu.
  *
  * @since    1.3
  * @param    array $args   Arguments, see below
  * @return   string        Menu HTML
  */
  public static function getPageMenu($args = array()){

    $defaults = array(
      'container'           => 'div',
      'container_class'     => '',
      'container_id'        => '',
      'menu_class'          => 'menu',
      'menu_id'             => '',

      // these two are ignored by wp_page_menu; @todo: find a way to use them on the generated menu, regex maybe?
      'before'              => '',
      'after'               => '',

      'link_before'         => '',
      'link_after'          => '',
      'depth'               => 0,
      'walker'              => '',
      'slug'                => 'menu-pages', // extra
      'include_home'        => true,         // extra, home link
      'include_search'      => false,        // extra, search as menu entry
      'exclude'             => '',
      'post_type'           => 'page',       // @todo: test support

      'sort_column'         => 'menu_order, post_title',
    );

    $args = wp_parse_args($args, $defaults);

    // we need either an instance of our walker or a child of our walker
    if(!($args['walker'] instanceof AtomWalkerPages))
      $args['walker'] = new AtomWalkerPages();

    $output = $items = array();

    $show_container = false;
    if($args['container']){
      $allowed_tags = apply_filters('wp_nav_menu_container_allowedtags', array('div', 'nav'));
      if(in_array($args['container'], $allowed_tags)){
        $show_container = true;
        $container_class = $args['container_class'] ? ' class="'.$args['container_class'].'"' : ' class="menu-'.$args['slug'].'-container"';
        $container_id = $args['container_id'] ? ' id="'.$args['container_id'].'"' : '';
        $output[] = '<'.$args['container'].$container_id.$container_class. '>';
      }
    }

    // add 'home' menu item
    if($args['include_home'])
      $items[] = '<li class="menu-home '.((is_front_page() && !is_paged()) ? 'active' : '').'"><a href="'.home_url('/').'" title="'.atom()->t('Home Page').'">'.$args['link_before'].atom()->t('Home').$args['link_after'].'</a></li>';


    // if the front page is a page, add it to the exclude list for get_posts() below
    if(get_option('show_on_front') === 'page'){

      // turn it into an array if it isn't already (get_pages can accept a string too)
      if(!is_array($args['exclude']))
        $args['exclude'] = wp_parse_id_list($args['exclude']);

      $args['exclude'][] = get_option('page_on_front');
      $args['exclude'] = array_unique($args['exclude']);
    }

    $pages = get_pages($args);

    if(!empty($pages)){
      global $wp_query;
      $current_page = (($args['post_type'] == get_query_var('post_type')) || is_attachment() || is_page() || $wp_query->is_posts_page) ? $wp_query->get_queried_object_id() : false;
      $items[] = apply_filters('wp_list_pages', walk_page_tree($pages, $args['depth'], $current_page, $args), $args);
    }

    if($args['include_search']){
      $search_form  = '<form method="get" action="'.home_url('/').'">';
      $search_form .= '<input type="text" name="s" data-default="'.atom()->t('Search Website').'" class="text clearField" value="" />';
      $search_form .= '</form>';
      $items[] = '<li class="menu-search">'.$search_form.'</li>';
    }

    // attributes
    $slug = empty($args['menu_id']) ? "menu-{$args['slug']}" : $args['menu_id'];

    $classes = $args['menu_class'] ? ' class="'.$args['menu_class'].'"' : '';

    $output[] = '<ul id="'.$slug.'" '.$classes.'>';
    $output[] = implode("\n", $items);
    $output[] = '</ul>';

    if($show_container)
      $output[] = '</'.$args['container'].'>';

    $output = apply_filters('wp_page_menu', implode("\n", $output), $args);

    return $output;
  }



 /*
  * Category navigation, not used by default (maybe some child themes will use it)
  * Contents are the home link and a list of categories/sub-categories
  *
  * @since    1.2
  * @param    array $args   Arguments
  * @return   string        Menu HTML
  */
  public static function getCategoryMenu($args = array()){
    $defaults = array(
      'container'           => 'div',
      'container_class'     => '',
      'container_id'        => '',
      'menu_class'          => 'menu',
      'menu_id'             => '',
      'before'              => '',    // these two are ignored by wp_page_menu; @todo: find a way to use them on the generated menu, regex maybe?
      'after'               => '',
      'link_before'         => '',
      'link_after'          => '',
      'depth'               => 0,
      'walker'              => '',
      'slug'                => 'menu-categories', // extra
      'include_search'      => false,             // extra, search as menu entry
      'include_home'        => true,              // extra, home link
    );

    $args = wp_parse_args($args, $defaults);

    // we need either an instance of our walker or a child of our walker
    if(!($args['walker'] instanceof AtomWalkerTerms))
      $args['walker'] = new AtomWalkerTerms();

    $output = $items = array();
    $show_container = false;

    if($args['container']){
      $allowed_tags = apply_filters('wp_nav_menu_container_allowedtags', array('div', 'nav'));
      if(in_array($args['container'], $allowed_tags)){
        $show_container = true;
        $container_class = $args['container_class'] ? ' class="'.$args['container_class'].'"' : ' class="menu-'.$args['slug'].'-container"';
        $container_id = $args['container_id'] ? ' id="'.$args['container_id'].'"' : '';
        $output[] = '<'.$args['container'].$container_id.$container_class. '>';
      }
    }

    // add 'home' menu item
    if($args['include_home'])
      $items[] = '<li class="menu-home '.((is_front_page() && !is_paged()) ? 'active' : '').'"><a href="'.home_url('/').'" title="'.atom()->t('Home Page').'">'.$args['link_before'].atom()->t('Home').$args['link_after'].'</a></li>';

    // get category list
    // passes $arg to wp_list_pages (most of them are ignored)
    $items[] = wp_list_categories(array_merge($args, array(
      'echo'             => false,
      'title_li'         => '',
      'show_option_none' => '',
      'slug'             => '',
    )));

    if($args['include_search']){
      $search_form  = '<form method="get" action="'.home_url('/').'">';
      $search_form .= '<input type="text" name="s" data-default="'.atom()->t('Search Website').'" class="text clearField" value="" />';
      $search_form .= '</form>';
      $items[] = '<li class="menu-search">'.$search_form.'</li>';
    }

    // attributes
    $slug = empty($args['menu_id']) ? "menu-{$args['slug']}" : $args['menu_id'];
    $classes = $args['menu_class'] ? ' class="'.$args['menu_class'].'"' : '';

    $output[] = '<ul id="'.$slug.'" '.$classes.'>';
    $output[] = implode("\n", $items);
    $output[] = '</ul>';

    if($show_container)
      $output[] = '</'.$args['container'].'>';

    return implode("\n", $output);
  }



 /*
  * Check if a theme option is enabled.
  * Replaces deprecated method isOptionEnabled()
  *
  * @since    2.0
  * @param    string $option   The option name.
  * @return   bool             True if exists, false otherwise
  */
  public function optionExists($option){
    // check for a prefix first
    return in_array($option, array_keys($this->default_theme_options));
  }



 /*
  * Uninstall the theme.
  * Removes theme settings and fires a hook that allows modules to remove their stuff too...
  *
  * @since   1.0
  * @param   bool $reset_only   Optional, if true the action is skipped
  */
  public function uninstall($reset_only = false){

    delete_option(ATOM);
    delete_option(ATOM.'-atom-mods');

    if(!$reset_only)
      $this->action('uninstall');

    // @todo: remove temp. files created by the theme in /uploads/ folder
  }



  /**
   * Return the javascript url for a file from the js directory
   *
   * @param string $name Name of the .js file
   * @since 1.4
   */
  public function jsURL($name){
    return $this->theme_url.'/js/'.$name.(!ATOM_DEV_MODE ? '.min' : '').'.js';
  }



  /**
   * check for a Atom request
   *
   * @since 1.3
   * @param string $req
   * @param string $req
   * @return bool
   */
  public static function request($req){
    return (get_query_var('atom') === $req);
  }



  /**
   * AJAX headers
   *
   * @since 1.3
   * @param string $nonce
   */
  public static function ajaxHeader($nonce = '', $content_type = 'text/html'){
    defined("DOING_AJAX") or define("DOING_AJAX", true);
    @header("Content-Type: {$content_type}; charset=".get_option('blog_charset'));
    @header("X-Content-Type-Options: nosniff");

    if($nonce) check_ajax_referer("atom_{$nonce}", "_ajax_nonce");
  }



  /**
   * Check if we're in theme preview mode (Atom > Design)
   *
   * @since 1.0
   * @return bool True or False
   */
  public static function previewMode(){
    return (isset($_GET['themepreview']) && current_user_can('edit_theme_options'));
  }



 /*
  * Generates page navigation links for (almost) every context
  * based on WP PageNavi - http://wordpress.org/extend/plugins/wp-pagenavi/
  *
  * @todo     Add comment pagination
  * @since    2.0
  * @param    array $args   Optional arguments, see below
  * @return   string        Pagination HTML
  */
  public function getPagination($args = array()){
    global $wp_query;

    // defaults
    $args = wp_parse_args($args, array(
      'type'                 => $this->options('post_navi'),              // valid values: "numbers", "prevnext" or "single"
      'class'                => '',                                       // extra classes to add to the container
      'pages_to_show'        => 5,                                        // numbers only
      'extended'             => false,                                    // numbers only
      'larger_page_to_show'  => 3,                                        // numbers only
      'larger_page_multiple' => 10,                                       // numbers only
      'status'               => false,                                    // show current page status (numbers & prevnext types only)
      'prev_next'            => true,                                     // show prev/next links (numbers only, always true for prevnext type)
      'object'               => null,                                     // for multipart posts (the post object must be passed)
      'current_page'         => max(1, absint($wp_query->get('paged'))),
      'total_pages'          => max(1, absint($wp_query->max_num_pages)),
    ));

    extract($this->getContextArgs('pagenavi', $args), EXTR_SKIP);

    $prev_next = ($type === 'prevnext') ? true : $prev_next;

    if($total_pages == 1)
      return '';

    // get_pagenum_link alternative (currently only needed for multipart posts)
    $getPageLink = ($object instanceof AtomObject) && (method_exists($object, 'getPageLink')) ? array($object, 'getPageLink') : 'get_pagenum_link';

    $half_page_start = floor(($pages_to_show - 1) / 2);
    $half_page_end = ceil(($pages_to_show - 1) / 2);

    $start_page = $current_page - $half_page_start;

    if($start_page <= 0)
      $start_page = 1;

    $end_page = $current_page + $half_page_end;

    if(($end_page - $start_page) !== ($pages_to_show - 1))
      $end_page = $start_page + ($pages_to_show - 1);

    if($end_page > $total_pages){
      $start_page = $total_pages - ($pages_to_show - 1);
      $end_page = $total_pages;
    }

    if($start_page <= 0)
      $start_page = 1;

    $out = array();

    if($type === 'single'){

      if(($current_page + 1) > $total_pages)
        return '';

      $out[] = '<a class="next" href="'.esc_url(call_user_func($getPageLink, $current_page + 1)).'" title="'.atom()->t('Show More').'">'.atom()->t('Show More').'</a>';
      $class .= "{$class} fadeThis";

    }else{

      if($status)
        $out[] = '<span class="pages">'.atom()->t('Page %1$s of %2$s', $current_page, $total_pages).'</span>';

      if($prev_next && ($current_page > 1))
        $out[] = '<a class="previous" href="'.esc_url(call_user_func($getPageLink, $current_page - 1)).'">'.atom()->t('&laquo; Previous').'</a>';

      // numbered page links
      if($type !== 'prevnext'){

        if(($start_page) >= 2 && ($pages_to_show < $total_pages)){
          $out[] = '<a class="first" href="'.esc_url(call_user_func($getPageLink, 1)).'">1</a>';
          $out[] = '<span class="dots">...</span>';
        }

        $larger_pages_array = array();
        if($larger_page_multiple)
          for($i = $larger_page_multiple; $i <= $total_pages; $i += $larger_page_multiple)
            $larger_pages_array[] = $i;

        if($extended){
          $larger_page_start = 0;
          foreach($larger_pages_array as $larger_page)
            if(($larger_page < $start_page) && ($larger_page_start < $larger_page_to_show)){
              $out[] = '<a class="ext page" href="'.esc_url(call_user_func($getPageLink, $larger_page)).'">'.$larger_page.'</a>';
              $larger_page_start++;
            }
        }

        foreach(range($start_page, $end_page) as $i)
          $out[] = ($i == $current_page) ? '<span class="current">'.$i.'</span>' : '<a class="page" href="'.esc_url(call_user_func($getPageLink, $i)).'">'.$i.'</a>';


        if($extended){
          $larger_page_end = 0;
          foreach($larger_pages_array as $larger_page)
            if(($larger_page > $end_page) && ($larger_page_end < $larger_page_to_show)){
              $out[] = '<a class="ext page" href="'.esc_url(call_user_func($getPageLink, $larger_page)).'">'.$larger_page.'</a>';
              $larger_page_end++;
            }
        }

        if($end_page < $total_pages){
          $out[] = '<span class="dots">...</span>';
          $out[] = '<a class="last" href="'.esc_url(call_user_func($getPageLink, $total_pages)).'">'.$total_pages.'</a>';
        }

      }

      if($prev_next && ($current_page < $total_pages))
        $out[] = '<a class="next" href="'.esc_url(call_user_func($getPageLink, $current_page + 1)).'">'.atom()->t('Next &raquo;').'</a>';

    }

    if(!empty($out))
      return "<!-- page navigation -->\n<div class=\"page-navi pagination {$type} {$class} clear-block\">\n".implode("\n", $out)."\n</div>\n<!-- /page navigation -->";

  }



 /*
  * Retrieve external theme styles (style-*.css files from the CSS folder)
  *
  * @since    1.0
  * @return   array   An array containing relevant meta info for each file
  */
  public static function getStyles(){

    $style_headers = array(
      'name'         => 'Style Name',
      'color'        => 'Color',
      'description'  => 'Description',
      'author'       => 'Author',
      'version'      => 'Version',
    );

    $styles = array();
    foreach(glob(TEMPLATEPATH."/css/style-*.css") as $filename)
      if($meta = get_file_data($filename, $style_headers)){
        $meta['id'] = substr(basename($filename, '.css'), 6);
        $styles[] = $meta;
      }

    return $styles;
  }



 /*
  * Retrieve modules located in the theme's "mods" directory
  *
  * @since    1.8
  * @param    string $type   Type of module, 'core' or 'user' (child theme)
  * @return   array          An array containing each file meta info
  */
  public static function getModules($type = false){

    // all types?
    if(!$type)
      return array_merge(atom()->getModules('user'), atom()->getModules('core')); // @todo: reverse?

    $from_where = ($type !== 'core' && is_child_theme()) ? STYLESHEETPATH.'/mods' : TEMPLATEPATH.'/mods';

    $modules = array();
    $module_headers = array(
      'name'        => 'Module Name',
      'description' => 'Description',
      'author'      => 'Author',
      'url'         => 'Author URI',
      'version'     => 'Version',
      'priority'    => 'Priority',
      'auto'        => 'Auto Enable',
    );

    $modules_dir = @opendir($from_where);
    $module_files = array();

    if($modules_dir)
      while(($file = readdir($modules_dir)) !== false){

        if(substr($file, 0, 1) == '.') continue;

        if(is_dir($from_where.'/'.$file)){
          $module_dir = @opendir($from_where.'/'.$file);

          if($module_dir)
            while(($sub_file = readdir($module_dir)) !== false){
              if(substr($sub_file, 0, 1) == '.') continue;

              if(substr($sub_file, -4) == '.php')
                $module_files[] = "{$file}/{$sub_file}";
            }
        }
      }

    else
      return $modules;


    @closedir($modules_dir);
    @closedir($module_dir);

    foreach($module_files as $file){
      if(!is_readable($from_where.'/'.$file)) continue;
      $data = get_file_data($from_where.'/'.$file, $module_headers);

      if(empty($data['name'])) continue;

      $data['type'] = $type;
      $modules[dirname($file)] = $data;
    }

    return $modules;
  }



 /*
  * Checks if a module is running
  *
  * @since    2.0
  * @param    string $name    Module name (ID)
  * @return   bool
  */
  public function isModuleActive($name){
    return isset($this->modules[strtolower($name)]);
  }



 /*
  * Registers all theme hooks and runs typical theme setup routines
  *
  * @since 1.7
  */
  public function setup(){

    // make sure the 'ATOM' constant doesn't contain illegal characters
    if((ATOM === get_stylesheet()) && (preg_replace('/[^A-Za-z0-9_-]/', '', ATOM) !== ATOM))
      $this->log('The theme folder name contains invalid characters! The theme might not work correctly', Atom::WARNING);

    // old wp version?
    if(!atom_register_hooks()) return;

    AtomShortcodes::register();

    // load modules
    // and enable all AutoEnable-type modules from the parent theme dir if the option doesn't exist (first use)
    if(($active_modules = get_option(ATOM.'-atom-mods')) === false){

      $module_data = $this->getModules('core');

      // check if auto-enable is off
      foreach($module_data as $module => $data)
        if(in_array($data['auto'], array('no', 'false', 'off', 'disabled'))) unset($module_data[$module]);

      // sort by 'priority' tag
      uasort($module_data, create_function('$a, $b', 'return ((int)$a["priority"] < (int)$b["priority"]);'));

      $active_modules = array_keys($module_data);

      update_option(ATOM.'-atom-mods', $active_modules);
    }

    if(is_array($active_modules))
      foreach($active_modules as $module){
        $type = false;

        // check (possible) child theme first
        if(is_readable(STYLESHEETPATH.'/mods/'.$module.'/module.php')){
          require(STYLESHEETPATH.'/mods/'.$module.'/module.php');
          $type = is_child_theme() ? 'user' : 'core';

        // parent theme, only check if a child theme is active
        }elseif(is_child_theme() && is_readable(TEMPLATEPATH.'/mods/'.$module.'/module.php')){
          require(TEMPLATEPATH.'/mods/'.$module.'/module.php');
          $type = 'core';
        }

        $class = "AtomMod{$module}";
        if($type && class_exists($class)){
          $this->modules[strtolower($module)] = new $class($module, $type);

        }elseif($type){
          $this->log("&lt;{$class}&gt; class not found. Mod not loaded");

        }else{
          $this->log("&lt;{$class}&gt; is missing files. Mod not loaded");
        }
      }


    // execute functions-user.php
    // some users might not know how to use this feature and because we don't want random characters before html headers,
    // we will store any direct output inside the buffer;
    // any echoes should be done inside functions hooked on filter tags anyway...
    if(!is_admin() && (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') === false) && is_child_theme() && is_readable($this->user_functions)){

      ob_start();
      $this->load($this->user_functions);
      $have_output = ob_get_clean();

      if($have_output)
        $this->log('You have invalid code in your "User Functions" which generates output in the wrong place.', Atom::WARNING);
    }

    // initialize modules
    foreach($this->modules as $module)
      if(method_exists($module, 'onInit')) $module->onInit(); elseif(method_exists($module, 'init')) $module->init(); // @compat

    // synchronize theme options
    $this->syncOptions();

    // register WP editor styles
    add_editor_style('css/editor.css');


    // post thumbnails
    if($this->options('post_thumbs')){

      // set post thumbnail size
      $post_thumb = ($this->options('post_thumb_size') == 'media') ? array(get_option('thumbnail_size_w'), get_option('thumbnail_size_h')) : explode('x', $this->options('post_thumb_size'));
      list($w, $h) = $post_thumb;
      set_post_thumbnail_size($w, $h, true); // same as add_image_size('post-thumbnail' ...);
    }

    // nav menus
    if(!empty($this->menus))
      register_nav_menus($this->menus);

    // adjust content width variable, probably useless...
    if(!isset($GLOBALS['content_width']) && $this->options('page_width') === 'fixed'){
      $primary = explode(';', $this->options('dimensions_fixed_'.$this->options('layout')));
      $primary = empty($primary[0]) ? 960 : (int)$primary[0];
      $GLOBALS['content_width'] = $primary - 20;
    }

    // hooks
    if($this->options('remove_settings'))
      add_action('switch_theme', array($this, 'uninstall'));

    $this->action('core');

    // expose $atom in templates?
    set_query_var('atom', $this);

    define('ATOM_INITIALIZED', true);
  }



 /*
  * Synchronizes theme settings.
  *
  * Options are synced when:
  * - a new theme version is installed
  * - a module is de/activated (if the module has option fields)
  * - theme options from the db are invalid (either missing or extra options)
  *
  * @since 1.0
  */
  protected function syncOptions(){

    // sets up the "current_theme_options" property
    // only needed for the front-end, because within the admin area options() will be called sooner
    $this->options();

    // only go further if the theme version from the database differs from the one in style.css;
    // not really required, because the desync check below should take care of it,
    // but it's here in case new theme versions want to force the update of an existing option value...
    $new_version = version_compare($this->current_theme_options['theme_version'], $this->theme_version, '!=');

    // ...or if option names present in the database are different than the default option names
    $desynced = array_diff_key($this->default_theme_options, $this->current_theme_options) !== array_diff_key($this->current_theme_options, $this->default_theme_options);

    if($new_version || $desynced){

      // save old version info
      $old_version = $this->current_theme_options['theme_version'];

      // highest priority: a new theme version might want to force an existing option value to be updated
      $this->action('sync_options', $old_version, $this->default_theme_options, $this->current_theme_options);

      $new_options = array();

      // check for new settings and load defaults
      // also removes deprecated options...
      foreach($this->default_theme_options as $option => $value)
        $new_options[$option] = isset($this->current_theme_options[$option]) ? $this->current_theme_options[$option] : $value;

      $this->current_theme_options = $new_options;

      // update theme version info
      $this->current_theme_options['theme_version'] = $this->theme_version;

      // update db
      $this->setOptions($this->current_theme_options);

      wp_cache_flush();

      // update permalink structure a little later
      if(!defined('INSTALLING_ATOM'))
        add_action('wp', 'flush_rewrite_rules');

      // full theme update
      if($new_version)
        $this->log("Theme updated {$old_version} => {$this->theme_version}. Settings were synchronized.");

      // just a config update
      else
        $this->log('Theme configuration has changed. Settings were synchronized.');

    }

  }



 /*
  * Get the appropriate layout type for the current page
  *
  * @since    1.0
  * @return   string    The layout type (ID)
  */
  public function getLayoutType(){

    $layout = $this->getCurrentLayout();

    if(!$this->previewMode() && !defined('ATOM_FORCE_LAYOUT')){

      $s1_active = ($layout !== 'c1') ? $this->isAreaActive('sidebar1') : false;
      $s2_active = ($layout !== 'c1' && $layout !== 'c2left' && $layout !== 'c2right') ? $this->isAreaActive('sidebar2') : false;

      // revert to a different layout if the current area is empty (or no widgets are visible)
      if(!$s1_active && ($layout !== 'c1'))
        $layout = 'c1';

      if(!$s2_active && ($layout === 'c3' || $layout === 'c3left'))
        $layout = $s1_active ? 'c2left' : 'c1';

      if(!$s2_active && ($layout === 'c3' || $layout === 'c3right'))
        $layout = $s1_active ? 'c2right' : 'c1';

    }

    return $layout;
  }



 /*
  * Get the status of a widgetized area (replaces wp's is_active_sidebar)
  * This function also verifies if the widgets inside the sidebar are visible to the current user or not (splitters are ignored)
  * Note: If we're in preview mode areas are always considered to be active, but the returned widget count may be 0 (boolean false).
  * (use type equivalence checking to avoid confusions)
  *
  * @since    1.0
  * @global   $wp_registered_widgets   Stored registered widgets.
  * @param    string $index            Area (sidebar) ID
  * @return   int|bool                 False or the visible widget count, based on widget visibility status
  */
  public function isAreaActive($index){
    global $wp_registered_widgets;

    // always return true if the layout options are not enabled. obviously, the css designer wants to handle this
    if(!$this->OptionExists('layout')) return true;

    // default (setCurrentLayout can override this)
    $layout = $this->getCurrentLayout();

    $index = (is_int($index)) ? "sidebar{$index}" : sanitize_html_class($index);
    $sidebars_widgets = wp_get_sidebars_widgets();

    $visible_widgets = 0;

    // only check widget settings if we have widgets in this sidebar
    if(!empty($sidebars_widgets[$index]))
      foreach($sidebars_widgets[$index] as $widget_id)
        if(isset($wp_registered_widgets[$widget_id])){
          $number = $wp_registered_widgets[$widget_id]['params'][0]['number'];

          $callback = AtomWidget::getObject($widget_id);
          if(!$callback) continue;

          $options = get_option($callback->option_name);

          // count only visible widgets that are not "splitters"
          // @important atom_visibility_check() can also return array(),
          // which means that the widget is there but it's settings are missing, or it doesn't have any (like fallback widgets)
          if((strpos($widget_id, 'atom-splitter') === false) && atom_visibility_check($options[$number]) !== false)
            $visible_widgets++;
        }


    // always show active if we're in preview mode (eg. theme setting site preview), regardless of the contents
    if($this->previewMode())
      return $visible_widgets;

    // check free column(s) for sidebar(s)
    if($index === 'sidebar1' && $layout === 'c1')
      return false;

    if($index === 'sidebar2' && in_array($layout, array('c1', 'c2left', 'c2right')))
      return false;

    // get sidebar contents
    $first_check = false;
    if(!isset($this->widget_areas[$index]['output'])){
      ob_start();
      dynamic_sidebar($index);
      $this->widget_areas[$index]['output'] = ob_get_clean();
      $first_check = true;
    }

    // filter cannot be applied to the global variable because the column splitter can make irreversible changes
    $area_contents = apply_filters('atom_area_check', $this->widget_areas[$index]['output'], $index);

    if(empty($area_contents) && $first_check)
      $this->log("No active widgets in &lt;{$index}&gt;. Area disabled.");

    if((!empty($sidebars_widgets[$index]) && ($visible_widgets > 0)) && !empty($area_contents))
      return $visible_widgets;

    return false;
  }



 /*
  * Capture the output of a widget area (sidebar); uses output buffering to allow content filtering.
  * Certain design types (like "Arclite") might need this to correct widget HTML.
  * This function also checks if the number of widget splitters present in the sidebars are even, and adds the closing splitter tags if necessary.
  *
  * @since    1.3
  * @param    string $area    Sidebar ID
  * @return   string|bool     HTML output, or false if sidebar is empty
  */
  public function getWidgets($area){

    // get sidebar contents (this shouldn't run because cache should be set by is_active_area above which is called early)
    if(!isset($this->widget_areas[$area]['output'])){
      ob_start();
      dynamic_sidebar($area);
      $this->widget_areas[$area]['output'] = ob_get_clean();
    }

    $this->widget_areas[$area]['output'] = apply_filters('atom_widget_area', $this->widget_areas[$area]['output'], $area);

    return empty($this->widget_areas[$area]['output']) ? false : $this->widget_areas[$area]['output'];
  }



 /*
  * Get the output of a widget instance
  *
  * @since    1.3
  * @global   array $wp_registered_widgets     Registered widgets
  * @global   array $wp_registered_sidebars    Registered sidebars
  * @param    string $widget_id                The widget instance ID
  * @param    string $area                     Use this sidebar parameters to render the widget (default is 'arbitrary')
  * @param    bool|string $remove_title        Display or hide the title (eg. remove 'h2', 'h3' or false to keep the title)
  * @return   string                           Widget HTML output
  */
  public static function getWidget($widget_id, $area = 'arbitrary', $remove_title = 'h3'){
    global $wp_registered_widgets, $wp_registered_sidebars;

    // does the instance exist?
    $callback = AtomWidget::getCallback($widget_id);

    if(!$callback)
      return atom()->log("Requested widget instance doesn't exist: {$widget_id}");

    ob_start();  // catch the echo output, so we can control where it appears in the text

    $params = array_merge(array(array_merge($wp_registered_sidebars[$area], array('widget_id' => $widget_id, 'widget_name' => $wp_registered_widgets[$widget_id]['name']))), (array)$wp_registered_widgets[$widget_id]['params']);

    // align classes?
    //if($align) $params[0]['before_widget'] = str_replace('block', 'block align'.$align, $params[0]['before_widget']);

    // Substitute HTML id and class attributes into before_widget
    $classname_ = '';
    foreach((array)$wp_registered_widgets[$widget_id]['classname'] as $cn)
      if(is_string($cn)) $classname_ .= '_'.$cn; elseif(is_object($cn)) $classname_ .= '_'.get_class($cn);

    $classname_ = ltrim($classname_, '_');
    $params[0]['before_widget'] = sprintf($params[0]['before_widget'], $widget_id, $classname_);
    $params = apply_filters('dynamic_sidebar_params', $params);

    if(is_callable($callback))
      call_user_func_array($callback, $params);

    // remove h3?
    $widget_contents = $remove_title ? preg_replace('#<'.$remove_title.' class="title">(.*?)</'.$remove_title.'>#', '', ob_get_clean()) : ob_get_clean();

    return apply_filters('atom_widget', $widget_contents, $widget_id); // probably useless filter
  }



 /*
  * Get the current page URL.
  * Most likely not used anymore, but I'm too lazy to check :)
  *
  * @since   1.0
  * @return  string   The URL of the current page
  */
  public static function getCurrentPageURL(){

    $request = esc_url($_SERVER["REQUEST_URI"]);

    // wp-themes fake request url fix :)
    if(strpos($_SERVER["SERVER_NAME"], 'wp-themes.com') !== false)
      $request = str_replace($request, '/wordpress/', '/');

    // ssl?
    $url = (is_ssl() ? 'https' : 'http').'://';

    $url .= ($_SERVER['SERVER_PORT'] != '80') ? "{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}{$request}" : "{$_SERVER['SERVER_NAME']}{$request}";

    // check if the site uses "www" or not
    if(strpos(home_url(), '://www.') === false)
      $url = str_replace('://www.', '://', $url);

    if(strpos(home_url(), '://www.') !== false && strpos($url, '://www.') === false)
      $url = str_replace('://', '://www.', $url);

    return $url;
  }



 /*
  * Create pagination links for the comments on the current post.
  *
  * @see paginate_links()
  *
  * @param string $classes Optional classes
  * @param string|array $args Optional args. See paginate_links.
  */
  public function getCommentNavi($classes = '', $args = array()){
    global $wp_rewrite;

    // no pagination if it's disabled in the options
    if(!get_option('page_comments')) return '';

    $current_page = get_query_var('cpage');

    $links = paginate_links(wp_parse_args($args, array(
      'base'         => $wp_rewrite->using_permalinks() ? user_trailingslashit(trailingslashit(get_permalink()).'comment-page-%#%', 'commentpaged') : add_query_arg('cpage', '%#%'),
      'format'       => '',
      'total'        => $this->post->countCommentPages(),
      'current'      => $current_page ? $current_page : 1,
      'add_fragment' => '#comments'
    )));

    if($links)
      return "<div class=\"page-navi pagination {$classes}\">\n{$links}\n</div>\n";
  }



 /*
  * Renders comment form input fields (excluding the textarea)
  *
  * @since 2.0
  */
  public function getCommentFormFields(){

    ob_start();

    $fields = array();

    // generate author/email/url fields if the user is not logged in
    if(!is_user_logged_in()){

      $labels = array(
        'author' => atom()->t('Name').(get_option('require_name_email') ? ' '.atom()->t('(required)') : ''),
        'email'  => atom()->t('E-mail').(get_option('require_name_email') ? ' '.atom()->t('(required, will not be published)') : atom()->t('(will not be published)')),
        'url'    => atom()->t('Website'),
      );

      // to keep compatibility with plugins
      foreach($labels as $name => $label){
        $value = esc_attr(($name !== 'author') ? $this->commenter["comment_author_{$name}"] :  $this->commenter['comment_author']);

        // prepend label element if clearfield is not available
        if($this->options('jquery')){
          $fields[$name] = '<input type="text" data-default="'.esc_attr($label).'" name="'.$name.'" id="'.$name.'" class="text clearField" value="'.$value.'" size="40" />';

        }else{
          $fields[$name] = '<input type="text" name="'.$name.'" id="'.$name.'" class="text" value="'.$value.'" size="40" />';
          $fields[$name] = '<label for="author">'.$label.'</label> '.$fields[$name];
        }

      }
    }

    // plugins will most likely change this array
    $fields = apply_filters('comment_form_default_fields', $fields);

    // split default fields into a separate array so we can use the toggle js on our fields;
    // that's because we don't want to auto-hide a captcha field...
    $auth_fields = $extra_fields = array();

    foreach($fields as $field => $html)
      if(in_array($field, array('author', 'email', 'url'))) $auth_fields[$field] = $html; else $extra_fields[$field] = $html;

    do_action('comment_form_before_fields'); ?>

    <?php if(!empty($auth_fields) || !empty($extra_fields)): ?>
    <div id="comment-fields">

      <?php if(!empty($auth_fields)): ?>
      <div id="comment-user-auth" <?php if(!empty($this->commenter['comment_author']) && $this->options('jquery')) echo 'class="hidden"'; ?>>
        <?php foreach($auth_fields as $name => $field): ?>
         <div class="clear-block">
           <?php echo apply_filters("comment_form_field_{$name}", $field); ?>
         </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php foreach($extra_fields as $name => $field): ?>
       <div class="clear-block">
         <?php echo apply_filters("comment_form_field_{$name}", $field); ?>
       </div>
      <?php endforeach; ?>

    </div>
    <?php endif; ?>

    <?php

    // compat.
    comment_id_fields();

    do_action('comment_form_after_fields');

    return ob_get_clean();
  }



 /*
  * Renders a template part, like "teaser", "comment", "about-the-author", "meta" etc.
  *
  * See http://codex.wordpress.org/Template_Hierarchy
  * A similar template hierarchy system is implemented here
  *
  * @since   1.7
  * @param   string $name   Template name
  * @param   object $query  WP Query, don't use - experimental
  */
  public function template($name, $query = null){

    // get_header/footer/sidebar compat
    if(in_array($name, array('header', 'footer', 'sidebar', 'searchform'))){
      ($name !== 'searchform') ? do_action("get_{$name}", $name) : do_action('get_search_form');

      // don't load this template if only the content was requested trough a query variable (the single pagination option does this)
      if($this->request('content_only')) return;
    }

    // @todo: improve
    if(strpos($name, 'teaser') !== false){

      // reset current post
      if(is_null($query))
        the_post();

      elseif($query !== false)
        $query->the_post();

      $this->setCurrentPost(false); // will use the global set by the_post()

    }else{
      $this->resetCurrentPost();

    }

    // 3rd party comments template? eg. intense debate
    // then load it, and forget about meta...
    // @note: "Social" plugin uses require instead of require_once in the comments_template hook so we have to run this filter only once...
    if($name === 'meta' && class_exists('Social')){
      comments_template();
      return;

    }elseif($name === 'meta'  && ($_custom_template = apply_filters('comments_template', ''))){
      comments_template($_custom_template);
      return;
    }

    // search for higher priority templates based on the current context
    // eg. meta-post-aside or sidebar-category etc.
    $templates = array();

    // 'page' post type
    if(is_page()){
      $page_id = get_queried_object_id();
      $page_name = get_query_var('pagename');

      if(!$page_name && $page_id > 0){
        // if a static page is set as the front page, $page_name will not be set. Retrieve it from the queried object
        $post = get_queried_object();
        $page_name = $post->post_name;
      }

      if($page_name)
        $templates[] = $name.'-page-'.$page_name;

      if($page_id)
        $templates[] = $name.'-page-'.$page_id;

      $templates[] = $name.'-page';

    // post loop / or single post
    }elseif(in_the_loop() || is_single()){
      $templates[] = $name.'-'.$this->post->getID();
      $templates[] = $name.'-'.$this->post->getType().'-'.$this->post->getFormat();
      $templates[] = $name.'-'.$this->post->getType();
      $templates[] = $name.'-'.$this->post->getFormat();

    // archive page (not in loop)
    }elseif(is_archive()){
      $term = get_queried_object();
      if(isset($term->taxonomy) && isset($term->slug)){
        $templates[] = $name.'-'.$term->taxonomy.'-'.$term->slug;
        $templates[] = $name.'-'.$term->taxonomy;
      }
    }

    $templates[] = $name;

    $location = $this->findTemplate($templates);

    if(!$location)
      return $this->log('Template part not found: "'.$name.'". Skipping...', 1);

    $this->action("before_{$name}");

    // include the template
    $this->load($location);

    if(!in_array($name, array('comment', 'ping')))
      $this->action("after_{$name}");
  }



 /*
  * Replaces locate_template, so we can search in our sub-directory for templates
  *
  * @since   2.0
  * @param   string|array $templates   Template(s) to search
  * @param   string $extension         Optional, php by default
  * @return  string                    Full path to the template that was found
  */
  public static function findTemplate($templates, $extension = 'php'){

    $located = '';

    // find the first available
    foreach((array)$templates as $template){

      $fn = "{$template}.{$extension}";

      // highest priority - child theme 'templates' folder / or parent theme (if a child theme is not active)
      if(file_exists(STYLESHEETPATH.'/templates/'.$fn)){
        $located = STYLESHEETPATH.'/templates/'.$fn;
        break;

      // child/parent theme root folder
      }elseif(file_exists(STYLESHEETPATH.'/'.$fn)){
        $located = STYLESHEETPATH.'/'.$fn;
        break;

      // lowest priority - parent theme 'templates' folder
      }elseif(file_exists(TEMPLATEPATH.'/templates/'.$fn)){
        $located = TEMPLATEPATH.'/templates/'.$fn;
        break;
      }
    }

    return $located;
  }



 /*
  * Loads a .php file, in a variable-clear environment
  * $app is exposed for compatibility with Atom < 2.0; atom() should be used instead
  *
  * @since   2.0
  * @param   string $_file   File to include
  * @param   array $_args    Optional arguments
  */
  public static function load($_file, $_args = array()){

    // for compatibility -- there's old code with $app in the forums for the old Mystique, and people might use it...
    $app = atom();

    if($_args)
      extract($_args);

    require $_file;
  }



 /*
  * Renders a simple block template (really basic templating system for widgets). Replaces {VAR} with $parameters['var'];
  * original was from: Fabien Potencier's "Design patterns revisited with PHP 5.3" (page 45), but this version is slightly faster
  *
  * @param    string $string
  * @param    array $parameters
  * @return   string
  */
  public static function getBlockTemplate($string, $parameters = array()){

    $searches = $replacements = array();

    // replace {KEYWORDS} with variable values
    foreach($parameters as $find => $replace){
      $searches[] = '{'.$find.'}';
      $replacements[] = $replace;
    }

    return str_replace($searches, $replacements, $string);
  }



  /**
   * Create nonce
   *
   * @since 1.3
   * @param string $req
   */
  public function getNonce($req){
    return wp_create_nonce("atom_{$req}");
  }




 /*
  * Return a list of sites for the current network (replaces old get_blog_list())
  * based on http://core.trac.wordpress.org/ticket/14511
  *
  * @since    1.3
  * @global   $wpdb                WP database
  * @param    array|string $args   Optional. Override default arguments.
  * @return   array                List of sites
  */
  public static function getSites($args = array()){
    global $wpdb;

    $defaults = array(
      'exclude_id'        => '',             // excludes these sites from the results, comma-delimted
      'blogname_like'     => '',             // domain or path is like this value
      'reg_date_since'    => '',             // sites registered since (accepts pretty much any valid date like tomorrow, today, 5/12/2009, etc.)
      'reg_date_before'   => '',             // sites registered before
      'exclude_user_id'   => '',             // don't include sites owned by these users, comma-delimited
      'exclude_archived'  => false,          // exclude archived sites
      'exclude_spam'      => false,          // exclude spammy sites
      'exclude_mature'    => false,          // exclude blogs marked as mature
      'public_only'       => true,           // Include only blogs marked as public
      'sort_column'       => 'last_updated', // or registered, last_updated, site_id
      'order'             => 'ASC',          // or desc
      'limit_results'     => false,          // return this many results
      'start'             => 0,              // return results starting with this item
    );

    // array_merge
    $r = wp_parse_args($args, $defaults);
    extract($r, EXTR_SKIP);

    $query = "SELECT {$wpdb->blogs}.blog_id, {$wpdb->blogs}.domain, {$wpdb->blogs}.path, {$wpdb->blogs}.registered, {$wpdb->blogs}.last_updated, {$wpdb->blogs}.public, {$wpdb->blogs}.archived, {$wpdb->blogs}.mature, {$wpdb->registration_log}.email FROM {$wpdb->blogs}, {$wpdb->registration_log} WHERE site_id = '{$wpdb->siteid}' AND {$wpdb->blogs}.blog_id = {$wpdb->registration_log}.blog_id ";

    if(!empty($exclude_id))
      $query .= " AND {$wpdb->blogs}.blog_id NOT IN ('".implode("','", explode(',', $exclude_id))."') ";

    if(!empty($blogname_like))
      $query .= " AND ({$wpdb->blogs}.domain LIKE '%{$blogname_like}%' OR {$wpdb->blogs}.path LIKE '%{$blogname_like}%') ";

    if(!empty($reg_date_since))
      $query .= " AND unix_timestamp({$wpdb->registration_log}.date_registered) > '".strtotime($reg_date_since)."' ";

    if(!empty($reg_date_before))
      $query .= " AND unix_timestamp({$wpdb->registration_log}.date_registered) < '".strtotime($reg_date_before)."' ";

    if(!empty($exclude_user_id)){
      $the_users = explode(',', $exclude_user_id);
      $the_emails = array();
      foreach((array)$the_users as $user_id){
        $the_user = get_userdata($user_id);
        $the_emails[] = $the_user->user_email;
       }
      $list = implode("','", $the_emails);
      $query .= " AND {$wpdb->registration_log}.email NOT IN ('{$list}') ";
    }

    if($public_only) $query .= " AND {$wpdb->blogs}.public = '1'";
    if($exclude_archived) $query .= " AND {$wpdb->blogs}.archived = '0'";
    if($exclude_spam) $query .= " AND {$wpdb->blogs}.spam = '0'";
    if($exclude_mature) $query .= " AND {$wpdb->blogs}.mature = '0'";

    if($sort_column == 'id')
      $query .= " ORDER BY {$wpdb->blogs}.blog_id ";
    elseif($sort_column == 'last_updated')
      $query .= " ORDER BY last_updated ";
    elseif($sort_column == 'registered')
      $query .= " ORDER BY {$wpdb->blogs}.registered ";


    $order = ('DESC' == $order) ? 'DESC' : 'ASC';
    $query .= $order;

    $limit = '';
    if($limit_results){
      if($start !== 0) $limit = $start.", ";
      $query .= ' LIMIT '.$limit.$limit_results;
    }

    // check cache
    $key = md5($query);
    $cache = wp_cache_get('get_sites', 'atom');
    if(!isset($cache[$key])){
      $entries = $wpdb->get_results($query, ARRAY_A);
      $cache[$key] = $entries;
      wp_cache_set('get_sites', $cache, ATOM);

    }else{
      $entries = $cache[$key];
    }

    return empty($entries) ? array() : $entries;
  }



 /*
  * Get the user avatar based on his email address
  *
  * @since     1.0
  * @param     string $email      Valid Email Address
  * @param     int $size          Optional. Image size
  * @param     string $default    Default image
  * @param     string $alt        Alternate text
  * @return    string             Avatar image (HTML)
  */
  public function getAvatar($email, $size = 48, $default = '', $alt = false){

    // if not, display the user's gravatar
    return get_avatar($email, $size, $default, $alt);
  }



  /**
   * Number of posts/comments user has written.
   * Can count custom post types
   *
   * @since 1.3
   * @uses $wpdb WordPress database object for queries.
   * @param int $user_id (optional) Count only posts/comments of a user
   * @param int $what_to_count Post types or comments, Default is "post"
   *
   * @return int count
   */
  public static function getCount($user_id = null, $what_to_count = 'post') {
    global $wpdb;

    if(strtoupper($user_id) == 'ALL')
      $user_id = null;

    $where = $what_to_count == 'comment' ? "WHERE comment_approved = 1" : get_posts_by_author_sql($what_to_count, true, $user_id);

    if(!empty($user_id) && $what_to_count == 'comment')
      $where .= sprintf(' AND user_id = %d', (int)$user_id);

    $from = "FROM ".(($what_to_count == 'comment') ? $wpdb->comments : $wpdb->posts);

    $query = "SELECT COUNT(*) {$from} {$where}";

    // check cache
    $key = md5($query);
    $cache = wp_cache_get('get_count', 'atom');
    if(!isset($cache[$key])){
      $count = $wpdb->get_var($query);
      $cache[$key] = $count;
      wp_cache_set('get_count', $cache, ATOM);

    }else{
      $count = $cache[$key];
    }

    return apply_filters("atom_user_{$what_to_count}_count", (int)$count, $user_id);
  }



 /*
  * Display or retrieve the HTML dropdown list of terms or posts.
  * Replaces wp_dropdown_categories() and wp_dropdown_pages.
  * Almost same behaviour, the only difference is the "extra_attributes" argument (to allow the use of form dependency rules),
  * and more flexibility with custom post types/taxonomies
  *
  * @since   1.4
  * @param   array $what   Post type or taxonomy
  * @param   array $args   Arguments
  * @return  string        HTML
  */
  public static function getDropdown($what = 'category', $args = array()){

    $defaults = array(
      'show_option_all'    => '',
      'show_option_none'   => '',
      'show_last_update'   => 0,           // useless option that throws a notice if not present; deprecated maybe?
      'option_none_value'  => -1,
      'orderby'            => 'id',
      'order'              => 'ASC',
      'show_count'         => 0,           // tax only
      'hide_empty'         => 1,           // tax only
      'hide_if_empty'      => false,
      'child_of'           => 0,           // tax only
      'post_parent'        => '',          // posts only
      'exclude'            => '',
      'selected'           => 0,
      'hierarchical'       => 1,
      'numberposts'        => -1,          // posts only
      'name'               => 'category',
      'id'                 => '',
      'class'              => '',
      'depth'              => 0,
      'tab_index'          => 0,
      'extra_attributes'   => '',          // like dependency rules
      'pad_counts'         => true,        // tax only, hierarchical must be true
    );

    if(array_key_exists($what, get_taxonomies())){
      $defaults['taxonomy'] = $what;
      $_is_tax = true;

    }else{
      $defaults['post_type'] = $what;
      $_is_tax = false;
    }

    // current category selected by default ($args overrides it)
    if($_is_tax) $defaults['selected'] = (is_category()) ? get_query_var('cat') : 0;

    $r = wp_parse_args($args, $defaults);

    extract($r);
    unset($r['name'], $r['id'], $r['class']); // conflicts with get_posts(), possible others too

    $tab_index_attribute = ((int)$tab_index > 0) ? " tabindex=\"{$tab_index}\"" : '';
    $entries = $_is_tax ? get_terms($taxonomy, $r) : get_posts($r);

    $name = esc_attr($name);
    $class = $class ? ' class="'.esc_attr($class).'"' : '';
    $id = $id ? ' id="'.esc_attr($id).'"' : '';

    // no entries, return if hide_if_empty is enabled
    if(empty($entries) && $hide_if_empty) return false;

    $output = "<select name=\"{$name}\" {$id} {$class} {$extra_attributes} {$tab_index_attribute}>\n";
    if(empty($entries) && !empty($show_option_none)){
      $show_option_none = apply_filters('list_cats', $show_option_none);
      $output .= "\t<option value=\"".esc_attr($option_none_value)."\" selected=\"selected\">{$show_option_none}</option>\n";
    }

    if(!empty($entries)){
      if($show_option_all){
        $show_option_all = apply_filters('list_cats', $show_option_all);
        $selected = ('0' === strval($r['selected'])) ? ' selected="selected"' : '';
        $output .= "\t<option value='0'{$selected}>{$show_option_all}</option>\n";
      }
      if($show_option_none){
        $show_option_none = apply_filters('list_cats', $show_option_none);
        $selected = ('-1' === strval($r['selected'])) ? ' selected="selected"' : '';
        $output .= "\t<option value='-1'{$selected}>{$show_option_none}</option>\n";
      }
      $depth = $hierarchical ? $r['depth'] : -1; // // walk the full depth or flat
      $output .= $_is_tax ? walk_category_dropdown_tree($entries, $depth, $r) : walk_page_dropdown_tree($entries, $depth, $r);
    }

    $output .= "</select>\n";
    $output = apply_filters($_is_tax ? 'wp_dropdown_pages' : 'wp_dropdown_cats', $output);

    return $output;
  }



 /*
  * Displays control links, like 'post-edit', 'post-print', 'comment-reply' etc.
  *
  * @since    1.6
  * @param   string $args   Comma separated arguments...
  * @return  string         HTML / Links
  */
  public function getControls(){
    $args = func_get_args();
    $args = array_values($args);

    $links = array();

    foreach($args as $arg){
      list($object, $control) = explode('-', $arg);
      $control = apply_filters("atom_{$object}_{$control}_control", $this->$object->getControl($control));

      if($control){

        $classes = isset($control['class']) ? $control['class'].' '.$arg : $arg;

        $links[$arg] = '<a href="'.(isset($control['target']) ? $control['target'] : '#').'" class="button small '.$classes.'"';

        if(isset($control['data']))
          foreach($control['data'] as $key => $value)
            $links[$arg] .= " data-{$key}=\"{$value}\"";

        if(isset($control['id']))
          $links[$arg] .=' id="'.$control['id'].'"';

        $links[$arg] .= '>'.$control['label'].'</a>';
      }
    }

    if(!empty($links))
      return "<div class=\"controls\">\n".implode("\n", $links)."</div>\n";
  }



 /*
  * Get user arguments for a specific context
  * This can replace apply_filters when we need to globally change arguments somewhere in the code.
  * @todo -- maybe -- add a priority system?
  *
  * @since    1.7
  * @param    string $context       Context. eg. 'pagenavi'
  * @param    array $current_args   Current arguments (can pass multiple parameters, they will all be merged)
  * @return   array                 New arguments
  */
  public function getContextArgs($context){

    $args = func_get_args();
    $args = array_values($args);

    if(isset($this->context_config[$context]))
      $args[] = $this->context_config[$context];

    array_shift($args); // drop the $context argument

    $current_args = array();

    foreach($args as $entry)
      if(!empty($entry))
        $current_args = (array)$entry + $current_args;

    return $current_args;
  }



 /*
  * Set user arguments for a specific context.
  * Overrides all existing arguments in the context config array
  *
  * @since    1.7
  * @param    string $context    Context
  * @param    array $args      Arguments
  */
  public function setContextArgs($context, $args){
    $this->context_config[$context] = $args;
  }



 /*
  * Add user arguments for a specific context.
  * Existing arguments with the same name as replaced.
  *
  * @since    1.7
  * @param    string $context    Context
  * @param    arrayons $args     Arguments
  * @return   array              All arguments for the given context
  */
  public function addContextArgs($context, $args){

    $current_args = isset($this->context_config[$context]) ? $this->context_config[$context] : array();
    $this->context_config[$context] = $args + $current_args;

    return $this->context_config[$context];
  }



 /*
  * Check if a shortcode has already been registered.
  * We don't want to interfere with shortcodes registered by plugins.
  *
  * @since    2.0
  * @param    string $tag        Shortcode Tag
  * @param    string $callback   Callback to ignore, optional
  * @return   bool
  */
  public static function ShortcodeExists($tag, $callback = false){
    global $shortcode_tags;

    if(!$callback)
      return isset($shortcode_tags[$tag]);

    // check callback too?
    return
      isset($shortcode_tags[$tag]) && ($shortcode_tags[$tag] !== $callback);
  }



 /*
  * Generates breadcrumb trails based on the current page location
  *
  * @since    2.0
  * @param    string $separator      Item separator
  * @param    bool $show_home        Hide or show the home link
  * @param    array $args            Advanced arguments, see below
  * @return   string                 Breadcrumb navigation (HTML)
  */
  public static function getBreadcrumbs($separator = '&raquo;', $show_home = true, $args = array()){

    // advanced arguments
    $args = atom()->getContextArgs('breadcrumbs', wp_parse_args($args, array(
      'home_label'       => ($front_id = get_option('page_on_front')) ? get_the_title($front_id) : atom()->t('Home'),
      'ignore_labels'    => array('page', 'post', 'category'),
      'item_title_limit' => 24,
    )));

    extract($args);

    if(class_exists('bbPress') && is_bbpress())
      return bbp_get_breadcrumb(array(
        'before'        => '<div class="breadcrumbs clear-block">',
        'after'         => '</div>',
        'sep'           => $separator,
        'include_home'  => $show_home,
        'home_text'     => $home_label,
      ));


    $parts = array();

    // determine if the front page is a static page
    $posts_page = (get_option('show_on_front') === 'page') ? (int)get_option('page_for_posts') : 0;

    // search results
    if(is_search()){
      $parts[] = atom()->t('Search: %s', get_search_query());

    // nothing on home page (blog or static) and 404 pages
    }elseif(is_404() || is_front_page()){
      return '';

    // display real page title on the posts page (if it's not the front page)
    }elseif(is_home() && $posts_page){
      $parts[] = atom()->post($posts_page)->getTitle($item_title_limit);

    // date archives
    }elseif(is_date()){
      $format = '';
      if(is_month()) $format = 'F, Y'; elseif(is_year()) $format = 'Y';

      // check if we have a page that uses the "archive" template and add it in the breadcrumb
      if($page_id = atom()->getPageByTemplate('archives')){
        $parts[] = get_the_date($format);

        $page = atom()->post($page_id);
        $parts[] = array($page->getURL(), $page->getTitle());

      }else{
        $parts[] = atom()->t('Archives: %s', get_the_date($format));

      }

    // everything else (singular & archives)
    }else{

      $object = 'post';

      // author page?
      if(is_author()){
        $parts[] = atom()->author->getName();

      }else{
        // determine what kind of object is this
        if(is_category() || is_tag() || is_tax())
          $object = 'term';

        $object_type = atom()->$object->getType();
        $settings = ($object !== 'term') ? get_post_type_object($object_type) : get_taxonomy($object_type);

        if(!is_post_type_archive()){

          // don't show prefix on these post types / taxonomies
          if(empty($settings->has_archive) && !in_array($object_type, (array)$ignore_labels, true))
            $parts[] = sprintf('%s: %s', $settings->labels->singular_name, atom()->$object->getTitle($item_title_limit));

          else
            $parts[] = atom()->$object->getTitle($item_title_limit);

          // find term taxonomy, or post type if this is a post object
          if(atom()->$object->getParent() !== 0){
            $ancestors = get_ancestors(atom()->$object->getID(), $object_type);

            foreach($ancestors as $ancestor){
              $item = atom()->$object($ancestor, $object_type);
              $parts[] = array($item->getURL(), $item->getTitle($item_title_limit));
            }
          }
        }
      }

      // check if we have a page that uses the "tag cloud" template and add it in the breadcrumb
      if(is_tag() && ($page_id = atom()->getPageByTemplate('tags')))
        $parts[] = array(atom()->post($page_id)->getURL(), atom()->post($page_id)->getTitle());

      // if this a single post / category / tag archive append the posts page, but only if it's not the front page
      if((is_category() || is_tag() || is_singular(array('post'))) && $posts_page){
        $parts[] = array(atom()->post($posts_page)->getURL(), atom()->post($posts_page)->getTitle());

      // if this is a custom post type and it has an archive page, append the archive page link / title
      }elseif(!empty($settings->has_archive)){
        if(is_post_type_archive())
          $parts[] = apply_filters('post_type_archive_title', $settings->labels->name);

        else
          $parts[] = array(get_post_type_archive_link($object_type), apply_filters('post_type_archive_title', $settings->labels->name));
      }

    }

    // append the home item if requested
    if($show_home)
      $parts[] = array(home_url('/'), $home_label);

    $output = array();

    // build html
    foreach(array_reverse($parts) as $index => $part){
      $url = '';
      $title = $part;

      if(is_array($part))
        list($url, $title) = $part;

      $item = $url ? '<a href="'.$url.'">'.$title.'</a>' : '<span class="active">'.$title.'</span>';
      $output[] = apply_filters('atom_breadcrumb_item', $item, $url, $title);
    }

    return apply_filters('atom_breadcrumbs', '<div class="breadcrumbs clear-block">'.implode(" {$separator} ", $output).'</div>');
  }



 /*
  * Queues a log message
  *
  * @since    1.3
  * @param    string $message    Message to add to the log queue
  * @param    int $code          Code
  * @return   bool               Always false
  */
  public function log($message, $code = Atom::NOTICE){

    if($this->options('debug') && current_user_can('edit_theme_options'))
      $this->log_messages[$code][] = $message;

    // required because in some cases we use this function as a return value for empty widgets etc.
    return false;
  }



 /*
  * Closes the HTML document
  *
  * @since 1.3
  */
  public function end(){

    // debug info?
    if($this->options('debug') && current_user_can('edit_theme_options') && !$this->previewMode()){

      if(!ATOM_DEV_MODE && true === false){  // disabled for now... until the html5 validator is not experimental anymore :)
        // html validation
        $url = 'http://validator.w3.org/check?uri='.urlencode($this->getCurrentPageURL());
        $response = wp_remote_retrieve_body(wp_remote_request("{$url}&output=soap12"));

        // we could use simplexml for more accuracy here -- @todo ?
        foreach(explode("\n", $response) as $line)
          if((strpos($line, 'm:errorcount') !== false) && ($errors = (int)trim(strip_tags($line))))
            $this->log('Found '.$errors.' markup error(s) in the current page. Please <a href="'.$url.'" target="_blank">validate</a> your HTML, otherwise this page might display incorrectly in some browsers.', Atom::WARNING);
      }

      // page load & db requests info
      $this->log(do_shortcode('[load]'));

      if(!empty($this->log_messages)): ?>
        <style>
          #debug{color:#999;background:#252628;font:14px Monaco, Andale Mono, monospace;position:fixed;width:400px;height:32px;overflow:hidden;left:20px;top:60px;z-index: 100;opacity: 0.25;-webkit-transition: 0.15s linear all;-moz-transition: 0.15s linear all;-ms-transition: 0.15s linear all;-o-transition: 0.15s linear all;transition: 0.15s linear all;}
          #debug:hover{opacity:1;-webkit-transition: 0.15s linear all;-moz-transition: 0.15s linear all;-ms-transition: 0.15s linear all;-o-transition: 0.15s linear all;transition: 0.15s linear all;width:70%;height:400px;}
          #debug p{padding:7px 10px;margin:0;}
          #debug p.lead{background: #000;color:#ddd;font-weight:bold;}
          #debug p.warning{background: #a2281d;color:#ffa074;}
          #debug p.warning.even{background: #b02e21;}
          #debug p.notice.even{background: #2b2c2e;}
          #debug a{color:#fff;text-decoration:underline;}
          #page #debug{display:none;} /* to avoid flicker before messages are appended to the <body> */
        </style>
        <div id="debug">
          <p class="lead">THEME DEBUG INFO (Turn this off when you're done!)</p>
          <?php

            if(isset($this->log_messages[Atom::WARNING]))
              foreach($this->log_messages[Atom::WARNING] as $key => $message)
                echo '<p class="warning '.(($key %2) ? 'even' : '').'">'.$message.'</p>';

            if(isset($this->log_messages[Atom::NOTICE]))
              foreach($this->log_messages[Atom::NOTICE] as $key => $message)
                echo '<p class="notice '.(($key %2) ? 'even' : '').'">'.$message.'</p>';
          ?>
        </div>
        <script>
          /* <![CDATA[ */
          document.body.insertBefore(document.getElementById('debug'), document.body.firstChild);
          /* ]]> */
        </script>
        <?php
      endif;
    }
  }

}






/*
 * Atom Objects.
 *
 * @since 1.0
 */
abstract class AtomObject{

  protected
    $data       = null,
    $controls   = array();  // links such as "print", "edit", "more" etc.



  public function __call($name, $args = array()){

    $getter = "get{$name}";

    if(method_exists($this, $getter)){
       echo count($args) ? call_user_func_array(array($this, $getter), $args) : $this->$getter();
       return;
    }

    if(defined('ATOM_COMPAT_MODE')){
      // method doesn't exist, check if it has been deprecated
      $deprecated_name = '__'.__CLASS__.$name;
      if(function_exists($deprecated_name))
        return call_user_func_array($deprecated_name, $args); // call the compat function
    }

    // not deprecated, throw the error
    throw new Exception("Method {$name} is not defined");
  }



  /**
   * __get magic method
   *
   * @since 1.0
   */
  public function __get($name){

    $getter = "_get{$name}";

    if(method_exists($this, $getter))
      return $this->$getter();

    throw new Exception("Property {$getter} is not defined.");
  }



  /**
   * __set magic method
   *
   * @since 1.0
   */
  public function __set($name, $value){

    $setter = "_set{$name}";
    if(method_exists($this, $setter))
      return $this->$setter($value);

    throw new Exception("Property {$setter} is not defined.");
  }



  /**
   * Get a $data field
   *
   * @since 1.0
   * @param mixed $field field to get
   * @return mixed field value
   */
  public function get($field){
    return (isset($this->data->$field)) ? $this->data->$field : false;
  }



  /**
  * Get the blog ID of the current object.
  * Only relevant within an AtomSWC query
  *
  * @since 1.7
  */
  function getBlogID(){

    if(isset($this->data->blog_id))
      return $this->data->blog_id;

    return get_current_blog_id();
  }



  /**
   * Get a control link
   *
   * @since 1.0
   * @param mixed $field field to get
   * @return array control
   */
  public function getControl($control){
    return (isset($this->controls[$control])) ? $this->controls[$control] : false;
  }

}





/*
 * Term object (like category or tag)
 *
 * @since 2.0
 */
class AtomObjectTerm extends AtomObject{

  protected
    $taxonomy     = '',
    $description  = false,
    $url          = false;


 /*
  * Constructor.
  * If no term is provided, we're assuming that this is a category/tag/tax archive and use the query var
  *
  * @since    2.0
  * @param    mixed $user_term   Term ID or object containing term properties
  * @param    string $taxonomy   Term taxonomy
  */
  public function __construct($user_term = false, $taxonomy = 'category'){

    // ID provided
    if(is_numeric($user_term)){
      $this->data = get_term($user_term, $taxonomy);

      // doesn't exist
      if(is_wp_error($this->data))
        throw new Exception("Failed to get term {$user_term}");

    // If an object is provided, assume it's a term
    }elseif(is_object($user_term)){
      $this->data = $user_term;
      $taxonomy = $this->data->taxonomy;

    // no valid value given, try to get the current category / tag / tax. term
    }else{

      // category page?
      if(is_category()){
        $taxonomy = 'category';
        $term_id = (int)get_query_var('cat');
      }

      // tag page?
      elseif(is_tag()){
        $taxonomy = 'post_tag';
        $term_id = (int)get_query_var('tag_id');
      }

      // custom taxonomy page?
      elseif(is_tax()){
        $taxonomy = get_query_var('taxonomy');
        $term_id = (int)get_query_var('term');
      }

      // if we have and ID, get the term
      if(isset($term_id))
        $this->data = get_term($term_id, $taxonomy);

      // no term, or no taxonomy
      if(!$taxonomy || is_wp_error($this->data))
        throw new Exception('You are not using this method in the right context.');

    }

    $this->taxonomy = $taxonomy;
  }



 /*
  * Get term ID
  *
  * @since    2.0
  * @return   int
  */
  public function getID(){
    return (int)$this->data->term_id;
  }



 /*
  * Get term taxonomy ID
  *
  * @since    2.0
  * @return   string
  */
  public function getTaxonomy(){
    return $this->taxonomy;
  }



 /*
  * Alias for getTaxonomy
  *
  * @since    2.0
  * @return   string
  */
  public function getType(){
    return $this->getTaxonomy();
  }



 /*
  * Get and format the term name
  *
  * @since    2.0
  * @param    int $character_limit   Limit to this number of characters
  * @return   string
  */
  public function getName($character_limit = 0){

    $name = esc_attr($this->data->name);

    // limit number of characters and append '...' if necessary
    if($character_limit > 0 && (mb_strlen($name) > $character_limit))
      return mb_substr($name, 0, $character_limit).'...';

    return $name;
  }



 /*
  * Alias for getName, should be used for pages headings.
  *
  * @since    2.0
  * @return   string
  */
  public function getTitle($character_limit = 0){

    $title = $this->getName($character_limit);

    if(is_category())
      $title = apply_filters('single_cat_title', $title);

    elseif(is_tag())
      $title = apply_filters('single_tag_title', $title);

    elseif(is_tax())
      $title = apply_filters('single_term_title', $title);

    return $title;
  }



 /*
  * Get term URL
  *
  * @since    2.0
  * @return   string
  */
  public function getURL(){
    if($this->url === false && !is_wp_error($url = get_term_link($this->getID(), $this->taxonomy)))
      $this->url = $url;

    return $this->url;
  }


 /*
  * Get and format term description
  *
  * @since    2.0
  * @param    int $character_limit    Limit to this number of characters (0 means no limit)
  * @param    bool $html              Allow HTML, false by default
  * @param    array $args             Arguments to pass to generateExcerpt(), only valid if HTML is allowed
  * @return   string                  Formatted term description
  */
  public function getDescription($character_limit = 0, $html = false, $args = array()){

    if($this->description === false)
      $this->description = term_description($this->getID(), $this->taxonomy);

    $description = $html ? $this->description : strip_tags($this->description);

    if($character_limit > 0){
      if(!$html && (mb_strlen($description) > $character_limit))
        return mb_substr($description, 0, $character_limit).'...';

      if($html)
        return atom()->generateExcerpt($character_limit, $description, $args);
    }

    return $description;
  }



 /*
  * Get post count
  *
  * @since    2.0
  * @return   int
  */
  public function getPostCount(){
    return (int)$this->data->count;
  }



 /*
  * Get term parent
  *
  * @since    2.0
  * @return   int
  */
  public function getParent(){
    return (int)$this->data->parent;
  }

}




/*
 * The post object
 *
 * @since 1.0
 */
class AtomObjectPost extends AtomObject{

  protected
    $post_format              = false,
    $post_title               = false,
    $post_url                 = false,
    $post_author              = false,    // post author
    $post_thumb               = array(),  // caches requested thumb sizes
    $available_thumb_sizes    = array(),
    $post_terms               = array(),  // caches requested taxonomy terms
    $post_views               = false,
    $comment_list             = false,    // stores the comment list for the current post
    $meta                     = array(),  // requested post meta
    $related_posts            = false,    // caches the related posts array
    $multipaged               = null,     // post has multiple pages
    $next                     = null,
    $previous                 = null;



  public function __construct($user_post = false, $setup_globals = true){
    global $post;

    if(is_numeric($user_post)){
      $this->data = &get_post($user_post);

      if(!is_object($this->data))
        throw new Exception("Failed to get post {$user_post}");

      // stupid WP globals :(
      // @note: we must explicitly check for a "true" value because getBreadcrumb might send the object type as the 2nd argument, which evaluates as false...
      if($setup_globals === true){
        $post = $this->data;
        setup_postdata($post);
      }

    }elseif(is_object($user_post)){
      $this->data = $user_post;

      if($setup_globals === true){
        $post = $this->data;
        setup_postdata($post);
      }

    }else{
      $this->data = &$post;

    }

    if($this->data){

      // set up control links
      if($url = get_edit_post_link($this->data->ID)) // get_edit_post_link handles permissions...
        $this->controls['edit'] = array(
          'target' => $url,
          'label'  => atom()->t('Edit'),
        );

    }
  }


  public function getData(){
    return $this->data;
  }



  public function getFormat(){
    if($this->post_format === false)
      $this->post_format = get_post_format($this->data->ID);

    return $this->post_format;
  }


  public function getType(){
    return $this->data->post_type;
  }


  public function getID(){
    return $this->data->ID;
  }


  public function getParent(){
    return $this->data->post_parent;
  }

  public function getMeta($field, $single = true){
    if(!isset($this->meta[$field])){
      $value = get_post_meta($this->getID(), $field, $single);
      $this->meta[$field] = empty($value) ? false : $value;
    }

    return $this->meta[$field];
  }



  public function isMultipaged(){

    if(!isset($this->multipaged))
      $this->multipaged = (strpos($this->data->post_content, '<!--nextpage-->') !== false);

    return $this->multipaged;
  }



 /*
  * Get multipart page URL
  *
  * @since     2.0
  * @param     int $page   Page to get
  * @return    string
  */
  public function getPageLink($page = 1){
    global $wp_rewrite;

    $url = $this->getURL();

    // @see: http://core.trac.wordpress.org/ticket/16973
    if($this->isMultipaged() && ($page !== 1)){
      if(get_option('permalink_structure') == '' || in_array($this->data->post_status, array('draft', 'pending')))
        $url = add_query_arg('page', $page, $url);

      elseif(get_option('show_on_front') == 'page' && get_option('page_on_front') == $this->getID())
        $url = trailingslashit($url).user_trailingslashit($wp_rewrite->pagination_base."/{$page}", 'single_paged');

      else
        $url = trailingslashit($url).user_trailingslashit($page, 'single_paged');
   }

   return $url;
 }



 /*
  * Generate multipart pagination
  *
  * @since     2.0
  * @param     array $args Arguments, see Atom::getPagination
  * @return    string
  */
  public function getPagination($args = array()){
    global $numpages;

    if($this->isMultipaged()){

      // determine the number of pages, if setup_postdata() wasn't called to do it
      if(empty($numpages))
        $nummpages = substr_count('<!--nextpage-->', $this->data->post_content);

      $args = wp_parse_args($args, array(
        'type'          => 'numbers',
        'status'        => true,
        'prev_next'     => false,
        'object'        => $this,
        'current_page'  => max(1, absint(get_query_var('page'))),
        'total_pages'   => max(1, $numpages),
      ));

      return atom()->getPagination($args);
    }

    return '';
  }



 /*
  * Generates next/previous post links relative to this post
  *
  * @since     2.0
  * @return    string
  */
  public function getAdjacentPostLinks(){

    $next = $this->getAdjacentPost();
    $prev = $this->getAdjacentPost(array('direction' => 'prev'));

    if(!$next && !$prev)
      return false;

    $output = array();
    $output[] = '<div class="post-links clear-block">';

    if($prev)
      $output[] = '<a class="prev" href="'.$prev->getURL().'">&laquo; '.$prev->getTitle().'</a>';

    if($next)
      $output[] = '<a class="next" href="'.$next->getURL().'">'.$next->getTitle().' &raquo;</a>';

    $output[] = '</div>';

    return implode("\n", $output);
  }



  public function _getNext(){
    if(!isset($this->next))
      $this->next = $this->getAdjacentPost();

    return $this->next;
  }


  public function _getPrevious(){
    if(!isset($this->next))
      $this->next = $this->getAdjacentPost(array('direction' => 'prev'));

    return $this->next;
  }


  public function next($args = array()){
    return $this->getAdjacentPost($args);
  }

  public function previous($args = array('direction' => 'prev')){
    return $this->getAdjacentPost($args);
  }


 /*
  * Get adjacent post
  *
  * @since 2.0
  * @param array $args Options
  * @return object
  */
  protected function getAdjacentPost($args = array()){
    global $wpdb;

    $args = wp_parse_args($args, array(
      'direction'    => 'next',
      'same_terms'   => false,
      'taxonomy'     => '',
    ));

    extract($args);

    $post_type = $this->getType();
    $connected_taxonomies = get_object_taxonomies($post_type);

    $current_post_date = $this->data->post_date;

    $join = '';
    $post_tax = '';

    if($connected_taxonomies && $same_terms){

      $join = "INNER JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";

      if(empty($taxonomy))
        $taxonomy = array_shift($connected_taxonomies);

      if(!in_array($taxonomy, $connected_taxonomies))
        throw new Exception("The {$post_type} post-type is not connected to the  {$taxonomy} taxonomy!");

      if($same_terms){
        $term_ids = wp_get_object_terms($this->data->ID, $taxonomy, array('fields' => 'ids'));
        $join .= " AND tt.taxonomy = '{$taxonomy}' AND tt.term_id IN (" . implode(',', $term_ids) . ")";
      }

      $post_tax = "AND tt.taxonomy = '{$taxonomy}'";

    }

    $op = ($direction !== 'next') ? '<' : '>';
    $order = ($direction !== 'next') ? 'DESC' : 'ASC';

    $where = $wpdb->prepare("WHERE p.post_date {$op} %s AND p.post_type = %s AND p.post_status = 'publish' {$post_tax}", $current_post_date, $post_type);
    $query = "SELECT p.* FROM $wpdb->posts AS p {$join} {$where} ORDER BY p.post_date {$order} LIMIT 1";

    $cache_key = 'atom_adjacent_post_'.md5($query);
    $result = wp_cache_get($cache_key, 'counts');

    if($result === false){
      $result = $wpdb->get_row($query);

      if($result === null)
        $result = '';

      wp_cache_set($cache_key, $result, 'counts');
    }

    return $result ? new self($result, false) : false;
  }



  /**
   * Get all post's comments
   * Replaces WP's comments_template(), so we can use the nice ajax comments feature
   * @todo: reduce database usage, by finding a way to load only the requested comments instead of all comments. This can be done using LIMIT, but then we get the wrong comment/ping count :(
   *
   * @since 1.2
   * @param int $post_id Optional post ID
   * @return array comments
   */
  protected function queryComments(){
    if($this->comment_list === false){

      global $user_ID, $overridden_cpage, $wpdb, $wp_query;

      if(!(is_single() || is_page() || $GLOBALS['withcomments'])) return false;

      $commenter = wp_get_current_commenter();
      $comment_author = wp_specialchars_decode($commenter['comment_author'], ENT_QUOTES); // Escaped by sanitize_comment_cookies()
      $comment_author_email = $commenter['comment_author_email'];  // Escaped by sanitize_comment_cookies()
      $comment_author_url = esc_url($commenter['comment_author_url']);

      // show non-approved comments based on the current user context
      if(current_user_can('moderate_comments')){
        $filter = " AND (comment_approved != 'trash')"; // we don't want trashed comments visible to anyone

      }else{
        if($user_ID)
          $filter = " AND (comment_approved = '1' OR (user_id = '{$user_ID}' AND comment_approved = '0'))";
        elseif(empty($comment_author))
          $filter = " AND comment_approved = '1'";
        else
          $filter = " AND (comment_approved = '1' OR (comment_author = '".wp_specialchars_decode($comment_author, ENT_QUOTES)."' AND comment_author_email = '{$comment_author_email}' AND comment_approved = '0'))";
      }

      $query = $wpdb->prepare("SELECT * FROM {$wpdb->comments} WHERE comment_post_ID = %d {$filter} ORDER BY comment_date_gmt", $this->data->ID);

      /*/ check cache -- too much memory on many comments...
      $key = md5($query);
      $cache = wp_cache_get('get_comments', 'atom');
      if(!isset($cache[$key])){
        $this->comment_list = $wpdb->get_results($query);
        $cache[$key] = $this->comment_list;
        wp_cache_set('get_comments', $cache, ATOM);
      }else{
        $this->comment_list = $cache[$key];
      }
      /*/
      $this->comment_list = $wpdb->get_results($query);

      $wp_query->comments = apply_filters('comments_array', $this->comment_list, $this->data->ID);
      $wp_query->comment_count = count($wp_query->comments);
      update_comment_cache($this->comment_list);
      $wp_query->comments_by_type = &separate_comments($this->comment_list);

      $overridden_cpage = false;
      if(get_query_var('cpage') == '' && get_option('page_comments')){
        set_query_var('cpage', (get_option('default_comments_page') === 'newest') ? $this->countCommentPages() : 1);
        $overridden_cpage = true;
      }

    }
    return $this->comment_list;
  }



 /**
  * List comments
  *
  * @param $type type
  * @param $args array
  */
  public function comments($type = 'comment', $args = array()){

    global $wp_query, $comment_alt, $comment_depth, $comment_thread_alt, $overridden_cpage, $in_comment_loop;

    $comments = $this->queryComments();
    if(empty($comments)) return;

    $in_comment_loop = true;
    $comment_alt = $comment_thread_alt = 0;
    $comment_depth = 1;

    $args = wp_parse_args($args, array(
      'max_depth'         => get_option('thread_comments') ? (int)get_option('thread_comments_depth') : -1,
      'style'             => 'ul',
      'type'              => $type,
      'page'              => $overridden_cpage ? false : (int)get_query_var('cpage'),
      'per_page'          => get_option('page_comments') ? get_query_var('comments_per_page') : 0,
      'per_page_force'    => true,
      'reverse_top_level' => (get_option('comment_order') === 'desc'),
      'reverse_children'  => false,
    ));

    if($args['type'] !== 'all'){
      $comments_by_type = &separate_comments($comments);
      if($args['type'] === 'ping') $args['type'] = 'pings'; // stupid wp...
      if(empty($comments_by_type[$args['type']])) return;
      $comments = $comments_by_type[$args['type']];
    }                                      //

    if(empty($args['per_page']) || (int)$args['per_page'] < 1){
      $args['per_page'] = 0;
      $args['page'] = 0;
    }

    if($args['page'] === false){
      $args['page'] = (get_option('default_comments_page') === 'newest') ? $this->countCommentPages($comments, $args['per_page'], (($args['max_depth'] !== -1))) : 1;
      set_query_var('cpage', $args['page']);
    }

    $walker = new AtomWalkerComments();
    $walker->paged_walk($comments, $args['max_depth'], $args['page'], $args['per_page'], $args);

    $wp_query->max_num_comment_pages = $walker->max_pages;

    $in_comment_loop = false;
  }



  public function getCommentCount(){
    global $wp_query;

    // comments have been loaded and separated
    if(isset($wp_query->comments_by_type))
      $count = count($wp_query->comments_by_type['comment']);

    // we're outside the meta template, return the recorded comment count from $post (imprecise, counts both comments and pings)
    elseif(isset($this->data->comment_count))
      $count = $this->data->comment_count;

    // invalid context, maybe outside the loop on a category page?
    else
      $count = 0;

    return apply_filters('get_comments_number', $count, $this->getID());
  }



  public function getPingCount(){
    global $wp_query;

    if(is_singular() && post_type_supports($this->getType(), 'comments'))
      $this->queryComments();

    if(isset($wp_query->comments_by_type))
      return count($wp_query->comments_by_type['pings']);

    // we're not in the meta template...
    else return 0;
  }



  public function countCommentPages($comments = null, $per_page = null, $threaded = null){
    global $wp_query;

    if(null === $comments && null === $per_page && null === $threaded && !empty($wp_query->max_num_comment_pages))
      return (int)$wp_query->max_num_comment_pages;

    if(!$comments || !is_array($comments))
      $comments = $this->queryComments();

    if(empty($comments))
      return 0;

    if(!isset($per_page))
      $per_page = (int)get_query_var('comments_per_page');

    if($per_page === 0)
      $per_page = (int)get_option('comments_per_page');

    if($per_page === 0)
      return 1;

    if(!isset($threaded))
      $threaded = get_option('thread_comments');

    if($threaded){
      $walker = new AtomWalkerComments();
      $count = ceil($walker->get_number_of_root_elements($comments) / $per_page);

    }else{
      $count = ceil(count($comments) / $per_page);
    }

    return $count;
  }



 /*
  * Return the post date
  *
  * @since    1.0
  * @param    string $mode
  * @return   string
  */
  public function getDate($mode = ''){

    // 'relative' or a date string like 'd-M-Y'
    $mode = empty($mode) ? atom()->options('post_date_mode') : $mode;

    if($mode === 'absolute')
      $mode = get_option('date_format');

    return ($mode !== 'relative') ? get_the_time($mode, $this->data->ID) : atom()->getTimeSince(abs(strtotime("{$this->data->post_date} GMT")));
  }



 /*
  * Get post view count
  *
  * @since 1.4
  */
  function getViews(){
    // also check for swc + mu
    if($this->post_views === false)
      $this->post_views = isset($this->data->views) ? (int)$this->data->views : (int)get_post_meta($this->data->ID, apply_filters('post_views_meta_key', 'views'), true);

    return $this->post_views;
  }



 /*
  * Get the blog ID of the current post.
  * Only relevant within an "Atom Site-Wide Content" query
  *
  * @since 1.4
  */
  function getBlogID(){
    if(isset($this->data->blog_id))
      return $this->data->blog_id;

    return get_current_blog_id();
  }



 /*
  * Update post view count
  *
  * @since 1.4
  */
  public static function incrementViews(){

    // wp-postviews installed? don't do anything then...
    if((defined('ATOM_LOG_VIEWS') && !ATOM_LOG_VIEWS) || function_exists('increment_views')) return;

    $post_id = atom()->post->getID();
    $new_records = $posts_seen = array();

    if(is_user_logged_in())
      $posts_seen = explode('-', get_user_meta(atom()->user->getID(), 'posts-seen', true));

    // import IDs from the cookie if it exists
    if(isset($_COOKIE['posts-seen'])){
      $posts_seen = array_merge($posts_seen, explode('-', $_COOKIE['posts-seen']));
    }

    // validate current records
    foreach(array_unique($posts_seen) as $entry)
      if(is_numeric($entry) && $entry > 0) $new_records[] = $entry;

    // post has already been seen by the current user
    if(in_array($post_id, $new_records)) return;

    $meta_key = apply_filters('post_views_meta_key', 'views');
    $views = get_post_meta($post_id, $meta_key, true);

    update_post_meta($post_id, $meta_key, absint($views) + 1, $views);

    $new_records[] = $post_id;

    // limit to 50 records
    if(count($new_records) > 50) array_splice($new_records, 0, count($new_records) - 40);

    if(is_user_logged_in()){
      update_user_meta(atom()->user->getID(), 'posts-seen', implode('-', $new_records));

      // expire cookie if set
      if(isset($_COOKIE['posts-seen']))
        setcookie('posts-seen', '', time() - 3600, '/');

    }else{
      setcookie('posts-seen', implode('-', $new_records), time() + 60*60*24*90, '/'); // 3 months (should get renewed every time the user reads a new post)
    }
  }



 /*
  * Retrieve the post title URL (to be used only inside the loop)
  * title_url custom field overrides the assigned permalink
  *
  * @since    1.0
  * @return   string
  */
  public function getURL(){
    if($this->post_url === false){

      // mu / swc
      if(isset($this->data->permalink)){
        $this->post_url = $this->data->permalink;

      }else{
        $title_url = get_post_meta($this->data->ID, 'title_url', true);
        $this->post_url = $title_url ? $title_url : get_permalink($this->data->ID);
      }
    }

    return $this->post_url;
  }



 /*
  * Create a TinyURL link - see tinyurl.com
  *
  * @since     1.0
  * @param     string $url    Website URL to convert to a TinyURL link; default is the current post permalink
  * @return    string         The TinyURL link
  */
  public function getTinyURL($url = ''){
    // only connect to tinyurl if the transient is not present in the database (to avoid slowing down page load)
    $response = false;
    $id = md5($url);

    if(($response = get_transient("tinyurl_{$id}")) === false){
      $response = wp_remote_retrieve_body(wp_remote_get('http://tinyurl.com/api-create.php?url='.($url ? $url : $this->getURL())));

      // add transient in the db for 1 day
      set_transient("tinyurl_{$id}", $response, 60*60*24);
    }
    return $response;
  }



 /*
  * Get the post title, trim if necessary
  *
  * @since     1.2
  * @param     int $character_limit     Limit title length
  * @return    string                   Post title
  */
  public function getTitle($character_limit = 0){

    if($this->post_title === false)
      $this->post_title = esc_attr(get_the_title($this->data->ID));  // @todo find a way to pass the post ID, without screwing up the SWC query...

    // limit number of characters and append '...' ?
    if($character_limit > 0 && (mb_strlen($this->post_title) > $character_limit))
      return mb_substr($this->post_title, 0, $character_limit).'...';

    return $this->post_title;
  }



 /*
  * Retrieve the terms for a post.
  * uses wp_get_post_terms()
  *
  * @since    1.0
  * @param    string $tax         Term Taxonomy (optional, defaults to tags)
  * @param    string $separator   Separator
  * @param    string $template    Entry template; if set to false the function will return the raw objects as an array
  * @return   array|string        Terms
  */
  public function getTerms($tax = 'post_tag', $separator = ' ', $template = '<a href="%url%" rel="tag" title="%title%">%name%</a>'){

    if(!isset($this->post_terms[$tax])){
      $terms = wp_get_post_terms($this->data->ID, $tax);

      if(is_wp_error($terms))
        return atom()->log("Unable to get terms for post {$this->data->ID} ({$tax} taxonomy)", 1);

      $this->post_terms[$tax] = $terms;
    }

    // no template, return array
    if(empty($template))
      return $this->post_terms[$tax];

    $output = array();
    foreach($this->post_terms[$tax] as $term){
      $keywords = array('%url%', '%title%', '%name%', '%count%');
      $url = get_term_link($term, $tax);
      $title = "{$term->name} (".(atom()->nt('%s topic', '%s topics', $term->count, number_format_i18n($term->count))).")";

      $output[] = str_replace($keywords, array($url, $title, $term->name, number_format_i18n($term->count)), $template);
    }

    if(!empty($output))
      return implode($separator, $output);
  }



 /*
  * Wrapper for getTerms; returns tags
  *
  * @since    2.0
  * @param    string $separator
  * @param    string $template
  * @return   array|string
  */
  public function getTags($separator = ' ', $template = '<a href="%url%" rel="tag" title="%title%">%name%</a>'){
    return $this->getTerms('post_tag', $separator, $template);
  }



 /*
  * Wrapper for getTerms; returns categories
  *
  * @since    2.0
  * @param    string $separator
  * @param    string $template
  * @return   array|string
  */
  public function getCategories($separator = ' ', $template = '<a href="%url%" title="%title%">%name%</a>'){
    return $this->getTerms('category', $separator, $template);
  }



 /*
  * Get post content.
  * uses get_the_content()
  *
  * @since    1.0
  * @param    int|string $mode     Mode: "full", "excerpt", "user" or number of characters
  * @param    array $options       Options to pass to generateExcerpt()
  * @return   string
  */
  public function getContent($mode = '', $options = array()){
    $limit = 0;

    $mode = $mode ? $mode : atom()->options('post_content_mode');

    if($mode === 'user')
      $limit = atom()->options('post_content_size');

    // the Posts widget passes the character limit as "$mode"
    elseif(is_numeric($mode))
      $limit = $mode;

    $defaults = array(
      'limit'  => $limit,
      'more'   => atom()->t('More &gt;')
    );

    $defaults = atom()->getContextArgs('post_content', $defaults);

    // "more" text, link by default
    if(!empty($defaults['more']))
      $defaults['more'] = '<a title="'.esc_attr($defaults['more']).'" href="'.$this->getURL().'" class="more-link" data-post="'.$this->data->ID.'">'.$defaults['more'].'</a>';

    // function arguments override everything
    $options = array_merge($defaults, $options);

    // stupid plugins will probably screw the excerpt fileters up...
    if($mode[0] == 'e'){
      //return in_the_loop() ? get_the_excerpt() : $this->data->post_excerpt;
      return get_the_excerpt();

    }else{
      $content = in_the_loop() ? str_replace(']]>', ']]&gt;', apply_filters('the_content', get_the_content())) : get_the_content();
      return ($mode !== 'f') ? atom()->generateExcerpt($content, $options) : $content;
    }
  }



 /*
  * Get the post thumbnail.
  * Replaces get_the_post_thumbnail() for two reasons:
  * - we get to know if a thumbnail size exists, instead of getting a browser-resized image
  * - if we know a thumbnail size doesn't exist we can regenerate the thumbnails for that attachment
  *
  * @since    1.0
  * @see      post-thumbnail-template.php   get_the_post_thumbnail()
  * @param    string|array $size            Optional. Thumbnail size, either the registered ID or an array of (width, height)
  * @return   string                        Thumbnail image
  */
  public function getThumbnail($size = 'post-thumbnail', $attr = ''){
    global $_wp_additional_image_sizes;

    $avail_image_sizes = $_wp_additional_image_sizes;
    $size_id = is_array($size) ? implode('x', $size) : $size;

    // handle an array type size
    if(is_array($size)){
      list($width, $height) = $size;
      foreach($avail_image_sizes as $id => $sizes)
        if(($width == $sizes['width']) && ($height == $sizes['height'])) $size = $id;
    }

    // already have it in cache?
    if(isset($this->post_thumb[$size_id]))
      return $this->post_thumb[$size_id];

    // swc + mu
    if(isset($this->data->thumbnails)){

      // we need array here
      $size = is_string($size) ? $avail_image_sizes[$size] : array('width' => $size[0], 'height' => $size[1]);

      foreach($this->data->thumbnails as $thumb_size => $url){
        list($thumb_width, $thumb_height) = explode('x', $thumb_size);

        // we have a match
        if($size['width'] == $thumb_width && $size['height'] == $thumb_height){

          $hwstring = image_hwstring($thumb_width, $thumb_height);

          $attributes = array(
            'src'   => $url,
            'class' => "attachment-{$size_id}",
            'alt'   => $this->getTitle(), // post title
          );

          $html = rtrim("<img {$hwstring}");

          foreach($attributes as $name => $value)
            $html .= " {$name}=".'"'.$value.'"';

          $html .= ' />';

          // maybe we shouldn't apply this filter?
          $this->post_thumb[$size_id] = apply_filters('post_thumbnail_html', $html, $this->data->ID, $this->data->ID, $size_id, $attr);
          $this->available_thumb_sizes[] = $size_id;
          return $this->post_thumb[$size_id];
        }
      }
      $this->post_thumb[$size_id] = '<span class="no-img" style="width:'.$size['width'].'px;height:'.$size['height'].'px">&nbsp;</span>';
      return $this->post_thumb[$size_id];
    }

    $html = '';
    //  $t = get_post_meta($this->data->ID, '_thumbnail_id', true);
    $t = get_post_thumbnail_id($this->data->ID);

    if(empty($t) && atom()->options('post_thumb_auto')){
      $attachments = get_children(array(
        'post_parent'    => $this->data->ID,
        'post_status'    => 'inherit',
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'order'          => 'ASC',
        'orderby'        => 'menu_order ID',
      ));
      $attachment = array_shift($attachments);
      $t = $attachment ? $attachment->ID : false;
    }

    $done = false;
    if(!isset($width) && !isset($height)){
      $width = $avail_image_sizes[$size]['width'];
      $height = $avail_image_sizes[$size]['height'];
    }

    if(is_numeric($t) && $this->thumbnailNeedsRegeneration($t, array($width, $height))){
      if(is_string($size))
        $html = '<span class="no-img regen" data-post="'.$this->data->ID.'" data-thumb="'.$t.'" data-size="'.$size_id.'" style="width:'.$width.'px;height:'.$height.'px">&nbsp;</span>';
      else
        $html = '<span class="no-img" style="width:'.$width.'px;height:'.$height.'px">&nbsp;</span>';

    }elseif(is_numeric($t)){
      do_action('begin_fetch_post_thumbnail_html', $this->data->ID, $t, $size);

      if(in_the_loop())
        update_post_thumbnail_cache();

      $html = wp_get_attachment_image($t, array($width, $height), false, $attr);
      do_action('end_fetch_post_thumbnail_html', $this->data->ID, $t, $size);
    }

    $html = apply_filters('post_thumbnail_html', $html, $this->data->ID, $t, $size, $attr);
    $this->post_thumb[$size_id] = $html ? $html : '<span class="no-img" style="width:'.$width.'px;height:'.$height.'px"></span>';

    if($html)
      $this->available_thumb_sizes[] = $size_id;

    return $this->post_thumb[$size_id];
  }



 /*
  * Check if the desired size exists
  *
  * @since    2.0
  * @param    string|array $size    Image size, ID or array
  * @return   bool
  */
  public function hasThumbnail($size = 'post-thumbnail'){

    // fills in thumbnail entry in the available_thumb_sizes array, if it exists
    $thumbnail = $this->getThumbnail($size);

    if(is_array($size))
      $size = implode('x', $size);

    return in_array($size, $this->available_thumb_sizes);
  }



  /**
   * Retrieves all attached images to a post
   *
   * @since 1.2
   * @param int $post_id Post ID.
   * @param string $order Order, ASC/DESC
   * @param string $orderby Order byl
   *
   * @return array results
   */
  public function getGallery($order = 'ASC', $orderby = 'menu_order ID'){
    return get_children(array(
      'post_parent'     => $this->data->ID,
      'post_status'     => 'inherit',
      'post_type'       => 'attachment',
      'post_mime_type'  => 'image',
      'order'           => $order,
      'orderby'         => $orderby,
    ));
  }


 /*
  * Check if a post thumbnail needs to be updated (missing size)
  * @todo: attempt to create and generate missing array(w,h) sizes, not just IDs (need a proper security check so we can do that)
  *
  * @since     1.3
  * @param     int $attachment_id    Attachment ID
  * @param     string|array $size    Thumbnail size, ID or array
  * @return    bool                  True on success, false on failure (eg. size not registered, or attachment doesn't exist)
  */
  public static function thumbnailNeedsRegeneration($attachment_id, $size = 'post-thumbnail'){
    global $_wp_additional_image_sizes;

    // handle an array type size
    if(is_array($size)){
      list($width, $height) = $size;
      foreach($_wp_additional_image_sizes as $id => $sizes)
        if(($width == $sizes['width']) && ($height == $sizes['height'])) $size = $id;
    }

    // a string size is required by this point
    if(!is_string($size)) return true;

    // no such size, probably fake request
    if(!isset($_wp_additional_image_sizes[$size])) return false;

    $requested_width = $_wp_additional_image_sizes[$size]['width'];
    $requested_height = $_wp_additional_image_sizes[$size]['height'];

    $meta = wp_get_attachment_metadata($attachment_id);
    if(!is_array($meta) || empty($meta)) return false;

    // small image? WP doesn't enlarge images
    if(!isset($meta['sizes'][$size]) && ($meta['width'] < $requested_width) && ($meta['height'] < $requested_height)) return false;

    // check if we already have this size
    if(isset($meta['sizes'][$size]['width']) && isset($meta['sizes'][$size]['height']))
      if(($meta['sizes'][$size]['width'] == $requested_width) || ($meta['sizes'][$size]['height'] == $requested_height)) return false;

    // check if the original image size already matches the requested size
    if(isset($meta['width']) && isset($meta['height']))
      if(($meta['width'] == $requested_width) || ($meta['height'] == $requested_height)) return false;

    return true;
  }



 /*
  * Update a post thumbnail (generate new image sizes)
  * Should be called trough an ajax request because it needs a lot of CPU and it will slow down page loading
  * - ideas from the 'Regenerate Thumbnails' plugin by Viper007Bond
  *
  * @since     1.0
  * @param     int $t                Thumbnail (attachment) ID
  * @param     string|array $size    Optional. Thumbnail size
  * @return    bool                  True if new meta data was generated, false otherwise
  */
  public static function regenerateThumbnail($attachment_id, $size = 'post-thumbnail'){

    if(atom()->options('generate_thumbs') && self::thumbnailNeedsRegeneration($attachment_id, $size)){

      $original_path = get_attached_file($attachment_id);

      // check if the image file exists
      if($original_path == false || !file_exists($original_path))
        return atom()->log('Failed to generate thumbnail for attachment '.$attachment_id.': Original image does not exist.', Atom::WARNING);

      require_once(ABSPATH.'/wp-admin/includes/image.php');

      @set_time_limit(300); // 5 minutes
      $metadata = wp_generate_attachment_metadata($attachment_id, $original_path);

      if(is_wp_error($metadata))
        return atom()->log('Failed to generate thumbnail for attachment '.$attachment_id.': '.$metadata->get_error_message(), Atom::WARNING);

      wp_update_attachment_metadata($attachment_id, $metadata);

      return true;
    }

    return false;
  }



 /*
  * Output the social media links to allow the user to share the current post
  * to be used only inside the loop!
  * @todo -- Re-check all website URLs + parameters
  *
  * @since    1.0
  * @return   string
  */
  public function getShareLinks(){

    // data to expose
    $fields = apply_filters('atom_share_fields', array(
      '{TITLE}'     => urlencode($this->getTitle()),
      '{AUTHOR}'    => !empty($this->author) ? urlencode($this->author->getName()) : atom()->t('Anonymous'), // @todo: bbp compat.
      '{URL}'       => urlencode($this->getURL()),
      '{SHORT_URL}' => urlencode($this->getTinyURL()),
      '{EXCERPT}'   => urlencode($this->getContent('e')),
      '{SOURCE}'    => urlencode(get_bloginfo('name')),
    ));

    // @todo: update this list, some links might not work anymore...
    $sites = apply_filters('atom_share_urls', array(
      'Twitter'          => 'http://twitter.com/home?status={TITLE}+-+{SHORT_URL}',
      'Digg'             => 'http://digg.com/submit?phase=2&amp;url={URL}&amp;title={TITLE}',
      'Facebook'         => 'http://www.facebook.com/share.php?u={URL}&amp;t={TITLE}',
      'Delicious'        => 'http://del.icio.us/post?url={URL}&amp;title={TITLE}',
      'StumbleUpon'      => 'http://www.stumbleupon.com/submit?url={URL}&amp;title={TITLE}',

      /*/ more?
      'Google Bookmarks' => 'http://www.google.com/bookmarks/mark?op=add&amp;bkmk={URL}&amp;title={TITLE}',
      'LinkedIn'         => 'http://www.linkedin.com/shareArticle?mini=true&amp;url={URL}&amp;title={TITLE}&amp;summary={EXCERPT}&amp;source={SOURCE}',
      'Yahoo Bookmarks'  => 'http://buzz.yahoo.com/buzz?targetUrl={URL}&amp;headline={TITLE}&amp;summary={EXCERPT}',
      'Technorati'       => 'http://technorati.com/faves?add={URL}',
      //*/
    ));

    $output = array();
    if(!empty($sites)){
      $output[] = '<ul class="sub-menu share-this">';
      $total = count($sites);
      foreach($sites as $name => $url){
        $url = str_replace(array_keys($fields), array_values($fields), $url);
        $title = esc_attr(atom()->t('Share this post on %s', $name));
        $class = implode(' ', array(sanitize_html_class(strtolower($name))));
        $output[] = '<li class="'.$class.'"><a href="'.$url.'" title="'.$title.'"><span>'.$name.'</span></a></li>';
      }
      $output[] = '</ul>';
    }

    return implode("\n", $output);
  }



 /*
  * Get the post author
  *
  * @since     1.0
  * @return   object      Atom author object
  */
  public function _getAuthor(){
    if(!($this->post_author instanceof AtomObjectUser) && isset($this->data->post_author))
      $this->post_author = new AtomObjectUser($this->data->post_author);

    return $this->post_author;
  }



 /*
  * Output posts related to the current post, by taxonomy terms
  *
  * @since 1.6
  * @return array|bool array of post objects, or false if no related posts were found
  */
  public function _getRelated(){

    if($this->related_posts === false){

      $tax_query = array();

      // figure out post taxonomies and attempt to match terms from all of them
      $taxonomies = ($this->data->post_type !== 'post') ? get_object_taxonomies($this->data->post_type) : array('post_tag');
      foreach($taxonomies as $taxonomy){
        if(!isset($this->post_terms[$taxonomy]))
          $this->post_terms[$taxonomy] = wp_get_post_terms($this->data->ID, $taxonomy);

        $term_ids = array();
        if(isset($this->post_terms[$taxonomy]))
          foreach($this->post_terms[$taxonomy] as $term) $term_ids[] = $term->term_id;

        if(!empty($term_ids))
          $tax_query[] = array(
            'taxonomy'  => $taxonomy,
            'field'     => 'id',
            'terms'     => $term_ids,
            'operator'  => 'IN',
          );
      }

      // no terms to compare = no related posts
      if(empty($tax_query)) return false;

      if(count($tax_query) > 1) // more than one taxonomy? set a relation
        $tax_query['relation'] = 'OR';

      $query = atom()->getContextArgs('related_posts', array(
        'offset'               => 0,
        'post__not_in'         => array($this->data->ID),
        'posts_per_page'       => 10,
        'tax_query'            => $tax_query,
        'ignore_sticky_posts'  => true,
        'suppress_filters'     => true,
      ));

      $related = new WP_Query($query);
      $this->related_posts = $related ? new AtomIteratorPosts($related->posts) : false;
    }

    return $this->related_posts;
  }

}






/*
 * User object
 *
 * @since 1.0
 */
class AtomObjectUser extends AtomObject{

  protected

    $name,
    $post_count,
    $posts_url,
    $feed,
    $avatar,              // array
    $description,
    $meta,                // array, user meta
    $karma;



  /**
   * The constructor.
   * Gets all the user info from the WP globals or the db, if necessary
   *
   * @since 1.7
   * @param mixed $user User ID or WP_User object
   */
  public function __construct($user = false){

    if(is_numeric($user)){
      $this->data = new WP_User($user);
      if(!($this->data instanceof WP_User))
        throw new Exception("Failed to get data for user {$user}. Does that user exist?");

    }elseif($user instanceof WP_User){
      $this->data = $user;

    }else{

      // author archive?
      if(get_query_var('author'))
        $this->data = new WP_User((int)get_query_var('author'));

      // bbpress page
      elseif(class_exists('bbPress') && is_bbpress())
        $this->data = new WP_User((int)bbp_get_user_id(0, true, false));

      // ...no. wtf?
      else
        throw new Exception('You are not using this method in the right context.');

    }

  }



 /*
  * Get the current user ID
  *
  * @since    1.7
  * @return   int
  */
  public function getID(){
    return (int)$this->data->ID;
  }



 /*
  * Get a user meta field
  *
  * @since    1.7
  * @param    string $field    Field
  * @return   string           User display name
  */
  public function getMeta($field){

    if(!isset($this->meta[$field]))
      $this->meta[$field] = get_the_author_meta($field, $this->data->ID);

    return $this->meta[$field];
  }



 /*
  * Get the current user nice name
  *
  * @since 2.0
  * @return string User display name
  */
  public function getSlug(){
    return $this->data->user_nicename;
  }



  /**
   * Get the display name
   *
   * @since 1.7
   * @return string User display name
   */
  public function getName(){

    if(!isset($this->name))
      $this->name = apply_filters('the_author', $this->data->display_name);

    return $this->name;
  }



  /**
   * Get user email address
   *
   * @since 2.0
   * @return string E-mail address
   */
  public function getEmail(){
    return $this->data->user_email;
  }



  /**
   * Get the post count of the current user.
   * Should be avoided in favour of count_many_users_posts() if using it inside a loop
   *
   * @since 1.7
   * @return int Post count
   */
  public function getPostCount(){

    if(!isset($this->post_count))
      $this->post_count = isset($this->data->post_count) ? (int)$this->data->post_count : (int)count_user_posts($this->data->ID);

    return $this->post_count;
  }



  /**
   * The link to the author page
   *
   * @since 1.7
   * @return string URL
   */
  public function getPostsURL(){

    if(!isset($this->posts_url))
      $this->posts_url = get_author_posts_url($this->data->ID);

    return $this->posts_url;
  }



  /**
   * Get the "karma" meta field of the current user
   *
   * @since 1.7
   * @return int karma value (At least 0)
   */
  public function getKarma(){
    if(!isset($this->karma))
      $this->karma = get_user_meta($this->data->ID, 'karma', true);

    return (int)$this->karma;
  }



  /**
   * Get the RSS Feed URL for the current user
   *
   * @since 1.7
   * @return string URL
   */
  public function getFeedURL(){
    if(!isset($this->feed))
      $this->feed = get_author_feed_link($this->data->ID);

    return $this->feed;
  }



  /**
   * Get the current user's avatar image URI
   *
   * @since 1.7
   * @param int $size Avatar size
   * @return string description as HTML
   */
  public function getAvatar($size = 160){
    if(!isset($this->avatar[$size]))
      $this->avatar[$size] = atom()->getAvatar($this->data->user_email, $size, false, $this->getName());

    return $this->avatar[$size];
  }



  /**
   * Get user description field (bio)
   *
   * @since 1.7
   * @param string $fallback Fallback description text if none is set
   * @return string description as HTML
   */
  public function getDescription($fallback = ''){
    if(!isset($this->description)){

      // author meta - highest priority
      if(isset($this->data->description))
        $description = $this->data->description;

      // fallback
      if(empty($description))
        $description = $fallback;

      $this->description = $description ? force_balance_tags(wpautop(convert_smilies($description))) : '';
    }

    return $this->description;
  }



  /**
   * Retrieve the post author as a link
   *
   * @since 1.7
   * @param bool $count_posts Include post count inside the title attribute? This might increase db queries, depending on the context
   * @return string Post Author as link
   */
  public function getNameAsLink($count_posts = false){
    $title = $count_posts ? atom()->t('Posts by %1$s (%2$s)', $this->getName(), $this->getPostCount()) : atom()->t('Posts by %s', $this->getName());
    return '<a href="'.$this->getPostsURL().'" title="'.$title.' ">'.$this->getName().'</a>';
  }



 /*
  * Get the account role of the author of the current post in the Loop.
  * from - http://core.trac.wordpress.org/attachment/ticket/5290/author-template-the_author_role.diff
  *
  * @since     1.3
  * @global    object $wpdb        WordPress database layer.
  * @global    object $wp_roles    Avaliable account roles and capabilities.
  * @param     string $display     Return type, "label or "slug"
  * @return    string              The author's account role, eg. "Administrator"
  */
  public function getRole($display = 'label') {
    global $wpdb, $wp_roles;

    if(!isset($wp_roles))
      $wp_roles = new WP_Roles();

    foreach($wp_roles->role_names as $role => $label){

      $caps = "{$wpdb->prefix}capabilities";

      if(array_key_exists($role, (array)$this->data->$caps))
        return ($display !== 'label') ? $role : $label;
    }

    return false;
  }




 /*
  * Number of posts/comments the user has written.
  * Can count custom post types
  *
  * @since    1.3
  * @uses     $wpdb                 WordPress database object for queries.
  * @param    int $what_to_count    Post types or comments, Default is "post"
  * @return   int                   Count
  */
  public function getCount($what_to_count = 'post') {
    global $wpdb;

    $where = $what_to_count == 'comment' ? "WHERE comment_approved = 1" : get_posts_by_author_sql($what_to_count, true, $this->data->ID);

    if($what_to_count == 'comment')
      $where .= sprintf(' AND user_id = %d', $this->data->ID);

    $from = "FROM ".(($what_to_count == 'comment') ? $wpdb->comments : $wpdb->posts);

    $query = "SELECT COUNT(*) {$from} {$where}";

    // check cache
    $key = md5($query);
    $cache = wp_cache_get('get_count', 'atom');
    if(!isset($cache[$key])){
      $count = $wpdb->get_var($query);
      $cache[$key] = $count;
      wp_cache_set('get_count', $cache, ATOM);

    }else{
      $count = $cache[$key];
    }

    return apply_filters("atom_user_{$what_to_count}_count", (int)$count, $this->data->ID);
  }




 /*
  * Get the online status of the user
  * The ATOM_TRACK_USERS constant must be set to "true" for this work
  *
  * @since    2.0
  * @param    int $time_limit    Under this limit, the user is assumed "offline"
  * @return   bool               Online status
  */
  public function isOnline($time_limit = 15) {

    // get the online users list
    $logged_in_users = get_transient('users_online');

    // online, if (s)he is in the list and last activity was less than $time_limit minutes ago
    return isset($logged_in_users[$this->data->ID]) && $logged_in_users[$this->data->ID] > (current_time('timestamp') - ($time_limit * 60));
  }

}






/**
 * Comment object
 *
 * @since 1.0
 */
class AtomObjectComment extends AtomObject{

  protected
    $comment_url       = false,

    $author            = false,
    $author_url        = false,
    $author_avatar     = array();

  public
    $display_index     = 0;       // current comment index



  public function __construct($user_comment = false){
    global $comment;

    if(is_numeric($user_comment)){
      $this->data = &get_comment($user_comment);
      if(!is_object($this->data))
        throw new Exception("Failed to get comment {$user_post_id}");

    }elseif(is_object($user_comment)){
      $this->data = $user_comment;
      $comment = $this->data;
    }else{
      $this->data = $comment;
    }

    // set up controls - edit link
    if(current_user_can('edit_comment', $this->data->comment_ID))
      $this->controls['edit'] = array(
        'target'   => get_edit_comment_link($this->data->comment_ID),
        'label'    => atom()->t('Edit'),
      );

    // delete / spam / approve links -- @todo: ajaxify these
    if(current_user_can('moderate_comments')){
      $this->controls['delete'] = array(
        'target'   => esc_url(add_query_arg(array('action' => 'cdc', 'c' => $this->data->comment_ID), admin_url('comment.php'))),
        'label'    => atom()->t('Delete'),
        'class'    => 'x',
      );

      $this->controls['spam'] = array(
        'target'   => esc_url(add_query_arg(array('action' => 'cdc', 'dt' => 'spam', 'c' => $this->data->comment_ID), admin_url('comment.php'))),
        'label'    => atom()->t('Spam'),
      );

      $this->controls['approve'] = array(
        'target'   => esc_url(add_query_arg(array('action' => 'mac', 'dt' => 'approve', 'c' => $this->data->comment_ID), admin_url('comment.php'))),
        'label'    => atom()->t('Approve'),
        'class'    => 'ok',
      );

    }

    if(is_singular() && atom()->options('jquery') && (comments_open())){
      if(get_option('thread_comments'))
        $this->controls['reply'] = array(
          'target' => esc_url(add_query_arg('replytocom', $this->data->comment_ID)).'#respond',
          'id'     => "reply-to-{$this->data->comment_ID}",
          'label'  => atom()->t('Reply'),
        );

      $this->controls['quote'] = array(
        'target'   => '#commentform',
        'label'    => atom()->t('Quote'),
      );

    }

  }

  public function getID(){
    // swc
    if(isset($this->data->comment_id))
      return $this->data->comment_id;

    return $this->data->comment_ID;
  }


  public function getURL(){
    if($this->comment_url === false)
      $this->comment_url = isset($this->data->permalink) ? $this->data->permalink : get_comment_link($this->data->comment_ID);

    return $this->comment_url;
  }



  public function getAvatar($size = 48){
    if(!isset($this->author_avatar[$size]))
      $this->author_avatar[$size] = atom()->getAvatar($this->data->comment_author_email, $size, false, $this->getAuthor());

    return $this->author_avatar[$size];
  }



  public function getParent(){
    return $this->data->comment_parent;
  }



  public function getNumber(){

    // echo (get_query_var('cpage') * get_query_var('comments_per_page')) - get_query_var('comments_per_page') + $this->display_index;

    // this is the comment number relative to all post's comments
    return (int)$this->display_index;
  }



  public function isApproved(){
    return ((int)$this->data->comment_approved !== 0);
  }



  public function belongsToCurrentUser(){
    return (is_user_logged_in() && ((int)$this->data->user_id === atom()->user->getID()));
  }



  /**
   * Check if the current user can rate a comment.
   * Can only be used inside the comment loop
   *
   * @since 1.3
   * @global $user_ID
   * @return bool
   */
  public function isRatingAllowed(){

    if(is_user_logged_in() && !$this->belongsToCurrentUser())
      return !in_array($this->data->comment_ID, explode(',', get_user_meta(atom()->user->getID(), 'comment_ratings', true)));

    return false;
  }



  /**
   * Checks if the current comment has a low karma
   *
   * @since 1.3
   * @return bool
   */
  public function isBuried(){
    if(!atom()->options('comment_karma') || defined('RETRIEVING_BURIED_COMMENT')) return false;

    $level = (int)$this->data->comment_parent;
    $current_comment = $this->data;
    if($current_comment->comment_karma <= atom()->options('comment_bury_limit')) return true;

    while($level !== 0){ // if this comment is not buried, make sure it doesn't have buried parent comments

      $current_comment = get_comment($current_comment->comment_parent); // hopefully this comes from cache
      $level = (int)$current_comment->comment_parent;

      // if it does we'll bury this one too
      if($current_comment->comment_karma <= atom()->options('comment_bury_limit')) return true;
    }

    return false;
  }



  /**
   * Displays the comment karma controls
   *
   * @since 1.3
   * @param string $classes Optional, extra CSS classes to add to the container
   * @return bool
   */
  public function karma($classes = ''){
    if(!atom()->options('comment_karma') || !atom()->options('jquery')) return;
    ?>
      <div class="comment-karma <?php echo $classes; ?>">
        <?php if($this->isBuried()): ?>
        <a class="show" data-comment="<?php comment_ID(); ?>"><?php atom()->te('Show'); ?></a>
        <?php endif; ?>
        <span class="karma <?php echo ($this->data->comment_karma > 0) ? 'positive' : 'negative'; ?>"><?php if($this->data->comment_karma != 0) echo str_replace('-', '&#8722;', $this->data->comment_karma); ?></span>
        <?php if($this->isRatingAllowed()): ?>
        <a class="vote up" data-vote="+/<?php comment_ID(); ?>">&#43;</a>
        <a class="vote down" data-vote="-/<?php comment_ID(); ?>">&#8722;</a>
        <?php endif; ?>
     </div>
    <?php
  }



 /*
  * Get the comment time/date in time since format
  *
  * @since   1.0
  * @param   string $mode
  * @return  string
  */
  public function getDate($mode = 'relative'){

    if($mode === 'absolute')
      $mode = get_option('date_format');

    return ($mode !== 'relative') ? get_comment_date($mode) : atom()->getTimeSince(abs(strtotime("{$this->data->comment_date} GMT")));
  }



  /**
   * Get the comment author name as a link or <b> tag
   * Can only be used inside the comment template
   *
   * @since 1.3
   */
  public function getAuthor(){
    if($this->author === false)
      $this->author = get_comment_author();

    return $this->author;
  }



  /**
   * Get the comment author name as a link or <b> tag
   * Can only be used inside the comment template
   *
   * @since 1.3
   */
  public function getAuthorURL(){
    if($this->author_url === false)
      $this->author_url = get_comment_author_url();

    return $this->author_url;
  }



  /**
   * Get the comment author name as a link or <b> tag
   * Can only be used inside the comment template
   *
   * @since 1.3
   * @param string $rel
   */
  public function getAuthorAsLink($rel = 'nofollow'){
    $url = get_comment_author_url();

    if($this->getAuthorURL()){
      $rel = $rel ? ' rel="'.$rel.'"' : '';
      $link = '<a itemprop="name" class="comment-author" id="comment-author-'.$this->data->comment_ID.'" href="'.$this->getAuthorURL().'"'.$rel.'>'.$this->getAuthor().'</a>';

    }else{
      $link = '<b itemprop="name" class="comment-author" id="comment-author-'.$this->data->comment_ID.'">'.$this->getAuthor().'</b>';
    }

    return apply_filters('atom_comment_author', $link, $this->data);
  }

}






/*
 * Iterator interface for Atom objects like posts, comments, users...
 *
 * @since 1.0
 */
abstract class AtomIterator implements Iterator{

  public $position = 0,
         $list;

  public function __construct($list) {
    $this->list = $list;
    $this->position = 0;
  }

  public function rewind() {
    $this->position = 0;
  }

  public function current() {
    return $this->list[$this->position];
  }

  public function key() {
    return $this->position;
  }

  public function next() {
    ++$this->position;
  }

  public function valid() {
    return isset($this->list[$this->position]);
  }
}






/*
 * Post iterator.
 *
 * @since 1.0
 */
class AtomIteratorPosts extends AtomIterator{

  public function current(){
    return new AtomObjectPost($this->list[$this->position]);
  }

  public function valid(){
    $valid = parent::valid();

    // just so we don't need to manually reset post data...
    if(!$valid)
      atom()->setCurrentPost(false);

    return $valid;
  }

}





/*
 * Comment iterator -- not yet implemented.
 * Will replace WP's walker (should offer more flexibility in templating and maybe a little more speed?)
 *
 * @todo start working on it :)
 * @since 1.0
 */
class AtomIteratorComments extends AtomIterator{

  public function current(){
    return new AtomObjectComment($this->list[$this->position]);
  }

}





/*
 * Module API.
 * Eventually this will be improved in the future. Currently it's only use is to provide module path/url...
 *
 * @since 1.0
 */
class AtomMod{

  protected
    $name,
    $url,
    $dir,
    $options;

  final public function __construct($name, $type){
    $this->name = $name;
    $this->type = $type;
    $this->url = ($type !== 'core') ? atom()->get('child_theme_url').'/mods/'.$name : atom()->get('theme_url').'/mods/'.$name;
    $this->dir = ($type !== 'core') ? STYLESHEETPATH.'/mods/'.$name : TEMPLATEPATH.'/mods/'.$name;
  }

  final public function getOptions($prefix){
    if(!isset($this->options)){
      $this->options = array();
      $all_options = atom()->options();
      foreach($all_options as $key => $value)
        if(strpos($key, "{$prefix}_") === 0) $this->options[$key] = $value;

    }

    return $this->options;
  }


  // @todo
  public function onActivation(){

  }

  public function onDeactivation(){

  }

  public function onUninstall(){

  }




  public function __call($name, $args = array()){

    $getter = "get{$name}";

    if(method_exists($this, $getter)){
      echo count($args) ? call_user_func_array(array($this, $getter), $args) : $this->$getter();
      return;
    }

    // not deprecated, throw the error
    throw new Exception("Method {$name} is not defined");
  }
}



/*
 * Term Walker -- temporary
 * Replaces WP's default category walker (which doesn't allow much control over the output and the css classes provided are useless)
 * All content inside the list item can be formatted trough the "item_display_callback" function
 *
 * @todo: use iterators instead
 * @since 1.0
 */
class AtomWalkerTerms extends Walker_Category{

  var $tree_type = 'category';
  var $db_fields = array('parent' => 'parent', 'id' => 'term_id');

  function start_lvl(&$output, $depth, $args){
    $indent = str_repeat("\t", $depth);
    $output .= "{$indent}<ul class=\"sub-menu\">\n";
  }

  function end_lvl(&$output, $depth, $args){
    $indent = str_repeat("\t", $depth);
    $output .= "{$indent}</ul>\n";
  }

  function start_el(&$output, $term, $depth, $args){

    extract($args, EXTR_SKIP);

    // @todo: handle "current" context for custom taxonomies
    if(isset($current_category) && $current_category)
      $_current_category = get_category($current_category);

    $output .= "\t<li";

    $classes = array();

    if($term->has_children)
      $classes[] = 'extends';

    if(isset($current_category) && $current_category && ($term->term_id == $current_category))
      $classes[] = 'active';

    elseif(isset($_current_category) && $_current_category && ($term->term_id == $_current_category->parent))
      $classes[] = 'active-parent';

    $classes = $classes ? ' class="'.implode(' ', $classes).'"' : '';
    $output .= " {$classes}>";

    // @todo
    // $item_display_template = '<a href="%url%" title="%description%">%name%</a>';

    if(is_callable($item_display_callback))
      $output .= call_user_func($item_display_callback, $term, $args, $depth);

    else $output .= '<a href="'.get_term_link($term, $term->taxonomy).'" title="'
                 .esc_attr(strip_tags(apply_filters('category_description', $term->description, $term))).'">'
                 .apply_filters('list_cats', esc_attr($term->name), $term).'</a>';
  }

  function end_el(&$output, $page, $depth, $args){
    $output .= "</li>\n";
  }

  function display_element($element, &$children, $max_depth, $depth = 0, $args, &$output) {

    $id_field = $this->db_fields['id'];
    $element->has_children = isset($children[$element->$id_field]);

    parent::display_element($element, $children, $max_depth, $depth, $args, $output);
  }
}






/**
 * Comment Walker (ATOM Framework) -- temporary
 *  - adds thread comments pagination helper functions - http://wordpress.stackexchange.com/questions/20506
 *  - generates print index count - http://wordpress.stackexchange.com/questions/20527
 *
 * @todo: use iterators instead
 * @since 1.0
 */
class AtomWalkerComments extends Walker{

  public
    $tree_type = 'comment',
    $db_fields = array('parent' => 'comment_parent', 'id' => 'comment_ID'),
    $current_comment_print_index = 0;

  function paged_walk($elements, $max_depth, $page_num, $per_page){

    $this->current_comment_print_index = 0;

    if(empty($elements) || $max_depth < -1)
      return '';

    $args = array_slice(func_get_args(), 4);
    $output = '';

    $id_field = $this->db_fields['id'];
    $parent_field = $this->db_fields['parent'];

    $count = -1;
    if(-1 == $max_depth)
      $total_top = count($elements);

    if($page_num < 1 || $per_page < 0){
      $paging = false;
      $start = 0;

      if(-1 == $max_depth)
        $end = $total_top;

      $this->max_pages = 1;

    }else{
      $paging = true;
      $start = ((int)$page_num - 1) * (int)$per_page;
      $end  = $start + $per_page;
      if(-1 == $max_depth)
        $this->max_pages = ceil($total_top / $per_page);
    }

    // flat display
    if (-1 == $max_depth){
      if(!empty($args[0]['reverse_top_level'])){
        $elements = array_reverse($elements);
        $oldstart = $start;
        $start = $total_top - $end;
        $end = $total_top - $oldstart;
      }

      if($paging){
        // HK: if paging enabled and its a flat display.
        // HK: mark the current print index from page number * comments per page
        $this->current_comment_print_index = ((int)$page_num - 1) * $per_page;
      }

      $empty_array = array();
      foreach($elements as $e){
        $count++;
        if($count < $start)
          continue;
        if($count >= $end)
          break;
        $this->display_element($e, $empty_array, 1, 0, $args, $output);
      }
      return $output;
    }

    /*
    * separate elements into two buckets: top level and children elements
    * children_elements is two dimensional array, eg.
    * children_elements[10][] contains all sub-elements whose parent is 10.
    */
    $top_level_elements = array();
    $children_elements  = array();
    foreach($elements as $e)
      if(0 == $e->$parent_field) $top_level_elements[] = $e; else $children_elements[$e->$parent_field][] = $e;

    $total_top = count($top_level_elements);
    if($paging) $this->max_pages = ceil($total_top / $per_page); else  $end = $total_top;

    if(!empty($args[0]['reverse_top_level'])){
      $top_level_elements = array_reverse($top_level_elements);
      $oldstart = $start;
      $start = $total_top - $end;
      $end = $total_top - $oldstart;
    }

    if(!empty($args[0]['reverse_children']))
      foreach($children_elements as $parent => $children)
        $children_elements[$parent] = array_reverse($children);

    foreach($top_level_elements as $e){
      $count++;

      // HK: current iteration index, will be added to global index
      // NOTE: will only be added to global index if already printed
      $iteration_comment_print_index = 1;
      // HK: count of current iteration children (includes grand children too)
      $iteration_comment_print_index += $this->count_children($e->comment_ID, $children_elements);

      // for the last page, need to unset earlier children in order to keep track of orphans
      if($end >= $total_top && $count < $start)
        $this->unset_children($e, $children_elements);

      if($count < $start){
        // HK: if we have already printed this top level comment
        // HK: then just add the count (including children) to global index and continue
        $this->current_comment_print_index += $iteration_comment_print_index;
        continue;
      }

      if($count >= $end) break;

      $this->display_element($e, $children_elements, $max_depth, 0, $args, $output);
    }

    if($end >= $total_top && count($children_elements) > 0){
      $empty_array = array();
      foreach($children_elements as $orphans)
        foreach($orphans as $op)
          $this->display_element($op, $empty_array, 1, 0, $args, $output);
    }

    return $output;
  }

  function display_element($element, &$children_elements, $max_depth, $depth=0, $args, &$output){

    if(!$element)
      return;

    // increment for current comment we are printing
    $this->current_comment_print_index += 1;

    parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
  }

  function count_children($comment_id, $children_elements){
    $children_count = 0;
    if(isset($children_elements[$comment_id])){
      $children_count = count($children_elements[$comment_id]);
      foreach($children_elements[$comment_id] as $child)
        $children_count += $this->count_children($child->comment_ID, $children_elements);

    }
    return $children_count;
  }

  //*/


  // @see Walker::start_lvl()
  function start_lvl(&$output, $depth, $args){
    $GLOBALS['comment_depth'] = $depth + 1;

    switch($args['style']){
      case 'div':
      break;
      case 'ol':
      echo "<ol class='children'>\n";
      break;
      default:
      case 'ul':
      echo "<ul class='children'>\n";
      break;
    }
  }



  // @see Walker::end_lvl()
  public function end_lvl(&$output, $depth, $args){
    $GLOBALS['comment_depth'] = $depth + 1;

    switch($args['style']){
      case 'div':
      break;
      case 'ol':
      echo "</ol>\n";
      break;
      default:
      case 'ul':
      echo "</ul>\n";
      break;
    }
  }



  /**
  * @see Walker::start_el()
  * @since 2.7.0
  * @param string $output Passed by reference. Used to append additional content.
  * @param object $comment Comment data object.
  * @param int $depth Depth of comment in reference to parents.
  * @param array $args
  */
  public function start_el(&$output, $comment, $depth, $args){
    $depth++;

    $GLOBALS['comment_depth'] = $depth;

    atom()->setCurrentComment($comment);
    atom()->comment->display_index = $this->current_comment_print_index;
    atom()->template($args['type'] !== 'comment' ? 'ping' : 'comment');
  }



  /**
  * @see Walker::end_el()
  * @since 2.7.0
  * @param string $output Passed by reference. Used to append additional content.
  * @param object $comment
  * @param int $depth Depth of comment.
  * @param array $args
  */
  public function end_el(&$output, $comment, $depth, $args){
    echo ($args['style'] !== 'div') ? "</li>\n" : "</div>\n";
  }
}



/*
 * Add "extends" css class to menus that have children
 * used for adding padding for the graphic arrow, and for identifying expandable-collapsible lists within the custom menu widget
 *
 * @since 1.0
 */
class AtomWalkerNavMenu extends Walker_Nav_Menu{
  function display_element($element, &$children, $max_depth, $depth = 0, $args, &$output) {
    $id_field = $this->db_fields['id'];

    if(isset($children[$element->$id_field]))
      $element->classes[] = 'extends';

    parent::display_element($element, $children, $max_depth, $depth, $args, $output);
  }
}



/*
 * Same as the above, but for pages
 *
 * @since 1.0
 */
class AtomWalkerPages extends Walker_Page{

  function start_lvl(&$output, $depth) {
    $indent = str_repeat("\t", $depth);
    $output .= "\n".$indent.'<ul class="sub-menu">'."\n";
  }

  function start_el(&$output, $page, $depth, $args, $current_page_id) {

    $indent = $depth ? str_repeat("\t", $depth) : '';

    extract($args, EXTR_SKIP);

    $classes = array();

    if($page->has_children)
      $classes[] = 'extends';

    if(!empty($current_page_id)){
      $current_page = get_page($current_page_id);
      _get_post_ancestors($current_page);

      if(isset($current_page->ancestors) && in_array($page->ID, (array)$current_page->ancestors))
        $classes[] = 'active-parent';

      if($page->ID == $current_page_id)
        $classes[] = 'active';

      elseif($current_page && $page->ID == $current_page->post_parent)
        $classes[] = 'active-parent';

    }elseif($page->ID == get_option('page_for_posts')){
       $classes[] = 'active-parent';
    }

    $classes = implode(' ', apply_filters('page_css_class', $classes, $page, $depth, $args, $current_page_id));

    if($classes)
      $classes = 'class="'.$classes.'"';

    $output .= $indent.'<li '.$classes.'>';

    if(!empty($item_display_callback))
      $output .= $item_display_callback($page, $args, $depth);

    else
      $output .= '<a href="'.get_permalink($page->ID).'">'.$link_before.apply_filters('the_title', $page->post_title, $page->ID).$link_after.'</a>';

    if(!empty($show_date)){
      $time = ($show_date !== 'modified') ? $page->post_date : $page->post_modified;
      $output .= ' '.mysql2date($date_format, $time);
    }
  }

  function display_element($element, &$children, $max_depth, $depth = 0, $args, &$output) {

    $id_field = $this->db_fields['id'];
    $element->has_children = isset($children[$element->$id_field]);

    parent::display_element($element, $children, $max_depth, $depth, $args, $output);
  }
}
