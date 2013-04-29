<?php
/**
 * Sample implementation of the Custom Header feature
 * http://codex.wordpress.org/Custom_Headers
 *
 * You can add an optional custom header image to header.php like so ...

	<?php $header_image = get_header_image();
	if ( ! empty( $header_image ) ) { ?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
			<img src="<?php header_image(); ?>" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="" />
		</a>
	<?php } // if ( ! empty( $header_image ) ) ?>

 *
 * @package Reddle
 * @since Reddle 1.0
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
 * @uses reddle_header_style()
 * @uses reddle_admin_header_style()
 * @uses reddle_admin_header_image()
 *
 * @package Reddle
 */
function reddle_custom_header_setup() {
	$args = array(
		'default-image'          => '',
		'default-text-color'     => '777',
		'width'                  => 1120,
		'height'                 => 252,
		'flex-height'            => true,
		'random-default'         => true,
		'wp-head-callback'       => 'reddle_header_style',
		'admin-head-callback'    => 'reddle_admin_header_style',
		'admin-preview-callback' => 'reddle_admin_header_image',
	);

	$args = apply_filters( 'reddle_custom_header_args', $args );

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
add_action( 'after_setup_theme', 'reddle_custom_header_setup' );

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
 * @package Reddle
 * @since Reddle 1.1
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

if ( ! function_exists( 'reddle_header_style' ) ) :
/**
 * Custom styles for our blog header
 */
function reddle_header_style() {
	// If no custom options for text are set, let's bail
	$header_image = get_header_image();
	if ( empty( $header_image ) && '' == get_header_textcolor() )
		return;
	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css">
	#masthead img {
		float: left;
	}
	<?php
		// Has the text been hidden? Let's hide it then.
		if ( 'blank' == get_header_textcolor() ) :
	?>
		#masthead > hgroup {
		    padding: 0;
		}
		#site-title,
		#site-description {
			position: absolute !important;
			clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
			clip: rect(1px, 1px, 1px, 1px);
		}
	<?php
		// If the user has set a custom color for the text use that
		else :
	?>
		#site-description {
			color: #<?php echo get_header_textcolor(); ?> !important;
		}
	<?php endif; ?>
	<?php
		// Is the main menu empty?
		if ( ! has_nav_menu( 'primary' ) ) :
	?>
		#header-image {
			margin-bottom: 3.23em;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif; // reddle_header_style()

if ( ! function_exists( 'reddle_admin_header_style' ) ) :
/**
 * Custom styles for the custom header page in the admin
 */
function reddle_admin_header_style() {
?>
	<style type="text/css">
	#headimg {
		background: #fff;
		text-align: center;
		max-width: 1120px;
	}
	#headimg .masthead {
		padding: 70px 0 41px;
	}
	.appearance_page_custom-header #headimg {
		border: none;
	}
	#headimg h1 {
		border-bottom: 1px solid #ddd;
		display: inline-block;
		font-weight: normal;
		font-family: Georgia, "Bitstream Charter", serif;
		font-size: 21px;
		line-height: 1.15;
		margin: 0;
		padding: 0 2.954209748892% .275em;
	}
	#headimg h1 a {
		color: #b12930;
		text-decoration: none;
	}
	#headimg h1 a:hover,
	#headimg h1 a:focus,
	#headimg h1 a:active {
		color: #000;
		text-decoration: none;
	}
	#headimg #desc {
		color: #777;
		display: block;
		font-family: Verdana, sans-serif;
		font-size: 11px;
		letter-spacing: 0.05em;
		line-height: 1.52727272727274;
		padding: 0.382em 0;
		text-transform: uppercase;
	}
	#headimg img {
		float: left;
		height: auto;
		max-width: 100%;
	}
	<?php
		// If the user has set a custom color for the text use that
		if ( get_header_textcolor() != HEADER_TEXTCOLOR ) :
	?>
		#headimg #desc {
			color: #<?php echo get_header_textcolor(); ?>;
		}
	<?php endif; ?>
	<?php
		// Has the text been hidden?
		if ( 'blank' == get_header_textcolor() ) :
	?>
	#headimg .masthead {
		display: none;
	}
	<?php endif; ?>
	</style>
<?php
}
endif; // reddle_admin_header_style

if ( ! function_exists( 'reddle_admin_header_image' ) ) :
/**
 * Custom markup for the custom header admin page
 */
function reddle_admin_header_image() { ?>
	<div id="headimg">
		<?php
		if ( 'blank' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) || '' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) )
			$style = ' style="display:none;"';
		else
			$style = ' style="color:#' . get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) . ';"';
		?>
		<div class="masthead">
			<h1><a onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
			<div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
		</div>
		<?php $header_image = get_header_image();
		if ( ! empty( $header_image ) ) : ?>
			<img src="<?php echo esc_url( $header_image ); ?>" alt="" />
		<?php endif; ?>
	</div>
<?php }
endif; // reddle_admin_header_image