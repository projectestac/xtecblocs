<?php

require_once __DIR__ . '/xtecfunc.php';

// Remove admin bar in signup page because it is empty and its space is empty
function xtec_remove_admin_bar(): void
{
    add_filter('show_admin_bar', '__return_false');
}
add_action('before_signup_header', 'xtec_remove_admin_bar', 1);

/**
 * Returns the URL to add or delete a blog from the favorites of the current user, protected with a nonce.
 *
 * @param string $action 'addPrefer' or 'delPrefer'.
 * @param int $blog_id The ID of the blog.
 * @return string The escaped URL.
 */
function xtec_favorites_url($action, $blog_id): string
{
    return esc_url(wp_nonce_url('index.php?a=' . $action . '&blogId=' . (int) $blog_id, 'xtec_favorites_' . $action . '_' . (int) $blog_id));
}

/**
 * Runs the portal actions that end with a redirect: the help and adding or deleting a favorite blog. They must be done
 * before any output is sent, so they can't be in the templates.
 */
function xtec_portal_redirect(): void
{
    if (!is_home()) {
        return;
    }

    $action = $_REQUEST['a'] ?? '';

    if ($action === 'help') {
        wp_redirect('http://sites.google.com/a/xtec.cat/ajudaxtecblocs/');
        exit;
    }

    if ($action === 'addPrefer' || $action === 'delPrefer') {
        $blog_id = (int)($_REQUEST['blogId'] ?? 0);
        $nonce = is_string($_REQUEST['_wpnonce'] ?? null) ? $_REQUEST['_wpnonce'] : '';

        if (is_user_logged_in() && wp_verify_nonce($nonce, 'xtec_favorites_' . $action . '_' . $blog_id)) {
            if ($action === 'addPrefer') {
                xtec_favorites_add_preferred($blog_id);
            } else {
                xtec_favorites_delete_preferred($blog_id);
            }
        }

        // Back to the page where the link was clicked
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        wp_redirect($referer !== '' ? wp_validate_redirect($referer, home_url('/')) : home_url('/'));
        exit;
    }
}

add_action('template_redirect', 'xtec_portal_redirect');
