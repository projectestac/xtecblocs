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

add_action('init', 'xtec_users_load_textdomain');
add_action('admin_menu', 'xtec_users_menu');
add_action('load-user-new.php', 'xtec_users_redirect_user_new');
add_action('load-users.php', 'xtec_users_rename_add_user_button');
add_action('admin_bar_menu', 'xtec_users_admin_bar_new_user', 100);

/**
 * Loads the translations of the plugin.
 */
function xtec_users_load_textdomain(): void
{
    load_plugin_textdomain('xtec-users', false, dirname(plugin_basename(XTEC_USERS_FILE)) . '/languages');
}

/**
 * Adds the pages to add and manage users to the menu Users, instead of the page of WordPress to add users.
 */
function xtec_users_menu(): void
{
    if (xtec_users_current_user_can('add_users')) {
        $add_users = add_submenu_page(
            'users.php',
            __('Add', 'xtec-users'),
            __('Add', 'xtec-users'),
            'promote_users',
            'xtec-add-users',
            'xtec_users_add_users_page'
        );
        remove_submenu_page('users.php', 'user-new.php');
        add_action("load-$add_users", 'xtec_users_add_users_actions');
        add_action("load-$add_users", 'xtec_users_enqueue_assets');
    }
    if (xtec_users_current_user_can('manage_users')) {
        $manage_users = add_submenu_page(
            'users.php',
            __('User management', 'xtec-users'),
            __('User management', 'xtec-users'),
            'promote_users',
            'xtec-manage-users',
            'xtec_users_manage_users_page'
        );
        add_action("load-$manage_users", 'xtec_users_manage_users_actions');
        add_action("load-$manage_users", 'xtec_users_enqueue_assets');
    }
}

/**
 * Replaces the page of WordPress to add users (user-new.php) with the page of the plugin, so that its rules can't be
 * skipped. Only super admins can still use the page of WordPress.
 */
function xtec_users_redirect_user_new(): void
{
    if (is_super_admin()) {
        return;
    }

    if (xtec_users_current_user_can('add_users')) {
        wp_safe_redirect(admin_url('users.php?page=xtec-add-users'));
        exit;
    }

    wp_die(
        esc_html__('Only XTEC users can add users to the blogs.', 'xtec-users'),
        '',
        ['response' => 403, 'back_link' => true]
    );
}

/**
 * Changes the text of the button to add users of the list of users. In a network, WordPress only lets the
 * administrators of a blog add existing users, so the button says "Add Existing User". With the plugin, they can also
 * create local users.
 */
function xtec_users_rename_add_user_button(): void
{
    add_filter('gettext', 'xtec_users_add_user_text', 10, 3);
}

/**
 * Replaces the text "Add Existing User" of WordPress with "Add User".
 *
 * @param string $translation The translated text.
 * @param string $text The original text.
 * @param string $domain The text domain.
 * @return string The translated text.
 */
function xtec_users_add_user_text(string $translation, string $text, string $domain): string
{
    return ($text === 'Add Existing User' && $domain === 'default') ? __('Add User') : $translation;
}

/**
 * Makes the link to add a user of the toolbar point to the page of the plugin, or removes it if the user can't use
 * that page. Super admins keep the link of WordPress.
 *
 * @param WP_Admin_Bar $wp_admin_bar The toolbar.
 */
function xtec_users_admin_bar_new_user(WP_Admin_Bar $wp_admin_bar): void
{
    if (is_super_admin() || !$wp_admin_bar->get_node('new-user')) {
        return;
    }

    if (xtec_users_current_user_can('add_users')) {
        $wp_admin_bar->add_node(['id' => 'new-user', 'href' => admin_url('users.php?page=xtec-add-users')]);
    } else {
        $wp_admin_bar->remove_node('new-user');
    }
}

/**
 * Loads the JavaScript and the stylesheet of the pages of the plugin.
 */
function xtec_users_enqueue_assets(): void
{
    $plugindir = plugin_dir_url(XTEC_USERS_FILE);
    // The date of the files is used as version so that browsers load them again when they change
    wp_enqueue_script(
        'xtec-users_js',
        $plugindir . 'xtec-users.js',
        ['password-strength-meter'],
        (string)filemtime(dirname(XTEC_USERS_FILE) . '/xtec-users.js'),
        true
    );
    wp_localize_script('xtec-users_js', 'xtecUsersL10n', [
        'checkError' => __('The username could not be checked. Please try again.', 'xtec-users'),
    ]);
    wp_enqueue_style(
        'xtec-users_css',
        $plugindir . 'xtec-users.css',
        [],
        (string)filemtime(dirname(XTEC_USERS_FILE) . '/xtec-users.css')
    );
}

/**
 * Stops a request that isn't valid, for example with data that has been manipulated or a user that doesn't exist.
 */
function xtec_users_invalid_request(): void
{
    wp_die(
        esc_html__('The request is not valid.', 'xtec-users'),
        '',
        ['response' => 403, 'back_link' => true]
    );
}

/**
 * Returns a text field of the data of the form, or an empty string if it is missing or it isn't a text.
 *
 * @param array $data The data of the form, already unslashed.
 * @param string $key The name of the field.
 * @return string The value of the field.
 */
function xtec_users_field(array $data, string $key): string
{
    return (isset($data[$key]) && is_string($data[$key])) ? $data[$key] : '';
}

/**
 * Returns the HTML of a notice of WordPress shown inside the content of the page.
 *
 * @param string $type The type of notice: info, success, warning or error.
 * @param string $html The content of the notice, already escaped.
 * @return string The HTML of the notice.
 */
function xtec_users_notice(string $type, string $html): string
{
    return "<div class='notice notice-$type inline'>$html</div>";
}

/**
 * Returns the HTML of the field of a password, with a button to generate another one and the strength of the password.
 * The password is shown, as it has to be given to the user.
 *
 * @param string $password The password shown in the field.
 * @return string The HTML of the field and its description.
 */
function xtec_users_password_field(string $password): string
{
    return "<input type='text' id='xtec-users-password' name='user[password]' class='regular-text' value='"
        . esc_attr($password) . "' autocomplete='off' spellcheck='false' /> "
        . "<button type='button' id='xtec-users-generate' class='button hide-if-no-js'>"
        . esc_html__('Generate another one', 'xtec-users') . '</button>'
        . "<p class='description'><span id='xtec-users-strength' aria-live='polite'></span> "
        /* translators: %d: Minimum number of characters. */
        . sprintf(esc_html__('It must have at least %d characters.', 'xtec-users'), XTEC_USERS_MIN_PASSWORD_LENGTH)
        . '</p>';
}

/**
 * Checks the length of a password.
 *
 * @param string $password The password.
 * @return string The HTML of the error, or an empty string if the password is valid.
 */
function xtec_users_password_error(string $password): string
{
    if ($password === '') {
        return '<p>' . esc_html__('You must enter a password.', 'xtec-users') . '</p>';
    }
    if (mb_strlen($password) < XTEC_USERS_MIN_PASSWORD_LENGTH) {
        /* translators: %d: Minimum number of characters. */
        $error = esc_html__('The password must have at least %d characters.', 'xtec-users');

        return '<p>' . sprintf($error, XTEC_USERS_MIN_PASSWORD_LENGTH) . '</p>';
    }

    return '';
}
