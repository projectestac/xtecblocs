<?php

load_theme_textdomain('news-portal', get_template_directory() . '/languages');

if ( function_exists('register_sidebars') )
 register_sidebars(2,array(
        'before_widget' => '<div class="block block-node">',
    'after_widget' => '</div>',
 'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));


// WP-newsportals Pages Box  
 function widget_newsportal_pages() {
?>
  <div class="block block-node">

<h3><?php _e('Pages','news-portal'); ?></h3>
   <ul>
<li class="page_item"><a href="<?php bloginfo('url'); ?>">Home</a></li>

<?php wp_list_pages('title_li='); ?>

 </ul>
 </div>

<?php
}
if ( function_exists('register_sidebar_widget') )
    register_sidebar_widget(__('Pages','news-portal'), 'widget_newsportal_pages');


// WP-newsportals Search Box  
 function widget_newsportal_search() {
?>

  <div class="block block-node">
  <h3><?php _e('Search','news-portal'); ?></h3>
   <ul>

<form method="get" id="searchform" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<input type="text" size="10" value="<?php echo wp_specialchars($s, 1); ?>" name="s" id="s" /><input type="submit" id="sidebarsubmit" value="<?php _e('Search','news-portal');?>" />

 </form>

 </ul>
 </div>

<?php
}
if ( function_exists('register_sidebar_widget') )
    register_sidebar_widget(__('Search','news-portal'), 'widget_newsportal_search');

// WP-newsportal Blogroll  

?>
