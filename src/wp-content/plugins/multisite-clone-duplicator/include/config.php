<?php
/**
 * Remember plugin path & URL
 */
define( 'MUCD_PATH', plugin_basename( realpath( dirname( __FILE__ ).'/..') ) );

define( 'MUCD_COMPLETE_PATH', WP_PLUGIN_DIR .'/'.MUCD_PATH );
// XTEC ************ AFEGIT - change the url of MUCD_URL in order to allow https.
// 2015.03.06 @vsaavedr
define( 'MUCD_URL', plugin_dir_url( realpath( dirname( __FILE__ ).'/..') ) .'/'.MUCD_PATH );
//************ ORIGINAL
/*
 define( 'MUCD_URL', WP_PLUGIN_URL.'/'.MUCD_PATH );
 */
// ************ FI

/**
 * Domaine
 */
define( 'MUCD_DOMAIN', 'multisite-clone-duplicator' );

/**
 * Slugs
 */
define( 'MUCD_SLUG_NETWORK_ACTION', 'multisite-clone-duplicator' );
define( 'MUCD_SLUG_ACTION_DUPLICATE', 'duplicate-site' );
define( 'MUCD_SLUG_ACTION_SETTINGS', '_mucdsettings' );

/**
 * Site to excude
 */
define( 'MUCD_SITE_DUPLICATION_EXCLUDE', '' );

/**
 * Environment constants
 */
define( 'MUCD_PRIMARY_SITE_ID', 1 );
define( 'MUCD_MAX_NUMBER_OF_SITE', 5000 );