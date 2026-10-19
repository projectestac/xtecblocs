<?php

defined('ABSPATH') || exit;

/**
 * Copyright 2026 Departament d'Educació i Formació Professional
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Network tables of the history of deleted blogs, named like the tables of WordPress core (e.g. $wpdb->blogs)
global $wpdb;
$wpdb->delblocs = $wpdb->base_prefix . 'delblocs';
$wpdb->delblocs_users = $wpdb->base_prefix . 'delblocs_users';

/**
 * Creates the tables of the history of deleted blogs, if they don't exist. They existed before this version of the
 * plugin, so their structure is kept and the existing tables aren't changed.
 */
function xtec_maintenance_activate(): void
{
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();
    $tables = [
        $wpdb->delblocs => "CREATE TABLE $wpdb->delblocs (
            id int(11) NOT NULL AUTO_INCREMENT,
            site_id varchar(60) NOT NULL,
            site_path varchar(100) NOT NULL,
            blogname varchar(255) NOT NULL,
            del_date datetime NOT NULL,
            status tinyint(4) NOT NULL DEFAULT 0,
            PRIMARY KEY  (id)
        ) $charset_collate;",
        $wpdb->delblocs_users => "CREATE TABLE $wpdb->delblocs_users (
            id int(11) NOT NULL AUTO_INCREMENT,
            blog_id int(11) NOT NULL,
            user_id int(11) NOT NULL,
            user_login varchar(60) NOT NULL,
            display_name varchar(60) NOT NULL,
            user_email varchar(50) NOT NULL,
            meta_value varchar(255) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;",
    ];

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    foreach ($tables as $table => $sql) {
        if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table))) !== $table) {
            dbDelta($sql);
        }
    }

    update_site_option('xtec_maintenance_db_version', XTEC_MAINTENANCE_DB_VERSION);
}

/**
 * Saves a blog and its users in the history of deleted blogs, before deleting it.
 *
 * @param int $blog_id The ID of the blog.
 * @param int $status The reason of the deletion, for example XTEC_MAINTENANCE_STATUS_INACTIVITY.
 */
function xtec_maintenance_log_deleted_blog(int $blog_id, int $status): void
{
    global $wpdb;

    $site = get_site($blog_id);
    if (!$site) {
        return;
    }

    // The values are cut to the length of the columns, so that the insertion doesn't fail
    $wpdb->insert($wpdb->delblocs, [
        'site_id' => (string)$blog_id,
        'site_path' => mb_substr($site->path, 0, 100),
        'blogname' => mb_substr((string)get_blog_option($blog_id, 'blogname'), 0, 255),
        // In UTC, like the previous versions of the plugin
        'del_date' => current_time('mysql', true),
        'status' => $status,
    ]);

    $capabilities_key = $wpdb->get_blog_prefix($blog_id) . 'capabilities';
    foreach (get_users(['blog_id' => $blog_id]) as $user) {
        $wpdb->insert($wpdb->delblocs_users, [
            'blog_id' => $blog_id,
            'user_id' => $user->ID,
            'user_login' => mb_substr($user->user_login, 0, 60),
            'display_name' => mb_substr($user->display_name, 0, 60),
            'user_email' => mb_substr($user->user_email, 0, 50),
            // The capabilities of the user in the blog, as in the history kept by the previous versions of the plugin
            'meta_value' => mb_substr(maybe_serialize(get_user_meta($user->ID, $capabilities_key, true)), 0, 255),
        ]);
    }
}
