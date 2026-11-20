<?php

defined('ABSPATH') || exit;

/**
 * Copyright 2026 Departament d'Educació i Formació Professional
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * Shows the settings of the requests and saves them.
 */
function xmm_settings(): void
{
    if (!current_user_can('manage_network_options')) {
        wp_die(esc_html__('Sorry, you are not allowed to access this page.'), '', ['response' => 403]);
    }

    $saved = false;
    if (!empty($_POST['xmm-quota-percentage']) && isset($_POST['xmm-email-addresses'])) {
        check_admin_referer('xmm-settings');
        update_site_option('xmm_quota_percentage', (int)$_POST['xmm-quota-percentage']);
        update_site_option('xmm_send_email', isset($_POST['xmm-send-email']) ? 1 : 0);
        update_site_option('xmm_email_addresses', sanitize_text_field($_POST['xmm-email-addresses']));
        $saved = true;
    }

    $checked = get_site_option('xmm_send_email') ? 'checked="checked"' : '';

    echo '<div class="wrap">';

    if ($saved) {
        echo '<div class="notice notice-success"><p>' . __('Settings updated successfully', 'xtec-ms-manager')
            . '</p></div>';
    }

    xmm_show_title(__('Settings', 'xtec-ms-manager'));
    xmm_show_subtitle(__('Requests settings', 'xtec-ms-manager'));

    echo '<form method="post">';
    echo '<table class="form-table">';
    echo '<tbody><tr>';

    echo '<th scope="row">' . __('Quota usage to allow ask for an extension', 'xtec-ms-manager') . '</th>';
    echo '<td><input type="text" name="xmm-quota-percentage" value="'
        . esc_attr(get_site_option('xmm_quota_percentage')) . '" />%</td>';
    echo '</tr><tr>';

    echo '<th scope="row">' . __('Send e-mail messages when requests are created', 'xtec-ms-manager') . '</th>';
    echo '<td><input type="checkbox" name="xmm-send-email" ' . $checked . ' /></td>';
    echo '</tr><tr>';

    echo '<th scope="row">' . __('E-email addresses to notify new requests', 'xtec-ms-manager') . '</th>';
    echo '<td><textarea name="xmm-email-addresses">' . esc_textarea(get_site_option('xmm_email_addresses'))
        . '</textarea><p class="description">' . __('Comma separated values', 'xtec-ms-manager') . '</p></td>';

    echo '</tr></tbody></table>';
    wp_nonce_field('xmm-settings');
    echo '<input type="submit" value="' . __('Save changes', 'xtec-ms-manager') . '" />';
    echo '</form></div>';
}
