<?php

/**
 * Plugin Name:       XTEC MultiSite Manager
 * Plugin URI:        https://github.com/projectestac/wordpress-xtec-ms-manager
 * Description:       Execution of SQL in the blogs of the network and requests of the blogs to the administrators.
 * Version:           2.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Network:           true
 * Text Domain:       xtec-ms-manager
 * Domain Path:       /languages
 * Author:            Toni Ginard
 * License:           GPL v3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * Copyright 2026 Departament d'Educació i Formació Professional
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

defined('ABSPATH') || exit;

const XMM_FILE = __FILE__;

require_once __DIR__ . '/includes/common-lib.php';

register_activation_hook(__FILE__, 'xmm_install');
add_action('init', 'xmm_init');
add_action('admin_enqueue_scripts', 'xmm_scripts');
// Link to request the extension of the quota in the dashboard of the blogs, if the conditions are met
add_action('activity_box_end', 'xmm_add_quota_request_to_dashboard');

/**
 * Loads the translations and adds the menus of the plugin, which is only for multisite networks.
 */
function xmm_init(): void
{
    load_plugin_textdomain('xtec-ms-manager', false, dirname(plugin_basename(XMM_FILE)) . '/languages');

    if (is_multisite()) {
        add_action('network_admin_menu', 'xmm_network_admin_menu');
        add_action('admin_menu', 'xmm_admin_menu');
    }
}

/**
 * Adds the menu of the plugin to the administration of the network.
 */
function xmm_network_admin_menu(): void
{
    $title = __('Multisite Manager', 'xtec-ms-manager');
    $hooks = [add_menu_page($title, $title, 'manage_network_options', 'xmm', 'xmm_main', 'dashicons-analytics', 99)];

    $pages = [
        'xmm-sql' => [__('SQL', 'xtec-ms-manager'), 'xmm_sql'],
        'xmm-requests' => [__('Requests', 'xtec-ms-manager'), 'xmm_requests'],
        'xmm-request-types' => [__('Request Types', 'xtec-ms-manager'), 'xmm_request_types'],
        'xmm-settings' => [__('Settings', 'xtec-ms-manager'), 'xmm_settings'],
    ];
    foreach ($pages as $slug => [$page_title, $callback]) {
        $hooks[] = add_submenu_page('xmm', $page_title, $page_title, 'manage_network_options', $slug, $callback);
    }

    foreach ($hooks as $hook) {
        add_action('load-' . $hook, 'xmm_load_page_files');
    }
}

/**
 * Adds the page of the requests of the blog to the menu Tools of the blogs.
 */
function xmm_admin_menu(): void
{
    $title = __('Requests', 'xtec-ms-manager');
    $hook = add_submenu_page('tools.php', $title, $title, 'manage_options', 'xmm-blog-requests', 'xmm_blog_requests');
    add_action('load-' . $hook, 'xmm_load_page_files');
}

/**
 * Loads the files of the page of the plugin that is being shown. It is done when WordPress loads the page, after
 * checking that the user can access it, and not in other requests.
 */
function xmm_load_page_files(): void
{
    global $plugin_page;

    $files = [
        'xmm' => ['main.php'],
        'xmm-sql' => ['sql-exec.php'],
        'xmm-requests' => ['requests.php', 'request-types.php'],
        'xmm-blog-requests' => ['requests.php'],
        'xmm-request-types' => ['request-types.php'],
        'xmm-settings' => ['settings.php'],
    ];

    foreach ($files[$plugin_page] ?? [] as $file) {
        require_once __DIR__ . '/includes/' . $file;
    }
}

/**
 * Loads the stylesheet of the plugin in its pages and in the dashboard, where the link to request more quota is.
 *
 * @param string $hook_suffix The page of the administration.
 */
function xmm_scripts(string $hook_suffix): void
{
    global $plugin_page;

    if ($hook_suffix === 'index.php' || str_starts_with((string)$plugin_page, 'xmm')) {
        wp_enqueue_style('xmm-admin', plugins_url('css/admin.css', XMM_FILE));
    }
}
