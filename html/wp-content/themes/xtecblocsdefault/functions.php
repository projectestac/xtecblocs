<?php

// Remove admin bar in signup page because it is empty and its space is empty
function xtec_remove_admin_bar(): void {
	add_filter('show_admin_bar', '__return_false');
}
add_action( 'before_signup_header', 'xtec_remove_admin_bar', 1 );

function xtec_theme_setup(): void {
	add_theme_support( 'post-thumbnails', array( 'xtecweekblog' ) );
	add_image_size( 'xtecweekblog', 363, 98, true );
}
add_action( 'after_setup_theme', 'xtec_theme_setup' );

/**
 * Returns the URL to add or delete a blog from the favorites of the current user, protected with a nonce.
 *
 * @param string $action 'addPrefer' or 'delPrefer'.
 * @param int $blog_id The ID of the blog.
 * @return string The escaped URL.
 */
function xtec_favorites_url($action, $blog_id): string {
	return esc_url(wp_nonce_url('index.php?a=' . $action . '&blogId=' . (int) $blog_id, 'xtec_favorites_' . $action . '_' . (int) $blog_id));
}
