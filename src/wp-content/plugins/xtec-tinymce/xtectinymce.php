<?php

/*
Plugin Name: XTEC TinyMCE
Plugin URI:
Description: This plugin has been developed using some TinyMCE plugins and files from TinyMCE Advanced 3.2 Plugin
Version: 1.0
Author: Francesc Bassas i Bullich
Author URI:
*/

function xtectinymce_addbuttons() {
   // Don't bother doing this stuff if the current user lacks permissions
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;
 
   // Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
     add_filter("mce_external_plugins", "add_tinymce_plugin",999);
     add_filter('mce_buttons_2', 'register_xtectinymce_buttons');
   }
}
 
function register_xtectinymce_buttons($buttons) {
   array_push($buttons, "separator", "table");
   array_push($buttons, "emotions");
   return $buttons;
}
 
function add_tinymce_plugin($plugin_array) {
   $plugin_array['table'] = WP_PLUGIN_URL . '/../plugins/xtec-tinymce/mce/table/editor_plugin.js';
   $plugin_array['emotions'] = WP_PLUGIN_URL . '/../plugins/xtec-tinymce/mce/emotions/editor_plugin.js';
   return $plugin_array;
}
 
add_action('init', 'xtectinymce_addbuttons');