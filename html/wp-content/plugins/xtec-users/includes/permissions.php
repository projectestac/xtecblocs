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

/**
 * Checks if the current user can do a specific action.
 *
 * @param string $action The action to check: add_users, manage_users, create_blogs, create_blog_today, delete_user or
 *                       change_user_password.
 * @param mixed $param Extra argument: the limit of blogs per day (create_blogs and create_blog_today) or the user ID
 *                     (delete_user and change_user_password).
 * @return bool|array True if the user can do the action, false otherwise. For create_blog_today, an empty array if the
 *                    user can create a blog today, or the IDs of the blogs created today otherwise.
 */
function xtec_users_current_user_can(string $action, mixed $param = ''): bool|array
{
    // Super admins can do it all except delete users who are still members of any blog other than the portal
    if (is_super_admin()) {
        if ($action === 'delete_user') {
            return xtec_users_can_delete_user((int)$param);
        }

        return ($action === 'create_blog_today') ? [] : true;
    }

    $user = wp_get_current_user();

    switch ($action) {
        case 'add_users':
            // XTEC users who can add users to the blog
            return current_user_can('add_users') && xtec_users_is_xtec_user($user->ID);

        case 'manage_users':
            return xtec_users_is_xtec_user($user->ID);

        case 'create_blogs':
            // XTEC users, unless the limit of blogs per day ($param) is 0
            return (int)$param !== 0 && xtec_users_is_xtec_user($user->ID);

        case 'create_blog_today':
            // Empty if the user hasn't reached the limit of blogs per day ($param), or the blogs created in the last
            // 24 hours otherwise
            $blogs = xtec_users_blogs_created_today($user->ID);

            return (count($blogs) < (int)$param) ? [] : $blogs;

        case 'delete_user':
            return xtec_users_can_delete_user((int)$param);

        case 'change_user_password':
            // Only the user who created the user ($param)
            return $user->user_login === get_user_meta((int)$param, 'xtec_user_creator', true);
    }

    return false;
}

/**
 * Checks if the current user can delete a user from XTECBlocs. Super admins can delete any user, and the rest only
 * the users that they have created. In both cases, the user can't be member of any blog other than the portal, which
 * is the main site.
 *
 * @param int $user_id The ID of the user to delete.
 * @param array|null $blogs The blogs of the user, if they have already been obtained.
 * @return bool True if the current user can delete the user.
 */
function xtec_users_can_delete_user(int $user_id, ?array $blogs = null): bool
{
    foreach ($blogs ?? get_blogs_of_user($user_id, true) as $blog) {
        if (!is_main_site($blog->userblog_id)) {
            return false;
        }
    }

    return is_super_admin() || wp_get_current_user()->user_login === get_user_meta($user_id, 'xtec_user_creator', true);
}

/**
 * Checks if a user is a XTEC user: registered through LDAP or OAuth, or with an e-mail of XTEC. It is the same
 * criterion as the plugin XTEC LDAP Login, which also considers XTEC users the ones registered before xtec_user_creator
 * existed.
 *
 * @param int $user_id The user ID.
 * @return bool True if the user is a XTEC user.
 */
function xtec_users_is_xtec_user(int $user_id): bool
{
    $creator = get_user_meta($user_id, 'xtec_user_creator', true);
    if ($creator === 'LDAP_XTEC' || $creator === 'OAUTH_XTEC') {
        return true;
    }

    $user = get_userdata($user_id);

    return $user && str_ends_with(strtolower($user->user_email), '@xtec.cat');
}

/**
 * Checks if a username only has the characters allowed by the plugin: lowercase letters, numbers, dots, hyphens and
 * underscores.
 *
 * @param string $username The username.
 * @return bool True if the username is valid, false otherwise.
 */
function xtec_users_is_valid_username(string $username): bool
{
    return !preg_match('/[^a-z0-9_.\-]/', $username);
}
