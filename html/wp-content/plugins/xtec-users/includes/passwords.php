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

add_filter('allow_password_reset', 'xtec_users_allow_password_reset', 10, 2);
add_filter('show_password_fields', 'xtec_users_show_password_fields');

/**
 * Prevents the reset of the password of the users who can't use it: XTEC users, whose password is managed by XTEC,
 * and local users without an e-mail, who can't receive the message to reset it.
 *
 * @param bool|WP_Error $allow Whether the password can be reset.
 * @param int $user_id The ID of the user.
 * @return bool|WP_Error The error that explains how to get a new password, or $allow otherwise.
 */
function xtec_users_allow_password_reset(bool|WP_Error $allow, int $user_id): bool|WP_Error
{
    $user = get_userdata($user_id);
    $is_xtec_user = xtec_users_is_xtec_user($user_id);
    if (!$user || (!$is_xtec_user && !str_ends_with($user->user_email, '@xtec.invalid'))) {
        return $allow;
    }

    $error = new WP_Error(
        'no_password_reset',
        '<strong>' . esc_html__('It is not possible to reset the password.', 'xtec-users') . '</strong>'
    );

    if ($is_xtec_user) {
        /* translators: %s: Link to the user support service of XTEC. */
        $text = esc_html__(
            'The password of XTEC users cannot be managed from XTECBlocs. If you have lost it, visit the %s.',
            'xtec-users'
        );
        $link = '<a href="' . esc_url(XTEC_USERS_SUPPORT_URL) . '">'
            . esc_html__('user support service of XTEC', 'xtec-users') . '</a>';
        $error->add('no_password_reset', sprintf($text, $link));
    } else {
        $error->add(
            'no_password_reset',
            esc_html__(
                'If you have lost your password, contact the user who registered you in the blog service.',
                'xtec-users'
            )
        );
    }

    return $error;
}

/**
 * Hides the fields to change the password in the profile of the users. Only super admins can change passwords there:
 * the password of XTEC users is managed by XTEC, and the password of local users is changed by who created them.
 *
 * @param bool $show Whether to show the fields.
 * @return bool True if the fields are shown.
 */
function xtec_users_show_password_fields(bool $show): bool
{
    return $show && is_super_admin();
}
