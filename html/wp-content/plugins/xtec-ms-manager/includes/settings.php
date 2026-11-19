<?php

/**
 * Manages all the actions in the settings page
 */
function xmm_settings() {

    // Check if we have to save params
    $saved = false;
    if ( isset($_POST['xmm-quota-percentage']) && !empty($_POST['xmm-quota-percentage']) && isset($_POST['xmm-email-addresses'] )) {
        $xmm_quota_percentage = intval($_POST['xmm-quota-percentage']);
        $xmm_send_email = ( isset( $_POST['xmm-send-email'] )) ? 1 : 0;
        $xmm_email_addresses = sanitize_text_field( $_POST['xmm-email-addresses'] );
        update_site_option( 'xmm_quota_percentage', $xmm_quota_percentage);
        update_site_option( 'xmm_send_email', $xmm_send_email);
        update_site_option( 'xmm_email_addresses', $xmm_email_addresses);
        $saved = true;
    }

    // Get params
    $xmm_quota_percentage = get_site_option( 'xmm_quota_percentage' );
    $xmm_send_email = get_site_option( 'xmm_send_email' );
    $checked = ($xmm_send_email) ? 'checked="checked"' : '';
    $xmm_email_addresses = get_site_option( 'xmm_email_addresses' );

    echo '<div class="wrap">';

    if ( $saved ) {
        echo '<div class="notice notice-success"><p>' . __('Settings updated successfully', 'xmm') . '</p></div>';
    }

    show_title( __( 'Settings', 'xmm' ));
    show_subtitle( __( 'Requests settings', 'xmm' ) );

    echo '<form method="post">';
    echo '<table class="form-table">';
    echo '<tbody><tr>';

    echo '<th scope="row">' . __('Quota usage to allow ask for an extension', 'xmm') . '</th>';
    echo '<td><input type="text" name="xmm-quota-percentage" value="' . $xmm_quota_percentage . '" />%</td>';
    echo '</tr><tr>';

    echo '<th scope="row">' . __('Send e-mail messages when requests are created', 'xmm') . '</th>';
    echo '<td><input type="checkbox" name="xmm-send-email" ' . $checked . ' /></td>';
    echo '</tr><tr>';

    echo '<th scope="row">' . __('E-email addresses to notify new requests', 'xmm') . '</th>';
    echo '<td>
              <textarea name="xmm-email-addresses">' . $xmm_email_addresses . '</textarea>
              <p class="description">' . __('Comma separated values', 'xmm') . '</p>
          </td>';

    echo '</tr></tbody></table>';
    echo '<input type="submit" value="' . __('Save changes', 'xmm') . '" />';
    echo '</form></div>';

    return ;
}
