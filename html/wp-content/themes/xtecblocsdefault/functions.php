<?php

require_once __DIR__ . '/xtecfunc.php';

// External URLs of the portal. They can be defined in wp-config.php to change them in other environments.
defined('XTEC_HELP_URL') || define('XTEC_HELP_URL', 'https://sites.google.com/a/xtec.cat/ajudaxtecblocs/');
defined('XTEC_TRAINING_URL') || define('XTEC_TRAINING_URL', 'https://blocs.xtec.cat/blocs_formacio/');

// Remove admin bar in signup page because it is empty and its space is empty
function xtec_remove_admin_bar(): void
{
    add_filter('show_admin_bar', '__return_false');
}
add_action('before_signup_header', 'xtec_remove_admin_bar', 1);

/**
 * Lets WordPress generate the title of the pages.
 */
function xtec_theme_setup(): void
{
    add_theme_support('title-tag');
}
add_action('after_setup_theme', 'xtec_theme_setup');

/**
 * Loads the stylesheet of the theme, with the date of the file as version so that browsers load it again when it
 * changes. It is enqueued with priority 1 so that, as before, it is printed before the styles of WordPress.
 */
function xtec_enqueue_style(): void
{
    $version = (string)filemtime(get_stylesheet_directory() . '/style.css');
    wp_enqueue_style('xtecblocsdefault', get_stylesheet_uri(), [], $version);
}
add_action('wp_enqueue_scripts', 'xtec_enqueue_style', 1);

/**
 * Redirects to the help, to the creation of a blog and to the login of the portal. It must be done before any output
 * is sent, so it can't be in the templates.
 */
function xtec_portal_redirect(): void
{
    if (!is_home()) {
        return;
    }

    $urls = [
        'help' => XTEC_HELP_URL,
        'new' => network_site_url('wp-signup.php'),
        'login' => wp_login_url(site_url()),
    ];
    $action = $_GET['a'] ?? '';

    if (is_string($action) && isset($urls[$action])) {
        wp_redirect($urls[$action]);
        exit;
    }
}

add_action('template_redirect', 'xtec_portal_redirect');
