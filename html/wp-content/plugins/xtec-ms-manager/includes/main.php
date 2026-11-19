<?php

function xmm_main() {
    echo '<div class="wrap">';
    show_title( __( 'XTEC Multisite Manager', 'xmm' ) );
    show_subtitle( '<a href="' . network_admin_url( 'admin.php?page=xmm-sql' ) . '">' . __( 'SQL execution', 'xmm' ) . '</a>' );
    show_subtitle( '<a href="' . network_admin_url( 'admin.php?page=xmm-requests' ) . '">' . __( 'Requests', 'xmm' ) . '</a>' );
    show_subtitle( '<a href="' . network_admin_url( 'admin.php?page=xmm-request-types' ) . '">' . __( 'Request Types', 'xmm' ) . '</a>' );
    show_subtitle( '<a href="' . network_admin_url( 'admin.php?page=xmm-settings' ) . '">' . __( 'Settings', 'xmm' ) . '</a>' );
    echo '</div>';
}
