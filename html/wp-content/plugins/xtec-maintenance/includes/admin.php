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

add_action('init', 'xtec_maintenance_load_textdomain');
add_action('network_admin_menu', 'xtec_maintenance_menu');

/**
 * Loads the translations of the plugin.
 */
function xtec_maintenance_load_textdomain(): void
{
    load_plugin_textdomain('xtec-maintenance', false, dirname(plugin_basename(XTEC_MAINTENANCE_FILE)) . '/languages');
}

/**
 * Adds the menu Maintenance to the administration of the network, with the pages of the plugin.
 */
function xtec_maintenance_menu(): void
{
    add_menu_page(
        __('Maintenance', 'xtec-maintenance'),
        __('Maintenance', 'xtec-maintenance'),
        'manage_network',
        'xtec-maintenance',
        'xtec_maintenance_page',
        'dashicons-admin-tools'
    );
    add_submenu_page(
        'xtec-maintenance',
        __('Maintenance', 'xtec-maintenance'),
        __('Summary', 'xtec-maintenance'),
        'manage_network',
        'xtec-maintenance',
        'xtec_maintenance_page'
    );
    $inactive = add_submenu_page(
        'xtec-maintenance',
        __('Inactive blogs', 'xtec-maintenance'),
        __('Inactive blogs', 'xtec-maintenance'),
        'manage_network',
        'xtec-maintenance-inactive',
        'xtec_maintenance_inactive_blogs_page'
    );
    add_action("load-$inactive", 'xtec_maintenance_inactive_blogs_actions');
    $deleted = add_submenu_page(
        'xtec-maintenance',
        __('Deleted blogs', 'xtec-maintenance'),
        __('Deleted blogs', 'xtec-maintenance'),
        'manage_network',
        'xtec-maintenance-deleted',
        'xtec_maintenance_deleted_blogs_page'
    );
    add_action("load-$deleted", 'xtec_maintenance_load_list_table');
    // The blogs deleted by their administrators are only flagged for deletion, and the list of sites of WordPress
    // already shows them and can delete them permanently
    add_submenu_page(
        'xtec-maintenance',
        __('Blogs deleted by users', 'xtec-maintenance'),
        __('Blogs deleted by users', 'xtec-maintenance'),
        'manage_network',
        'sites.php?status=deleted'
    );
}

/**
 * Loads the classes of the lists of blogs, based on the lists of WordPress.
 */
function xtec_maintenance_load_list_table(): void
{
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
    require_once dirname(XTEC_MAINTENANCE_FILE) . '/includes/class-inactive-blogs-table.php';
    require_once dirname(XTEC_MAINTENANCE_FILE) . '/includes/class-deleted-blogs-table.php';
}

/**
 * Stops a request that isn't valid, for example with data that has been manipulated.
 */
function xtec_maintenance_invalid_request(): void
{
    wp_die(
        esc_html__('The request is not valid.', 'xtec-maintenance'),
        '',
        ['response' => 403, 'back_link' => true]
    );
}

/**
 * Returns the HTML of a notice of WordPress shown inside the content of the page.
 *
 * @param string $type The type of notice: info, success, warning or error.
 * @param string $html The content of the notice, already escaped.
 * @return string The HTML of the notice.
 */
function xtec_maintenance_notice(string $type, string $html): string
{
    return "<div class='notice notice-$type inline'>$html</div>";
}

/**
 * Shows the summary of the maintenance: the number of blogs and the links to the pages of the plugin.
 */
function xtec_maintenance_page(): void
{
    global $wpdb;

    $flagged = get_sites(['count' => true, 'deleted' => 1, 'network_id' => get_current_network_id()]);
    $deleted = (int)$wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $wpdb->delblocs WHERE status = %d",
        XTEC_MAINTENANCE_STATUS_INACTIVITY
    ));
    $rows = [
        [__('Blogs of the network', 'xtec-maintenance'), get_blog_count(), ''],
        [
            __('Blogs flagged for deletion by their administrators', 'xtec-maintenance'),
            $flagged,
            network_admin_url('sites.php?status=deleted'),
        ],
        [
            __('Blogs deleted for inactivity', 'xtec-maintenance'),
            $deleted,
            network_admin_url('admin.php?page=xtec-maintenance-deleted'),
        ],
    ];
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Maintenance', 'xtec-maintenance'); ?></h1>

        <table class="widefat striped">
            <tbody>
            <?php
            foreach ($rows as [$label, $number, $url]) {
                echo '<tr><td>' . esc_html($label) . '</td><td><strong>' . esc_html(number_format_i18n($number))
                    . '</strong></td><td>';
                if ($url !== '') {
                    echo '<a href="' . esc_url($url) . '">' . esc_html__('Show', 'xtec-maintenance') . '</a>';
                }
                echo '</td></tr>';
            }
            ?>
            </tbody>
        </table>

        <p>
            <a href="<?php echo esc_url(network_admin_url('admin.php?page=xtec-maintenance-inactive')); ?>"
                class="button button-primary"><?php esc_html_e('Search inactive blogs', 'xtec-maintenance'); ?></a>
        </p>
        <p class="description">
            <?php
            esc_html_e(
                'The blogs flagged for deletion by their administrators are kept until a super admin deletes them permanently from the list of sites.',
                'xtec-maintenance'
            );
            ?>
        </p>
    </div>
    <?php
}
