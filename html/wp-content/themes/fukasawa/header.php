<!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

	<head profile="http://gmpg.org/xfn/11">
		
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" >
		 
		<?php wp_head(); ?>
	
	</head>
	
	<body <?php body_class(); ?>>

		<?php 
		if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open(); 
		}
		?>

		<a class="skip-link button" href="#site-content"><?php _e( 'Skip to the content', 'fukasawa' ); ?></a>
	
		<div class="mobile-navigation">
	
			<ul class="mobile-menu">
						
				<?php 
				if ( has_nav_menu( 'primary' ) ) {
																	
					$primary_nav = wp_nav_menu( array( 
						'container' 		=> '', 
						'echo'				=> false,
						'items_wrap' 		=> '%3$s',
						'theme_location' 	=> 'primary'
					) );

					echo $primary_nav;

				} else {

					$pages_list = wp_list_pages( array(
						'container' => '',
						'echo'		=> false,
						'title_li' 	=> ''
					) );

					echo $pages_list;
					
				} 
				?>
				
			 </ul>
		 
		</div><!-- .mobile-navigation -->
	
		<div class="sidebar">
		
			<?php 

			// Use a h1 element on the front page and/or the index of the blog, and a div elsewhere
			if ( is_front_page() || is_home() ) {
				$blog_title_elem = 'h1';
			} else {
				$blog_title_elem = 'div';
			}

			$custom_logo_id 	= get_theme_mod( 'custom_logo' );
			$legacy_logo_url 	= get_theme_mod( 'fukasawa_logo' );

			if ( $custom_logo_id || $legacy_logo_url ) : 

				$custom_logo_url = $custom_logo_id ? wp_get_attachment_image_url( $custom_logo_id, 'full' ) : $legacy_logo_url;
			
				?>
			
		        <<?php echo $blog_title_elem; ?> class="blog-logo">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		        		<img src="<?php echo esc_url( $custom_logo_url ); ?>" />
					</a>
					<span class="screen-reader-text"><?php echo get_bloginfo( 'title' ); ?></span>
		        </<?php echo $blog_title_elem; ?>>
		
			<?php elseif ( get_bloginfo( 'description' ) || get_bloginfo( 'title' ) ) : ?>
		
				<<?php echo $blog_title_elem; ?> class="blog-title">
					<a href="<?php echo esc_url( home_url() ); ?>" rel="home"><?php echo get_bloginfo( 'title' ); ?></a>
				</<?php echo $blog_title_elem; ?>>
				
			<?php endif; ?>
			
			<button type="button" class="nav-toggle">
			
				<div class="bars">
					<div class="bar"></div>
					<div class="bar"></div>
					<div class="bar"></div>
				</div>
				
				<p>
					<span class="menu"><?php _e( 'Menu', 'fukasawa' ); ?></span>
					<span class="close"><?php _e( 'Close', 'fukasawa' ); ?></span>
				</p>
			
			</button>
			
			<ul class="main-menu">
				<?php echo isset( $primary_nav ) ? $primary_nav : $pages_list; ?>
			</ul><!-- .main-menu -->

			<?php if ( is_active_sidebar( 'sidebar' ) ) : ?>
				<div class="widgets">
					<?php dynamic_sidebar( 'sidebar' ); ?>
				</div><!-- .widgets -->
			<?php endif; ?>

			<div class="credits">
				<p>&copy; <?php echo date( 'Y' ); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>.</p>
				<p><?php _e( 'Powered by', 'fukasawa' ); ?> <a href="https://wordpress.org">WordPress</a>.</p>
				<p><?php _e( 'Theme by', 'fukasawa' ); ?> <a href="https://andersnoren.se">Anders Nor&eacute;n</a>.</p>
			</div><!-- .credits -->
							
		</div><!-- .sidebar -->
	
		<main class="wrapper" id="site-content">