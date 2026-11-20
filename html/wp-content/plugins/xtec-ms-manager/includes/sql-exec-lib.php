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
 * Returns a parameter of the SQL execution: the one sent by the form or, if it hasn't been sent, the one kept in the
 * data of the user. The parameter sent is kept in the data of the user.
 *
 * @param string $param The name of the parameter.
 * @return string The value of the parameter.
 */
function xmm_get_param(string $param): string
{
    $value = isset($_POST[$param]) && is_string($_POST[$param]) ? stripslashes_deep(trim($_POST[$param])) : '';

    $data = xmm_get_sql_data();
    if (empty($value) && !empty($data[$param])) {
        $value = $data[$param];
    } else {
        $data[$param] = $value;
        xmm_set_sql_data($data);
    }

    return $value;
}

/**
 * Replaces the tags of an SQL sentence by the prefixes of the tables of the current blog.
 *
 * @param string $sql The SQL sentence, with the tags [prefix] and [base_prefix].
 * @return string The SQL sentence with the prefixes.
 */
function xmm_render_sql(string $sql): string
{
    global $wpdb;

    return str_replace(['[prefix]', '[base_prefix]'], [$wpdb->prefix, $wpdb->base_prefix], $sql);
}

/**
 * Expands the ranges of a list of IDs of blogs. For example, 1-3,7 becomes 1,2,3,7.
 *
 * @param string $xmm_blogid_list The list of IDs, separated by commas.
 * @return string The list of IDs without ranges nor white spaces.
 */
function xmm_expand_blogid_list(string $xmm_blogid_list): string
{
    return (string)preg_replace_callback(
        '/(\d+)-(\d+)/',
        static fn(array $value): string => implode(',', range($value[1], $value[2])),
        (string)preg_replace('/\s+/', '', $xmm_blogid_list)
    );
}

/**
 * Returns all the blogs of the network, only with their ID and their path. They are read directly from the table, as
 * get_sites() creates an object for each blog and keeps it in the cache.
 *
 * @return array The blogs, by their ID, with blog_id and path.
 */
function xmm_get_sites(): array
{
    global $wpdb;

    $sites = $wpdb->get_results("SELECT blog_id, path FROM {$wpdb->blogs} ORDER BY blog_id", ARRAY_A);

    return array_column($sites, null, 'blog_id');
}

/**
 * Prepares the results of the SQL execution in a blog to be shown, and adds them to the summary of all the blogs.
 *
 * @param array $results The results of the query: the rows, by the names of the columns.
 * @param bool $summarize If the summary is made, which is the case while all the results have a single column.
 * @param array $summary The summary: how many times each value appears in all the blogs.
 * @return array The names of the columns, the rows, the summary and if the summary is made.
 */
function xmm_process_results(array $results, bool $summarize, array $summary): array
{
    $headers = array_keys($results[0]);
    $data = [];

    // The summary counts how many times each value appears in all the blogs, so it is only made if all the results
    // have a single column
    if (count($headers) !== 1) {
        $summarize = false;
    }

    foreach ($results as $row) {
        $data[] = array_values($row);
        if ($summarize) {
            foreach ($row as $value) {
                // NULL is counted as an empty value, as it can't be a key
                $key = (string)$value;
                $summary[$key] = ($summary[$key] ?? 0) + 1;
            }
        }
    }

    return [$headers, $data, $summary, $summarize];
}

/**
 * Shows the latest error of the database.
 */
function xmm_print_error(): void
{
    global $wpdb;

    echo '<div style="color:red;">';
    echo __('An error has ocurred in sentence', 'xtec-ms-manager') . ': <code>' . esc_html($wpdb->last_query)
        . '</code><br />';
    echo __('Error description', 'xtec-ms-manager') . ': <code>' . esc_html($wpdb->last_error) . '</code><br>';
    echo '</div>';
}

/**
 * Shows the results of the SQL execution in a blog.
 *
 * @param array $headers The names of the columns.
 * @param array $data The rows.
 */
function xmm_print_data(array $headers, array $data): void
{
    echo '<table class="xmm-table" style=""><tbody><tr valign="top">';
    foreach ($headers as $value) {
        echo '<th>' . esc_html($value) . '</th>';
    }
    echo '</tr>';
    foreach ($data as $row) {
        echo '<tr>';
        foreach ($row as $value) {
            echo '<td>' . esc_html($value) . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
}

/**
 * Shows the summary of the results of all the blogs: how many times each value appears, how many values there are
 * and, if the values are numbers, the sum of all of them.
 *
 * @param array $summary How many times each value appears.
 */
function xmm_print_summary(array $summary): void
{
    arsort($summary);

    // $sum counts the values of all the blogs, and $sum_product sums the values that are numbers
    $sum = 0;
    $sum_product = 0;

    echo '<h3>' . __('Summary of results', 'xtec-ms-manager') . '</h3>';
    echo '<table class="xmm-table">';
    echo '<tbody>';

    echo '<tr style="border-bottom:5px solid;">';
    echo '<th>' . __('Value', 'xtec-ms-manager') . '</th>';
    echo '<th>' . __('Number of ocurrences', 'xtec-ms-manager') . '</th>';
    echo '</tr>';

    foreach ($summary as $value => $number) {
        echo '<tr>';
        echo '<td>' . esc_html($value) . '</td>';
        echo '<td align="right">' . (int)$number . '</td>';
        echo '</tr>';

        $sum += $number;
        if (is_numeric($value)) {
            $sum_product += $value * $number;
        }
    }

    echo '<tr style="border-top:5px solid;"><td><strong>' . __('Sum of ocurrences', 'xtec-ms-manager')
        . '</strong></td><td align="right"><strong>' . (int)$sum . '</strong></td></tr>';

    if ($sum_product > 0) {
        echo '<tr><td><strong>' . __('Sum of ocurrences x value', 'xtec-ms-manager')
            . '</strong></td><td align="right"><strong>' . esc_html($sum_product) . '</strong></td></tr>';
    }

    echo '</tbody>';
    echo '</table>';
}
