<?php

load_theme_textdomain('classic', get_template_directory() . '/languages');

/**
 * @package WordPress
 * @subpackage Classic_Theme
 */
if ( function_exists('register_sidebar') )
	register_sidebar(array(
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => '</li>',
		'before_title' => '',
		'after_title' => '',
	));

?>
