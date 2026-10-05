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

const XTEC_FAVORITES_DB_VERSION = '1.0';

add_action('wp_delete_site', 'xtec_favorites_delete_site');
add_action('template_redirect', 'xtec_favorites_template_redirect');

/**
 * Returns the URL to add or delete a blog from the favorites of the current user, protected with a nonce.
 *
 * @param string $action 'addPrefer' or 'delPrefer'.
 * @param int $blog_id The ID of the blog.
 * @return string The escaped URL.
 */
function xtec_favorites_url($action, $blog_id): string
{
    return esc_url(wp_nonce_url(
        'index.php?a=' . $action . '&blogId=' . (int) $blog_id,
        'xtec_favorites_' . $action . '_' . (int) $blog_id
    ));
}

/**
 * Adds or deletes a favorite blog of the current user from the links of the portal and goes back to the page where the
 * link was clicked. It must be done before any output is sent, so it can't be in the templates.
 */
function xtec_favorites_template_redirect(): void
{
    $action = $_REQUEST['a'] ?? '';

    if (!is_home() || ($action !== 'addPrefer' && $action !== 'delPrefer')) {
        return;
    }

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
