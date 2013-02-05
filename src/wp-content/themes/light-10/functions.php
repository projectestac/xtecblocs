<?php

load_theme_textdomain('light', get_template_directory() . '/languages');

if ( function_exists('register_sidebar') )
	register_sidebar(array(
		'before_widget' => '', // Removes <li>
		'after_widget' => '', // Removes </li>
		'before_title' => '<h2>', // Replaces <h2>
		'after_title' => '</h2>', // Replaces </h2>
	));
?>