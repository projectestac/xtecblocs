<?php

/**
 * LudicrousDB configuration file
 *
 * This file should be copied to ABSPATH/db-config.php and modified to suit your
 * database environment. This file comes with a basic configuration by default.
 *
 * See README.md for documentation.
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * This sets the default character set. Since WordPress 4.2, the suggested
 * setting is "utf8mb4". We strongly recommend not downgrading to utf8,
 * using latin1, or sticking to the default: utf8mb4.
 *
 * Default: utf8mb4
 */
$wpdb->charset = 'utf8mb4';

/**
 * This sets the default column collation. For best results, investigate which
 * collation is recommended for your specific character set.
 *
 * Default: utf8mb4_unicode_520_ci
 */
$wpdb->collate = 'utf8mb4_unicode_520_ci';

/**
 * This is useful for debugging. Queries are saved in $wpdb->queries. It is not
 * a constant because you might want to use it momentarily.
 * Default: false
 */
$wpdb->save_queries = false;

/**
 * The amount of time to wait before trying again to ping mysql server.
 *
 * Default: 0.1 (Seconds)
 */
$wpdb->recheck_timeout = 0.1;

/**
 * This determines whether to use mysql_connect or mysql_pconnect. The effects
 * of this setting may vary and should be carefully tested.
 *
 * Default: false
 */
$wpdb->persistent = false;

/**
 * This determines whether to use mysql connect or mysql connect has failed and to bail loading the rest of WordPress
 *
 * Default: false
 */
$wpdb->allow_bail = false;

/**
 * This is the number of mysql connections to keep open. Increase if you expect
 * to reuse a lot of connections to different servers. This is ignored if you
 * enable persistent connections.
 *
 * Default: 10
 */
$wpdb->max_connections = 10;

/**
 * Enables checking TCP responsiveness by fsockopen prior to mysql_connect or
 * mysql_pconnect. This was added because PHP's mysql functions do not provide
 * a variable timeout setting. Disabling it may improve average performance by
 * a very tiny margin but lose protection against connections failing slowly.
 *
 * Default: true
 */
$wpdb->check_tcp_responsiveness = true;

/**
 * The cache group that is used to store TCP responsiveness.
 *
 * Default: ludicrousdb
 */
$wpdb->cache_group = 'ludicrousdb';


// add global database
$wpdb->add_database(array(
    'host' => DB_HOST,
    'user' => DB_USER,
    'password' => DB_PASSWORD,
    'write' => 1,
    'read' => 1,
    'name' => DB_PREFIX . 'global',
    'dataset' => 'global',
));

// add additional databases
for ($db_id = 1; $db_id <= DB_NUMS; $db_id++) {
    add_additional_databases($db_id, $wpdb);
}

// a handy function for mapping datasets to databases
function add_additional_databases($db_id, $wpdb): void
{
    $wpdb->add_database([
        'host' => DB_HOST,
        'user' => DB_USER,
        'password' => DB_PASSWORD,
        'name' => DB_PREFIX . ($db_id - 1),
        'write' => 1,
        'read' => 1,
        'dataset' => 's' . $db_id,
    ]);
}

// add global in global database (10)
$wpdb->add_table('global', 'wp_blogs');
$wpdb->add_table('global', 'wp_blogmeta');
$wpdb->add_table('global', 'wp_blog_versions');
$wpdb->add_table('global', 'wp_registration_log');
$wpdb->add_table('global', 'wp_signups');
$wpdb->add_table('global', 'wp_site');
$wpdb->add_table('global', 'wp_sitecategories'); // ????????????
$wpdb->add_table('global', 'wp_sitemeta');
$wpdb->add_table('global', 'wp_usermeta');
$wpdb->add_table('global', 'wp_users');

// add specific XTECBlocs tables which are in global database (8)
$wpdb->add_table('global', 'wp_delblocs');
$wpdb->add_table('global', 'wp_delblocs_users');
$wpdb->add_table('global', 'wp_descriptors');
$wpdb->add_table('global', 'wp_descriptors_pre');
$wpdb->add_table('global', 'wp_globalposts');
$wpdb->add_table('global', 'wp_request_types');
$wpdb->add_table('global', 'wp_requests');
$wpdb->add_table('global', 'wp_search');
$wpdb->add_table('global', 'wp_user_blogs');

// tables from an upgrade of WPMU
// add the tables for the first blog (created during wpmu installation) in global database
$wpdb->add_table('global', 'wp_1_comments');
$wpdb->add_table('global', 'wp_1_commentmeta');
$wpdb->add_table('global', 'wp_1_links');
$wpdb->add_table('global', 'wp_1_options');
$wpdb->add_table('global', 'wp_1_postmeta');
$wpdb->add_table('global', 'wp_1_posts');
$wpdb->add_table('global', 'wp_1_terms');
$wpdb->add_table('global', 'wp_1_term_relationships');
$wpdb->add_table('global', 'wp_1_term_taxonomy');

// tables from a clean installation
// add the tables for the first blog (created during wp multisite installation) in global database
$wpdb->add_table('global', 'wp_comments');
$wpdb->add_table('global', 'wp_commentmeta');
$wpdb->add_table('global', 'wp_links');
$wpdb->add_table('global', 'wp_options');
$wpdb->add_table('global', 'wp_postmeta');
$wpdb->add_table('global', 'wp_posts');
$wpdb->add_table('global', 'wp_terms');
$wpdb->add_table('global', 'wp_term_relationships');
$wpdb->add_table('global', 'wp_term_taxonomy');

$wpdb->add_callback('dataset_distribution');

function dataset_distribution($query, $wpdb): ?string
{
    $blog_id = 0;
    $prefix = $wpdb->base_prefix;

    // 1. Extract blog_id directly from the raw SQL query.
    // Look for any mention of wp_NUMBER_ within the statement.
    if (preg_match("/['\"`\s]{$prefix}(\d+)_/i", $query, $matches)) {
        $blog_id = (int) $matches[1];
    }
    // 2. Capture the current blog context from public WP properties.
    elseif (!empty($wpdb->blogid)) {
        $blog_id = (int) $wpdb->blogid;
    }

    // If we determined we are not on the main blog (ID 1)
    if ($blog_id > 1) {
        // Apply logical sharding
        return 's' . (($blog_id % DB_NUMS) + 1);
    }

    // Fallback to the 'global' dataset
    return null;
}
