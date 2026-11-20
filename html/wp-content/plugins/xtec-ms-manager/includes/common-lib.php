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

// States of the requests
const XMM_STATE_PENDING = 1;
const XMM_STATE_DENIED = 2;
const XMM_STATE_ACCEPTED = 3;

// States of the request types
const XMM_STATE_ACTIVE = 1;
const XMM_STATE_INACTIVE = 0;

// Number of pages shown at the beginning and at the end of the pager
const XMM_NUM_ELEMS_TO_SHOW = 3;

// Data of the SQL execution of each user, kept between the steps of the process
const XMM_SQL_DATA = 'xmm_sql_data_';

// The data of the SQL execution is removed when the user logs in or out, as it was with the PHP session
add_action('wp_login', 'xmm_delete_sql_data_on_login', 10, 2);
add_action('wp_logout', 'xmm_delete_sql_data');

/**
 * Returns the data of the SQL execution of the current user.
 *
 * @return array The data: the SQL sentences, the selection of blogs and the selected blogs.
 */
function xmm_get_sql_data(): array
{
    $data = get_site_transient(XMM_SQL_DATA . get_current_user_id());

    return is_array($data) ? $data : [];
}

/**
 * Keeps the data of the SQL execution of the current user.
 *
 * @param array $data The data.
 */
function xmm_set_sql_data(array $data): void
{
    set_site_transient(XMM_SQL_DATA . get_current_user_id(), $data, DAY_IN_SECONDS);
}

/**
 * Removes the data of the SQL execution of a user.
 *
 * @param int $user_id The user ID.
 */
function xmm_delete_sql_data(int $user_id): void
{
    delete_site_transient(XMM_SQL_DATA . $user_id);
}

/**
 * Removes the data of the SQL execution of a user who logs in.
 *
 * @param string $user_login The username.
 * @param WP_User $user The user.
 */
function xmm_delete_sql_data_on_login(string $user_login, WP_User $user): void
{
    xmm_delete_sql_data($user->ID);
}

/**
 * Checks if a parameter of the URL is set to 1, which the links of the plugin use to choose the action.
 *
 * @param string $name The name of the parameter.
 * @return bool True if the parameter is 1.
 */
function xmm_query_flag(string $name): bool
{
    return isset($_GET[$name]) && $_GET[$name] === '1';
}

/**
 * Returns an ID of the URL.
 *
 * @param string $name The name of the parameter.
 * @return int The ID, or 0 if it isn't set.
 */
function xmm_query_id(string $name): int
{
    return isset($_GET[$name]) && is_string($_GET[$name]) ? (int)$_GET[$name] : 0;
}

/**
 * Returns a field of the form that has been sent.
 *
 * @param string $name The name of the field.
 * @return string The value of the field, without the slashes added by WordPress, or empty if it hasn't been sent.
 */
function xmm_post_field(string $name): string
{
    return isset($_POST[$name]) && is_string($_POST[$name]) ? wp_unslash($_POST[$name]) : '';
}

/**
 * Loads the script of the pages of the requests and the request types.
 */
function xmm_enqueue_requests_script(): void
{
    $file = 'javascript/requests.js';
    // The date of the file is used as version so that browsers load it again when it changes
    wp_enqueue_script(
        'xmm-requests',
        plugins_url($file, XMM_FILE),
        ['jquery'],
        (string)filemtime(dirname(XMM_FILE) . '/' . $file),
        true
    );
    wp_localize_script('xmm-requests', 'xmmRequests', [
        /* translators: %s: the ID of the element */
        'confirmDeletion' => __('Are you sure you want to delete the element with ID %s?', 'xtec-ms-manager'),
    ]);
}

/**
 * Adds the options of the plugin and creates its tables when it is activated.
 */
function xmm_install(): void
{
    global $wpdb;

    add_site_option('xmm_quota_percentage', 75);
    add_site_option('xmm_send_email', 1);
    add_site_option('xmm_email_addresses', '');

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $table_name = $wpdb->prefix . 'request_types';
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
        dbDelta("CREATE TABLE $table_name (
                id int(11) NOT NULL AUTO_INCREMENT,
                state tinyint(4) NOT NULL DEFAULT 1,
                name varchar(200) NOT NULL DEFAULT '',
                description text NOT NULL DEFAULT '',
                comments_text text NOT NULL DEFAULT '',
                PRIMARY KEY (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    $table_name = $wpdb->prefix . 'requests';
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
        dbDelta("CREATE TABLE $table_name (
                id int(11) NOT NULL AUTO_INCREMENT,
                state tinyint(4) NOT NULL DEFAULT 1,
                blog_id int(11) NOT NULL DEFAULT 0,
                request_type_id int(11) NOT NULL DEFAULT 0,
                user_id int(11) NOT NULL DEFAULT 0,
                user_login varchar(60) NOT NULL DEFAULT '',
                display_name varchar(250) NOT NULL DEFAULT '',
                user_email varchar(100) NOT NULL DEFAULT '',
                time_creation datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                time_edition datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                comments text NOT NULL DEFAULT '',
                response text NOT NULL DEFAULT '',
                priv_notes text NOT NULL DEFAULT '',
                PRIMARY KEY (id),
                KEY state (state),
                KEY blog_id (blog_id),
                KEY request_type_id (request_type_id),
                KEY user_id (user_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }
}

/**
 * Shows the title of a page.
 *
 * @param string $title The HTML of the title, already escaped.
 */
function xmm_show_title(string $title): void
{
    echo '<h2 id="xmm-title">' . $title . '</h2>';
}

/**
 * Shows a subtitle of a page.
 *
 * @param string $subtitle The HTML of the subtitle, already escaped.
 */
function xmm_show_subtitle(string $subtitle): void
{
    echo '<h3 id="xmm-sql-subtitle">' . $subtitle . '</h3>';
}

/**
 * Adds a link to request the extension of the quota in the dashboard of the blogs, if the usage of the quota exceeds
 * the percentage of the settings.
 */
function xmm_add_quota_request_to_dashboard(): void
{
    // Only the administrators of the blog can make requests
    if (!current_user_can('manage_options')) {
        return;
    }

    $quota = get_space_allowed();
    if ($quota <= 0) {
        return;
    }

    // Percentage of the quota used by the blog
    $blog_usage = round((get_space_used() / $quota) * 100, 2);

    if ($blog_usage >= get_site_option('xmm_quota_percentage')) {
        echo '<ul id="xmm-blog-quota-request"><li class="warning"><a href="'
            . esc_url(add_query_arg('page', 'xmm-blog-requests', 'tools.php')) . '">'
            . __('Request the extension of the quota', 'xtec-ms-manager') . '</a></li></ul>';
    }
}

/**
 * Shows a notice to the user with the result of an operation in the database.
 *
 * @param mixed $result The result of the operation: true or a positive number if it was successful.
 * @param string $success_text The text shown if the operation was successful.
 * @param string $fail_text The text shown if the operation failed.
 */
function xmm_log_to_user(mixed $result, string $success_text, string $fail_text): void
{
    if ($result === true || (is_numeric($result) && $result > 0)) {
        $message = $success_text;
        $class = 'notice-success';
    } else {
        $message = $fail_text;
        $class = 'notice-error';
    }

    // WordPress moves the notice to the right position of the page
    echo '<div class="notice ' . $class . '"><p>' . $message . '</p></div>';
}

/**
 * Shows the drop-down list to choose the state of a request or of a request type.
 *
 * @param string $name The name of the field.
 * @param array $states The states, by their ID, with their description (desc).
 * @param int|null $selected The ID of the selected state, or null if there isn't any.
 */
function xmm_print_state_select(string $name, array $states, ?int $selected = null): void
{
    echo '<select name="' . esc_attr($name) . '">';
    echo '<option id="0"></option>';
    foreach ($states as $key => $state) {
        echo '<option id="' . (int)$key . '" value="' . (int)$key . '"' . selected($selected, $key, false) . '>'
            . esc_html($state['desc']) . '</option>';
    }
    echo '</select>';
}

/**
 * Shows the page of an element that doesn't exist.
 *
 * @param string $title The HTML of the title, already escaped.
 * @param string $message The message.
 */
function xmm_print_not_found(string $title, string $message): void
{
    echo '<div class="wrap">';
    xmm_show_title($title);
    echo '<div class="info">' . esc_html($message) . '</div>';
    echo '</div>';
}

/**
 * Shows the pager of a list.
 *
 * @param int $current_page The current page.
 * @param int $last_page The number of pages.
 */
function xmm_print_pager(int $current_page, int $last_page): void
{
    if ($last_page <= 1) {
        return;
    }

    echo '<div class="tablenav">';
    echo '<div class="tablenav-pages">';

    // $elems_pre: number of pages shown before the current page
    for ($i = 1, $elems_pre = 0; $i <= $last_page; $i++) {
        $is_current = $i === $current_page;
        $before = $i < $current_page;

        // Quick access to the previous page
        if ($i === 1 && !$is_current) {
            echo '<a class="next page-numbers" href="' . esc_url(add_query_arg('current_page', $current_page - 1))
                . '">&laquo;</a>&nbsp;';
        }

        if ($is_current) {
            echo '<span class="page-numbers current">' . $i . '</span>&nbsp;';
        } else {
            // The page is shown if it is one of the first XMM_NUM_ELEMS_TO_SHOW pages, the previous or the next one of
            // the current page, or one of the last XMM_NUM_ELEMS_TO_SHOW pages
            if (
                ($elems_pre < XMM_NUM_ELEMS_TO_SHOW && $before)
                || ($last_page - $i) < XMM_NUM_ELEMS_TO_SHOW
                || abs($current_page - $i) === 1
            ) {
                echo '<a class="next page-numbers" href="' . esc_url(add_query_arg('current_page', $i)) . '">'
                    . $i . '</a>&nbsp;';
            }

            if ($before) {
                $elems_pre++;
            }
        }

        // Quick access to the next page
        if ($i === $last_page && !$is_current) {
            echo '<a class="next page-numbers" href="' . esc_url(add_query_arg('current_page', $current_page + 1))
                . '">&raquo;</a>&nbsp;';
        }
    }

    echo '</div>';
    echo '</div>';
}

/**
 * Sends an e-mail when a request is created, to the administrators of the requests, or when its state changes, to the
 * user who made it.
 *
 * @param string $type The event: insert_request or update_request.
 * @param array $args The arguments: the ID of the request (request_id).
 */
function xmm_send_email(string $type, array $args): void
{
    $sincerely = '<p style="margin-top:2em;">' . __('Sincerely yours,', 'xtec-ms-manager') . '</p><p>'
        . __('XTECBlocs team', 'xtec-ms-manager') . '</p><p style="font-weight:bold;">'
        . __('P.S.: This is an automatic message, please don\'t reply', 'xtec-ms-manager') . '</p>';

    switch_to_blog(1);

    $request = xmm_get_request((int)$args['request_id']);
    $request_type = xmm_get_request_type((int)$request['request_type_id']);

    if ($type === 'insert_request') {
        $time = DateTime::createFromFormat('Y-m-d H:i:s', $request['time_creation']);
        $user = $request['display_name'] . ' (' . $request['user_login'] . ' - ' . $request['user_email'] . ')';

        $email_text = '<p style="margin-left:1.5em;">' . __('Dear request admin,', 'xtec-ms-manager') . '</p><p>'
            . __('There is a new request pending to be attended:', 'xtec-ms-manager')
            . '<ul><li><strong>' . __('Request Type', 'xtec-ms-manager') . '</strong>: '
            . esc_html($request_type['name']) . '</li>'
            . '<li><strong>' . __('User', 'xtec-ms-manager') . '</strong>: ' . esc_html($user) . '</li>'
            . '<li><strong>' . __('Registration Time', 'xtec-ms-manager') . '</strong>: '
            . $time->format('d-m-Y H:i') . '</li>'
            . '<li><strong>' . __('Comments', 'xtec-ms-manager') . '</strong>:<p style="font-style:italic;">'
            . esc_html($request['comments']) . '</p></li></ul>'
            . $sincerely;

        $recipient = get_site_option('xmm_email_addresses');
        /* translators: %s: the name of the main blog */
        $subject = __('[%s] New request', 'xtec-ms-manager');
    } else {
        $request_state = xmm_get_request_state((int)$request['state']);
        $blog_url = network_site_url(get_blog_details($request['blog_id'])->path);

        $email_text = '<p style="margin-left:1.5em;">' . __('Dear user,', 'xtec-ms-manager') . '</p><p>'
            . __(
                'The state of the request <strong>##REQUESTTYPENAME##</strong> done in blog <a href="##BLOGURL##">##BLOGURL##</a> has changed to <strong>##REQUESTSTATE##</strong>.',
                'xtec-ms-manager'
            )
            . ' ';
        if (!empty($request['response'])) {
            $email_text .= __('The response of the administrators is:', 'xtec-ms-manager')
                . '</p><p style="font-style:italic; margin:2.5em;">##REQUESTRESPONSE##</p>';
        } else {
            $email_text .= '</p>';
        }
        $email_text .= '<p>'
            . __(
                'If you disagree with the resolution, you can do a new request and explain your reasons.',
                'xtec-ms-manager'
            )
            . '</p>' . $sincerely;

        // The data of the request replaces the tags of the text, escaped
        $email_text = str_replace(
            ['##REQUESTTYPENAME##', '##BLOGURL##', '##REQUESTSTATE##', '##REQUESTRESPONSE##'],
            [
                esc_html($request_type['name']),
                esc_url($blog_url),
                esc_html($request_state[1]),
                esc_html($request['response']),
            ],
            $email_text
        );

        $recipient = $request['user_email'];
        /* translators: %s: the name of the main blog */
        $subject = __('[%s] State of requests', 'xtec-ms-manager');
    }

    $subject = sprintf($subject, wp_specialchars_decode(get_option('blogname')));

    restore_current_blog();

    // The message is HTML, with the data of the request escaped
    wp_mail($recipient, $subject, $email_text, ['Content-Type: text/html; charset=UTF-8']);
}
