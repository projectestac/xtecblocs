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

// Number of requests of each page of the lists
const XMM_ITEMS_PER_PAGE = 30;

// Value of the dates that haven't been set
const XMM_EMPTY_DATE = '0000-00-00 00:00:00';

// Columns of the requests that are shown, with the name of their type
const XMM_REQUEST_COLUMNS = 'r.id, r.state, r.blog_id, r.request_type_id, r.user_id, r.user_login, r.display_name, '
    . 'r.user_email, r.time_creation, r.time_edition, r.comments, r.response, r.priv_notes, rt.name';

/**
 * Creates a request of the current blog, made by the current user. Only the active request types can be requested.
 *
 * @param int $request_type_id The ID of the request type.
 * @param string $request_comments The comments of the user.
 * @return int|false The ID of the request, or false if it couldn't be created.
 */
function xmm_insert_request(int $request_type_id, string $request_comments): int|false
{
    global $wpdb;

    $request_type = xmm_get_request_type($request_type_id);
    if ($request_type === null || (int)$request_type['state'] !== XMM_STATE_ACTIVE) {
        xmm_log_to_user(false, '', __('Could not create the request', 'xtec-ms-manager'));
        return false;
    }

    $user = wp_get_current_user();
    $blog_id = get_current_blog_id();

    // The requests are in the tables of the main blog
    switch_to_blog(1);
    $result = $wpdb->insert(
        $wpdb->prefix . 'requests',
        [
            'state' => XMM_STATE_PENDING,
            'blog_id' => $blog_id,
            'request_type_id' => $request_type_id,
            'user_id' => $user->ID,
            'user_login' => $user->user_login,
            'display_name' => $user->display_name,
            'user_email' => $user->user_email,
            'time_creation' => date('Y-m-d H:i:s'),
            'time_edition' => XMM_EMPTY_DATE,
            'comments' => sanitize_text_field($request_comments),
        ],
        ['%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s']
    );
    restore_current_blog();

    xmm_log_to_user(
        $result,
        __('Request successfully created', 'xtec-ms-manager'),
        __('Could not create the request', 'xtec-ms-manager')
    );

    return $result === false ? false : $wpdb->insert_id;
}

/**
 * Updates the state, the response and the private notes of a request.
 *
 * @param array $request The request: id, request-state, response and priv_notes.
 * @return bool True if the request has been updated.
 */
function xmm_update_request(array $request): bool
{
    global $wpdb;

    $result = $wpdb->update(
        $wpdb->prefix . 'requests',
        [
            'state' => (int)$request['request-state'],
            'response' => sanitize_text_field($request['response']),
            'priv_notes' => sanitize_text_field($request['priv_notes']),
            'time_edition' => date('Y-m-d H:i:s'),
        ],
        ['id' => (int)$request['id']],
        ['%d', '%s', '%s', '%s'],
        ['%d']
    );

    xmm_log_to_user(
        $result,
        __('Request successfully updated', 'xtec-ms-manager'),
        __('Could not update the request', 'xtec-ms-manager')
    );

    return $result !== false;
}

/**
 * Deletes a request.
 *
 * @param int $id The ID of the request.
 */
function xmm_delete_request(int $id): void
{
    global $wpdb;

    $result = $wpdb->delete($wpdb->prefix . 'requests', ['id' => $id], ['%d']);

    xmm_log_to_user(
        $result,
        __('Request successfully deleted', 'xtec-ms-manager'),
        __('Could not delete the request', 'xtec-ms-manager')
    );
}

/**
 * Shows the list of the requests of all the blogs, in the administration of the network.
 */
function xmm_list_requests(): void
{
    global $wpdb;

    echo '<div class="wrap">';
    xmm_show_title(esc_html__('Requests', 'xtec-ms-manager'));

    $num_records = (int)$wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}requests");
    [$current_page, $last_page, $offset] = xmm_get_pages($num_records);

    $requests = $wpdb->get_results(
        $wpdb->prepare(
            'SELECT ' . XMM_REQUEST_COLUMNS . " FROM {$wpdb->prefix}requests r
            LEFT JOIN {$wpdb->prefix}request_types rt ON r.request_type_id = rt.id
            ORDER BY r.state, r.time_edition ASC
            LIMIT %d OFFSET %d",
            XMM_ITEMS_PER_PAGE,
            $offset
        ),
        ARRAY_A
    );

    xmm_print_pager($current_page, $last_page);

    echo '<table cellspacing="3" cellpadding="3" width="100%" class="widefat">';
    echo '<thead><tr>';
    echo '<th scope="col">' . __('ID', 'xtec-ms-manager') . '</th>';
    echo '<th scope="col">' . __('Blog URL', 'xtec-ms-manager') . '</th>';
    echo '<th scope="col">' . __('State', 'xtec-ms-manager') . '</th>';
    echo '<th scope="col">' . __('Request Type', 'xtec-ms-manager') . '</th>';
    echo '<th scope="col">' . __('Quota usage', 'xtec-ms-manager') . '</th>';
    echo '<th scope="col">' . __('User', 'xtec-ms-manager') . '</th>';
    echo '<th scope="col">' . __('Request date', 'xtec-ms-manager') . '</th>';
    echo '<th scope="col">' . __('Edition date', 'xtec-ms-manager') . '</th>';
    echo '<th scope="col">' . __('Actions', 'xtec-ms-manager') . '</th>';
    echo '</tr></thead>';
    echo '<tbody>';

    $actions = ['insert', 'delete', 'new', 'update'];

    foreach ($requests as $request) {
        $id = (int)$request['id'];

        // Percentage of the quota used by the blog
        switch_to_blog((int)$request['blog_id']);
        $blog_usage = xmm_get_blog_usage();
        restore_current_blog();

        [$color, $state_desc] = xmm_get_request_state((int)$request['state']);

        $view_url = add_query_arg(['view' => 1, 'request-id' => $id]);
        $edit_url = remove_query_arg($actions, add_query_arg(['request-id' => $id, 'edit' => 1]));
        // The script adds the parameter delete to the link when the user confirms the deletion
        $delete_url = remove_query_arg($actions, add_query_arg([
            'request-id' => $id,
            '_wpnonce' => wp_create_nonce('xmm-delete-request_' . $id),
        ]));

        echo '<tr bgcolor="' . esc_attr($color) . '">';
        echo '<td>' . $id . '</td>';
        echo '<td>' . xmm_get_blog_link((int)$request['blog_id']) . '</td>';
        echo '<td>' . esc_html($state_desc) . '</td>';
        echo '<td>' . esc_html($request['name']) . '</td>';
        echo '<td>' . ($blog_usage === null ? '&mdash;' : esc_html($blog_usage) . ' %') . '</td>';
        echo '<td><span title="' . esc_attr($request['display_name']) . '">' . esc_html($request['user_login'])
            . '</span></td>';
        echo '<td>' . xmm_format_date($request['time_creation']) . '</td>';
        echo '<td>' . xmm_format_date($request['time_edition']) . '</td>';
        echo '<td>';
        echo '<a href="' . esc_url($view_url) . '"><span class="dashicons dashicons-info"></span></a> ';
        echo '<a id="xmm-edit-link-' . $id . '" href="' . esc_url($edit_url) . '">'
            . '<span class="dashicons dashicons-edit"></span></a> ';
        echo '<a id="xmm-del-link-' . $id . '" href="' . esc_url($delete_url) . '" onclick="confirm_deletion(' . $id
            . ')"><span class="dashicons dashicons-dismiss"></span></a>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';

    xmm_print_pager($current_page, $last_page);

    echo '</div>';
}

/**
 * Shows the list of the requests of the current blog.
 */
function xmm_list_blog_requests(): void
{
    global $wpdb;

    echo '<div class="wrap">';
    xmm_show_title(
        esc_html__('Requests', 'xtec-ms-manager') . '<a class="page-title-action" href="'
        . esc_url(add_query_arg('new', 1)) . '">' . esc_html__('New request', 'xtec-ms-manager') . '</a>'
    );

    $blog_id = get_current_blog_id();

    switch_to_blog(1);

    $num_records = (int)$wpdb->get_var(
        $wpdb->prepare("SELECT count(id) FROM {$wpdb->prefix}requests WHERE blog_id = %d", $blog_id)
    );
    [$current_page, $last_page, $offset] = xmm_get_pages($num_records);

    $requests = $wpdb->get_results(
        $wpdb->prepare(
            'SELECT ' . XMM_REQUEST_COLUMNS . " FROM {$wpdb->prefix}requests r
            LEFT JOIN {$wpdb->prefix}request_types rt ON r.request_type_id = rt.id
            WHERE r.blog_id = %d
            ORDER BY r.state, r.time_edition DESC
            LIMIT %d OFFSET %d",
            $blog_id,
            XMM_ITEMS_PER_PAGE,
            $offset
        ),
        ARRAY_A
    );

    restore_current_blog();

    xmm_print_pager($current_page, $last_page);

    echo '<table cellspacing="3" cellpadding="3" width="100%" class="widefat">';
    echo '<thead><tr>';
    echo '<th scope="col">' . __('ID', 'xtec-ms-manager') . '</th>';
    echo '<th scope="col">' . __('State', 'xtec-ms-manager') . '</th>';
    echo '<th scope="col">' . __('Request Type', 'xtec-ms-manager') . '</th>';
    echo '<th scope="col">' . __('User', 'xtec-ms-manager') . '</th>';
    echo '<th scope="col">' . __('Request date', 'xtec-ms-manager') . '</th>';
    echo '<th scope="col">' . __('Edition date', 'xtec-ms-manager') . '</th>';
    echo '<th scope="col">' . __('Actions', 'xtec-ms-manager') . '</th>';
    echo '</tr></thead>';
    echo '<tbody>';

    foreach ($requests as $request) {
        $id = (int)$request['id'];
        [$color, $state_desc] = xmm_get_request_state((int)$request['state']);
        $view_url = add_query_arg(['view' => 1, 'request-id' => $id]);

        echo '<tr bgcolor="' . esc_attr($color) . '">';
        echo '<td>' . $id . '</td>';
        echo '<td>' . esc_html($state_desc) . '</td>';
        echo '<td>' . esc_html($request['name']) . '</td>';
        echo '<td><span title="' . esc_attr($request['display_name']) . '">' . esc_html($request['user_login'])
            . '</span></td>';
        echo '<td>' . xmm_format_date($request['time_creation']) . '</td>';
        echo '<td>' . xmm_format_date($request['time_edition']) . '</td>';
        echo '<td><a href="' . esc_url($view_url) . '"><span class="dashicons dashicons-info"></span></a></td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';

    xmm_print_pager($current_page, $last_page);

    echo '</div>';
}

/**
 * Shows the form to make a request from a blog, with the active request types.
 */
function xmm_new_request(): void
{
    global $wpdb;

    // The request types are in the tables of the main blog
    switch_to_blog(1);
    $results = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}request_types WHERE state = %d", XMM_STATE_ACTIVE),
        ARRAY_A
    );
    restore_current_blog();

    echo '<div class="wrap">';
    xmm_show_title(esc_html__('New Request', 'xtec-ms-manager'));

    if (empty($results)) {
        echo '<div class="info">' . __('There are no available request types', 'xtec-ms-manager') . '</div>';
        echo '</div>';
        return;
    }

    echo '<form action="' . esc_url(add_query_arg('page', 'xmm-blog-requests', 'tools.php')) . '" method="post">';

    // The script shows the description and the text of the comments of the chosen request type
    echo '<script>var dataResults = '
        . wp_json_encode($results, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';</script>';

    echo '<div id="xmm-select-request">';
    echo '<span id="xmm-select-request-text">' . __('Select a request type', 'xtec-ms-manager') . '</span>';
    echo '<select id="select-request" name="select-request">';
    echo '<option value="0" selected></option>';
    foreach ($results as $result) {
        echo '<option value="' . (int)$result['id'] . '">' . esc_html($result['name']) . '</option>';
    }
    echo '</select>';
    echo '</div>';

    // The rest of the form is hidden until a request type is chosen
    echo '<div id="form-details">';
    echo '<div id="request-info"></div>';
    echo '<div id="comments-text"></div>';
    echo '<textarea id="request-comments" name="request-comments"></textarea>';
    echo '<input type="submit" value="' . esc_attr__('Submit request', 'xtec-ms-manager') . '" />';
    echo '</div>';
    echo '<input type="hidden" name="insert" value="1" />';
    wp_nonce_field('xmm-insert-request');

    echo '</form>';
    echo '</div>';
}

/**
 * Shows the form to edit a request, in the administration of the network.
 *
 * @param int $request_id The ID of the request.
 */
function xmm_edit_request(int $request_id): void
{
    $title = esc_html__('Request details', 'xtec-ms-manager');

    $request = xmm_get_request_details($request_id);
    if ($request === null) {
        xmm_print_not_found($title, __('Request not found', 'xtec-ms-manager'));
        return;
    }

    echo '<div class="wrap">';
    xmm_show_title($title);
    echo '<form action="' . esc_url(remove_query_arg('edit')) . '" method="post">';

    xmm_print_request_details($request, true);

    echo '<h2>' . __('Request state', 'xtec-ms-manager') . '</h2>';
    echo '<table class="form-table">';
    echo '<tbody>';

    echo '<tr><th scope="row">' . __('State', 'xtec-ms-manager') . '</th><td>';
    xmm_print_state_select('request-state', xmm_get_request_states(), (int)$request['state']);
    echo '</td></tr>';

    echo '<tr><th scope="row">' . __('Latest change', 'xtec-ms-manager') . '</th>';
    echo '<td>' . xmm_format_date($request['time_edition'], __('Not edited yet', 'xtec-ms-manager')) . '</td></tr>';

    echo '<tr><th scope="row">' . __('Response', 'xtec-ms-manager') . '</th>';
    echo '<td><textarea name="response" id="request-response">' . esc_textarea($request['response'])
        . '</textarea></td></tr>';

    echo '<tr><th scope="row">' . __('Private notes (only seen by administrators)', 'xtec-ms-manager') . '</th>';
    echo '<td><textarea name="priv_notes" id="request-priv_notes">' . esc_textarea($request['priv_notes'])
        . '</textarea></td></tr>';

    echo '</tbody>';
    echo '</table>';

    echo '<input type="hidden" name="update" value="1" />';
    wp_nonce_field('xmm-update-request_' . $request_id);
    echo '<input type="submit" value="' . esc_attr__('Save changes', 'xtec-ms-manager') . '" />';
    echo '</form>';
    echo '</div>';
}

/**
 * Shows a request. The administrators of the blogs can only see the requests of their blog, and only the
 * administrators of the network can see the private notes.
 *
 * @param int $request_id The ID of the request.
 */
function xmm_view_request(int $request_id): void
{
    $title = esc_html__('Request details', 'xtec-ms-manager');
    $is_network_admin = is_network_admin();
    $blog_id = get_current_blog_id();

    $request = xmm_get_request_details($request_id, $is_network_admin ? 0 : $blog_id);
    if ($request === null) {
        xmm_print_not_found($title, __('Request not found', 'xtec-ms-manager'));
        return;
    }

    [$color, $state_desc] = xmm_get_request_state((int)$request['state']);

    echo '<div class="wrap">';
    xmm_show_title($title);

    xmm_print_request_details($request, $is_network_admin);

    echo '<h2>' . __('Request state', 'xtec-ms-manager') . '</h2>';
    echo '<table class="form-table">';
    echo '<tbody>';

    echo '<tr><th scope="row">' . __('State', 'xtec-ms-manager') . '</th>';
    echo '<td bgcolor="' . esc_attr($color) . '">' . esc_html($state_desc) . '</td></tr>';

    if ($request['time_edition'] !== XMM_EMPTY_DATE) {
        echo '<tr><th scope="row">' . __('Latest change', 'xtec-ms-manager') . '</th>';
        echo '<td>' . xmm_format_date($request['time_edition']) . '</td></tr>';
    }

    echo '<tr><th scope="row">' . __('Response', 'xtec-ms-manager') . '</th>';
    echo '<td>' . esc_html($request['response']) . '</td></tr>';

    if ($is_network_admin) {
        echo '<tr><th scope="row">' . __('Private notes (only seen by administrators)', 'xtec-ms-manager') . '</th>';
        echo '<td>' . esc_html($request['priv_notes']) . '</td></tr>';
    }

    echo '</tbody>';
    echo '</table>';

    if ($is_network_admin) {
        $edit_url = remove_query_arg('view', add_query_arg(['request-id' => $request_id, 'edit' => 1]));
        echo '<a href="' . esc_url($edit_url) . '" class="button button-primary">'
            . __('Edit the request', 'xtec-ms-manager') . '</a>&nbsp;&nbsp;';
        $list_url = network_admin_url('admin.php?page=xmm-requests');
    } else {
        $list_url = get_admin_url($blog_id, 'tools.php?page=xmm-blog-requests');
    }

    echo '<a href="' . esc_url($list_url) . '" class="button button-primary">'
        . __('Back to the request list', 'xtec-ms-manager') . '</a>';
    echo '</div>';
}

/**
 * Shows the data of a request sent by the user.
 *
 * @param array $request The request.
 * @param bool $show_blog If the URL of the blog is shown.
 */
function xmm_print_request_details(array $request, bool $show_blog): void
{
    echo '<table class="form-table">';
    echo '<tbody>';

    echo '<tr><th scope="row">' . __('Request Type', 'xtec-ms-manager') . '</th>';
    echo '<td>' . esc_html($request['name']) . '</td></tr>';

    if ($show_blog) {
        echo '<tr><th scope="row">' . __('URL', 'xtec-ms-manager') . '</th>';
        echo '<td>' . xmm_get_blog_link((int)$request['blog_id']) . '</td></tr>';
    }

    echo '<tr><th scope="row">' . __('User', 'xtec-ms-manager') . '</th>';
    echo '<td>' . esc_html($request['display_name'] . ' (' . $request['user_login'] . ' - '
        . $request['user_email'] . ')') . '</td></tr>';

    echo '<tr><th scope="row">' . __('Registration Time', 'xtec-ms-manager') . '</th>';
    echo '<td>' . xmm_format_date($request['time_creation']) . '</td></tr>';

    echo '<tr><th scope="row">' . __('Comments', 'xtec-ms-manager') . '</th>';
    echo '<td>' . esc_html($request['comments']) . '</td></tr>';

    echo '</tbody>';
    echo '</table>';
    echo '<hr />';
}

/**
 * Returns a request with the name of its type. The requests are in the tables of the main blog.
 *
 * @param int $request_id The ID of the request.
 * @param int $blog_id The blog the request must belong to, or 0 if it can be of any blog.
 * @return array|null The request, or null if it doesn't exist.
 */
function xmm_get_request_details(int $request_id, int $blog_id = 0): ?array
{
    global $wpdb;

    $condition = $blog_id ? $wpdb->prepare(' AND r.blog_id = %d', $blog_id) : '';

    switch_to_blog(1);
    $request = $wpdb->get_row(
        $wpdb->prepare(
            'SELECT ' . XMM_REQUEST_COLUMNS . " FROM {$wpdb->prefix}requests r
            LEFT JOIN {$wpdb->prefix}request_types rt ON r.request_type_id = rt.id
            WHERE r.id = %d",
            $request_id
        ) . $condition,
        ARRAY_A
    );
    restore_current_blog();

    return $request;
}

/**
 * Returns the current page of a list, which is in the URL, the last page and the number of rows before the current
 * page.
 *
 * @param int $num_records The number of rows of the list.
 * @return array The current page, the last page and the offset.
 */
function xmm_get_pages(int $num_records): array
{
    $current_page = max(1, xmm_query_id('current_page'));
    $last_page = (int)ceil($num_records / XMM_ITEMS_PER_PAGE);

    return [$current_page, $last_page, ($current_page - 1) * XMM_ITEMS_PER_PAGE];
}

/**
 * Returns the link to the administration of a blog.
 *
 * @param int $blog_id The ID of the blog.
 * @return string The HTML of the link, or empty if the blog doesn't exist.
 */
function xmm_get_blog_link(int $blog_id): string
{
    $blog_details = get_blog_details($blog_id);
    if ($blog_details === false) {
        return '';
    }

    return '<a href="' . esc_url($blog_details->siteurl . '/wp-admin/') . '" target="_blank" title="'
        . esc_attr($blog_details->blogname) . '">' . esc_html($blog_details->siteurl) . '</a>';
}

/**
 * Formats a date of the database.
 *
 * @param string $datetime The date, in the format of the database.
 * @param string|null $empty_text The text shown if the date isn't set. By default, "Date not set" in italics.
 * @return string The HTML of the date.
 */
function xmm_format_date(string $datetime, ?string $empty_text = null): string
{
    $time = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);

    if ($datetime === XMM_EMPTY_DATE || $time === false) {
        return $empty_text ?? '<em>' . __('Date not set', 'xtec-ms-manager') . '</em>';
    }

    return $time->format('d-m-Y H:i');
}

/**
 * Returns the color and the description of a state of the requests.
 *
 * @param int $state_id The ID of the state.
 * @return array The color and the description.
 */
function xmm_get_request_state(int $state_id): array
{
    return match ($state_id) {
        XMM_STATE_PENDING => ['#fcf8e3', __('Pending', 'xtec-ms-manager')],
        XMM_STATE_DENIED => ['#f2dede', __('Denied', 'xtec-ms-manager')],
        XMM_STATE_ACCEPTED => ['#dff0d8', __('Accepted', 'xtec-ms-manager')],
        default => ['white', __('Request state not set', 'xtec-ms-manager')],
    };
}

/**
 * Returns the states of the requests.
 *
 * @return array The states, by their ID, with their color and their description.
 */
function xmm_get_request_states(): array
{
    $states = [];
    foreach ([XMM_STATE_PENDING, XMM_STATE_DENIED, XMM_STATE_ACCEPTED] as $state) {
        [$color, $desc] = xmm_get_request_state($state);
        $states[$state] = ['color' => $color, 'desc' => $desc];
    }

    return $states;
}

/**
 * Returns a request. The requests are in the tables of the main blog.
 *
 * @param int $id The ID of the request.
 * @return array|null The request, or null if it doesn't exist.
 */
function xmm_get_request(int $id): ?array
{
    global $wpdb;

    switch_to_blog(1);
    $request = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}requests WHERE id = %d", $id),
        ARRAY_A
    );
    restore_current_blog();

    return $request;
}
