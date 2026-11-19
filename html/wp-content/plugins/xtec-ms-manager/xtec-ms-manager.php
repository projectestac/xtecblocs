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
	add_menu_page( __( 'Multisite Manager', 'xmm' ), __( 'Multisite Manager', 'xmm' ), 'manage_network_options', 'xmm', 'xmm_main', 'dashicons-analytics', 99 );
	add_submenu_page( 'xmm', __( 'SQL', 'xmm' ), __( 'SQL', 'xmm' ), 'manage_network_options', 'xmm-sql', 'xmm_sql' );
    add_submenu_page( 'xmm', __( 'Requests', 'xmm' ), __( 'Requests', 'xmm' ), 'manage_network_options', 'xmm-requests', 'xmm_requests' );
    add_submenu_page( 'xmm', __( 'Request Types', 'xmm' ), __( 'Request Types', 'xmm' ), 'manage_network_options', 'xmm-request-types', 'xmm_request_types' );
    add_submenu_page( 'xmm', __( 'Settings', 'xmm' ), __( 'Settings', 'xmm' ), 'manage_network_options', 'xmm-settings', 'xmm_settings' );
}

function xmm_admin_menu() {
    add_submenu_page( 'tools.php', __( 'Requests', 'xmm' ), __( 'Requests', 'xmm' ), 'manage_options', 'xmm-blog-requests', 'xmm_blog_requests' );
}

function xmm_scripts() {
    wp_enqueue_style( 'admin', plugins_url( 'css/admin.css', __FILE__ ) );
}
add_action( 'admin_print_styles', 'xmm_scripts' );

// Load features that are used, only those
if ( isset( $_GET[ 'page' ] ) && 'xmm' == $_GET['page'] ) {
    include_once 'includes/main.php';
}

if ( isset( $_GET[ 'page' ] ) && 'xmm-sql' == $_GET['page'] ) {
    include_once 'includes/sql-exec.php';
}

if ( isset( $_GET[ 'page' ] ) && ( 'xmm-requests' == $_GET['page'] || 'xmm-blog-requests' == $_GET['page'] )) {
    include_once 'includes/requests.php';
}

if ( isset( $_GET[ 'page' ] ) && ( 'xmm-requests' == $_GET['page'] || 'xmm-request-types' == $_GET['page'] )) {
    include_once 'includes/request-types.php';
}

if ( isset( $_GET[ 'page' ] ) && ( 'xmm-settings' == $_GET['page'] || 'xmm-settings' == $_GET['page'] )) {
    include_once 'includes/settings.php';
}

// Show link to request the extension of the quota if conditions are met
add_action('activity_box_end', 'xmm_add_quota_request_to_dashboard');
