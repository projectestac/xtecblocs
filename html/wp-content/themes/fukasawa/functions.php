<?php


/* ---------------------------------------------------------------------------------------------
   THEME SETUP
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'fukasawa_setup' ) ) :
	function fukasawa_setup() {
		
		// Automatic feed
		add_theme_support( 'automatic-feed-links' );
		
		// Set content-width
		global $content_width;
		if ( ! isset( $content_width ) ) $content_width = 620;
		
		// Post thumbnails
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size ( 88, 88, true );
		
		add_image_size( 'post-image', 973, 9999 );
		add_image_size( 'post-thumb', 508, 9999 );
		
		// Post formats
		add_theme_support( 'post-formats', array( 'gallery', 'image', 'video' ) );
			
		// Jetpack infinite scroll
		add_theme_support( 'infinite-scroll', array(
			'type' 		=> 'click',
			'container'	=> 'posts',
			'footer' 	=> false,
		) );

		// Custom logo
		add_theme_support( 'custom-logo', array(
			'height'      => 240,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
			'header-text' => array( 'blog-title' ),
		) );
		
		// Title tag
		add_theme_support( 'title-tag' );
		
		// Add nav menu
		register_nav_menu( 'primary', __( 'Primary Menu', 'fukasawa' ) );
		
	}
	add_action( 'after_setup_theme', 'fukasawa_setup' );
endif;


/*	-----------------------------------------------------------------------------------------------
	REQUIRED FILES
	Include required files
--------------------------------------------------------------------------------------------------- */

// Handle Customizer settings
require get_template_directory() . '/inc/classes/class-fukasawa-customize.php';


/* ---------------------------------------------------------------------------------------------
   GET THEME VERSION
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'fukasawa_get_version' ) ) :
	function fukasawa_get_version() {

		return wp_get_theme( 'fukasawa' )->theme_version;

	}
endif;


/* ---------------------------------------------------------------------------------------------
   ENQUEUE SCRIPTS
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'fukasawa_load_javascript_files' ) ) :
	function fukasawa_load_javascript_files() {

		wp_register_script( 'fukasawa_flexslider', get_template_directory_uri() . '/assets/js/flexslider.js', '2.7.0', true );

		wp_enqueue_script( 'fukasawa_global', get_template_directory_uri() . '/assets/js/global.js', array( 'jquery', 'masonry', 'imagesloaded', 'fukasawa_flexslider' ), fukasawa_get_version(), true );

		if ( is_singular() ) wp_enqueue_script( 'comment-reply' );

	}
	add_action( 'wp_enqueue_scripts', 'fukasawa_load_javascript_files' );
endif;


/* ---------------------------------------------------------------------------------------------
   ENQUEUE STYLES
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'fukasawa_load_style' ) ) :
	function fukasawa_load_style() {

		if ( ! is_admin() ) {

			$dependencies = array();

			wp_register_style( 'fukasawa_googleFonts', get_theme_file_uri( '/assets/css/fonts.css' ) );
			$dependencies[] = 'fukasawa_googleFonts';

			wp_register_style( 'fukasawa_genericons', get_theme_file_uri( '/assets/fonts/genericons/genericons.css' ) );
			$dependencies[] = 'fukasawa_genericons';

			wp_enqueue_style( 'fukasawa_style', get_stylesheet_uri(), $dependencies, fukasawa_get_version() );
		}

	}
	add_action( 'wp_print_styles', 'fukasawa_load_style' );
endif;


/* ---------------------------------------------------------------------------------------------
   ADD EDITOR STYLES
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'fukasawa_add_editor_styles' ) ) :
	function fukasawa_add_editor_styles() {

		add_editor_style( array( 'assets/css/fukasawa-block-editor-styles.css', 'assets/css/fonts.css' ) );
		
	}
	add_action( 'init', 'fukasawa_add_editor_styles' );
endif;


/* ---------------------------------------------------------------------------------------------
   REGISTER WIDGET AREAS
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'fukasawa_sidebar_registration' ) ) :
	function fukasawa_sidebar_registration() {

		register_sidebar( array(
			'name' 			=> __( 'Sidebar', 'fukasawa' ),
			'id' 			=> 'sidebar',
			'description' 	=> __( 'Widgets in this area will be shown in the sidebar.', 'fukasawa' ),
			'before_title' 	=> '<h3 class="widget-title">',
			'after_title' 	=> '</h3>',
			'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content clear">',
			'after_widget' 	=> '</div></div>'
		) );

	}
	add_action( 'widgets_init', 'fukasawa_sidebar_registration' ); 
endif;


/* ---------------------------------------------------------------------------------------------
   ADD THEME WIDGETS
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'fukasawa_widget_registration' ) ) :
	function fukasawa_widget_registration() {
		require_once( get_template_directory() . '/widgets/recent-comments.php' );
		require_once( get_template_directory() . '/widgets/recent-posts.php' );
	}
	add_action( 'widgets_init', 'fukasawa_widget_registration' ); 
endif;


/* ---------------------------------------------------------------------------------------------
   DELIST WIDGETS REPLACED BY THEME ONES
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'fukasawa_unregister_default_widgets' ) ) {

	function fukasawa_unregister_default_widgets() {
		unregister_widget( 'WP_Widget_Recent_Comments' );
		unregister_widget( 'WP_Widget_Recent_Posts' );
	}
	add_action( 'widgets_init', 'fukasawa_unregister_default_widgets', 11 );

}


/* ---------------------------------------------------------------------------------------------
   CHECK FOR JAVASCRIPT SUPPORT
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'fukasawa_html_js_class' ) ) {

	function fukasawa_html_js_class () {
		echo '<script>document.documentElement.className = document.documentElement.className.replace("no-js","js");</script>'. "\n";
	}
	add_action( 'wp_head', 'fukasawa_html_js_class', 1 );

}


/* ---------------------------------------------------------------------------------------------
   ADD CLASSES TO PAGINATION
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'fukasawa_posts_link_attributes_1' ) ) {

	function fukasawa_posts_link_attributes_1() {
		return 'class="archive-nav-older fleft"';
	}
	add_filter( 'next_posts_link_attributes', 'fukasawa_posts_link_attributes_1' );

}


if ( ! function_exists( 'fukasawa_posts_link_attributes_2' ) ) {

	function fukasawa_posts_link_attributes_2() {
		return 'class="archive-nav-newer fright"';
	}
	add_filter( 'previous_posts_link_attributes', 'fukasawa_posts_link_attributes_2' );

}


/* ---------------------------------------------------------------------------------------------
   CHANGE LENGTH OF EXCERPTS
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'fukasawa_custom_excerpt_length' ) ) {

	function fukasawa_custom_excerpt_length( $length ) {
		return 28;
	}
	add_filter( 'excerpt_length', 'fukasawa_custom_excerpt_length', 999 );

}


/* ---------------------------------------------------------------------------------------------
   CHANGE EXCERPT SUFFIX
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'fukasawa_new_excerpt_more' ) ) {

	function fukasawa_new_excerpt_more( $more ) {
		return '...';
	}
	add_filter( 'excerpt_more', 'fukasawa_new_excerpt_more' );

}


/* ---------------------------------------------------------------------------------------------
   BODY CLASSES
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'fukasawa_body_classes' ) ) {
 
	function fukasawa_body_classes( $classes ){
	
		// Check for mobile visitor
		$classes[] = wp_is_mobile() ? 'wp-is-mobile' : 'wp-is-not-mobile';
		
		return $classes;
	}
	add_filter( 'body_class', 'fukasawa_body_classes' );

}


/* ---------------------------------------------------------------------------------------------
   GET COMMENT EXCERPT
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'fukasawa_get_comment_excerpt' ) ) {
	
	function fukasawa_get_comment_excerpt( $comment_ID = 0, $num_words = 20 ) {
		$comment = get_comment( $comment_ID );
		$comment_text = strip_tags( $comment->comment_content );
		$blah = explode( ' ', $comment_text );
		if ( count( $blah ) > $num_words ) {
			$k = $num_words;
			$use_dotdotdot = 1;
		} else {
			$k = count( $blah );
			$use_dotdotdot = 0;
		}
		$excerpt = '';
		for ( $i = 0; $i < $k; $i++ ) {
			$excerpt .= $blah[$i] . ' ';
		}
		$excerpt .= ( $use_dotdotdot ) ? '...' : '';
		return apply_filters( 'get_comment_excerpt', $excerpt );
	}

}


/* ---------------------------------------------------------------------------------------------
   ADD ADMIN CSS
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'fukasawa_admin_css' ) ) {

	function fukasawa_admin_css() { 
	echo '
		<style type="text/css">

			#postimagediv #set-post-thumbnail img {
				max-width: 100%;
				height: auto;
			}

		</style>';
	}
	add_action( 'admin_head', 'fukasawa_admin_css' );

}


/* ---------------------------------------------------------------------------------------------
   FLEXSLIDER FUNCTION
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'fukasawa_flexslider' ) ) {

	function fukasawa_flexslider( $size = 'thumbnail' ) {

		$attachment_parent = is_page() ? $post->ID : get_the_ID();

		$image_args = array(
			'numberposts'    => -1, // show all
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'post_parent'    => $attachment_parent,
			'post_type'      => 'attachment',
			'post_status'    => null,
			'post_mime_type' => 'image',
		);

		$images = get_posts( $image_args );

		if ( $images ) : ?>
		
			<div class="flexslider">
			
				<ul class="slides">
		
					<?php foreach( $images as $image ) :

						$attimg = wp_get_attachment_image( $image->ID, $size ); ?>
						
						<li>
							<?php echo $attimg; ?>
						</li>
						
					<?php endforeach; ?>
			
				</ul>
				
			</div>
			
			<?php
			
		endif;
	}

}


/* ---------------------------------------------------------------------------------------------
   COMMENT FUNCTION
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'fukasawa_comment' ) ) {
	function fukasawa_comment( $comment, $args, $depth ) {
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback' :
		?>
		
		<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		
			<?php __( 'Pingback:', 'fukasawa' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( 'Edit', 'fukasawa' ), '<span class="edit-link">', '</span>' ); ?>
			
		</li>
		<?php
				break;
			default :
			global $post;
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		
			<div id="comment-<?php comment_ID(); ?>" class="comment">
				
				<div class="comment-header">
				
					<?php echo get_avatar( $comment, 160 ); ?>
					
					<div class="comment-header-inner">
												
						<h4><?php echo get_comment_author_link(); ?></h4>
						
						<div class="comment-meta">
							<a class="comment-date-link" href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><?php echo get_comment_date( get_option( 'date_format' ) ); ?></a>
						</div><!-- .comment-meta -->
					
					</div><!-- .comment-header-inner -->
				
				</div>

				<div class="comment-content post-content">
				
					<?php comment_text(); ?>
					
				</div><!-- .comment-content -->
				
				<div class="comment-actions clear">
				
					<?php if ( 0 == $comment->comment_approved ) : ?>
					
						<p class="comment-awaiting-moderation fright"><?php _e( 'Your comment is awaiting moderation.', 'fukasawa' ); ?></p>
						
					<?php endif; ?>
					
					<div class="fleft">
				
						<?php 
						comment_reply_link( array( 
							'reply_text' 	=> __( 'Reply', 'fukasawa' ),
							'depth'			=> $depth, 
							'max_depth' 	=> $args['max_depth'],
							'before'		=> '',
							'after'			=> ''
							) 
						); 
						edit_comment_link( __( 'Edit', 'fukasawa' ), '<span class="sep">/</span>', '' ); 
						?>
					
					</div>
				
				</div><!-- .comment-actions -->
											
			</div><!-- .comment-## -->
					
		<?php
			break;
		endswitch;
	}
}


/* ---------------------------------------------------------------------------------------------
   SPECIFY BLOCK EDITOR SUPPORT
------------------------------------------------------------------------------------------------ */


if ( ! function_exists( 'fukasawa_add_gutenberg_features' ) ) :
	function fukasawa_add_gutenberg_features() {

		/* Block Editor Features ------------- */

		add_theme_support( 'align-wide' );

		/* Block Editor Palette -------------- */

		$accent_color = get_theme_mod( 'accent_color', '#019EBD' );

		add_theme_support( 'editor-color-palette', array(
			array(
				'name' 	=> _x( 'Accent', 'Name of the accent color in the Gutenberg palette', 'fukasawa' ),
				'slug' 	=> 'accent',
				'color' => $accent_color,
			),
			array(
				'name' 	=> _x( 'Black', 'Name of the black color in the Gutenberg palette', 'fukasawa' ),
				'slug' 	=> 'black',
				'color' => '#333',
			),
			array(
				'name' 	=> _x( 'Dark Gray', 'Name of the dark gray color in the Gutenberg palette', 'fukasawa' ),
				'slug' 	=> 'dark-gray',
				'color' => '#444',
			),
			array(
				'name' 	=> _x( 'Medium Gray', 'Name of the medium gray color in the Gutenberg palette', 'fukasawa' ),
				'slug' 	=> 'medium-gray',
				'color' => '#666',
			),
			array(
				'name' 	=> _x( 'Light Gray', 'Name of the light gray color in the Gutenberg palette', 'fukasawa' ),
				'slug' 	=> 'light-gray',
				'color' => '#767676',
			),
			array(
				'name' 	=> _x( 'White', 'Name of the white color in the Gutenberg palette', 'fukasawa' ),
				'slug' 	=> 'white',
				'color' => '#fff',
			),
		) );

		/* Block Editor Font Sizes ----------- */

		add_theme_support( 'editor-font-sizes', array(
			array(
				'name' 		=> _x( 'Small', 'Name of the small font size in Gutenberg', 'fukasawa' ),
				'shortName' => _x( 'S', 'Short name of the small font size in the Gutenberg editor.', 'fukasawa' ),
				'size' 		=> 16,
				'slug' 		=> 'small',
			),
			array(
				'name' 		=> _x( 'Regular', 'Name of the regular font size in Gutenberg', 'fukasawa' ),
				'shortName' => _x( 'M', 'Short name of the regular font size in the Gutenberg editor.', 'fukasawa' ),
				'size' 		=> 18,
				'slug' 		=> 'normal',
			),
			array(
				'name' 		=> _x( 'Large', 'Name of the large font size in Gutenberg', 'fukasawa' ),
				'shortName' => _x( 'L', 'Short name of the large font size in the Gutenberg editor.', 'fukasawa' ),
				'size' 		=> 24,
				'slug' 		=> 'large',
			),
			array(
				'name' 		=> _x( 'Larger', 'Name of the larger font size in Gutenberg', 'fukasawa' ),
				'shortName' => _x( 'XL', 'Short name of the larger font size in the Gutenberg editor.', 'fukasawa' ),
				'size' 		=> 27,
				'slug' 		=> 'larger',
			),
		) );

	}
	add_action( 'after_setup_theme', 'fukasawa_add_gutenberg_features' );
endif;


/* ---------------------------------------------------------------------------------------------
   BLOCK EDITOR STYLES
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'fukasawa_block_editor_styles' ) ) :
	function fukasawa_block_editor_styles() {

		$dependencies = array();

		wp_register_style( 'fukasawa-block-editor-styles-font', get_theme_file_uri( '/assets/css/fonts.css' ) );
		$dependencies[] = 'fukasawa-block-editor-styles-font';

		wp_register_style( 'fukasawa-block-editor-styles-genericons', get_theme_file_uri( '/assets/fonts/genericons/genericons.css' ) );
		$dependencies[] = 'fukasawa-block-editor-styles-genericons';

		// Enqueue the editor styles
		wp_enqueue_style( 'fukasawa-block-editor-styles', get_theme_file_uri( '/assets/css/fukasawa-block-editor-styles.css' ), $dependencies, fukasawa_get_version(), 'all' );

	}
	add_action( 'enqueue_block_editor_assets', 'fukasawa_block_editor_styles', 1 );
endif;
