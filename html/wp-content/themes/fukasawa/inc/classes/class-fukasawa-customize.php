<?php

/* ---------------------------------------------------------------------------------------------
   THEME OPTIONS
   --------------------------------------------------------------------------------------------- */


if ( ! class_exists( 'Fukasawa_Customize' ) ) {
	class Fukasawa_Customize {

		public static function register( $wp_customize ) {

			/* Accent Color ------------------ */
		
			$wp_customize->add_setting( 'accent_color', array(
				'default' 			=> '#019EBD',
				'type' 				=> 'theme_mod',
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color'
			) );
		
			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'fukasawa_accent_color', array(
				'label' 	=> __( 'Accent Color', 'fukasawa' ),
				'section' 	=> 'colors',
				'settings' 	=> 'accent_color',
				'priority' 	=> 10,
			) ) );

			/* Fukasawa Logo ----------------- */

			// Only display the Customizer section for the fukasawa_logo setting if it already has a value.
			// This means that site owners with existing logos can remove them, but new site owners can't add them.
			// Since v2.0.0, the core custom_logo setting (in the Site Identity Customizer panel) should be used instead.

			if ( get_theme_mod( 'fukasawa_logo' ) ) {

				$wp_customize->add_section( 'fukasawa_logo_section' , array(
					'title'       => __( 'Logo', 'fukasawa' ),
					'priority'    => 40,
					'description' => __( 'Upload a logo to replace the default site title in the sidebar/header', 'fukasawa' ),
				) );

				$wp_customize->add_setting( 'fukasawa_logo', array( 
					'sanitize_callback' => 'esc_url_raw'
				) );

				$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'fukasawa_logo', array(
					'label'    => __( 'Logo', 'fukasawa' ),
					'section'  => 'fukasawa_logo_section',
					'settings' => 'fukasawa_logo',
				) ) );

			}
		
		}

		public static function header_output() {
			echo '<!-- Customizer CSS -->';
			echo '<style type="text/css">';
				self::generate_css( 'body a', 'color', 'accent_color' );
				self::generate_css( '.main-menu .current-menu-item:before', 'color', 'accent_color' );
				self::generate_css( '.main-menu .current_page_item:before', 'color', 'accent_color' );
				self::generate_css( '.widget-content .textwidget a:hover', 'color', 'accent_color' );
				self::generate_css( '.widget_fukasawa_recent_posts a:hover .title', 'color', 'accent_color' );
				self::generate_css( '.widget_fukasawa_recent_comments a:hover .title', 'color', 'accent_color' );
				self::generate_css( '.widget_archive li a:hover', 'color', 'accent_color' );
				self::generate_css( '.widget_categories li a:hover', 'color', 'accent_color' );
				self::generate_css( '.widget_meta li a:hover', 'color', 'accent_color' );
				self::generate_css( '.widget_nav_menu li a:hover', 'color', 'accent_color' );
				self::generate_css( '.widget_rss .widget-content ul a.rsswidget:hover', 'color', 'accent_color' );
				self::generate_css( '#wp-calendar thead', 'color', 'accent_color' );
				self::generate_css( '.widget_tag_cloud a:hover', 'background', 'accent_color' );
				self::generate_css( '.search-button:hover .genericon', 'color', 'accent_color' );
				self::generate_css( '.flex-direction-nav a:hover', 'background-color', 'accent_color' );
				self::generate_css( 'a.post-quote:hover', 'background', 'accent_color' );
				self::generate_css( '.posts .post-title a:hover', 'color', 'accent_color' );

				self::generate_css( '.post-content blockquote:before', 'color', 'accent_color' );
				self::generate_css( '.post-content fieldset legend', 'background', 'accent_color' );
				self::generate_css( '.post-content input[type="submit"]:hover', 'background', 'accent_color' );
				self::generate_css( '.post-content input[type="button"]:hover', 'background', 'accent_color' );
				self::generate_css( '.post-content input[type="reset"]:hover', 'background', 'accent_color' );

				self::generate_css( '.post-content .has-accent-color', 'color', 'accent_color' );
				self::generate_css( '.post-content .has-accent-background-color', 'background-color', 'accent_color' );

				self::generate_css( '.page-links a:hover', 'background', 'accent_color' );
				self::generate_css( '.comments .pingbacks li a:hover', 'color', 'accent_color' );
				self::generate_css( '.comment-header h4 a:hover', 'color', 'accent_color' );
				self::generate_css( '.bypostauthor.commet .comment-header:before', 'background', 'accent_color' );
				self::generate_css( '.form-submit #submit:hover', 'background-color', 'accent_color' );

				self::generate_css( '.nav-toggle.active', 'background-color', 'accent_color' );
				self::generate_css( '.mobile-menu .current-menu-item:before', 'color', 'accent_color' );
				self::generate_css( '.mobile-menu .current_page_item:before', 'color', 'accent_color' );

				self::generate_css( 'body#tinymce.wp-editor a', 'color', 'accent_color' );
				self::generate_css( 'body#tinymce.wp-editor a:hover', 'color', 'accent_color' );
				self::generate_css( 'body#tinymce.wp-editor fieldset legend', 'background', 'accent_color' );
				self::generate_css( 'body#tinymce.wp-editor blockquote:before', 'color', 'accent_color' );
			echo '</style>';
			echo '<!--/Customizer CSS-->';
		}

		public static function generate_css( $selector, $style, $mod_name, $prefix='', $postfix='', $echo=true ) {
			$return = '';
			$mod = get_theme_mod( $mod_name );
			if ( ! empty( $mod ) ) {
				$return = sprintf('%s { %s:%s; }',
					$selector,
					$style,
					$prefix.$mod.$postfix
				);
				if ( $echo ) {
					echo $return;
				}
			}
			return $return;
		}

	}

	// Setup the Theme Customizer settings and controls...
	add_action( 'customize_register', array( 'Fukasawa_Customize', 'register' ) );

	// Output custom CSS to live site
	add_action( 'wp_head', array( 'Fukasawa_Customize', 'header_output' ) );

}