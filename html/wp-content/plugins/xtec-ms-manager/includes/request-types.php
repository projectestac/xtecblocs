<?php

require_once 'request-types-lib.php';

// Load javascript
wp_register_script('request-js', plugins_url() . '/xtec-ms-manager/javascript/requests.js', array('jquery'), '1.1', true);
wp_enqueue_script('request-js');

function xmm_request_types() {
    if ( ! current_user_can( 'manage_network_options' ) ) {
        wp_die( esc_html__( 'Sorry, you are not allowed to access this page.' ), '', [ 'response' => 403 ] );
    }

    // Remove automatic added quotes
    $_POST = array_map('stripslashes', $_POST);

    // Check for insert request
    if (isset($_POST['request-type-name']) && !empty($_POST['request-type-name']) && isset($_POST['request-type-state'])
        && isset($_POST['description']) && !empty($_POST['description']) && isset($_GET['insert']) && ($_GET['insert'] == 1)) {
        check_admin_referer( 'xmm-insert-request-type' );
        xmm_insert_request_type($_POST['request-type-name'], $_POST['request-type-state'], $_POST['description'], $_POST['comments_text']);
    }

    // Check for new request (Show form)
    if (isset($_GET['new']) && ($_GET['new'] == 1)) {
        xmm_new_request_type();
        return;
    }

    // Check for edit request (show the form)
    if (isset($_GET['type-id']) && !empty($_GET['type-id']) && isset($_GET['edit']) && ($_GET['edit'] == 1)) {
        xmm_edit_request_type( intval( $_GET['type-id'] ) );
        return;
    }

    // Check for update request
    if (isset($_GET['type-id']) && !empty($_GET['type-id']) && isset($_POST['request-type-state']) && isset($_POST['update']) && ($_POST['update'] == 1)) {
        check_admin_referer( 'xmm-update-request-type_' . intval( $_GET['type-id'] ) );
        $request_type = array(
            'id' => intval( $_GET['type-id'] ),
            'state' => $_POST['request-type-state'],
            'name' => $_POST['request-type-name'],
            'description' => $_POST['description'],
            'comments_text' => $_POST['comments_text']
        );

        xmm_update_request_type($request_type);
    }

    // Check for delete request
    if (isset($_GET['type-id']) && !empty($_GET['type-id']) && isset($_GET['delete']) && ($_GET['delete'] == 1)) {
        check_admin_referer( 'xmm-delete-request-type_' . intval( $_GET['type-id'] ) );
        xmm_delete_request_type( intval( $_GET['type-id'] ) );
    }

    // Show the list of request types
    xmm_list_request_types();

    return ;
}