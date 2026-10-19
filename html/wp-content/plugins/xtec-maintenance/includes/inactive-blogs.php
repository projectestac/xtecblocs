<?php

defined('ABSPATH') || exit;

/**
 * Copyright 2026 Departament d'Educació i Formació Professional
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Returns the criteria to search inactive blogs, from the data of a request or, if they aren't there, the default ones.
 *
 * @param array $source The data of the request ($_GET or $_POST).
 * @return array The number of days without updates (days), the maximum number of posts and pages (posts) and whether
 *               files have been uploaded to the blogs (uploads: never or some).
 */
function xtec_maintenance_criteria(array $source): array
{
    $uploads = $source['uploads'] ?? '';
    if (!in_array($uploads, ['never', 'some'], true)) {
        $uploads = ((int)get_site_option('xtec_maintenance_never_upload_file', 1) === 1) ? 'never' : 'some';
    }

    return [
        'days' => isset($source['days']) && is_string($source['days'])
            ? absint($source['days'])
            : (int)get_site_option('xtec_maintenance_inactivity_days', 90),
        'posts' => isset($source['posts']) && is_string($source['posts'])
            ? absint($source['posts'])
            : (int)get_site_option('xtec_maintenance_posts_pages', 2),
        'uploads' => $uploads,
    ];
}

/**
 * Returns the URL of the page of inactive blogs with some criteria.
 *
 * @param array $criteria The criteria, as returned by xtec_maintenance_criteria().
 * @param array $args Other parameters of the URL.
 * @return string The URL, not escaped.
 */
function xtec_maintenance_inactive_url(array $criteria, array $args = []): string
{
    return add_query_arg($criteria + $args, network_admin_url('admin.php?page=xtec-maintenance-inactive'));
}

/**
 * Counts the content of some blogs: the posts and pages, without the automatic drafts that WordPress creates when the
 * editor is opened and the ones in the trash, and the files of the media library. Each blog is counted with a query
 * of its own, as the tables of the blogs are in different databases (LudicrousDB), so a single query can't join them.
 *
 * @param int[] $blog_ids The IDs of the blogs.
 * @return array The number of posts and pages (posts) and of files (files), indexed by the ID of the blog.
 */
function xtec_maintenance_count_content(array $blog_ids): array
{
    global $wpdb;

    $counts = [];
    // A blog without the table of posts has no content
    $suppress = $wpdb->suppress_errors(true);
    foreach ($blog_ids as $blog_id) {
        $table = $wpdb->get_blog_prefix($blog_id) . 'posts';
        $row = $wpdb->get_row(
            "SELECT
                SUM(post_type IN ('post', 'page') AND post_status NOT IN ('auto-draft', 'trash')) AS posts,
                SUM(post_type = 'attachment') AS files
            FROM `$table` WHERE post_type IN ('post', 'page', 'attachment')"
        );
        $counts[$blog_id] = ['posts' => (int)($row->posts ?? 0), 'files' => (int)($row->files ?? 0)];
    }
    $wpdb->suppress_errors($suppress);

    return $counts;
}

/**
 * Searches the blogs that meet the criteria. The main site is never included. As checking the posts and the files of
 * the blogs is slow in a big network, the result is kept for some minutes.
 *
 * @param array $criteria The criteria, as returned by xtec_maintenance_criteria().
 * @return int[] The IDs of the blogs, the least recently updated first.
 */
function xtec_maintenance_find_inactive_blogs(array $criteria): array
{
    global $wpdb;

    $key = md5((string)wp_json_encode($criteria));
    $cache = get_site_transient('xtec_maintenance_inactive_blogs');
    if (is_array($cache) && ($cache['key'] ?? '') === $key) {
        return $cache['blogs'];
    }

    // The dates of the blogs are in UTC
    $candidates = array_map('intval', $wpdb->get_col($wpdb->prepare(
        "SELECT blog_id FROM $wpdb->blogs
        WHERE site_id = %d AND blog_id != %d AND deleted = 0 AND last_updated < %s
        ORDER BY last_updated",
        get_current_network_id(),
        get_main_site_id(),
        gmdate('Y-m-d H:i:s', time() - $criteria['days'] * DAY_IN_SECONDS)
    )));

    // Files have been uploaded to a blog if its media library has some file
    $with_files = $criteria['uploads'] === 'some';
    $blogs = [];
    foreach (xtec_maintenance_count_content($candidates) as $blog_id => $content) {
        if ($content['posts'] <= $criteria['posts'] && ($content['files'] > 0) === $with_files) {
            $blogs[] = $blog_id;
        }
    }

    set_site_transient('xtec_maintenance_inactive_blogs', ['key' => $key, 'blogs' => $blogs], 10 * MINUTE_IN_SECONDS);

    return $blogs;
}

/**
 * Returns the blogs selected in the list, which must exist and can't be the main site.
 *
 * @return int[] The IDs of the blogs.
 */
function xtec_maintenance_selected_blogs(): array
{
    $blog_ids = (isset($_POST['blogs']) && is_array($_POST['blogs'])) ? array_map('absint', $_POST['blogs']) : [];

    return array_values(array_filter(
        array_unique($blog_ids),
        static fn(int $blog_id): bool => $blog_id !== get_main_site_id() && get_site($blog_id) !== null
    ));
}

/**
 * Returns the action chosen in the list of blogs, in any of its two selectors.
 *
 * @return string The action: touch, delete or an empty string.
 */
function xtec_maintenance_bulk_action(): string
{
    foreach (['action', 'action2'] as $field) {
        $action = $_POST[$field] ?? '';
        if ($action === 'touch' || $action === 'delete') {
            return $action;
        }
    }

    return '';
}

/**
 * Does the actions of the page of inactive blogs before it is shown, so that the browser can be redirected afterwards
 * and reloading the page doesn't do them again: saving the criteria as default, updating the date of the selected
 * blogs and deleting them after the confirmation.
 */
function xtec_maintenance_inactive_blogs_actions(): void
{
    xtec_maintenance_load_list_table();

    if (isset($_POST['xtec_maintenance_save_criteria'])) {
        check_admin_referer('xtec-maintenance-criteria');
        $criteria = xtec_maintenance_criteria(wp_unslash($_POST));
        update_site_option('xtec_maintenance_inactivity_days', $criteria['days']);
        update_site_option('xtec_maintenance_posts_pages', $criteria['posts']);
        update_site_option('xtec_maintenance_never_upload_file', ($criteria['uploads'] === 'never') ? 1 : 0);
        wp_safe_redirect(xtec_maintenance_inactive_url($criteria, ['done' => 'saved']));
        exit;
    }

    $criteria = xtec_maintenance_criteria(wp_unslash($_GET));
    $action = xtec_maintenance_bulk_action();
    $confirmed = isset($_POST['xtec_maintenance_confirm_delete']);
    if ($action === '' && !$confirmed) {
        return;
    }

    check_admin_referer($confirmed ? 'xtec-maintenance-delete' : 'bulk-blogs');
    $blog_ids = xtec_maintenance_selected_blogs();
    if (!$blog_ids) {
        wp_safe_redirect(xtec_maintenance_inactive_url($criteria, ['done' => 'none']));
        exit;
    }

    if ($action === 'touch') {
        foreach ($blog_ids as $blog_id) {
            update_blog_details($blog_id, ['last_updated' => current_time('mysql', true)]);
        }
        $done = 'touched';
    } elseif ($confirmed) {
        foreach ($blog_ids as $blog_id) {
            xtec_maintenance_log_deleted_blog($blog_id, XTEC_MAINTENANCE_STATUS_INACTIVITY);
            wpmu_delete_blog($blog_id, true);
        }
        $done = 'deleted';
    } else {
        // The deletion is confirmed in the page
        return;
    }

    delete_site_transient('xtec_maintenance_inactive_blogs');
    wp_safe_redirect(xtec_maintenance_inactive_url($criteria, ['done' => $done, 'count' => count($blog_ids)]));
    exit;
}

/**
 * Returns the message of the result of an action, after the redirection of xtec_maintenance_inactive_blogs_actions().
 *
 * @return string The HTML of the notice, or an empty string if there isn't any result.
 */
function xtec_maintenance_inactive_blogs_message(): string
{
    $done = (isset($_GET['done']) && is_string($_GET['done'])) ? $_GET['done'] : '';
    $count = isset($_GET['count']) ? absint($_GET['count']) : 0;

    switch ($done) {
        case 'saved':
            return xtec_maintenance_notice(
                'success',
                '<p>' . esc_html__('The criteria have been saved as default.', 'xtec-maintenance') . '</p>'
            );
        case 'none':
            return xtec_maintenance_notice(
                'error',
                '<p>' . esc_html__('You have not selected any blog.', 'xtec-maintenance') . '</p>'
            );
        case 'touched':
            /* translators: %s: Number of blogs. */
            $text = _n(
                'The date of the last update of %s blog has been changed to today.',
                'The date of the last update of %s blogs has been changed to today.',
                $count,
                'xtec-maintenance'
            );
            break;
        case 'deleted':
            /* translators: %s: Number of blogs. */
            $text = _n('%s blog has been deleted.', '%s blogs have been deleted.', $count, 'xtec-maintenance');
            break;
        default:
            return '';
    }

    return xtec_maintenance_notice('success', '<p>' . esc_html(sprintf($text, number_format_i18n($count))) . '</p>');
}

/**
 * Shows the page to confirm the deletion of the selected blogs.
 *
 * @param int[] $blog_ids The IDs of the blogs.
 * @param string $page_url The URL of the page of inactive blogs, with the criteria.
 */
function xtec_maintenance_confirm_delete(array $blog_ids, string $page_url): void
{
    ?>
    <h2><?php esc_html_e('Delete the blogs permanently', 'xtec-maintenance'); ?></h2>
    <?php
    echo xtec_maintenance_notice(
        'warning',
        '<p>' . esc_html__(
            'The following blogs will be deleted permanently, with all their contents and files. This action cannot be undone.',
            'xtec-maintenance'
        ) . '</p>'
    );
    ?>
    <ul class="ul-disc">
        <?php
        foreach ($blog_ids as $blog_id) {
            $site = get_site($blog_id);
            echo '<li><strong>' . esc_html((string)get_blog_option($blog_id, 'blogname')) . '</strong> ('
                . esc_html($site->domain . $site->path) . ')</li>';
        }
        ?>
    </ul>
    <form method="post" action="<?php echo esc_url($page_url); ?>">
        <?php wp_nonce_field('xtec-maintenance-delete'); ?>
        <?php
        foreach ($blog_ids as $blog_id) {
            echo '<input type="hidden" name="blogs[]" value="' . esc_attr((string)$blog_id) . '" />';
        }
        ?>
        <p class="submit">
            <button type="submit" name="xtec_maintenance_confirm_delete" value="1" class="button button-primary">
                <?php esc_html_e('Yes, delete them permanently', 'xtec-maintenance'); ?>
            </button>
            <a href="<?php echo esc_url($page_url); ?>" class="button">
                <?php esc_html_e('Cancel', 'xtec-maintenance'); ?>
            </a>
        </p>
    </form>
    <?php
}

/**
 * Shows the page of inactive blogs: the form of the criteria and the list of the blogs that meet them, which can be
 * deleted or have the date of their last update changed to today, so that they aren't deleted.
 */
function xtec_maintenance_inactive_blogs_page(): void
{
    $criteria = xtec_maintenance_criteria(wp_unslash($_GET));
    $page_url = xtec_maintenance_inactive_url($criteria);
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Inactive blogs', 'xtec-maintenance'); ?></h1>

        <?php
        if (xtec_maintenance_bulk_action() === 'delete') {
            // The nonce and the selection have been checked in xtec_maintenance_inactive_blogs_actions()
            xtec_maintenance_confirm_delete(xtec_maintenance_selected_blogs(), $page_url);
            echo '</div>';

            return;
        }

        echo xtec_maintenance_inactive_blogs_message();
        ?>

        <form method="get" action="<?php echo esc_url(network_admin_url('admin.php')); ?>">
            <input type="hidden" name="page" value="xtec-maintenance-inactive" />
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="xtec-maintenance-days">
                            <?php esc_html_e('Days without updates', 'xtec-maintenance'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="number" id="xtec-maintenance-days" name="days" min="0" class="small-text"
                            value="<?php echo esc_attr((string)$criteria['days']); ?>" />
                        <p class="description">
                            <?php
                            esc_html_e(
                                'Minimum number of days since the last update of the blog. With 0, the date is not taken into account.',
                                'xtec-maintenance'
                            );
                            ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="xtec-maintenance-posts">
                            <?php esc_html_e('Maximum of posts and pages', 'xtec-maintenance'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="number" id="xtec-maintenance-posts" name="posts" min="0" class="small-text"
                            value="<?php echo esc_attr((string)$criteria['posts']); ?>" />
                        <p class="description">
                            <?php
                            esc_html_e(
                                'The automatic drafts that WordPress creates when the editor is opened and the posts and pages in the trash are not counted.',
                                'xtec-maintenance'
                            );
                            ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="xtec-maintenance-uploads">
                            <?php esc_html_e('Uploaded files', 'xtec-maintenance'); ?>
                        </label>
                    </th>
                    <td>
                        <select id="xtec-maintenance-uploads" name="uploads">
                            <option value="never" <?php selected($criteria['uploads'], 'never'); ?>>
                                <?php esc_html_e('No file has ever been uploaded', 'xtec-maintenance'); ?>
                            </option>
                            <option value="some" <?php selected($criteria['uploads'], 'some'); ?>>
                                <?php esc_html_e('Some file has been uploaded', 'xtec-maintenance'); ?>
                            </option>
                        </select>
                        <p class="description">
                            <?php
                            esc_html_e(
                                'The files that have been uploaded to the blog are the ones of its media library.',
                                'xtec-maintenance'
                            );
                            ?>
                        </p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" class="button button-primary">
                    <?php esc_html_e('Search', 'xtec-maintenance'); ?>
                </button>
            </p>
        </form>

        <form method="post" action="<?php echo esc_url($page_url); ?>">
            <?php wp_nonce_field('xtec-maintenance-criteria'); ?>
            <?php
            foreach ($criteria as $name => $value) {
                echo '<input type="hidden" name="' . esc_attr($name) . '" value="' . esc_attr((string)$value) . '" />';
            }
            ?>
            <p>
                <button type="submit" name="xtec_maintenance_save_criteria" value="1" class="button">
                    <?php esc_html_e('Save these criteria as default', 'xtec-maintenance'); ?>
                </button>
            </p>
        </form>

        <form method="post" action="<?php echo esc_url($page_url); ?>">
            <?php
            $table = new XtecMaintenance\InactiveBlogsTable($criteria);
            $table->prepare_items();
            $table->display();
            ?>
        </form>
    </div>
    <?php
}
