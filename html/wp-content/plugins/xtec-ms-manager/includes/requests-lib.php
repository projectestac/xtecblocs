<?php

// Pager
const ITEMS_PER_PAGE = 30;

function xmm_insert_request( $request_type_id, $request_comments ) {

    // Get the info to save
    $user = wp_get_current_user();
    $blog_id = get_current_blog_id();
    $request_type_id = intval( $request_type_id );
    $user_id = isset( $user->ID ) ? (int) $user->ID : 0;
    $user_login = $user->data->user_login;
    $display_name = $user->data->display_name;
    $user_email = $user->data->user_email;
    $comments = sanitize_text_field( $request_comments );

    global $wpdb;

    switch_to_blog(1);

    $result = $wpdb->insert(
        $wpdb->prefix . 'requests',
        array(
            'state' => 1,
            'blog_id' => $blog_id,
            'request_type_id' => $request_type_id,
            'user_id' => $user_id,
            'user_login' => $user_login,
            'display_name' => $display_name,
            'user_email' => $user_email,
            'time_creation' => date('Y-m-d H:i:s'),
            'time_edition' => '0000-00-00 00:00:00',
            'comments' => $comments
        ),
        array(
            '%d',
            '%d',
            '%d',
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s'
        )
    );

    restore_current_blog();

    // Log for the user
    xmm_log_to_user( $result, __( 'Request successfully created', 'xmm' ), __( 'Could not create the request', 'xmm' ));

    if ( $result !== false ) {
        return $wpdb->insert_id;
    } else {
        return false;
    }
}

function xmm_update_request( $request ) {

    // Get the info to save
    $id = intval( $request['id'] );
    $request_state = intval( $request['request-state'] );
    $response = sanitize_text_field( $request['response'] );
    $priv_notes = sanitize_text_field( $request['priv_notes'] );
    $time_edition = date('Y-m-d H:i:s');

    global $wpdb;

    $result = $wpdb->update(
        $wpdb->prefix . 'requests',
        array(
            'state' => $request_state,
            'response' => $response,
            'priv_notes' => $priv_notes,
            'time_edition' => $time_edition
        ),
        array( 'id' => $id ),
        array(
            '%d',
            '%s',
            '%s',
            '%s'
        ),
        array( '%d' )
    );

    // Log for the user
    xmm_log_to_user( $result, __( 'Request successfully updated', 'xmm' ), __( 'Could not update the request', 'xmm' ));

    return ;

}

function xmm_delete_request( $id ) {

    global $wpdb;

    $result = $wpdb->delete(
        $wpdb->prefix . 'requests',
        array(
            'id' => $id,
        )
    );

    // Log for the user
    xmm_log_to_user( $result, __( 'Request successfully deleted', 'xmm' ), __( 'Could not delete the request', 'xmm' ));

    return ;
}

function xmm_list_requests() {

    echo '<div class="wrap">';
    show_title( __( 'Requests', 'xmm' ) );

    global $wpdb;

    // Pager
    $current_page = isset( $_GET['current_page'] ) ? intval( $_GET['current_page'] ) : 1;
    $offset = ( $current_page - 1 ) * ITEMS_PER_PAGE;
    $num_records = $wpdb->get_var( "SELECT count(r.id) FROM $wpdb->prefix" . "requests r" );
    $last_page = intval( ceil( $num_records / ITEMS_PER_PAGE ));

    // Get all the requests
    $requests = $wpdb->get_results("
        SELECT r.id, r.state, r.blog_id, r.request_type_id, r.user_id, r.user_login, r.display_name, r.user_email, r.time_creation, r.time_edition, r.comments, rt.name  
        FROM $wpdb->prefix" . "requests r
        LEFT JOIN $wpdb->prefix" . "request_types rt ON r.request_type_id = rt.id
        ORDER BY r.state, r.time_edition ASC
        LIMIT " . ITEMS_PER_PAGE . " OFFSET $offset
        ", ARRAY_A );

    xmm_print_pager( $current_page, $last_page );

    echo '
        <table cellspacing="3" cellpadding="3" width="100%" class="widefat">
            <thead>
                <tr>
                    <th scope="col">' . __('ID', 'xmm') .' </th>
                    <th scope="col">' . __('Blog URL', 'xmm') . '</th>
                    <th scope="col">' . __('State', 'xmm') . '</th>
                    <th scope="col">' . __('Request Type', 'xmm') . '</th>
                    <th scope="col">' . __('Quota usage', 'xmm') . '</th>
                    <th scope="col">' . __('User', 'xmm') . '</th>
                    <th scope="col">' . __('Request date', 'xmm') . '</th>
                    <th scope="col">' . __('Edition date', 'xmm') . '</th>
                    <th scope="col">' . __('Actions', 'xmm') . '</th>
                </tr>
            </thead>

            <tbody>
        ';

    foreach ($requests as $request) {

        // Get the quota usage
        switch_to_blog($request['blog_id']);
        $quota = get_space_allowed();
        $used = get_space_used();
        $blog_usage = round(($used / $quota) * 100, 2);
        restore_current_blog();

        // Get blog information
        $blog_details = get_blog_details($request['blog_id']);

        // Set background color and state description
        list($color, $state_desc) = xmm_get_request_state($request['state']);

        // Print the row
        echo '<tr bgcolor="' . esc_attr( $color ) . '">';
        echo '<td>' . intval( $request['id'] ) . '</td>';
        echo '<td><a href="' . esc_url( $blog_details->siteurl . '/wp-admin/' ) . '" target="_blank" title="' . esc_attr( $blog_details->blogname ) . '">' . esc_html( $blog_details->siteurl ) . '</a></td>';
        echo '<td>' . esc_html( $state_desc ) . '</td>';
        echo '<td>' . esc_html( $request['name'] ) . '</td>';
        echo '<td>' . esc_html( $blog_usage ) . ' %</td>';
        echo '<td><span title="' . esc_attr( $request['display_name'] ) . '">' . esc_html( $request['user_login'] ) . '</span></td>';

        // Process datetime
        if ($request['time_creation'] != '0000-00-00 00:00:00') {
            $time = DateTime::createFromFormat("Y-m-d H:i:s", $request['time_creation']);
            echo '<td>' . $time->format("d-m-Y H:i") . '</td>';
        } else {
            echo '<td><em>' . __('Date not set', 'xmm') . '</em></td>';
        }
        if ($request['time_edition'] != '0000-00-00 00:00:00') {
            $time = DateTime::createFromFormat("Y-m-d H:i:s", $request['time_edition']);
            echo '<td>' . $time->format("d-m-Y H:i") . '</td>';
        } else {
            echo '<td><em>' . __('Date not set', 'xmm') . '</em></td>';
        }

        // Actions
        echo '<td>
                  <a href="' . esc_url( add_query_arg( array( 'view' => 1, 'request-id' => $request['id'] ))) . '">
                      <span class="dashicons dashicons-info"></span>
                  </a>
                  <a id="xmm-edit-link-' . $request['id'] . '" href="' . esc_url( remove_query_arg( array( 'insert', 'delete', 'new', 'update' ), add_query_arg( array( 'request-id' => $request['id'], 'edit' => 1 )))) . '">
                      <span class="dashicons dashicons-edit"></span>
                  </a>
                  <a id="xmm-del-link-' . $request['id'] . '" href="' . esc_url( remove_query_arg( array( 'insert', 'delete', 'new', 'update' ), add_query_arg( array( 'request-id' => $request['id'], '_wpnonce' => wp_create_nonce( 'xmm-delete-request_' . $request['id'] ) )))) . '" onclick="confirm_deletion(' . intval( $request['id'] ) . ')">
                      <span class="dashicons dashicons-dismiss"></span>
                  </a>
              </td>';
        echo '</tr>';
    }

    echo '
            </tbody>
        </table>
        ';

    xmm_print_pager( $current_page, $last_page );

    return ;
}

function xmm_list_blog_requests() {

    // Show the header
    echo '<div class="wrap">';
    $header = __( 'Requests', 'xmm' )
        . '<a class="page-title-action" href="' . esc_url( add_query_arg( 'new', 1 ) ) . '">'
        . __('New request', 'xmm')
        . '</a>';
    show_title( $header );

    global $wpdb;

    $blog_id = get_current_blog_id();

    switch_to_blog(1);

    // Pager
    $current_page = isset( $_GET['current_page'] ) ? intval( $_GET['current_page'] ) : 1;
    $offset = ( $current_page - 1 ) * ITEMS_PER_PAGE;
    $num_records = $wpdb->get_var( "SELECT count(r.id) FROM $wpdb->prefix" . "requests r WHERE r.blog_id = $blog_id" );
    $last_page = intval( ceil( $num_records / ITEMS_PER_PAGE ));

    $requests = $wpdb->get_results("
        SELECT r.id, r.state, r.blog_id, r.user_id, r.user_login, r.display_name, r.user_email, r.time_creation, r.time_edition, r.comments, r.response, r.priv_notes, rt.name  
        FROM {$wpdb->prefix}requests r
        LEFT JOIN {$wpdb->prefix}request_types rt ON r.request_type_id = rt.id
        WHERE r.blog_id = $blog_id
        ORDER BY r.state, r.time_edition DESC
        LIMIT " . ITEMS_PER_PAGE . " OFFSET $offset
      ", ARRAY_A );

    restore_current_blog();

    xmm_print_pager( $current_page, $last_page );

    echo '
    <table cellspacing="3" cellpadding="3" width="100%" class="widefat">
        <thead>
            <tr>
                <th scope="col">' . __('ID', 'xmm') .' </th>
                <th scope="col">' . __('State', 'xmm') . '</th>
                <th scope="col">' . __('Request Type', 'xmm') . '</th>
                <th scope="col">' . __('User', 'xmm') . '</th>
                <th scope="col">' . __('Request date', 'xmm') . '</th>
                <th scope="col">' . __('Edition date', 'xmm') . '</th>
                <th scope="col">' . __('Actions', 'xmm') . '</th>
            </tr>
        </thead>

        <tbody>
    ';

    foreach ($requests as $request) {

        // Set background color and state description
        list($color, $state_desc) = xmm_get_request_state($request['state']);

        echo '<tr bgcolor="' . esc_attr( $color ) . '">';
        echo '<td>' . intval( $request['id'] ) . '</td>';
        echo '<td>' . esc_html( $state_desc ) . '</td>';
        echo '<td>' . esc_html( $request['name'] ) . '</td>';
        echo '<td><span title="' . esc_attr( $request['display_name'] ) . '">' . esc_html( $request['user_login'] ) . '</span></td>';

        // Process datetime
        if ($request['time_creation'] != '0000-00-00 00:00:00') {
            $time = DateTime::createFromFormat("Y-m-d H:i:s", $request['time_creation']);
            echo '<td>' . $time->format("d-m-Y H:i") . '</td>';
        } else {
            echo '<td><em>' . __('Date not set', 'xmm') . '</em></td>';
        }
        if ($request['time_edition'] != '0000-00-00 00:00:00') {
            $time = DateTime::createFromFormat("Y-m-d H:i:s", $request['time_edition']);
            echo '<td>' . $time->format("d-m-Y H:i") . '</td>';
        } else {
            echo '<td><em>' . __('Date not set', 'xmm') . '</em></td>';
        }

        // Actions
        echo '<td>
                  <a href="' . esc_url( add_query_arg( array( 'view' => 1, 'request-id' => $request['id'] ))) . '">
                      <span class="dashicons dashicons-info"></span>
                  </a>
              </td>';
        echo '</tr>';
    }

    echo '
        </tbody>
    </table>
    ';

    xmm_print_pager( $current_page, $last_page );

    echo '</div>';

    return ;
}

function xmm_new_request() {

    global $wpdb;

    // Get the list of request types from the table in the main database
    switch_to_blog(1);
    $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}request_types WHERE state=" . STATE_ACTIVE, ARRAY_A );
    restore_current_blog();

    echo '<div class="wrap">';
    show_title( __( 'New Request', 'xmm' ) );
    echo '<form action="' . esc_url( add_query_arg( 'page', 'xmm-blog-requests', 'tools.php' ) ) . '" method="post">';

    // Request type choice
    if (empty($results)) {
        echo '<div class="info">' . __('There are no available request types', 'xmm') . '</div>';
    } else {
        // Save info in javascript var for later ajax update
        echo '<script> var dataResults = ' . wp_json_encode( $results, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . '; </script>';

        // Build select dropdown menu for request type
        echo '<div id="xmm-select-request">';
        echo '<span id="xmm-select-request-text">' . __( 'Select a request type', 'xmm') . '</span>';
        echo '<select id="select-request" name="select-request">';
        echo '<option value="0" selected></option>';
        foreach ($results as $result) {
            echo '<option value="' . intval( $result['id'] ) . '">' . esc_html( $result['name'] ) . '</option>';
        }
        echo '</select>';
        echo '</div>';

        // The rest of the form. Initially hidden
        echo '
        <div id="form-details">
            <div id="request-info"></div>
            <div id="comments-text"></div>
            <textarea id="request-comments" name="request-comments"></textarea>
            <input type="submit" value="' . __( 'Submit request', 'xmm' ) . '" />
        </div>
        <input type="hidden" name="insert" value="1" />
        ' . wp_nonce_field( 'xmm-insert-request', '_wpnonce', true, false );

        echo '</form></div>';
    }

    return ;

}

function xmm_edit_request( $request_id ) {

    global $wpdb;

    // Get the request info
    $request = $wpdb->get_results( $wpdb->prepare( "
        SELECT r.id, r.state, r.blog_id, r.user_id, r.user_login, r.display_name, r.user_email, r.time_creation, r.time_edition, r.comments, r.response, r.priv_notes, rt.name  
        FROM {$wpdb->prefix}requests r
        LEFT JOIN {$wpdb->prefix}request_types rt ON r.request_type_id = rt.id
        WHERE r.id = %d
        ", $request_id ), ARRAY_A );

    // If the request is not found or if there are many results, show an error and exit
    if (count($request) == 1) {
        $request = array_pop($request);
    } else {
        echo '<div class="wrap">';
        echo '<h1>' . __( 'Request details', 'xmm' ) . '</h1>';
        echo '<div class="info">';
        _e('Request not found', 'xmm');
        echo '</div></div>';

        return ;
    }

    // Show the request details
    echo '<div class="wrap">';
    echo '<h1>' . __( 'Request details', 'xmm' ) . '</h1>';
    echo '<form action="' . esc_url( remove_query_arg( 'edit' ) ) . '" method="post">';
    echo '<table class="form-table">';
    echo '<tbody><tr>';

    echo '<th scope="row">' . __('Request Type', 'xmm') . '</th>';
    echo '<td>' . esc_html( $request['name'] ) . '</td>';
    echo '</tr><tr>';

    $blog_details = get_blog_details($request['blog_id']);
    echo '<th scope="row">' . __('URL', 'xmm') . '</th>';
    echo '<td><a href="' . esc_url( $blog_details->siteurl . '/wp-admin/' ) . '" target="_blank" title="' . esc_attr( $blog_details->blogname ) . '">' . esc_html( $blog_details->siteurl ) . '</a></td>';
    echo '</tr><tr>';

    echo '<th scope="row">' . __('User', 'xmm') . '</th>';
    echo '<td>' . esc_html( $request['display_name'] . ' (' . $request['user_login'] . ' - ' . $request['user_email'] . ')' ) . '</td>';
    echo '</tr><tr>';

    echo '<th scope="row">' . __('Registration Time', 'xmm') . '</th>';
    if ($request['time_creation'] != '0000-00-00 00:00:00') {
        $time = DateTime::createFromFormat("Y-m-d H:i:s", $request['time_creation']);
        echo '<td>' . $time->format("d-m-Y H:i") . '</td>';
    } else {
        echo '<td><em>' . __('Date not set', 'xmm') . '</em></td>';
    }
    echo '</tr><tr>';

    echo '<th scope="row">' . __('Comments', 'xmm') . '</th>';
    echo '<td>' . esc_html( $request['comments'] ) . '</td>';
    echo '</tr><tr>';

    echo '</tr></tbody></table><hr />';

    // Response to the user
    echo '<h2>' . __( 'Request state', 'xmm' ) . '</h2>';
    echo '<table class="form-table">';
    echo '<tbody><tr>';

    $states = xmm_get_request_states();
    echo '<th scope="row">' . __('State', 'xmm') . '</th>';
    echo '<td><select name="request-state">';
    echo '<option id="0"></option>';
    foreach ($states as $key => $state) {
        $selected = ($request['state'] == $key) ? ' selected="selected"' : '';
        echo '<option id="' . intval( $key ) . '" value="' . intval( $key ) . '"' . $selected . '>' . esc_html( $state['desc'] ) . '</option>';
    }
    echo '</select></td>';
    echo '</tr><tr>';

    echo '<th scope="row">' . __('Latest change', 'xmm') . '</th>';
    if ($request['time_edition'] != '0000-00-00 00:00:00') {
        $time = DateTime::createFromFormat("Y-m-d H:i:s", $request['time_edition']);
        echo '<td>' . $time->format("d-m-Y H:i") . '</td>';
    } else {
        echo '<td>' . __('Not edited yet', 'xmm') . '</td>';
    }
    echo '</tr><tr>';

    echo '<th scope="row">' . __('Response', 'xmm') . '</th>';
    echo '<td><textarea name="response" id="request-response">' . esc_textarea( $request['response'] ) . '</textarea></td>';

    echo '</tr><tr>';
    echo '<th scope="row">' . __('Private notes (only seen by administrators)', 'xmm') . '</th>';
    echo '<td><textarea name="priv_notes" id="request-priv_notes">' . esc_textarea( $request['priv_notes'] ) . '</textarea></td>';

    echo '</tr></tbody></table>';

    echo '<input type="hidden" name="update" value="1" />';
    wp_nonce_field( 'xmm-update-request_' . $request['id'] );
    echo '<input type="submit" value="' . __('Save changes', 'xmm') . '" />';
    echo '</form>';
    echo '</div>';

    return ;

}

function xmm_view_request( $request_id ) {

    global $wpdb;

    $is_network_admin = is_network_admin();

    // Blog admins can only see the requests of their blog
    $additional_condition = '';
    $values = array( $request_id );
    if (!$is_network_admin) {
        $blog_id = get_current_blog_id();
        $additional_condition = 'AND r.blog_id = %d';
        $values[] = $blog_id;
    }

    switch_to_blog(1);

    // Get the request info
    $request = $wpdb->get_results( $wpdb->prepare( "
        SELECT r.id, r.state, r.blog_id, r.user_id, r.user_login, r.display_name, r.user_email, r.time_creation, r.time_edition, r.comments, r.response, r.priv_notes, rt.name  
        FROM {$wpdb->prefix}requests r
        LEFT JOIN {$wpdb->prefix}request_types rt ON r.request_type_id = rt.id
        WHERE r.id = %d $additional_condition
        ORDER BY r.state, r.time_edition
        ", $values ), ARRAY_A );

    restore_current_blog();

    // If the request is not found or if there are many results, show an error and exit
    if (count($request) == 1) {
        $request = array_pop($request);
    } else {
        echo '<div class="wrap">';
        echo '<h1>' . __( 'Request details', 'xmm' ) . '</h1>';
        echo '<div class="info">';
        _e('Request not found', 'xmm');
        echo '</div></div>';

        return ;
    }

    list($color, $state_desc) = xmm_get_request_state($request['state']);

    // Show the request details
    echo '<div class="wrap">';
    show_title( __( 'Request details', 'xmm' ));
    echo '<table class="form-table">';
    echo '<tbody><tr>';

    echo '<th scope="row">' . __('Request Type', 'xmm') . '</th>';
    echo '<td>' . esc_html( $request['name'] ) . '</td>';
    echo '</tr><tr>';

    if ($is_network_admin) {
        $blog_details = get_blog_details($request['blog_id']);
        echo '<th scope="row">' . __('URL', 'xmm') . '</th>';
        echo '<td><a href="' . esc_url( $blog_details->siteurl . '/wp-admin/' ) . '" target="_blank" title="' . esc_attr( $blog_details->blogname ) . '">' . esc_html( $blog_details->siteurl ) . '</a></td>';
        echo '</tr><tr>';
    }

    echo '<th scope="row">' . __('User', 'xmm') . '</th>';
    echo '<td>' . esc_html( $request['display_name'] . ' (' . $request['user_login'] . ' - ' . $request['user_email'] . ')' ) . '</td>';
    echo '</tr><tr>';

    echo '<th scope="row">' . __('Registration Time', 'xmm') . '</th>';
    if ( $request[ 'time_creation' ] != '0000-00-00 00:00:00' ) {
        $time = DateTime::createFromFormat( "Y-m-d H:i:s", $request[ 'time_creation' ]);
        echo '<td>' . $time->format("d-m-Y H:i") . '</td>';
    } else {
        echo '<td><em>' . __('Date not set', 'xmm') . '</em></td>';
    }
    echo '</tr><tr>';

    echo '<th scope="row">' . __('Comments', 'xmm') . '</th>';
    echo '<td>' . esc_html( $request['comments'] ) . '</td>';
    echo '</tr><tr>';

    echo '</tr></tbody></table><hr />';

    // Response to the user
    echo '<h2>' . __( 'Request state', 'xmm' ) . '</h2>';
    echo '<table class="form-table">';
    echo '<tbody><tr>';

    echo '<th scope="row">' . __('State', 'xmm') . '</th>';
    echo '<td bgcolor="' . esc_attr( $color ) . '">' . esc_html( $state_desc ) . '</td>';
    echo '</tr><tr>';

    if ($request['time_edition'] != '0000-00-00 00:00:00') {
        $time = DateTime::createFromFormat("Y-m-d H:i:s", $request['time_edition']);
        echo '<th scope="row">' . __('Latest change', 'xmm') . '</th>';
        echo '<td>' . $time->format("d-m-Y H:i") . '</td>';
        echo '</tr><tr>';
    }

    echo '<th scope="row">' . __('Response', 'xmm') . '</th>';
    echo '<td>' . esc_html( $request['response'] ) . '</td>';

    if ($is_network_admin) {
        echo '</tr><tr>';
        echo '<th scope="row">' . __('Private notes (only seen by administrators)', 'xmm') . '</th>';
        echo '<td>' . esc_html( $request['priv_notes'] ) . '</td>';
    }

    echo '</tr></tbody></table>';

    if ($is_network_admin) {
        echo '<a href="' . esc_url( remove_query_arg( 'view', add_query_arg( array( 'request-id' => $request['id'], 'edit' => 1 )))) . '" class="button button-primary">' . __('Edit the request', 'xmm') .'</a>';
        echo '&nbsp;&nbsp;';
    }

    $url = ($is_network_admin) ? network_admin_url('admin.php') . '?page=xmm-requests' : get_admin_url( $blog_id) . 'tools.php?page=xmm-blog-requests';

    echo '<a href="' . esc_url( $url ) . '" class="button button-primary">' . __('Back to the request list', 'xmm') .'</a>';
    echo '</div>';

    return ;
}

function xmm_get_request_state( $state_id ) {

    $color = 'white';
    $desc = __('Request state not set', 'xmm');

    switch( $state_id ) {

        case STATE_PENDING:
            $color = '#fcf8e3'; // Yellow
            $desc = __('Pending', 'xmm');
            break;

        case STATE_DENIED:
            $color = '#f2dede'; // Red
            $desc = __('Denied', 'xmm');
            break;

        case STATE_ACCEPTED:
            $color = '#dff0d8'; // Green
            $desc = __('Accepted', 'xmm');
            break;
    }

    return array ($color, $desc);
}

function xmm_get_request_states() {

    $states_list = array(STATE_PENDING, STATE_DENIED, STATE_ACCEPTED);

    $states = array();
    foreach ($states_list as $state){
        list ($color, $desc) = xmm_get_request_state($state);
        $states[$state] = array (
            'color' => $color,
            'desc' => $desc
        );
    }

    return $states;
}

function xmm_get_request( $id ) {

    global $wpdb;

    switch_to_blog( 1 );

    $request = $wpdb->get_row( $wpdb->prepare( "
        SELECT *  
        FROM $wpdb->prefix" . "requests r
        WHERE r.id = %d
        ", $id ),
        ARRAY_A,
        0);

    restore_current_blog();

    return $request;
}
