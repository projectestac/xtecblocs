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

require_once __DIR__ . '/request-types-lib.php';

xmm_enqueue_requests_script();

/**
 * Shows the page of the request types and does its actions: create, edit, update and delete a request type.
 */
function xmm_request_types(): void
{
    if (!current_user_can('manage_network_options')) {
        wp_die(esc_html__('Sorry, you are not allowed to access this page.'), '', ['response' => 403]);
    }

    if (
        !empty($_POST['request-type-name']) && isset($_POST['request-type-state']) && !empty($_POST['description'])
        && xmm_query_flag('insert')
    ) {
        check_admin_referer('xmm-insert-request-type');
        xmm_insert_request_type(
            xmm_post_field('request-type-name'),
            (int)$_POST['request-type-state'],
            xmm_post_field('description'),
            xmm_post_field('comments_text')
        );
    }

    if (xmm_query_flag('new')) {
        xmm_new_request_type();
        return;
    }

    $id = xmm_query_id('type-id');

    if ($id && xmm_query_flag('edit')) {
        xmm_edit_request_type($id);
        return;
    }

    if ($id && isset($_POST['request-type-state']) && xmm_post_field('update') === '1') {
        check_admin_referer('xmm-update-request-type_' . $id);
        xmm_update_request_type([
            'id' => $id,
            'state' => xmm_post_field('request-type-state'),
            'name' => xmm_post_field('request-type-name'),
            'description' => xmm_post_field('description'),
            'comments_text' => xmm_post_field('comments_text'),
        ]);
    }

    if ($id && xmm_query_flag('delete')) {
        check_admin_referer('xmm-delete-request-type_' . $id);
        xmm_delete_request_type($id);
    }

    xmm_list_request_types();
}
