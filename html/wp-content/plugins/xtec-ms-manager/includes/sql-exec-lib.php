<?php

/**
 * Get parameter used in the process and keep it in the data of the SQL execution of the user
 *
 * @param mixed $param
 * @return mixed
 */
function get_xmm_param( $param ) {

    $value = isset( $_POST[ $param ] ) ? stripslashes_deep( trim( $_POST[ $param ] ) ) : '';

    $data = xmm_get_sql_data();
    if ( empty( $value ) && ! empty( $data[ $param ] ) ) {
        $value = $data[ $param ];
    } else {
        $data[ $param ] = $value;
        xmm_set_sql_data( $data );
    }

    return $value;
}

/**
 * Substitute template values in SQL by it's actual values
 *
 * @param string $sql
 * @return mixed
 */
function render_sql( $sql ) {
    global $wpdb;

    $render_templates = array(
        '[prefix]'      => $wpdb->prefix,
        '[base_prefix]' => $wpdb->base_prefix
    );

    $sql_rendered = $sql;

    foreach ( $render_templates as $search => $replace ) {
        $sql_rendered = str_replace( $search, $replace, $sql_rendered );
    }

    return $sql_rendered;
}

/**
 * Convert ID ranges in a list of ID's
 *
 * @param string $xmm_blogid_list
 * @return string
 */
function xmm_expand_blogid_list($xmm_blogid_list)
{
    // Remove all white spaces
    $clean_list = preg_replace('/\s+/', '', $xmm_blogid_list);

    // Expand ranges. Ex: convert 1-5 in 1,2,3,4,5
    $result = preg_replace_callback(
        '/(\d+)-(\d+)/',
        function ($value) {
            return implode(',', range($value[1], $value[2]));
        },
        $clean_list
    );

    return $result;
}

/**
 * Get the list of all the sites, include only the blog_id and the blog path to save memory
 *
 * @return array
 */
function xmm_get_sites() {
    $selected_sites = array();

    // Deactivate large network restriction, which affects get_sites()
    add_filter( 'wp_is_large_network', '__return_false' );
    $sites = get_sites(array('number' => XMM_MAX_BLOG_NUMBER)); // 'Number' is the maximum number of blog to be returned. We want them all :-)

    // Make the list of selected sites using both lists
    foreach ($sites as $site) {
        $selected_sites[$site->blog_id] = array ('blog_id' => $site->blog_id, 'path' => $site->path);
    }

    return $selected_sites;
}

/**
 * Build an array with the results of the SQL execution ready to be printed. Also build the summary if apply
 *
 * @param array $results
 * @param boolean $summarize
 * @param array $summary
 * @return array
 */
function process_results( $results, $summarize, $summary ) {
    $is_first = true;
    $num_row  = 0;
    $headers  = $data = array();

    // The summary counts how many times each value appears in all the blogs, so it is only made if all the results
    // have a single column
    if ( 1 !== count( $results[0] ) ) {
        $summarize = false;
    }

    foreach ( $results as $row ) {
        foreach ( $row as $key => $value ) {
            if ( $is_first ) {
                $headers[] = $key;
            }
            $data[ $num_row ][] = $value;
            if ( $summarize ) {
                if ( isset( $summary[ $value ] ) ) {
                    $summary[ $value ] ++;
                } else {
                    $summary[ $value ] = 1;
                }
            }
        }
        $is_first = false;
        $num_row ++;
    }

    return array( $headers, $data, $summary, $summarize );
}

/**
 * Show the latest SQL error
 */
function print_error() {
    global $wpdb;

    echo '<div style="color:red;">';
    echo __( 'An error has ocurred in sentence', 'xmm' ) . ': <code>' . esc_html( $wpdb->last_query ) . '</code><br />';
    echo __( 'Error description', 'xmm' ) . ': <code>' . esc_html( $wpdb->last_error ) . '</code><br>';
    echo '</div>';
}

/**
 * Print the results of an SQL execution for a blog
 *
 * @param array $headers
 * @param array $data
 */
function print_data( $headers, $data ) {
    echo '
        <table class="xmm-table" style="">
            <tbody>
                <tr valign="top">
        ';
    foreach ( $headers as $value ) {
        echo '<th>' . esc_html( $value ) . '</th>';
    }
    echo '</tr>';
    foreach ( $data as $row ) {
        echo '<tr>';
        foreach ( $row as $value ) {
            echo '<td>' . esc_html( $value ) . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
}

/**
 * Print the summary results for all the blogs, when available
 *
 * @param array $summary
 */
function print_summary( $summary ) {

    // Sort values with descending order
    arsort( $summary );

    // $sum accumulates the sum of all the results of the query in all the sites. $sum_product is used when the result
    //  of the query is a number an accumulates the product of the result by the times it happens.
    $sum = 0;
    $sum_product = 0;

    echo '<h3>' . __( 'Summary of results', 'xmm' ) . '</h3>';
    echo '<table class="xmm-table">';
    echo '<tbody>';

    echo '<tr style="border-bottom:5px solid;">';
    echo '<th>' . __( 'Value', 'xmm' ) . '</th>';
    echo '<th>' . __( 'Number of ocurrences', 'xmm' ) . '</th>';
    echo '</tr>';

    foreach ( $summary as $value => $number ) {
        echo '<tr>';
        echo '<td>' . esc_html( $value ) . '</td>';
        echo '<td align="right">' . intval( $number ) . '</td>';
        echo '</tr>';

        $sum += $number;
        if ( is_numeric( $value )) {
            $sum_product += $value * $number;
        }
    }

    // Print the sum of all the numbers in the table
    echo '<tr style="border-top:5px solid;"><td><strong>' . __( 'Sum of ocurrences', 'xmm' ) . '</strong></td><td align="right"><strong>' . intval( $sum ) . '</strong></td></tr>';

    if ( $sum_product > 0 ) {
        // Print the sum products of all the numbers in the table
        echo '<tr><td><strong>' . __( 'Sum of ocurrences x value', 'xmm' ) . '</strong></td><td align="right"><strong>' . esc_html( $sum_product ) . '</strong></td></tr>';
    }

    echo '</tbody>';
    echo '</table>';

    return;
}
