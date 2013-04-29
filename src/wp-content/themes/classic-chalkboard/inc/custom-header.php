<?php
/**
 *
 * @package Chalkboard
 * @since Chalkboard 1.0
 */

/**
 * Setup the WordPress core custom header feature.
 *
 * Use add_theme_support to register support for WordPress 3.4+
 * as well as provide backward compatibility for previous versions.
 * Use feature detection of wp_get_theme() which was introduced
 * in WordPress 3.4.
 *
 * @todo Rework this function to remove WordPress 3.4 support when WordPress 3.6 is released.
 *
 * @uses chalkboard_header_style()
 * @uses chalkboard_admin_header_style()
 * @uses chalkboard_admin_header_image()
 *
 * @package Chalkboard
 */
function chalkboard_custom_header_setup() {
	$args = array(
		'default-image'          => '',
		'default-text-color'     => 'fff',
		'width'                  => 960,
		'height'                 => 250,
		'flex-height'            => true,
		'flex-width'            => true,
		'wp-head-callback'       => 'chalkboard_header_style',
		'admin-head-callback'    => 'chalkboard_admin_header_style',
		'admin-preview-callback' => 'chalkboard_admin_header_image',
	);

	$args = apply_filters( 'chalkboard_custom_header_args', $args );

	if ( function_exists( 'wp_get_theme' ) ) {
		add_theme_support( 'custom-header', $args );
	} else {
		// Compat: Versions of WordPress prior to 3.4.
		define( 'HEADER_TEXTCOLOR',    $args['default-text-color'] );
		define( 'HEADER_IMAGE',        $args['default-image'] );
		define( 'HEADER_IMAGE_WIDTH',  $args['width'] );
		define( 'HEADER_IMAGE_HEIGHT', $args['height'] );
		add_custom_image_header( $args['wp-head-callback'], $args['admin-head-callback'], $args['admin-preview-callback'] );
	}
}
add_action( 'after_setup_theme', 'chalkboard_custom_header_setup' );

/**
 * Shiv for get_custom_header().
 *
 * get_custom_header() was introduced to WordPress
 * in version 3.4. To provide backward compatibility
 * with previous versions, we will define our own version
 * of this function.
 *
 * @todo Remove this function when WordPress 3.6 is released.
 * @return stdClass All properties represent attributes of the curent header image.
 *
 * @package Chalkboard
 * @since Chalkboard 1.1
 */

if ( ! function_exists( 'get_custom_header' ) ) {
	function get_custom_header() {
		return (object) array(
			'url'           => get_header_image(),
			'thumbnail_url' => get_header_image(),
			'width'         => HEADER_IMAGE_WIDTH,
			'height'        => HEADER_IMAGE_HEIGHT,
		);
	}
}

if ( ! function_exists( 'chalkboard_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see chalkboard_custom_header_setup().
 *
 * @since Chalkboard 1.0
 */
function chalkboard_header_style() {

	// If no custom options for text are set, let's bail
	// get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value
	if ( HEADER_TEXTCOLOR == get_header_textcolor() )
		return;
	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css">
	<?php
		// Has the text been hidden?
		if ( 'blank' == get_header_textcolor() ) :
	?>
		.site-title,
		.site-description {
			position: absolute !important;
			clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
			clip: rect(1px, 1px, 1px, 1px);
		}
	<?php
		// If the user has set a custom color for the text use that
		else :
	?>
		.site-title a,
		.site-description {
			color: #<?php echo get_header_textcolor(); ?> !important;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif; // chalkboard_header_style

if ( ! function_exists( 'chalkboard_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * @see chalkboard_custom_header_setup().
 *
 * @since Chalkboard 1.0
 */
function chalkboard_admin_header_style() {
?>
	<style type="text/css">
	.appearance_page_custom-header #headimg {
		background: #000 url('<?php echo get_template_directory_uri(); ?>/images/chalk2.jpg') repeat;
		background-size: 360px auto;
		border: none;
		border-radius: 10px;
		box-shadow: 5px 5px 20px rgba(0,0,0,.5) inset;
		max-width: 1160px;
		padding: 5px 0;
	}
	#headimg h1,
	#desc {
	}
	#headimg h1 {
		font-family: "Gloria Hallelujah", Arial, Helvetica, sans-serif;
		font-size: 50px;
		font-weight: bold;
		line-height: 62px;
		margin: 33px 0 5px;
		text-align: center;
		text-decoration: none;
		text-transform: uppercase;
	}
	#headimg h1 a {
		text-decoration: none;
	}
	#desc {
		font-family: "Gloria Hallelujah", Arial, Helvetica, sans-serif;
		font-size: 24px;
		font-weight: normal;
		line-height: 38px;
		margin: 0 0 1.6em;
		text-align: center;
	}
	#headimg img {
		display: block;
		margin: 15px auto;
	}
	</style>
<?php
}
endif; // chalkboard_admin_header_style

if ( ! function_exists( 'chalkboard_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * @see chalkboard_custom_header_setup().
 *
 * @since Chalkboard 1.0
 */
function chalkboard_admin_header_image() { ?>
	<div id="headimg">
		<?php
		if ( 'blank' == get_header_textcolor() || '' == get_header_textcolor() )
			$style = ' style="display:none;"';
		else
			$style = ' style="color:#' . get_header_textcolor() . ';"';
		?>
		<?php $header_image = get_header_image();
		if ( ! empty( $header_image ) ) : ?>
			<img src="<?php echo esc_url( $header_image ); ?>" alt="" />
		<?php endif; ?>
		<h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
	</div>
<?php }
endif; // chalkboard_admin_header_image