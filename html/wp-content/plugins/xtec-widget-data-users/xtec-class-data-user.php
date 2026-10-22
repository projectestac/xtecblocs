<?php

/**
 * Plugin Name:       XTEC Widget Data Users
 * Description:       Widget to present the author of the blog: image, name, description and contact details.
 * Version:           2.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Text Domain:       xtec-widget-data-users
 * Domain Path:       /languages
 * Author:            Toni Ginard
 * License:           GPL v2
 * License URI:       https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * The folder, the file of the plugin and the identifier of the widget are the same as in the previous version, so
 * that the blogs keep the plugin active and their widgets.
 */

defined('ABSPATH') || exit;

const XTEC_WIDGET_DATA_USERS_FILE = __FILE__;

require_once __DIR__ . '/includes/class-widget.php';

add_action('init', 'xtec_widget_data_users_load_textdomain');
add_action('widgets_init', 'xtec_widget_data_users_register');
add_action('wp_enqueue_scripts', 'xtec_widget_data_users_enqueue_style');
add_action('admin_enqueue_scripts', 'xtec_widget_data_users_enqueue_admin');
add_action('customize_controls_enqueue_scripts', 'xtec_widget_data_users_enqueue_form_assets');

/**
 * Loads the translations of the plugin.
 */
function xtec_widget_data_users_load_textdomain(): void
{
    load_plugin_textdomain(
        'xtec-widget-data-users',
        false,
        dirname(plugin_basename(XTEC_WIDGET_DATA_USERS_FILE)) . '/languages'
    );
}

/**
 * Registers the widget.
 */
function xtec_widget_data_users_register(): void
{
    register_widget(XtecWidgetDataUsers\Widget::class);
}

/**
 * Returns the URL of a file of the plugin, with the date of the file as version so that browsers load it again when
 * it changes.
 *
 * @param string $file The path of the file inside the plugin.
 * @return array The URL and the version.
 */
function xtec_widget_data_users_asset(string $file): array
{
    return [
        plugin_dir_url(XTEC_WIDGET_DATA_USERS_FILE) . $file,
        (string)filemtime(dirname(XTEC_WIDGET_DATA_USERS_FILE) . '/' . $file),
    ];
}

/**
 * Loads the stylesheet of the widget in the blog, only if the widget is in a sidebar.
 */
function xtec_widget_data_users_enqueue_style(): void
{
    if (is_active_widget(false, false, XtecWidgetDataUsers\Widget::ID_BASE)) {
        [$url, $version] = xtec_widget_data_users_asset('assets/widget.css');
        wp_enqueue_style('xtec-widget-data-users', $url, [], $version);
    }
}

/**
 * Loads the scripts of the form of the widget in the page of the widgets.
 *
 * @param string $hook The page of the administration.
 */
function xtec_widget_data_users_enqueue_admin(string $hook): void
{
    if ($hook === 'widgets.php') {
        xtec_widget_data_users_enqueue_form_assets();
    }
}

/**
 * Loads the media library and the script and the stylesheet of the form of the widget.
 */
function xtec_widget_data_users_enqueue_form_assets(): void
{
    wp_enqueue_media();

    [$url, $version] = xtec_widget_data_users_asset('assets/admin.js');
    wp_enqueue_script('xtec-widget-data-users-admin', $url, ['media-editor'], $version, true);
    wp_localize_script('xtec-widget-data-users-admin', 'xtecWidgetDataUsers', [
        'title' => __('Choose an image', 'xtec-widget-data-users'),
        'button' => __('Use this image', 'xtec-widget-data-users'),
    ]);

    [$url, $version] = xtec_widget_data_users_asset('assets/admin.css');
    wp_enqueue_style('xtec-widget-data-users-admin', $url, [], $version);
}
