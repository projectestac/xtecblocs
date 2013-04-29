<?php /* Mystique/digitalnature */


// Theme setup
add_action('after_setup_theme',   'mystique_setup');
add_action('widgets_init',        'mystique_widgets_init');



// Class adjustments (makes the css compatible with the Atom version of the theme)
add_filter('page_css_class',      'mystique_page_css_classes', 10, 2);
add_filter('nav_menu_css_class',  'mystique_menu_css_classes', 10, 2);
add_filter('body_class',          'mystique_body_class');
add_filter('post_class',          'mystique_post_class');
add_filter('comment_class',       'mystique_comment_class');



// We don't want to confuse old theme users, so we show a notice in the dashboard
// telling the user that this is the basic version of the theme, and where he can find the official version
if(is_admin()){

  if(!get_option('mystique_basic_notice')){

    add_action('admin_notices',                 'mystique_basic_notice');
    add_action('wp_ajax_mystique_hide_notice',  'mystique_hide_notice');

    function mystique_basic_notice(){
       ?>
      <div class="basic-notice updated">
        <p>
          <?php
            printf(__('You are using the standard version of Mystique. Starting from version 3.0, Mystique has been also ported to a powerful framework that incorporates extended functionality and advanced customization options. Download and install %1$s from the %2$s, <strong>only if you need its features</strong>!', 'mystique'),
              sprintf('<a href="http://digitalnature.eu/themes/mystique/">%s</a>', '<strong>Mystique 3+</strong>'),
              sprintf('<a href="http://digitalnature.eu/">%s</a>', __('official website', 'mystique'))
            );
          ?>
        </p>
        <p>
          <a class="button-secondary hide-me"><?php _e('Close and don\'t show this message again', 'mystique'); ?></a>
          <br clear="all" />
        </p>
      </div>

      <script type="text/javascript">
       jQuery(document).ready(function($){
         $('#wpbody').delegate('.basic-notice a.hide-me', 'click', function(){
           $.ajax({
             url: ajaxurl,
             type: 'GET',
             context: this,
             data: ({
               action: 'mystique_hide_notice',
               _ajax_nonce: '<?php echo wp_create_nonce('mystique_hide_notice'); ?>'
             }),
             success: function(data){
               $(this).parents('.basic-notice').remove();
             }
           });
         });
       });

      </script>
      <?php
    }

    function mystique_hide_notice(){
      check_ajax_referer('mystique_hide_notice');
      update_option('mystique_basic_notice', true);
      die();
    }

  }

  // removes the notice status from the db
  add_action('switch_theme', 'mystique_remove_notice_record');

  function mystique_remove_notice_record(){
    delete_option('mystique_basic_notice');
  }

}


// Set up widget areas (sidebars)
function mystique_widgets_init(){

  // one sidebar, even though the theme supports 3 columns (because there's no interface to switch the layout)
  register_sidebar(array(
    'name'           => __('Primary Sidebar', 'mystique'),
    'id'             => 'sidebar-1',
    'before_widget'  => '<li class="block"><div class="block-content block-%2$s clear-block" id="instance-%1$s">',
    'after_widget'   => '</div></li>',
    'before_title'   => '<div class="title"><h3>',
    'after_title'    => '</h3><div class="bl"></div><div class="br"></div></div>'
  ));

}



// Main theme set up
function mystique_setup(){

  // match the editor with the theme css
  add_editor_style();

  // thumbnail support
  add_theme_support('post-thumbnails');

  // posts & comments RSS feed links
  add_theme_support('automatic-feed-links');

  // translations (/lang dir)
  load_theme_textdomain('mystique', TEMPLATEPATH.'/lang' );

  $locale = get_locale();
  $locale_file = TEMPLATEPATH.'/lang/$locale.php';

  if(is_readable($locale_file))
    require_once($locale_file);

  // custom menus
  register_nav_menus(array(
    'top'     => __('Top Navigation',     'mystique'),
    'primary' => __('Primary Navigation', 'mystique'),
    'footer'  => __('Footer Navigation',  'mystique'),
  ));

  // yet another useless variable to fill up the WP global space...
  global $content_width;

  if(!isset($content_width))
    $content_width = 640;
}



// Loads the comment template.
// Used as a callback function for wp_list_comments()
function mystique_comment($comment, $args, $depth){
  $GLOBALS['comment'] = $comment;
  get_template_part('comment');
}



// Logo text
function mystique_logo(){
  $sitename = get_bloginfo('name');

  // h1 only on the front/home page, for seo reasons
  $tag = (is_home() || is_front_page()) ? 'h1' : 'div';

  $words = explode(" ", $sitename); // we get a special treat if the logo is made out of 2 or 3 words
    if(!empty($words[1]) && empty($words[3])):
      $words[1] = "<span class=\"alt\">{$words[1]}</span>";
      $sitename = implode(" ", $words); // leave the space here and remove it trough css to avoid seo problems
    endif;

  echo '<'.$tag.' id="logo"><a href="'.home_url().'">'.$sitename.'</a></'.$tag.'>';
}



// Generates semantic classes for <body>
function mystique_body_class($classes){
  global $wp_query, $current_user, $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

  // Special classes for BODY element when a single post
  if(is_single()):
    $postname = $wp_query->post->post_name;

    the_post();

    // post title
    $classes[] = "title-{$postname}";

    // Adds category classes for each category on single posts
    if($cats = get_the_category())
      foreach($cats as $cat)
        $classes[] = "category-{$cat->slug}";

    // Adds tag classes for each tags on single posts
    if($tags = get_the_tags())
      foreach($tags as $tag)
        $classes[] = "tag-{$tag->slug}";

    // Adds author class for the post author
    $classes[] = "author-".sanitize_title_with_dashes(strtolower(get_the_author_meta('user_login')));
    rewind_posts();

  elseif(is_page()):
    $pagename = $wp_query->post->post_name;
    the_post();
    $classes[] = "page-{$pagename}";
    $classes[] = "author-".sanitize_title_with_dashes(strtolower(get_the_author_meta('user_login')));

    rewind_posts();
  endif;

  // wp's "browser detection"
  if($is_lynx) $browser = 'lynx';
  elseif($is_gecko) $browser = 'gecko';
  elseif($is_opera) $browser = 'opera';
  elseif($is_NS4) $browser = 'ns4';
  elseif($is_safari) $browser = 'safari';
  elseif($is_chrome) $browser = 'chrome';
  elseif($is_IE) $browser = 'ie';
  else $browser = 'unknown';
  if($is_iphone) $browser .= '-iphone';

  $classes[] = "browser-{$browser}";

  return $classes;
}



// Generates semantic classes for posts
function mystique_post_class($classes){
  global $wp_query;

  $current_post = $wp_query->current_post + 1;

  // post alt
  $classes[] = "count-{$current_post}";
  $classes[] = ($current_post % 2) ? 'odd' : 'even alt';

  // author
  $classes[] = 'author-'.sanitize_html_class(get_the_author_meta('user_nicename'), get_the_author_meta('ID'));

  // password-protected?
  if (post_password_required())
    $classes[] = 'protected';

  // first/last class ("first" and "count-1" are the same)
  if($current_post == 1) $classes[] = 'first'; elseif($current_post == $wp_query->post_count) $classes[] = 'last';

  return $classes;
}



// Generates semantic classes for comments
function mystique_comment_class($classes) {
  global $post, $comment;

  // avatars enabled?
  if(get_option('show_avatars')) $classes[] = 'with-avatars';

  // user roles
  if($comment->user_id > 0):
    $user = new WP_User($comment->user_id);

    if (is_array($user->roles))
      foreach ($user->roles as $role) $classes[] = "role-{$role}";
    $classes[] = 'user-'.sanitize_html_class($user->user_nicename, $user->ID);
  else:
    $classes[] = 'reader name-'.sanitize_title(get_comment_author());
  endif;

  // needs moderation?
  if($comment->comment_approved == '0') $classes[] = 'awaiting-moderation';

  return $classes;
}



// Adjust classes added by the page walker to match the ones from custom menus
function mystique_page_css_classes($classes, $page){
  // use page (safe) name instead of ID; nobody styles IDs...
  $new_classes = array("page-{$page->post_name}");

  // adjust active menu classes to match the ones added by wp_nav_menu()
  foreach($classes as $class)
    if($class == 'current_page_item') $new_classes[] = 'current-menu-item';
    elseif($class == 'current_page_parent') $new_classes[] = 'current-menu-parent';
    elseif($class == 'current_page_ancestor') $new_classes[] = 'current-menu-ancestor';

  // overwrite
  return $new_classes;
}



// Adjust classes added by the menu walker. Removes useless bloat classes like id-xxx
function mystique_menu_css_classes($classes, $item){
  $new_classes = array('menu-'.sanitize_title($item->title));

  foreach($classes as $class)
    if(in_array($class, array('current-menu-item', 'current-menu-parent', 'current-menu-ancestor'))) $new_classes[] = $class;

  return $new_classes;
}



// Default page menu. Make the mark-up match the one from custom menus
function mystique_page_menu($args){
  $defaults = array(
    'container'       => 'div',
    'container_class' => '',
    'container_id'    => '',
    'menu_class'      => 'menu',
    'menu_id'         => '',
    'echo'            => true,
    'before'          => '',    // these two are ignored by wp_page_menu; @todo: find a way to use them on the generated menu, regex maybe?
    'after'           => '',
    'link_before'     => '',
    'link_after'      => '',
    'depth'           => 0,
    'walker'          => '',
    'slug'            => 'menu-pages', // extra
    'include_home'    => true,
  );

  $args = wp_parse_args($args, $defaults);
  $args = apply_filters('wp_nav_menu_args', $args);
  $args = (object)$args;

  $nav_menu = $items = '';

  $show_container = false;
  if($args->container):
    $allowed_tags = apply_filters('wp_nav_menu_container_allowedtags', array('div', 'nav'));
    if(in_array($args->container, $allowed_tags)):
      $show_container = true;
      $class = $args->container_class ? ' class="'.$args->container_class.'"' : ' class="menu-'.$args->slug.'-container"';
      $id = $args->container_id ? ' id="'.$args->container_id.'"' : '';
      $nav_menu .= '<'.$args->container.$id.$class. '>';
    endif;
  endif;

  // add 'home' menu item
  if($args->include_home)
    $items .= '<li class="home '.((is_front_page() && !is_paged()) ? 'current-menu-item' : '').'"><a href="'.home_url('/').'" title="'.__('Home Page', 'mystique').'">'.$args->link_before.__('Home', 'mystique').$args->link_after.'</a></li>';

  // pass arguments to wp_list_pages (most of them are ignored)
  $page_list_args = (array)$args;

  // if the front page is a page, add it to the exclude list
  if(get_option('show_on_front') == 'page') $page_list_args['exclude'] = get_option('page_on_front');

  // other extra arguments
  $page_list_args['echo'] = false;
  $page_list_args['title_li'] = '';

  // get page list
  $items .= str_replace(array("\r", "\n", "\t"), '', wp_list_pages($page_list_args));

  // attributes
  $slug = empty($args->menu_id) ? 'menu-'.$args->slug : $args->menu_id;

  $attributes = ' id="'.$slug.'"';
  $attributes .= $args->menu_class ? ' class="'.$args->menu_class.'"' : '';

  $nav_menu .= '<ul'.$attributes.'>';
  $nav_menu .= $items;
  $nav_menu .= '</ul>';

  if($show_container)
    $nav_menu .= '</'.$args->container.'>';

  $nav_menu = apply_filters('wp_page_menu', $nav_menu, $args);

  if($args->echo) echo $nav_menu; else return $nav_menu;
}


