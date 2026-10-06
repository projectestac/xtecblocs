<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package Reddle
 */

function reddle_jetpack_setup() {
	/**
	 * Add theme support for Responsive Videos.
	 */
	add_theme_support( 'jetpack-responsive-videos' );

	/**
	 * Add theme support for Infinte Scrolling.
	 */
	add_theme_support( 'infinite-scroll', array(
		'container' => 'content',
		'footer' => 'page',
		'footer_widgets' => array( 'sidebar-3', 'sidebar-4', 'sidebar-5' ),
	) );
}
add_action( 'after_setup_theme', 'reddle_jetpack_setup' );
