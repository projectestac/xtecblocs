<?php
/*
 * Deprecated functions, methods (here as functions) or action/filter tags.
 * These only exist to provide backwards compatibility with older versions of Atom,
 * inside outdated theme extensions such as child themes, atom modules or plugins.
 * They will be removed at some point from the code...
 *
 * Function naming conventions:
 * - for class methods: __ClassNameMethodName
 * - for functions: __atom_function_name
 *
 * Read the documentation for more info: http://digitalnature.eu/docs/
 *
 * @revised   January 2, 2012
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */



/**
 * Notifies the user of a deprecated function or method
 *
 * @since 2.0
 * @param string $message Message to display
 * @return bool
 */
function atom_deprecated($message, $level = 4){
  $debug = debug_backtrace();
  $message = '&lt;'.basename($debug[$level]['file']).'&gt; '.$message;

  trigger_error($message);

  return atom()->log($message, Atom::WARNING);
}



/***************************************************************************************************************************************************************************/



/**
 * The 'jquery_init' atom action tag
 *
 * @deprecated 2.0
 */
add_action('wp_footer', '__atom_jquery_init', 999);
function __atom_jquery_init(){
  global $wp_filter;
  if(isset($wp_filter['jquery_init'])){

    ob_start();
    Atom::action('jquery_init');
    $js = trim(ob_get_clean());

    if(!empty($js)){
      ?>
      <script>
       jQuery(document).ready(function($){
         <?php echo $js; ?>
       });
      </script>
      <?php
    }

    atom_deprecated('The jquery_init tag has been deprecated since Atom 2.0 with no alternative available. Please update your template files or modules that are still using this tag.');
  }

}

/**
 * The 'ajax_requests' atom action tag
 *
 * @deprecated 2.0
 */
Atom::add('requests', '__atom_ajax_requests', 999);
function __atom_ajax_requests(){
  global $wp_filter;
  if(isset($wp_filter['atom_ajax_requests'])){
    Atom::action('ajax_requests');
    atom_deprecated('The atom_ajax_requests action tag has been deprecated since Atom 2.0. Use the atom_requests tag instead.');

  }
}




/**
 * Returns the comment search query
 *
 * @deprecated 2.0
 */
function __AtomGetCommentSearch(){
  return atom_deprecated('Comment Search is deprecated since Atom 2.0. This feature has been removed. Please update your template files.');
}



/**
 * Outputs the comment search query
 *
 * @deprecated 2.0
 */
function __AtomCommentSearch(){
  atom_deprecated('Comment Search is deprecated since Atom 2.0. This feature has been removed. Please update your template files.');
}



/**
 * Outputs the comment search query
 *
 * @deprecated 2.0
 */
function __AtomCommentPostRedirectURL(){
  atom_deprecated('This method has been deprecated since Atom 2.0. Please update your template files.');
  return '';
}



/**
 * Registers default theme options
 *
 * @deprecated 2.0
 */
function __AtomAddDefaultOptions($options){
  atom()->registerDefaultOptions($options);
  atom_deprecated('AddDefaultOptions() is deprecated since Atom 2.0 in favour of RegisterDefaultOptions()');
}



/**
 * Registers default theme options
 *
 * @deprecated 2.0
 */
function __AtomSetDeafultOptions($options){
  atom()->registerDefaultOptions($options);
  atom_deprecated('SetDefaultOptions() is deprecated since Atom 2.0 in favour of RegisterDefaultOptions()');
}



/**
 * Set up widget areas (sidebars).
 *
 * @deprecated 2.0
 */
function __AtomSetWidgetAreas(){
  $args = func_get_args();
  $app = &Atom::app();
  call_user_func_array(array(&$app, 'RegisterWidgetArea'), array_values($args));
  atom_deprecated('setWidgetAreas() is deprecated since Atom 2.0 in favour of RegisterWidgetArea()');
}



/**
 * Registers default theme options
 *
 * @deprecated 2.0
 */
function __AtomSetLayoutTypes(){

  $args = func_get_args();
  foreach($args as $arg)
    atom()->registerLayoutTypes($arg);

  atom_deprecated('SetLayoutType() is deprecated since Atom 2.0 in favour of RegisterLayoutType()');
}



/**
 * Registers default theme options
 *
 * @deprecated 2.0
 */
function __AtomSetMenus(){

  $args = func_get_args();
  foreach($args as $arg)
    atom()->registerMenu($arg);

  atom_deprecated('SetMenus() is deprecated since Atom 2.0 in favour of registerMenu()');
}



/**
 * Registers default theme options
 *
 * @deprecated 2.0
 */
function __AtomSetActiveWidgets(){
  return atom_deprecated('SetActiveWidgets() is deprecated since Atom 2.0 with no alternative');
}



/**
 * The theme settings object
 *
 * @deprecated 2.0
 */
function __AtomAdmin(){
  atom_deprecated('The Atom::admin() method is deprecated since Atom 2.0 in favour of the interface property');
  return atom()->interface;
}




/**
 * Queues a debug message
 *
 * @since       1.3
 * @deprecated  2.0
 * @param       string $message
 * @param       int $code
 * @return      bool
 */
function __AtomAddDebugMessage($message, $code = Atom::NOTICE){
  atom_deprecated('The AddDebugMessage() method is deprecated since Atom 2.0 in favour of the log() method');
  return atom()->log($message, $code);
}




/*
 * Check if a theme option is enabled
 * $options can also be a prefix of a set of options, eg. 'media'
 *   (Useful if the theme has custom-made options that mix with default ones, or are from the same category)
 *
 * @since       1.0
 * @deprecated  2.0
 * @param       string $option   The option name.
 * @return      bool             True if exists, false otherwise
 */
function __AtomIsOptionEnabled($option){
  atom_deprecated('The isOptionEnabled() method is deprecated since Atom 2.0 in favour of the OptionExists() method');
  return atom()->OptionExists($option);
}



/*
 * Get social media links
 *
 * @since       1.0
 * @deprecated  2.0
 * @param       string $deprecated   Optional CSS classes, doesn't have any effect any more
 * @param       string $deprecated   Nudge direction, doesn't have any effect any more
 * @param       int $deprecated      Nudge amount, doesn't have any effect any more
 */
function __AtomGetSocialMediaLinks($deprecated = '', $deprecated = false, $deprecated = false){
  Atom::action('social_media_links');
}



/*
 * Output social media links
 *
 * @since       1.0
 * @deprecated  2.0
 * @param       string $deprecated   Optional CSS classes, doesn't have any effect any more
 * @param       string $deprecated   Nudge direction, doesn't have any effect any more
 * @param       int $deprecated      Nudge amount, doesn't have any effect any more
 */
function __AtomSocialMediaLinks($deprecated = '', $deprecated = false, $deprecated = false){
  Atom::action('social_media_links');
}



/*
 * Output social media links
 *
 * @since       1.0
 * @deprecated  2.0
 * @param       string $deprecated   Optional CSS classes, doesn't have any effect any more
 * @param       string $deprecated   Nudge direction, doesn't have any effect any more
 * @param       int $deprecated      Nudge amount, doesn't have any effect any more
 */
function __AtomGetPageNavi($args = array()){
  return atom()->getPagination($args);
}



/*
 * Output social media links
 *
 * @since       1.0
 * @deprecated  2.0
 * @param       string $deprecated   Optional CSS classes, doesn't have any effect any more
 * @param       string $deprecated   Nudge direction, doesn't have any effect any more
 * @param       int $deprecated      Nudge amount, doesn't have any effect any more
 */
function __AtomPageNavi($args = array()){
  echo atom()->getPagination($args);
}








/**
 * Displays page-links for paginated posts.
 * Same as wp_link_pages(), but formats the output to match the other pagination blocks.
 * Must be used inside the loop
 *
 * @since 1.0
 * @todo: find a way to integrate this into pageNavi()
 */
function __AtomObjectPaginate(){
  atom()->Pagination();
}





/*
 * Get the translation of a string
 * Uses WP's translate() function.
 * These functions only exist to make templating easier by omitting the textdomain (ATOM) :)
 *
 * @since        1.2
 * @deprecated   2.0
 * @param        string $string    String to translate
 * @return       string            Translated string
 */
function _a($string){
  atom_deprecated('The _a() function is deprecated since Atom 2.0 in favour of the atom()->t() method');
  return translate($string, ATOM);
}



/*
 * Output the translation of a string
 * Uses WP's translate() function.
 *
 * @since        1.2
 * @deprecated   2.0
 * @param        string $string
 */
function _ae($string){
  atom_deprecated('The _ae() function is deprecated since Atom 2.0 in favour of the atom()->te() method');
  echo translate($string, ATOM);
}



/*
 * Get the single/plural form of a string
 *
 * @since        1.2
 * @deprecated   2.0
 * @param        string $string
 * @param        string $plural
 * @param        int $number
 * @return       string
 */
function _an($single, $plural, $number){
  atom_deprecated('The _an() function is deprecated since Atom 2.0 in favour of the atom()->nt() method');
  $translations = &get_translations_for_domain(ATOM);
  $translation = $translations->translate_plural($single, $plural, $number);
  return apply_filters('ngettext', $translation, $single, $plural, $number, ATOM);
}
