<?php
/**
 * Eduhack theme functions and definitions.
 *
 * @package XTEC Blocs
 * @subpackage Eduhack
 * @since Eduhack 1.0
 */


/**
 * Enqueues the theme scripts.
 *
 * @since Eduhack 1.0
 */
function ehth_enqueue_scripts() {
    if ( is_admin() )
        return;
    
    // Paths to Fukusawa and this child theme
    
    $parent_uri = get_template_directory_uri();
    $child_uri = get_stylesheet_directory_uri();
    
    // Enqueue the default Fukasawa scripts; except for the masonry,
    // that gets replaced by a dummy jQuery plugin
    
    wp_enqueue_script( 'fukasawa_flexslider', "$parent_uri/js/flexslider.min.js",
        array('jquery', 'eduhack_masonry'), '', true );
    
    wp_enqueue_script( 'fukasawa_global', "$parent_uri/js/global.js",
        array('jquery', 'eduhack_masonry'), '', true );
    
    wp_enqueue_script( 'eduhack_masonry', "$child_uri/scripts/masonry.js",
        array('jquery'), '', true );
    
    if ( is_singular() ) {
        wp_enqueue_script( 'comment-reply' );
    }
}


/**
 * Enqueues the theme CSS styles.
 *
 * @since Eduhack 1.0
 */
function ehth_print_styles() {
    if ( is_admin() )
        return;
    
    $parent_uri = get_template_directory_uri();
    $child_uri = get_stylesheet_directory_uri();
    
    wp_enqueue_style( 'fukasawa_genericons', "$parent_uri/genericons/genericons.css" );
    wp_enqueue_style( 'fukasawa_style', "$parent_uri/style.css" );
    wp_enqueue_style( 'eduhack_style', "$child_uri/style.css",
        array( 'fukasawa_style' ), wp_get_theme()->get('Version') );
}


/**
 * Returns the current posts order set for a category.
 *
 * This method checks the session variable 'xtec_category' to obtain the
 * order, which is defined by the built-in extension 'cat_sort'.
 *
 * @since Eduhack 1.0
 * @return string   'asc' or 'desc' (default).
 */
function ehth_get_category_order() {
    if ( isset($_SESSION['xtec_category']) ) {
        $order = $_SESSION['xtec_category'];
        return ($order === 'ASC') ? 'asc' : 'desc';
    }
    
    return 'desc';
}


/**
 * Prints pagination links to the posts on the same category as
 * the current post.
 *
 * @since Eduhack 1.0
 */
function ehth_category_links() {
    $links = [];
    $index = 0;
    
    // Obtain the current post and category
    
    $post_id = get_the_ID();
    $category = end(get_the_category($post_id));
    $meta = get_option( "category_" . $category->term_id );
    $order = isset($meta['sort_posts']) ? $meta['sort_posts'] : 'DESC';
    $stickies = get_option( 'sticky_posts' );
    
    // Prepend sticky post to the links
    
    if (count($stickies) > 0) {
        $stickyQuery = new WP_Query([
            'category_name' => $category->slug,
            'posts_per_page' => -1,
            'fields' => 'ids',
            'orderby' => [ 'date' => $order ],
            'post__in' => $stickies
        ]);
        
        if ( $stickyQuery->have_posts() ) {
            while ( $stickyQuery->have_posts() ) {
                $stickyQuery->the_post();
                
                $links[$index++] = [
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'href' => get_permalink()
                ];
            }
        }
    }
    
    // Append non-sticky post to the links
    
    $postQuery = new WP_Query([
        'category_name' => $category->slug,
        'posts_per_page' => -1,
        'fields' => 'ids',
        'orderby' => [ 'date' => $order ],
        'post__not_in' => $stickies
    ]);
    
    if ( $postQuery->have_posts() ) {
        while ( $postQuery->have_posts() ) {
            $postQuery->the_post();
            
            $links[$index++] = [
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'href' => get_permalink()
            ];
        }
    }
    
    // Reset the post data
    
    wp_reset_postdata();
    
    return $links;
}


/**
 * Prints an image for the current post category.
 *
 * @since Eduhack 1.0
 */
function ehth_cateogry_image() {
    $post_id = get_the_ID();
    $term = end(get_the_category($the_ID));
    $image = get_term_meta( $term->term_id, 'xtec_image', true);
    
    if ( $image !== '' ) {
        echo "<img src=\"$image\" alt=\"{$term->name}\">";
    }
}


/**
 * Registers this theme's action hooks and removes the actions that
 * conflict with this theme behaviour.
 *
 * @since Eduhack 1.0
 */
add_action( 'after_setup_theme', function() {
    add_action( 'wp_enqueue_scripts', 'ehth_enqueue_scripts' );
    add_action( 'wp_print_styles', 'ehth_print_styles' );
    
    remove_action( 'wp_print_styles', 'fukasawa_load_style' );
    remove_action( 'wp_enqueue_scripts', 'fukasawa_load_javascript_files' );
});


/**
 * Reverse the default posts order for categories.
 *
 * This hook forces the order of the posts in a category to be in ascending
 * chronological order and removes sticky post from the results.
 *
 * @since Eduhack 1.0
 */
add_action( 'pre_get_posts', function( $query ) {
    if ( !$query->is_main_query() )
        return;

    if ( !$query->is_category() )
        return;
    
    // Obtain the posts order for the category
    
    $order = ehth_get_category_order();
    
    // Set the posts order and reset the posts per page limit
    
    $query->set( 'posts_per_page', get_option( 'posts_per_page' ) );
    $query->set( 'post__not_in', get_option( 'sticky_posts' ) );
    $query->set( 'orderby', 'date' );
    $query->set( 'order', $order );
});


/**
 * Prepend sticky posts to the first page of a category.
 *
 * @since Eduhack 1.0
 */
add_filter( 'the_posts', function( $posts, $query ) {
    if ( is_admin() || !$query->is_main_query() )
        return $posts;
    
    if ( $query->paged > 1 || !$query->is_category() )
        return $posts;
    
    // Prepend sticky posts at the top
    
    $order = ehth_get_category_order();
    $post_in = get_option( 'sticky_posts' );
    $category = get_query_var('cat');
    
    if ( $post_in && $category ) {
        $stickies = get_posts([
            'post__in' => $post_in,
            'category' => $category,
            'post_status' => 'publish',
            'nopaging' => true,
            'orderby' => 'date',
            'order' => $order
        ]);
        
        foreach ( array_reverse($stickies) as $post ) {
            array_unshift( $posts, $post );
        }
    }
    
    return $posts;
}, 10, 2);


/**
 * Add the 'post' CSS class to custom post types. This is required in order
 * to correctly format the posts types created with the plugin Toolset Types.
 *
 * @since Eduhack 1.0
 */
add_filter( 'post_class', function($classes) {
    return array_merge($classes, ['post']);
});
