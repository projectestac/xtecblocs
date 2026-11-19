<?php

function xmm_insert_request_type( $name, $state, $description, $comments_text ) {

    // Get the info to save
    $name = sanitize_text_field( $name );
    $state = intval( $state );
    $description = sanitize_text_field( $description );
    $comments_text = sanitize_text_field( $comments_text );

    global $wpdb;

    $result = $wpdb->insert(
        $wpdb->prefix . 'request_types',
        array(
            'name' => $name,
            'state' => $state,
            'description' => $description,
            'comments_text' => $comments_text
        ),
        array(
            '%s',
            '%d',
            '%s',
            '%s'
        )
    );

    // Log for the user
    xmm_log_to_user( $result, __( 'Request type successfully created', 'xmm' ), __( 'Could not create the request type', 'xmm' ));

    return ;

}

function xmm_update_request_type( $request_type ) {

    // Get the info to save
    $id = intval( $request_type['id'] );
    $state = intval( $request_type['state'] );
    $name = sanitize_text_field( $request_type['name'] );
    $description = sanitize_text_field( $request_type['description'] );
    $comments_text = sanitize_text_field( $request_type['comments_text'] );

    global $wpdb;

    $result = $wpdb->update(
        $wpdb->prefix . 'request_types',
        array(
            'name' => $name,
            'state' => $state,
            'description' => $description,
            'comments_text' => $comments_text
        ),
        array( 'id' => $id ),
        array(
            '%s',
            '%d',
            '%s',
            '%s'
        ),
        array( '%d' )
    );

    // Log for the user
    xmm_log_to_user( $result, __( 'Request type successfully updated', 'xmm' ), __( 'Could not update the request type', 'xmm' ));

    return ;

}

function xmm_delete_request_type( $id ) {

    global $wpdb;

    $result = $wpdb->delete(
        $wpdb->prefix . 'request_types',
        array(
            'id' => $id
        )
    );

    // Log for the user
    xmm_log_to_user( $result, __( 'Request type successfully deleted', 'xmm' ), __( 'Could not delete the request type', 'xmm' ));

    return ;

}

function xmm_list_request_types() {

    echo '<div class="wrap">';
    $header = __( 'Request Types', 'xmm' )
        . '<a class="page-title-action" href="' . remove_query_arg( array( 'type-id', 'insert', 'edit', 'delete', 'update' ), add_query_arg( 'new', 1 )) . '">'
        . __('New request type', 'xmm')
        . '</a>';
    show_title( $header );

    global $wpdb;

    $request_types = $wpdb->get_results("
        SELECT *  
        FROM $wpdb->prefix" . "request_types rt
        ORDER BY rt.id
        ", ARRAY_A );

    echo '
        <table cellspacing="3" cellpadding="3" width="100%" class="widefat">
            <thead>
                <tr>
                    <th scope="col">' . __('ID', 'xmm') .' </th>
                    <th scope="col">' . __('Name', 'xmm') . '</th>
                    <th scope="col">' . __('State', 'xmm') . '</th>
                    <th scope="col">' . __('Description', 'xmm') . '</th>
                    <th scope="col">' . __('Title for comments', 'xmm') . '</th>
                    <th scope="col">' . __('Actions', 'xmm') . '</th>
                </tr>
            </thead>

            <tbody>
        ';

    foreach ($request_types as $type) {

        // Set background color and state description
        list($color, $state_desc) = xmm_get_request_type_state($type['state']);

        // Print the row
        echo "<tr bgcolor=\"$color\">";
        echo "<td>$type[id]</td>";
        echo "<td>$type[name]</td>";
        echo "<td>$state_desc</td>";
        echo "<td>$type[description]</td>";
        echo "<td>$type[comments_text]</td>";

        // Actions
        echo '<td>
                  <a id="xmm-edit-link-' . $type['id'] . '" 
                     href="' . remove_query_arg( array( 'insert', 'delete', 'new', 'update' ), add_query_arg( array( 'type-id' => $type['id'], 'edit' => 1 ))) . '")">
                      <span class="dashicons dashicons-edit"></span>
                  </a>
                  <a id="xmm-del-link-' . $type['id'] . '" 
                     href="' . remove_query_arg( array( 'insert', 'delete', 'new', 'update' ), add_query_arg( array( 'type-id' => $type['id'] ))) . '" onclick="confirm_deletion(' . $type['id'] . ')">
                      <span class="dashicons dashicons-dismiss"></span>
                  </a>
              </td>';
        echo '</tr>';
    }

    echo '
            </tbody>
        </table>
        ';

    return ;

}

function xmm_new_request_type() {

    echo '<div class="wrap">';
    show_title( __( 'New Request Type', 'xmm' ) );
    echo '<form action="' . remove_query_arg( 'new', add_query_arg( 'insert', 1 )) . '" method="post">';

    echo '<table class="form-table">';
    echo '<tbody><tr>';

    echo '<th scope="row">' . __('Request type name', 'xmm') . '</th>';
    echo "<td><input type=\"text\" name=\"request-type-name\" /></td>";
    echo '</tr><tr>';

    $states = xmm_get_request_type_states();
    echo '<th scope="row">' . __('State', 'xmm') . '</th>';
    echo '<td><select name="request-type-state">';
    echo '<option id="0"></option>';
    foreach ($states as $key => $state) {
        echo '<option id="' . $key . '" value="' . $key . '">' . $state['desc'] . '</option>';
    }
    echo '</select></td>';
    echo '</tr><tr>';

    echo '<th scope="row">' . __('Description', 'xmm') . '</th>';
    echo "<td><textarea name=\"description\"></textarea></td>";

    echo '</tr><tr>';
    echo '<th scope="row">' . __('Text for optional comments field', 'xmm') . '</th>';
    echo "<td><textarea name=\"comments_text\"></textarea></td>";

    echo '</tr></tbody></table>';

    echo '<input type="submit" value="' . __('Save changes', 'xmm') . '" />';
    echo '</form>';
    echo '</div>';

    return ;

}

function xmm_edit_request_type( $type_id ) {

    global $wpdb;

    // Get the request type info
    $request_type = $wpdb->get_results("
        SELECT *  
        FROM $wpdb->prefix" . "request_types rt
        WHERE rt.id = $type_id
        ", ARRAY_A );

    // If the request type is not found or if there are many results, show an error and exit
    if (count($request_type) == 1) {
        $request_type = array_pop($request_type);
    } else {
        echo '<div class="wrap">';
        show_title( __( 'Request type edition', 'xmm' ));
        echo '<div class="info">';
        _e('Request type not found', 'xmm');
        echo '</div></div>';

        return ;
    }

    // Show the request details
    echo '<div class="wrap">';
    show_title( __( 'Request type edition', 'xmm' ));
    echo '<form action="' . remove_query_arg('edit') . '" method="post">';
    echo '<table class="form-table">';
    echo '<tbody><tr>';

    echo '<th scope="row">' . __('Request type name', 'xmm') . '</th>';
    echo "<td><input type=\"text\" name=\"request-type-name\" value=\"$request_type[name]\" /></td>";
    echo '</tr><tr>';

    $states = xmm_get_request_type_states();
    echo '<th scope="row">' . __('State', 'xmm') . '</th>';
    echo '<td><select name="request-type-state">';
    echo '<option id="0"></option>';
    foreach ($states as $key => $state) {
        $selected = ($request_type['state'] == $key) ? ' selected="selected"' : '';
        echo '<option id="' . $key . '" value="' . $key . '"' . $selected . '>' . $state['desc'] . '</option>';
    }
    echo '</select></td>';
    echo '</tr><tr>';

    echo '<th scope="row">' . __('Description', 'xmm') . '</th>';
    echo "<td><textarea name=\"description\">$request_type[description]</textarea></td>";

    echo '</tr><tr>';
    echo '<th scope="row">' . __('Text for optional comments field', 'xmm') . '</th>';
    echo "<td><textarea name=\"comments_text\">$request_type[comments_text]</textarea></td>";

    echo '</tr></tbody></table>';

    echo '<input type="hidden" name="update" value="1" />';
    echo '<input type="submit" value="' . __('Save changes', 'xmm') . '" />';
    echo '</form>';
    echo '</div>';

    return ;

}

function xmm_get_request_type_state( $state_id ) {

    $color = 'white';
    $desc = __('Request type state not set', 'xmm');

    switch($state_id) {
        case STATE_ACTIVE:
            $color = '#dff0d8';
            $desc = __('Active', 'xmm');
            break;
        case STATE_INACTIVE:
            $color = '#f2dede';
            $desc = __('Inactive', 'xmm');
            break;
    }

    return array ($color, $desc);
}

function xmm_get_request_type_states() {

    $states_list = array(STATE_ACTIVE, STATE_INACTIVE);

    $states = array();
    foreach ($states_list as $state){
        list ($color, $desc) = xmm_get_request_type_state($state);
        $states[$state] = array (
            'color' => $color,
            'desc' => $desc
        );
    }

    return $states;
}

function xmm_get_request_type( $id ) {

    global $wpdb;

    switch_to_blog( 1 );

    $request_type = $wpdb->get_row("
        SELECT *  
        FROM $wpdb->prefix" . "request_types rt
        WHERE rt.id = $id
        ",
        ARRAY_A,
        0);

    restore_current_blog();

    return $request_type;
}
