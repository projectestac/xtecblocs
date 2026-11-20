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
 * Creates a request type.
 *
 * @param string $name The name.
 * @param int $state The state: active or inactive.
 * @param string $description The description, shown to the users when they choose the type.
 * @param string $comments_text The text shown over the field of the comments of the requests.
 */
function xmm_insert_request_type(string $name, int $state, string $description, string $comments_text): void
{
    global $wpdb;

    $result = $wpdb->insert(
        $wpdb->prefix . 'request_types',
        [
            'name' => sanitize_text_field($name),
            'state' => $state,
            'description' => sanitize_text_field($description),
            'comments_text' => sanitize_text_field($comments_text),
        ],
        ['%s', '%d', '%s', '%s']
    );

    xmm_log_to_user(
        $result,
        __('Request type successfully created', 'xtec-ms-manager'),
        __('Could not create the request type', 'xtec-ms-manager')
    );
}

/**
 * Updates a request type.
 *
 * @param array $request_type The request type: id, name, state, description and comments_text.
 */
function xmm_update_request_type(array $request_type): void
{
    global $wpdb;

    $result = $wpdb->update(
        $wpdb->prefix . 'request_types',
        [
            'name' => sanitize_text_field($request_type['name']),
            'state' => (int)$request_type['state'],
            'description' => sanitize_text_field($request_type['description']),
            'comments_text' => sanitize_text_field($request_type['comments_text']),
        ],
        ['id' => (int)$request_type['id']],
        ['%s', '%d', '%s', '%s'],
        ['%d']
    );

    xmm_log_to_user(
        $result,
        __('Request type successfully updated', 'xtec-ms-manager'),
        __('Could not update the request type', 'xtec-ms-manager')
    );
}

/**
 * Deletes a request type.
 *
 * @param int $id The ID of the request type.
 */
function xmm_delete_request_type(int $id): void
{
    global $wpdb;

    $result = $wpdb->delete($wpdb->prefix . 'request_types', ['id' => $id], ['%d']);

    xmm_log_to_user(
        $result,
        __('Request type successfully deleted', 'xtec-ms-manager'),
        __('Could not delete the request type', 'xtec-ms-manager')
    );
}

/**
 * Shows the list of the request types.
 */
function xmm_list_request_types(): void
{
    global $wpdb;

    // Parameters of the actions that are removed from the links
    $actions = ['insert', 'delete', 'new', 'update'];

    echo '<div class="wrap">';
    xmm_show_title(
        esc_html__('Request Types', 'xtec-ms-manager') . '<a class="page-title-action" href="'
        . esc_url(remove_query_arg(['type-id', 'insert', 'edit', 'delete', 'update'], add_query_arg('new', 1))) . '">'
        . esc_html__('New request type', 'xtec-ms-manager') . '</a>'
    );

    $request_types = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}request_types ORDER BY id", ARRAY_A);

    echo '<table cellspacing="3" cellpadding="3" width="100%" class="widefat">';
    echo '<thead><tr>';
    echo '<th scope="col">' . __('ID', 'xtec-ms-manager') . '</th>';
    echo '<th scope="col">' . __('Name', 'xtec-ms-manager') . '</th>';
    echo '<th scope="col">' . __('State', 'xtec-ms-manager') . '</th>';
    echo '<th scope="col">' . __('Description', 'xtec-ms-manager') . '</th>';
    echo '<th scope="col">' . __('Title for comments', 'xtec-ms-manager') . '</th>';
    echo '<th scope="col">' . __('Actions', 'xtec-ms-manager') . '</th>';
    echo '</tr></thead>';
    echo '<tbody>';

    foreach ($request_types as $type) {
        $id = (int)$type['id'];
        [$color, $state_desc] = xmm_get_request_type_state((int)$type['state']);

        $edit_url = remove_query_arg($actions, add_query_arg(['type-id' => $id, 'edit' => 1]));
        // The script adds the parameter delete to the link when the user confirms the deletion
        $delete_url = remove_query_arg($actions, add_query_arg([
            'type-id' => $id,
            '_wpnonce' => wp_create_nonce('xmm-delete-request-type_' . $id),
        ]));

        echo '<tr bgcolor="' . esc_attr($color) . '">';
        echo '<td>' . $id . '</td>';
        echo '<td>' . esc_html($type['name']) . '</td>';
        echo '<td>' . esc_html($state_desc) . '</td>';
        echo '<td>' . esc_html($type['description']) . '</td>';
        echo '<td>' . esc_html($type['comments_text']) . '</td>';
        echo '<td>';
        echo '<a id="xmm-edit-link-' . $id . '" href="' . esc_url($edit_url) . '">'
            . '<span class="dashicons dashicons-edit"></span></a> ';
        echo '<a id="xmm-del-link-' . $id . '" href="' . esc_url($delete_url) . '" onclick="confirm_deletion(' . $id
            . ')"><span class="dashicons dashicons-dismiss"></span></a>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}

/**
 * Shows the form to create a request type.
 */
function xmm_new_request_type(): void
{
    echo '<div class="wrap">';
    xmm_show_title(esc_html__('New Request Type', 'xtec-ms-manager'));
    echo '<form action="' . esc_url(remove_query_arg('new', add_query_arg('insert', 1))) . '" method="post">';
    xmm_print_request_type_fields();
    wp_nonce_field('xmm-insert-request-type');
    echo '<input type="submit" value="' . esc_attr__('Save changes', 'xtec-ms-manager') . '" />';
    echo '</form>';
    echo '</div>';
}

/**
 * Shows the form to edit a request type.
 *
 * @param int $type_id The ID of the request type.
 */
function xmm_edit_request_type(int $type_id): void
{
    $title = esc_html__('Request type edition', 'xtec-ms-manager');

    $request_type = xmm_get_request_type($type_id);
    if ($request_type === null) {
        xmm_print_not_found($title, __('Request type not found', 'xtec-ms-manager'));
        return;
    }

    echo '<div class="wrap">';
    xmm_show_title($title);
    echo '<form action="' . esc_url(remove_query_arg('edit')) . '" method="post">';
    xmm_print_request_type_fields($request_type);
    echo '<input type="hidden" name="update" value="1" />';
    wp_nonce_field('xmm-update-request-type_' . $type_id);
    echo '<input type="submit" value="' . esc_attr__('Save changes', 'xtec-ms-manager') . '" />';
    echo '</form>';
    echo '</div>';
}

/**
 * Shows the fields of the form of a request type.
 *
 * @param array|null $request_type The request type that is edited, or null if it is a new one.
 */
function xmm_print_request_type_fields(?array $request_type = null): void
{
    echo '<table class="form-table">';
    echo '<tbody>';

    echo '<tr><th scope="row">' . __('Request type name', 'xtec-ms-manager') . '</th>';
    echo '<td><input type="text" name="request-type-name" value="' . esc_attr($request_type['name'] ?? '')
        . '" /></td></tr>';

    echo '<tr><th scope="row">' . __('State', 'xtec-ms-manager') . '</th><td>';
    xmm_print_state_select(
        'request-type-state',
        xmm_get_request_type_states(),
        isset($request_type['state']) ? (int)$request_type['state'] : null
    );
    echo '</td></tr>';

    echo '<tr><th scope="row">' . __('Description', 'xtec-ms-manager') . '</th>';
    echo '<td><textarea name="description">' . esc_textarea($request_type['description'] ?? '')
        . '</textarea></td></tr>';

    echo '<tr><th scope="row">' . __('Text for optional comments field', 'xtec-ms-manager') . '</th>';
    echo '<td><textarea name="comments_text">' . esc_textarea($request_type['comments_text'] ?? '')
        . '</textarea></td></tr>';

    echo '</tbody>';
    echo '</table>';
}

/**
 * Returns the color and the description of a state of the request types.
 *
 * @param int $state_id The ID of the state.
 * @return array The color and the description.
 */
function xmm_get_request_type_state(int $state_id): array
{
    return match ($state_id) {
        XMM_STATE_ACTIVE => ['#dff0d8', __('Active', 'xtec-ms-manager')],
        XMM_STATE_INACTIVE => ['#f2dede', __('Inactive', 'xtec-ms-manager')],
        default => ['white', __('Request type state not set', 'xtec-ms-manager')],
    };
}

/**
 * Returns the states of the request types.
 *
 * @return array The states, by their ID, with their color and their description.
 */
function xmm_get_request_type_states(): array
{
    $states = [];
    foreach ([XMM_STATE_ACTIVE, XMM_STATE_INACTIVE] as $state) {
        [$color, $desc] = xmm_get_request_type_state($state);
        $states[$state] = ['color' => $color, 'desc' => $desc];
    }

    return $states;
}

/**
 * Returns a request type. They are in the tables of the main blog.
 *
 * @param int $id The ID of the request type.
 * @return array|null The request type, or null if it doesn't exist.
 */
function xmm_get_request_type(int $id): ?array
{
    global $wpdb;

    switch_to_blog(1);
    $request_type = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}request_types WHERE id = %d", $id),
        ARRAY_A
    );
    restore_current_blog();

    return $request_type;
}
