<?php

// Set the content width

if ( ! isset( $content_width ) )
	$content_width = 600; /* pixels */

if ( ! function_exists( 'delicacy_setup' ) ):

function delicacy_setup() {

	// Enable theme translations
	load_theme_textdomain( 'delicacy', get_template_directory() . '/languages' );

	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Add stylesheet for the WYSIWYG editor
	add_editor_style();
	
	// Image thumbnails
	if (function_exists('add_theme_support')) {
		add_theme_support('post-thumbnails');
		set_post_thumbnail_size(136, 136, true);
		add_image_size('side-thumb', 80, 70, true);
		add_image_size('related-thumb', 100, 100, true);
		add_image_size('nivo-thumb', 588, 289, true); // nivo slider image
	}
	
	// Register menu
	register_nav_menus( array(
		'primary-menu' => __('Delicacy Main Menu','delicacy'),
	) );

	// Clean up the <head>
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'wp_generator');
	
	// Custom backgrounds support
	$defaults = array(
	'default-color'          => 'f8f8f8',
	'default-image'          => get_template_directory_uri().'/images/bg/bg01.png',
	'wp-head-callback'       => 'delicacy_custom_background_callback'
	);
	add_theme_support( 'custom-background', $defaults );
	
}
endif;

add_action( 'after_setup_theme', 'delicacy_setup' );

function delicacy_custom_background_callback() {

	/* Get the background image. */
	$image = get_background_image();

	/* If there's an image, just call the normal WordPress callback. We won't do anything here. */
	if ( !empty( $image ) ) {
		_custom_background_cb();
		return;
	}

	/* Get the background color. */
	$color = get_background_color();

	/* If no background color, return. */
	if ( empty( $color ) )
		return;

	/* Use 'background' instead of 'background-color'. */
	$style = "background: #{$color};";

?>
<style type="text/css">body { <?php echo trim( $style ); ?> }</style>
<?php

}

// Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.

function delicacy_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'delicacy_page_menu_args' );

function delicacy_filter_wp_title( $title ) {
    // Get the Site Name
    $site_name = get_bloginfo( 'name' );
    $filtered_title = '';
	// If site front page, append description
    if ( is_home() ) {
        // Get the Site Description
        $site_description = get_bloginfo( 'description' );
        // Append Site Description to title
        $filtered_title = $site_name . ' | ' . $site_description;
    } else {
		// Prepend name on single post and page
		$filtered_title = $title . ' | ' . $site_name;    
	}
	
    // Return the modified title
    return $filtered_title;
}
// Hook into 'wp_title'
add_filter( 'wp_title', 'delicacy_filter_wp_title' );

// Excerpt config
function delicacy_excerpt($more)
{
	global $post;
	return '... <a href="' . get_permalink($post->ID) . '">'.__('Continue reading', 'delicacy').' &raquo;</a>';
}
add_filter('excerpt_more', 'delicacy_excerpt');

function delicacy_excerpt_length($length)
{
	global $post;
	return 20;
}
add_filter('excerpt_length', 'delicacy_excerpt_length');

// JavaScript & CSS

function delicacy_enqueue_scripts()
{
	if (!is_admin()) {
		wp_enqueue_script('jquery');
		wp_register_script('delicacy', get_template_directory_uri() . '/js/delicacy.js');
		wp_enqueue_script('delicacy');
		wp_register_script('sfmenu', get_template_directory_uri() . '/js/superfish.js');
		wp_enqueue_script('sfmenu');
	}
	if (is_home()){
		wp_register_script('nivoslider', get_template_directory_uri() . '/js/nivo-slider/jquery.nivo.slider.pack.js');
		wp_enqueue_script('nivoslider');	
	}
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action('wp_enqueue_scripts', 'delicacy_enqueue_scripts');

function delicacy_enqueue_css()
{
	wp_register_style('color_scheme', get_template_directory_uri() . '/images/' . of_get_option('color_scheme','red') . '/style.css');
	wp_enqueue_style('color_scheme');
	wp_register_style('sfcss', get_template_directory_uri() . '/js/superfish.css');
	wp_enqueue_style('sfcss');
	wp_enqueue_style('style', get_stylesheet_uri());
	if (is_home()){
		wp_register_style('nivocss', get_template_directory_uri() . '/js/nivo-slider/nivo-slider.css');
		wp_enqueue_style('nivocss');
	}
}
add_action('wp_print_styles', 'delicacy_enqueue_css');


// Copyright function
function delicacy_copy_date() {

	global $wpdb;
	$daty = $wpdb->get_results("SELECT YEAR(min(post_date)) as 'start', YEAR(now()) as 'end' from wp_posts where post_status = 'publish'");

	if ($daty[0]->start != $daty[0]->end){
		$copy = "&copy; ".$daty[0]->start." - ".$daty[0]->end;
	}else{
		$copy = "&copy; ".$daty[0]->start;
	}
	return $copy;
}

/*-----------------------------------------------------------------------------------*/
/*	Meta-boxes setup
/*-----------------------------------------------------------------------------------*/
add_action('admin_init','Delicacy_admin_init');
function Delicacy_admin_init(){
	wp_enqueue_style('Delicacy_admin', get_template_directory_uri(). '/admin.css');
	wp_enqueue_script('tiny_mce');
}

require dirname(__FILE__).'/meta-box/meta-box-3.2.2.class.php';

class Delicacy_Meta_Box extends RW_Meta_Box {
	function check_field_wysiwyg() {
		if ($this->has_field('wysiwyg') && self::is_edit_page()) {
			add_action('admin_print_footer_scripts', 'wp_tiny_mce', 25);
		}
	}


	function show_field_wysiwyg($field, $meta) {
		$this->show_field_begin($field, $meta);
		echo "<textarea class='rw-wysiwyg foooobar large-text' name='{$field['id']}' id='{$field['id']}' cols='60' rows='10'>$meta</textarea>";
		$this->show_field_end($field, $meta);
		wp_editor( true, // true makes the editor "teeny"
			array(
				"editor_selector" => "rw-wysiwyg"
			)
		);
	}
}


/*-----------------------------------------------------------------------------------*/
/*	Comments
/*-----------------------------------------------------------------------------------*/
function delicacy_comments($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">

		<div class="the-comment">

			<?php echo get_avatar($comment,$size='60'); ?>

			<div class="comment-box">

				<div class="comment-author">
					<strong><?php echo get_comment_author_link() ?></strong>
					<small><?php printf(__('%1$s at %2$s','delicacy'), get_comment_date(),  get_comment_time()) ?><?php edit_comment_link(__('Edit','delicacy'),'  ','') ?> <?php comment_reply_link(array_merge( $args, array('reply_text' => 'Reply', 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?></small>
				</div>

				<div class="comment-text">
					<?php if ($comment->comment_approved == '0') : ?>
					<em><?php _e('Your comment awaits moderation.','delicacy') ?></em>
					<br />
					<?php endif; ?>
					<?php comment_text() ?>
				</div>

			</div>

		</div>

<?php }



/*-----------------------------------------------------------------------------------*/
/*	Register widets
/*-----------------------------------------------------------------------------------*/
if ( function_exists('register_sidebar') ) {
	register_sidebar(array(
		'name' => 'Right Sidebar',
		'before_widget' => '<div class="widget">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	));
	register_sidebar(array(
		'name' => 'Header Widget',
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => '',
	));
}

// Include functions
include("admin/widgets/about-widget.php");
include("admin/widgets/recent-posts-widget.php");
include("admin/widgets/archives-widget.php");
include("admin/widgets/social-widget.php");
include("admin/widgets/seasonal-recipes-widget.php");
include("admin/recipe-config.php");


/*-----------------------------------------------------------------------------------*/
/*	Options framework
/*-----------------------------------------------------------------------------------*/

if ( !function_exists( 'optionsframework_init' ) ) {
	define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/admin/options-framework/' );
	require_once dirname( __FILE__ ) . '/admin/options-framework/options-framework.php';
}

/*
 * This is an example of how to add custom scripts to the options panel.
 * This one shows/hides the an option when a checkbox is clicked.
 */

add_action('optionsframework_custom_scripts', 'optionsframework_custom_scripts');

function optionsframework_custom_scripts() { ?>

<script type="text/javascript">
jQuery(document).ready(function() {

	jQuery('#example_showhidden').click(function() {
  		jQuery('#section-example_text_hidden').fadeToggle(400);
	});

	if (jQuery('#example_showhidden:checked').val() !== undefined) {
		jQuery('#section-example_text_hidden').show();
	}

});
</script>

<?php
}
