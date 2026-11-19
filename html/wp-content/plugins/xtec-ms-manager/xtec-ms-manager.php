<?php
/**
 * Plugin Name: XTEC MultiSite Manager
 * Plugin URI: https://github.com/projectestac/wordpress-xtec-ms-manager
 * Description: Manage blogs in MultiSite configuration of WordPress
 * Version: 1.1.0
 * Author: Toni Ginard
 * Author URI: http://agora.xtec.cat/nodes
 * License: GPLv3
 */

// If this file is called directly, abort.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

include_once 'session.php'; // Activate PHP sessions in XMM
include_once 'includes/common-lib.php'; // Plugin general lib

register_activation_hook( __FILE__, 'xmm_install' );

// Initialization tasks
add_action( 'init', 'xmm_init' );
function xmm_init() {
	load_plugin_textdomain( 'xmm', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	// This plugin is only for multisite version
	if ( is_multisite() ) {
		add_action( 'network_admin_menu', 'xmm_network_admin_menu' );
        add_action( 'admin_menu', 'xmm_admin_menu' );
	}
}

// Create plugin menus
function xmm_network_admin_menu() {
	$hooks = [];
	$hooks[] = add_menu_page( __( 'Multisite Manager', 'xmm' ), __( 'Multisite Manager', 'xmm' ), 'manage_network_options', 'xmm', 'xmm_main', 'dashicons-analytics', 99 );
	$hooks[] = add_submenu_page( 'xmm', __( 'SQL', 'xmm' ), __( 'SQL', 'xmm' ), 'manage_network_options', 'xmm-sql', 'xmm_sql' );
    $hooks[] = add_submenu_page( 'xmm', __( 'Requests', 'xmm' ), __( 'Requests', 'xmm' ), 'manage_network_options', 'xmm-requests', 'xmm_requests' );
    $hooks[] = add_submenu_page( 'xmm', __( 'Request Types', 'xmm' ), __( 'Request Types', 'xmm' ), 'manage_network_options', 'xmm-request-types', 'xmm_request_types' );
    $hooks[] = add_submenu_page( 'xmm', __( 'Settings', 'xmm' ), __( 'Settings', 'xmm' ), 'manage_network_options', 'xmm-settings', 'xmm_settings' );

    foreach ( $hooks as $hook ) {
        add_action( 'load-' . $hook, 'xmm_load_page_files' );
    }
}

function xmm_admin_menu() {
    $hook = add_submenu_page( 'tools.php', __( 'Requests', 'xmm' ), __( 'Requests', 'xmm' ), 'manage_options', 'xmm-blog-requests', 'xmm_blog_requests' );
    add_action( 'load-' . $hook, 'xmm_load_page_files' );
}

/**
 * Loads the files of the page of the plugin that is being shown. It is done when WordPress loads the page, after
 * checking that the user can access it, and not in other requests.
 */
function xmm_load_page_files(): void {
    global $plugin_page;

    $files = [
        'xmm' => [ 'main.php' ],
        'xmm-sql' => [ 'sql-exec.php' ],
        'xmm-requests' => [ 'requests.php', 'request-types.php' ],
        'xmm-blog-requests' => [ 'requests.php' ],
        'xmm-request-types' => [ 'request-types.php' ],
        'xmm-settings' => [ 'settings.php' ],
    ];

    foreach ( $files[ $plugin_page ] ?? [] as $file ) {
        include_once __DIR__ . '/includes/' . $file;
    }
}

/**
 * Loads the stylesheet of the plugin in its pages and in the dashboard, where the link to request more quota is.
 *
 * @param string $hook_suffix The page of the administration.
 */
function xmm_scripts( string $hook_suffix ): void {
    global $plugin_page;

    if ( 'index.php' === $hook_suffix || str_starts_with( (string) $plugin_page, 'xmm' ) ) {
        wp_enqueue_style( 'xmm-admin', plugins_url( 'css/admin.css', __FILE__ ) );
    }
}
add_action( 'admin_enqueue_scripts', 'xmm_scripts' );

// Show link to request the extension of the quota if conditions are met
add_action('activity_box_end', 'xmm_add_quota_request_to_dashboard');
