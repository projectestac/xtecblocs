<?php

/**

 * Chalkboard functions and definitions

 *

 * @package Chalkboard

 * @since Chalkboard 1.0

 */



/**

 * Set the content width based on the theme's design and stylesheet.

 *

 * @since Chalkboard 1.0

 */

if ( ! isset( $content_width ) )

	$content_width = 960; /* pixels */



function chalkboard_content_width() {



	global $content_width;



	if ( is_active_sidebar( 'sidebar-1' ) )

		$content_width = 624;



	if ( is_page_template( 'nosidebar-page.php' ) )

		$content_width = 960;

}

add_action( 'template_redirect', 'chalkboard_content_width' );



if ( ! function_exists( 'chalkboard_setup' ) ) :

/**

 * Sets up theme defaults and registers support for various WordPress features.

 *

 * Note that this function is hooked into the after_setup_theme hook, which runs

 * before the init hook. The init hook is too late for some features, such as indicating

 * support post thumbnails.

 *

 * @since Chalkboard 1.0

 */

function chalkboard_setup() {


	/**

	 * Theme options panel.

	 */

	require_once ( get_template_directory() . '/theme-options.php' );



	/**

	 * Custom template tags for this theme.

	 */

	require( get_template_directory() . '/inc/template-tags.php' );



	/**

	 * Custom functions that act independently of the theme templates

	 */

	require( get_template_directory() . '/inc/extras.php' );



	/**

	 * Customizer additions

	 */

	require( get_template_directory() . '/inc/customizer.php' );



	/* Add support for custom backgrounds */

	$args = array(

		'default-color' => 'f1eedc',

		'default-image' => get_template_directory_uri() . '/images/walltexture.png',

	);



	$args = apply_filters( 'chalkboard_custom_background_args', $args );



	if ( function_exists( 'wp_get_theme' ) ) { //TODO: Remove this shiv after 3.6

		add_theme_support( 'custom-background', $args );

	} else {

		define( 'BACKGROUND_COLOR', $args['default-color'] );

		define( 'BACKGROUND_IMAGE', $args['default-image'] );

		add_custom_background();

	}

function add_nofollow_cat( $text ) {
$text = str_replace('rel="category"', '', $text); 
$text = str_replace('rel="category tag"', 'rel="tag"', $text); 
return $text;
}
add_filter( 'the_category', 'add_nofollow_cat' );



	/**

	 * Make theme available for translation

	 * Translations can be filed in the /languages/ directory

	 * If you're building a theme based on Chalkboard, use a find and replace

	 * to change 'classicchalkboard' to the name of your theme in all the template files

	 */

	load_theme_textdomain( 'classicchalkboard', get_template_directory() . '/languages' );



	/**

	 * Add default posts and comments RSS feed links to head

	 */

	add_theme_support( 'automatic-feed-links' );



	/**

	 * Enable support for Post Thumbnails

	 */

	add_theme_support( 'post-thumbnails' );



	/**

	 * This theme uses wp_nav_menu() in one location.

	 */

	register_nav_menus( array(

		'primary' => __( 'Primary Menu', 'classicchalkboard' ),

	) );



	/**

	 * Enable support for Post Formats

	 */

	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );

}

endif; // chalkboard_setup

add_action( 'after_setup_theme', 'chalkboard_setup' );



/**

 * Setup the WordPress core custom background feature.

 *

 * Use add_theme_support to register support for WordPress 3.4+

 * as well as provide backward compatibility for WordPress 3.3

 * using feature detection of wp_get_theme() which was introduced

 * in WordPress 3.4.

 *

 * @todo Remove the 3.3 support when WordPress 3.6 is released.

 *

 * Hooks into the after_setup_theme action.

 */

function chalkboard_register_custom_background() {

	$args = array(

		'default-color' => 'ffffff',

		'default-image' => '',

	);



	$args = apply_filters( 'chalkboard_custom_background_args', $args );



	if ( function_exists( 'wp_get_theme' ) ) {

		add_theme_support( 'custom-background', $args );

	} else {

		define( 'BACKGROUND_COLOR', $args['default-color'] );

		if ( ! empty( $args['default-image'] ) )

			define( 'BACKGROUND_IMAGE', $args['default-image'] );

		add_custom_background();

	}

}

add_action( 'after_setup_theme', 'chalkboard_register_custom_background' );



/**

 * Register widgetized area and update sidebar with default widgets

 *

 * @since Chalkboard 1.0

 */

function chalkboard_widgets_init() {

	register_sidebar( array(

		'name' => __( 'Sidebar', 'classicchalkboard' ),

		'id' => 'sidebar-1',

		'before_widget' => '<aside id="%1$s" class="widget %2$s">',

		'after_widget' => '</aside>',

		'before_title' => '<h1 class="widget-title">',

		'after_title' => '</h1>',

	) );

	register_sidebar( array(

		'name' => __( 'Footer Sidebar 1', 'classicchalkboard' ),

		'id' => 'footer-sidebar-1',

		'before_widget' => '<aside id="%1$s" class="widget %2$s">',

		'after_widget' => '</aside>',

		'before_title' => '<h1 class="widget-title">',

		'after_title' => '</h1>',

	) );

	register_sidebar( array(

		'name' => __( 'Footer Sidebar 2', 'classicchalkboard' ),

		'id' => 'footer-sidebar-2',

		'before_widget' => '<aside id="%1$s" class="widget %2$s">',

		'after_widget' => '</aside>',

		'before_title' => '<h1 class="widget-title">',

		'after_title' => '</h1>',

	) );

	register_sidebar( array(

		'name' => __( 'Footer Sidebar 3', 'classicchalkboard' ),

		'id' => 'footer-sidebar-3',

		'before_widget' => '<aside id="%1$s" class="widget %2$s">',

		'after_widget' => '</aside>',

		'before_title' => '<h1 class="widget-title">',

		'after_title' => '</h1>',

	) );

}

add_action( 'widgets_init', 'chalkboard_widgets_init' );





/**

 * Enqueue Google Fonts

 */



function chalkboard_fonts() {



	$protocol = is_ssl() ? 'https' : 'http';



	/*	translators: If there are characters in your language that are not supported

		by Gloria Hallelujah, translate this to 'off'. Do not translate into your own language. */



	if ( 'off' !== _x( 'on', 'Gloria Hallelujah font: on or off', 'classicchalkboard' ) ) {



		wp_register_style( 'chalkboard-gloria', "$protocol://fonts.googleapis.com/css?family=Gloria+Hallelujah" );



	}



	/*	translators: If there are characters in your language that are not supported

	by Roberto Condensed, translate this to 'off'. Do not translate into your own language. */



	if ( 'off' !== _x( 'on', 'Roberto Condensed font: on or off', 'minimalizine' ) ) {



		$roboto_subsets = 'latin,latin-ext';



		/* translators: To add an additional Open Sans character subset specific to your language, translate

		this to 'greek', 'cyrillic' or 'vietnamese'. Do not translate into your own language. */

		$roboto_subset = _x( 'no-subset', 'Open Sans font: add new subset (greek, cyrillic, vietnamese)', 'minimalizine' );



		if ( 'cyrillic' == $roboto_subset )

			$roboto_subsets .= ',cyrillic,cyrillic-ext';

		elseif ( 'greek' == $roboto_subset )

			$roboto_subsets .= ',greek,greek-ext';

		elseif ( 'vietnamese' == $roboto_subset )

			$roboto_subsets .= ',vietnamese';



		$roboto_query_args = array(

			'family' => 'Roboto:400italic,700italic,400,700',

			'subset' => $roboto_subsets,

		);

		wp_register_style( 'chalkboard-roboto', add_query_arg( $roboto_query_args, "$protocol://fonts.googleapis.com/css" ), array(), null );

	}



}



add_action( 'init', 'chalkboard_fonts' );



/**

 * Enqueue scripts and styles

 */

function chalkboard_scripts() {

	wp_enqueue_style( 'style', get_stylesheet_uri() );



	wp_enqueue_script( 'small-menu', get_template_directory_uri() . '/js/small-menu.js', array( 'jquery' ), '20120206', true );

global $classicchalkboard_options; $classicchalkboard_settings = get_option( 'classicchalkboard_options', $classicchalkboard_options );
	if ($classicchalkboard_settings['green_background'] ) {
	wp_enqueue_script( 'chalkboardbackground', get_template_directory_uri() . '/js/jquery.chalkboard.js', array( 'jquery' ), '20130518', true );
}
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {

		wp_enqueue_script( 'comment-reply' );

	}



	if ( is_singular() && wp_attachment_is_image() ) {

		wp_enqueue_script( 'keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );

	}

	wp_enqueue_style( 'chalkboard-gloria' );

	wp_enqueue_style( 'chalkboard-roboto' );

}

add_action( 'wp_enqueue_scripts', 'chalkboard_scripts' );



/**

 * Enqueue font styles in custom header admin

 */



function chalkboard_admin_fonts( $hook_suffix ) {



	if ( 'appearance_page_custom-header' != $hook_suffix )

		return;



	wp_enqueue_style( 'chalkboard-gloria' );



}

add_action( 'admin_enqueue_scripts', 'chalkboard_admin_fonts' );



/**

 * Implement the Custom Header feature

 */

require( get_template_directory() . '/inc/custom-header.php' );