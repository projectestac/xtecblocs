<?php

defined('ABSPATH') || exit;

/**
 * Copyright 2011 German Antolin Priotto
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

const XTEC_FAVORITES_DB_VERSION = '1.0';

add_action('wp_delete_site', 'xtec_favorites_delete_site');

/**
 * Deletes a deleted blog of the preferred blogs of all the users.
 *
 * @param WP_Site $site The deleted blog.
 */
function xtec_favorites_delete_site(WP_Site $site): void
{
    global $wpdb;
    $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->user_blogs} WHERE blogId = %d", $site->id));
}

/**
 * Adds a preferred blog to the current user.
 *
 * @param int $blogId The ID of the blog.
 */
function xtec_favorites_add_preferred($blogId): bool
{
    global $wpdb;

    $user_id = get_current_user_id();
    if (!$user_id) {
        return false;
    }

    // verify that not exists
    $exists = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT count(ubid) FROM {$wpdb->user_blogs} WHERE userId = %d and blogId = %d",
            $user_id,
            $blogId
        )
    );

    //Create a new entry in user prefered blogs
    if (!$exists) {
        $wpdb->query(
            $wpdb->prepare(
                "INSERT INTO {$wpdb->user_blogs} (userId,blogId) VALUES (%d, %d)",
                $user_id,
                $blogId
            )
        );
    }
    return true;
}

/**
 * Deletes a preferred blog of the current user.
 *
 * @param int $blogId The ID of the blog.
 */
function xtec_favorites_delete_preferred($blogId): void
{
    global $wpdb;
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->user_blogs} WHERE blogId = %d AND userId = %d",
            $blogId,
            get_current_user_id()
        )
    );
}

/**
 * Gets the preferred blogs of the current user. The deactivated, archived and spam blogs are skipped, but they are
 * kept as preferred in case they are restored.
 *
 * @return array The IDs of the blogs.
 */
function xtec_favorites_get_user_preferred_blogs(): array
{
    global $wpdb;

    $blogsArray = [];
    $blogs = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT ub.userId, ub.blogId FROM {$wpdb->user_blogs} ub JOIN {$wpdb->blogs} b ON b.blog_id = ub.blogId " .
            "WHERE ub.userId = %d AND b.`deleted` = '0' AND b.`archived` = '0' AND b.`spam` = '0'",
            get_current_user_id()
        )
    );

    foreach ($blogs as $blog) {
        $blogsArray[] = $blog->blogId;
    }

    return $blogsArray;
}

/**
 * Creates XTEC Favorites database table.
 */
function xtec_favorites_activation_hook(): void
{
    global $wpdb;

    $table_name = $wpdb->user_blogs;

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
        $sql = "CREATE TABLE $table_name (
                ubid int(11) NOT NULL AUTO_INCREMENT,
                userId int(11) NOT NULL DEFAULT '0',
                blogId int(11) NOT NULL DEFAULT '0',
                PRIMARY KEY (ubid),
                KEY userId (userId));";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    add_option('xtec_favorites_db_version', XTEC_FAVORITES_DB_VERSION);
}
