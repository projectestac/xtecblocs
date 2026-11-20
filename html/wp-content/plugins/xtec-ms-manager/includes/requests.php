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

require_once __DIR__ . '/requests-lib.php';
require_once __DIR__ . '/request-types-lib.php';

xmm_enqueue_requests_script();

/**
 * Shows the page of the requests of the network and does its actions: view, edit, update and delete a request.
 */
function xmm_requests(): void
{
    if (!current_user_can('manage_network_options')) {
        wp_die(esc_html__('Sorry, you are not allowed to access this page.'), '', ['response' => 403]);
    }

    $id = xmm_query_id('request-id');

    if ($id && xmm_query_flag('view')) {
        xmm_view_request($id);
        return;
    }

    if ($id && xmm_query_flag('edit')) {
        xmm_edit_request($id);
        return;
    }

    if ($id && isset($_POST['request-state']) && xmm_post_field('update') === '1') {
        check_admin_referer('xmm-update-request_' . $id);
        $request = [
            'id' => $id,
            'request-state' => xmm_post_field('request-state'),
            'response' => xmm_post_field('response'),
            'priv_notes' => xmm_post_field('priv_notes'),
        ];

        $request_original = xmm_get_request($id);

        // The user who made the request is notified by e-mail only if its state changes
        if (
            xmm_update_request($request) !== false
            && (int)$request_original['state'] !== (int)$request['request-state']
        ) {
            xmm_send_email('update_request', ['request_id' => $id]);
        }
    }

    if ($id && xmm_query_flag('delete')) {
        check_admin_referer('xmm-delete-request_' . $id);
        xmm_delete_request($id);
    }

    xmm_list_requests();
}

/**
 * Shows the page of the requests of the blog and does its actions: create and view a request.
 */
function xmm_blog_requests(): void
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Sorry, you are not allowed to access this page.'), '', ['response' => 403]);
    }

    if (!empty($_POST['select-request']) && xmm_post_field('insert') === '1') {
        check_admin_referer('xmm-insert-request');
        $inserted_id = xmm_insert_request((int)$_POST['select-request'], xmm_post_field('request-comments'));

        // The administrators of the requests are notified by e-mail that there is a new request
        if ($inserted_id !== false && get_site_option('xmm_send_email')) {
            xmm_send_email('insert_request', ['request_id' => $inserted_id]);
        }
    }

    if (xmm_query_flag('new')) {
        xmm_new_request();
        return;
    }

    $id = xmm_query_id('request-id');
    if ($id && xmm_query_flag('view')) {
        xmm_view_request($id);
        return;
    }

    xmm_list_blog_requests();
}
