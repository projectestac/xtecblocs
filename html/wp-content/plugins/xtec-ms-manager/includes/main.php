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
 * Shows the main page of the plugin, with the links to its pages.
 */
function xmm_main(): void
{
    if (!current_user_can('manage_network_options')) {
        wp_die(esc_html__('Sorry, you are not allowed to access this page.'), '', ['response' => 403]);
    }

    $pages = [
        'xmm-sql' => __('SQL execution', 'xtec-ms-manager'),
        'xmm-requests' => __('Requests', 'xtec-ms-manager'),
        'xmm-request-types' => __('Request Types', 'xtec-ms-manager'),
        'xmm-settings' => __('Settings', 'xtec-ms-manager'),
    ];

    echo '<div class="wrap">';
    xmm_show_title(__('XTEC Multisite Manager', 'xtec-ms-manager'));
    foreach ($pages as $page => $title) {
        xmm_show_subtitle('<a href="' . esc_url(network_admin_url('admin.php?page=' . $page)) . '">' . $title . '</a>');
    }
    echo '</div>';
}
