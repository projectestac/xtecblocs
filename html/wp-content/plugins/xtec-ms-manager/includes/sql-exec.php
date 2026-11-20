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

require_once __DIR__ . '/sql-exec-lib.php';

/**
 * Shows the page of the SQL execution, in four steps: the SQL sentence, the selection of blogs, the confirmation and
 * the results of the execution.
 */
function xmm_sql(): void
{
    if (!current_user_can('manage_network_options')) {
        wp_die(esc_html__('Sorry, you are not allowed to access this page.'), '', ['response' => 403]);
    }

    $action = isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : 'step1';

    echo '<div class="wrap">';
    xmm_show_title(__('SQL execution', 'xtec-ms-manager'));

    switch ($action) {
        case 'step2':
            check_admin_referer('xmm-sql');
            xmm_sql_step2();
            break;
        case 'step3':
            check_admin_referer('xmm-sql');
            xmm_sql_step3();
            break;
        case 'step4':
            check_admin_referer('xmm-sql');
            xmm_sql_step4();
            break;
        default:
            xmm_sql_step1();
    }

    echo '</div>';
}

/**
 * Shows the description of the keyword [prefix] of the SQL sentences.
 */
function xmm_sql_prefix_description(): void
{
    echo wp_kses(
        __(
            'Use the keyword <code>[prefix]</code> to get the proper blog prefix (wp_, wp_1_, wp_2_, ...)',
            'xtec-ms-manager'
        ),
        ['code' => []]
    );
}

/**
 * Shows the SQL sentences and the selection of blogs of the execution.
 *
 * @param string $xmm_sql The SQL sentence.
 * @param string $xmm_select_blogs The selection of blogs: main_db, all, query or some.
 * @param string $xmm_match_query The SQL sentence that the blogs must match.
 * @param array|null $selected_sites The blogs selected manually, or null if they aren't shown.
 */
function xmm_sql_show_execution(
    string $xmm_sql,
    string $xmm_select_blogs,
    string $xmm_match_query,
    ?array $selected_sites = null
): void {
    echo '<div id="xmm-sql" class="info">';
    echo '<p><strong>' . __('SQL sentence', 'xtec-ms-manager') . '</strong>: <code>' . esc_html($xmm_sql)
        . '</code></p>';

    if ($xmm_select_blogs === 'query' && !empty($xmm_match_query)) {
        echo '<p><strong>' . __('SQL match sentence', 'xtec-ms-manager') . '</strong>: <code>'
            . esc_html($xmm_match_query) . '</code></p>';
    } elseif ($xmm_select_blogs === 'main_db') {
        echo '<p><strong>' . __('Execute in main database', 'xtec-ms-manager') . '</strong></p>';
    } elseif ($xmm_select_blogs === 'all') {
        echo '<p><strong>' . __('Execute in all blogs', 'xtec-ms-manager') . '</strong></p>';
    } elseif ($xmm_select_blogs === 'some' && $selected_sites !== null) {
        echo '<div><strong>' . __('Selected blogs', 'xtec-ms-manager') . '</strong>:<ul>';
        foreach ($selected_sites as $site) {
            echo '<li>' . esc_html('(' . $site['blog_id'] . ') ' . $site['path']) . '</li>';
        }
        echo '</ul></div>';
    }

    echo '</div>';
}

/**
 * Step 1: the SQL sentence.
 */
function xmm_sql_step1(): void
{
    xmm_show_subtitle(__('Step 1 - SQL sentence', 'xtec-ms-manager'));

    $xmm_sql = xmm_get_param('xmm-sql');

    $data = xmm_get_sql_data();
    unset($data['selected_sites']);
    xmm_set_sql_data($data);
    ?>
    <form method="post" action="<?php echo esc_url(add_query_arg('action', 'step2')); ?>">
        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row"><?php esc_html_e('SQL sentence', 'xtec-ms-manager'); ?></th>
                <td>
                    <textarea rows="10" name="xmm-sql"><?php echo esc_textarea($xmm_sql); ?></textarea>
                    <p id="xmm-sql-desc" class="description"><?php xmm_sql_prefix_description(); ?></p>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
        wp_nonce_field('xmm-sql');
        submit_button(__('Step 2', 'xtec-ms-manager'), 'primary', 'xmm-submit', true);
        ?>
    </form>
    <?php
}

/**
 * Step 2: the selection of blogs.
 */
function xmm_sql_step2(): void
{
    xmm_show_subtitle(__('Step 2 - Select Blogs', 'xtec-ms-manager'));

    $sql = xmm_get_param('xmm-sql');
    $xmm_select_blogs = xmm_get_param('xmm-select-blogs');
    $xmm_match_query = xmm_get_param('xmm-match-query');
    $xmm_blogid_list = xmm_get_param('xmm-blogid-list');
    $xmm_blogname_list = xmm_get_param('xmm-blogname-list');

    echo '<div id="xmm-sql" class="info"><strong>' . __('SQL sentence', 'xtec-ms-manager') . '</strong>: '
        . esc_html($sql) . '</div>';
    ?>
    <form method="post" action="<?php echo esc_url(add_query_arg('action', 'step3')); ?>">
        <ul id="xmm-step2-list">
            <li class="depth1">
                <input id="option1" type="radio" name="xmm-select-blogs" value="main_db"
                    <?php checked($xmm_select_blogs, 'main_db'); ?>/>
                <label for="option1"><?php esc_html_e('Main database', 'xtec-ms-manager'); ?></label>
            </li>
            <li class="depth1">
                <input id="option2" type="radio" name="xmm-select-blogs" value="all"
                    <?php checked($xmm_select_blogs, 'all'); ?>/>
                <label for="option2"><?php esc_html_e('All blogs', 'xtec-ms-manager'); ?></label>
            </li>
            <li class="depth1">
                <input id="option3" type="radio" name="xmm-select-blogs" value="query"
                    <?php checked($xmm_select_blogs, 'query'); ?>/>
                <label for="option3">
                    <?php esc_html_e('Blogs that match this query', 'xtec-ms-manager'); ?>
                    <br/>
                    <textarea id="xmm-match-query" name="xmm-match-query" rows="4"><?php
                        echo esc_textarea($xmm_match_query);
                    ?></textarea>
                    <p id="xmm-match-query-desc" class="description desc_textarea">
                        <?php xmm_sql_prefix_description(); ?>
                    </p>
                </label>
            </li>
            <li class="depth1">
                <input id="option4" type="radio" name="xmm-select-blogs" value="some"
                    <?php checked($xmm_select_blogs, 'some'); ?>/>
                <label for="option4">
                    <?php esc_html_e('Some blogs selected manually', 'xtec-ms-manager'); ?>
                    <div class="xmm-textarea">
                        <div class="xmm-textarea-title">
                            <?php
                            esc_html_e('List of blogs\' IDs', 'xtec-ms-manager');
                            echo '&nbsp;';
                            esc_html_e('(Comma separated. Ranges allowed in the form 1-5)', 'xtec-ms-manager');
                            ?>
                        </div>
                        <textarea id="xmm-blogid-list" name="xmm-blogid-list" rows="4"><?php
                            echo esc_textarea($xmm_blogid_list);
                        ?></textarea>
                    </div>
                    <div class="xmm-textarea">
                        <div class="xmm-textarea-title">
                            <?php
                            esc_html_e('List of blogs\' names', 'xtec-ms-manager');
                            echo '&nbsp;';
                            esc_html_e('(Comma separated)', 'xtec-ms-manager');
                            ?>
                        </div>
                        <textarea id="xmm-blogname-list" name="xmm-blogname-list" rows="4"><?php
                            echo esc_textarea($xmm_blogname_list);
                        ?></textarea>
                    </div>
                </label>
            </li>
        </ul>
        <?php
        wp_nonce_field('xmm-sql');
        submit_button(__('Step 3', 'xtec-ms-manager'), 'primary', 'xmm-submit', true);
        ?>
        <a class="button" href="<?php echo esc_url(remove_query_arg('action')); ?>">
            <?php esc_html_e('Go Back', 'xtec-ms-manager'); ?>
        </a>
    </form>
    <?php
}

/**
 * Step 3: the confirmation of the execution, with the selected blogs.
 */
function xmm_sql_step3(): void
{
    global $current_blog;

    xmm_show_subtitle(__('Step 3 - Confirm execution', 'xtec-ms-manager'));

    $xmm_select_blogs = xmm_get_param('xmm-select-blogs');
    $xmm_match_query = xmm_get_param('xmm-match-query');
    $xmm_blogid_list = xmm_get_param('xmm-blogid-list');
    $xmm_blogname_list = xmm_get_param('xmm-blogname-list');
    $data = xmm_get_sql_data();
    $xmm_sql = $data['xmm-sql'] ?? '';

    unset($data['selected_sites']);

    switch ($xmm_select_blogs) {
        case 'query':
        case 'all':
            // The whole list of blogs is got in the execution, as it can be too big to be kept
            $data['selected_sites'] = 'all';
            break;

        case 'main_db':
            $data['selected_sites'] = [['blog_id' => 1, 'path' => $current_blog->path]];
            break;

        case 'some':
            // Both lists are combined: the IDs, with the ranges expanded, and the paths of the names of the blogs,
            // which are in the path of the main blog
            $base_path = $current_blog->path;
            $xmm_blogid_list_array = explode(',', xmm_expand_blogid_list($xmm_blogid_list));
            $xmm_blogname_list_array = array_map(
                static fn(string $name): string => $base_path . trim($name) . '/',
                explode(',', $xmm_blogname_list)
            );

            foreach (xmm_get_sites() as $blog_id => $site) {
                if (
                    in_array((string)$blog_id, $xmm_blogid_list_array, true)
                    || in_array($site['path'], $xmm_blogname_list_array, true)
                ) {
                    $data['selected_sites'][$blog_id] = $site;
                }
            }
            break;
    }

    xmm_set_sql_data($data);

    xmm_sql_show_execution(
        $xmm_sql,
        $xmm_select_blogs,
        $xmm_match_query,
        is_array($data['selected_sites'] ?? null) ? $data['selected_sites'] : []
    );

    $back_url = add_query_arg(['action' => 'step2', '_wpnonce' => wp_create_nonce('xmm-sql')]);

    echo '<form method="post" action="' . esc_url(add_query_arg('action', 'step4')) . '">';
    wp_nonce_field('xmm-sql');
    submit_button(__('Execute SQL now!', 'xtec-ms-manager'), 'primary', 'xmm-submit', true);
    echo '<a class="button" href="' . esc_url($back_url) . '">' . esc_html__('Go Back', 'xtec-ms-manager') . '</a>';
    echo '</form>';
}

/**
 * Step 4: the execution of the SQL sentence in the selected blogs, with its results.
 */
function xmm_sql_step4(): void
{
    global $wpdb;

    xmm_show_subtitle(__('Step 4 - Results of the execution', 'xtec-ms-manager'));

    $data = xmm_get_sql_data();
    $xmm_sql = $data['xmm-sql'] ?? '';
    $xmm_match_query = $data['xmm-match-query'] ?? '';
    $xmm_select_blogs = $data['xmm-select-blogs'] ?? '';

    if ($xmm_select_blogs === 'query' && !preg_match('/^\s*(select) /i', $xmm_match_query)) {
        die(esc_html__('The match sentence must be a select', 'xtec-ms-manager'));
    }

    xmm_sql_show_execution($xmm_sql, $xmm_select_blogs, $xmm_match_query);

    $summarize = true;
    $summary = [];

    // The whole list of blogs isn't kept in the data of the user, as it can be too big, so it is got again
    if (isset($data['selected_sites']) && $data['selected_sites'] === 'all') {
        $selected_sites = xmm_get_sites();
    } else {
        $selected_sites = $data['selected_sites'] ?? [];
    }

    // The execution in all the blogs can take long
    set_time_limit(3600);

    // The options of each blog, which WordPress reads when switching to the blog, aren't kept in the cache, so that
    // the memory doesn't grow with the number of blogs
    $suspend_cache = wp_suspend_cache_addition();
    wp_suspend_cache_addition(true);

    foreach ($selected_sites as $site) {
        switch_to_blog($site['blog_id']);

        // The SQL sentences with the prefixes of the blog
        $sql_rendered = xmm_render_sql($xmm_sql);
        $sql_match_rendered = xmm_render_sql($xmm_match_query);

        if ($xmm_select_blogs === 'query') {
            $results = $wpdb->get_results($sql_match_rendered, ARRAY_A);
            if (!$results) {
                continue;
            }
        }

        echo esc_html('(' . $site['blog_id'] . '): ' . $site['path']) . '&nbsp;';

        if (preg_match('/^\s*(select) /i', $sql_rendered)) {
            $results = $wpdb->get_results($sql_rendered, ARRAY_A);
        } else {
            $results = $wpdb->query($sql_rendered);
        }

        // When a SELECT fails, $wpdb returns an empty array, the same as when there are no results, so the error is
        // checked too
        if ($wpdb->last_error !== '' || $results === null || $results === false) {
            xmm_print_error();
        } elseif ($results === true) {
            // CREATE, ALTER, TRUNCATE, DROP
            echo '<span style="color:green;">' . __('Query executed successfully', 'xtec-ms-manager')
                . '</span><br />';
        } elseif (is_numeric($results)) {
            // INSERT, DELETE, UPDATE, etc.
            echo '<span style="color:green;">'
                . __('Query executed successfully. Number of rows affected:', 'xtec-ms-manager') . '&nbsp;'
                . (int)$results . '</span><br />';
        } elseif (empty($results)) {
            // SELECT without results
            echo '<span style="color:green;">'
                . __('The query was executed successfully, but returns no results', 'xtec-ms-manager')
                . '</span><br />';
        } else {
            [$headers, $rows, $summary, $summarize] = xmm_process_results($results, $summarize, $summary);
            xmm_print_data($headers, $rows);
        }

        restore_current_blog();

        if (isset($_GET['debug'])) {
            echo '<div style="text-align:right">' . memory_get_usage() . '</div>';
        }
    }

    wp_suspend_cache_addition($suspend_cache);

    if ($summarize && !empty($summary)) {
        xmm_print_summary($summary);
    }
}
