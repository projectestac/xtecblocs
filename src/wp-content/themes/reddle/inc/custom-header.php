<?php
/**
 * Sample implementation of the Custom Header feature
 * http://codex.wordpress.org/Custom_Headers
 *
 * @package Reddle
 * @since Reddle 1.0
 */

/**
 * Setup the WordPress core custom header feature.
 *
 * @uses reddle_header_style()
 * @uses reddle_admin_header_style()
 * @uses reddle_admin_header_image()
 *
 * @package Reddle
 */
function reddle_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'reddle_custom_header_args', array(
		'default-text-color'     => '777',
		'width'                  => 1120,
		'height'                 => 252,
		'flex-height'            => true,
		'random-default'         => true,
		'wp-head-callback'       => 'reddle_header_style',
		'admin-head-callback'    => 'reddle_admin_header_style',
		'admin-preview-callback' => 'reddle_admin_header_image',
	) ) );
}
add_action( 'after_setup_theme', 'reddle_custom_header_setup' );

if ( ! function_exists( 'reddle_header_style' ) ) :
/**
 * Custom styles for our blog header
 */
function reddle_header_style() {
	$header_image      = get_header_image();
	$header_text_color = get_header_textcolor();
	// If no custom options for text are set, let's bail

	if ( empty( $header_image ) && '' == $header_text_color )
		return;
	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css">
	#masthead img {
		float: left;
	}
	<?php
		// Has the text been hidden? Let's hide it then.
		if ( 'blank' == $header_text_color ) :
	?>
		#masthead > .site-branding {
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
			color: #<?php echo $header_text_color; ?> !important;
		}
	<?php endif;

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
		padding: 70px 2.954209748892% .275em;
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
		padding: 0.382em 0 44px;
		text-transform: uppercase;
	}
	#headimg img {
		float: left;
		height: auto;
		max-width: 100%;
	}
	</style>
<?php
}
endif; // reddle_admin_header_style

if ( ! function_exists( 'reddle_admin_header_image' ) ) :
/**
 * Custom markup for the custom header admin page
 */
function reddle_admin_header_image() {
	$style        = sprintf( ' style="color:#%s;"', get_header_textcolor() );
	$header_image = get_header_image();
?>
	<div id="headimg">
		<h1 class="displaying-header-text"><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div class="displaying-header-text" id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
		<?php if ( ! empty( $header_image ) ) : ?>
			<img src="<?php echo esc_url( $header_image ); ?>" alt="">
		<?php endif; ?>
	</div>
<?php
}
endif; // reddle_admin_header_image
