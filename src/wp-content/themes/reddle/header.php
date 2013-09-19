<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package Reddle
 * @since Reddle 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed">
	<header id="masthead" role="banner">
		<hgroup>
			<h1 id="site-title"><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
			<h2 id="site-description"><?php bloginfo( 'description' ); ?></h2>
		</hgroup>

		<?php
			// Check to see if the header image has been removed
			$header_image = get_header_image();
			if ( ! empty( $header_image ) ) :
		?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<img id="header-image" src="<?php header_image(); ?>" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="" />
		</a>
		<?php endif; ?>

		<?php if ( has_nav_menu( 'primary' ) ) : ?>
		<nav id="access" role="navigation">
			<h1 class="assistive-text section-heading"><?php _e( 'Main menu', 'reddle' ); ?></h1>
			<div class="skip-link assistive-text"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'reddle' ); ?>"><?php _e( 'Skip to content', 'reddle' ); ?></a></div>

			<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
		</nav><!-- #access -->
		<?php endif; ?>
	</header><!-- #masthead -->

	<div id="main">