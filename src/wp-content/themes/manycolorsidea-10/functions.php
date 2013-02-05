<?php

load_theme_textdomain('colors-idea', get_template_directory() . '/languages');

if ( function_exists('register_sidebar') )
    register_sidebar(array(
        'before_widget' => '<div class="boxed">',
    'after_widget' => '</div>',
 'before_title' => '<h2 class="title">',
        'after_title' => '</h2>',
    ));


// WP-atom Pages Box 
 function widget_atom_pages() {
?>

<div class="boxed">
<h2 class="title"><?php _e('Pages','colors-idea'); ?></h2>
   <ul>
<li class="page_item"><a href="<?php bloginfo('url'); ?>">Home</a></li>

<?php wp_list_pages('title_li='); ?>

 </ul>
 
 </div>

<?php
}
if ( function_exists('register_sidebar_widget') )
    register_sidebar_widget(__('Pages','colors-idea'), 'widget_atom_pages');


// WP-atom Search Box 
 function widget_atom_search() {
?>
 
<div class="boxed">
<h2 class="title"><?php _e('Search Posts','colors-idea'); ?></h2>
 
 
    <ul>
<li>
   <form id="searchform" method="get" action="<?php bloginfo('url'); ?>/index.php">
 
            <input type="text" name="s" size="18" /><br>

     
            <input type="submit" id="submit" name="Submit" value="<?php _e('Search','colors-idea');?>" />
      
     
 </form>

 
</li>
</ul>
</div>


<?php
}
if ( function_exists('register_sidebar_widget') )
    register_sidebar_widget(__('Search','colors-idea'), 'widget_atom_search');


 function widget_links_with_style() {
   global $wpdb;
   $link_cats = $wpdb->get_results("SELECT term_id, name FROM $wpdb->terms");
   foreach ($link_cats as $link_cat) {
  ?>
<div class="boxed">
<h2 class="title"><?php echo $link_cat->name; ?></h2>

   <ul>
   <?php get_links($link_cat->term_id, '<li>', '</li>', '<br />', FALSE, 'rand', TRUE,  TRUE, -1, TRUE); ?>
   </ul>
</div>
   <?php } ?>
   <?php }
   if ( function_exists('register_sidebar_widget') )
   register_sidebar_widget(__(' Links With Style','colors-idea'), 'widget_links_with_style');
   
 


?>