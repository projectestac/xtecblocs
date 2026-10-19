<?php

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

namespace XtecMaintenance;

// The methods override the ones of WP_List_Table, so they must have the same names, which aren't in camel caps
// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

/**
 * List of the history of the blogs deleted for inactivity, with the users that they had.
 */
class DeletedBlogsTable extends \WP_List_Table
{
    private const PER_PAGE = 25;

    public function __construct()
    {
        parent::__construct(['singular' => 'deleted-blog', 'plural' => 'deleted-blogs']);
    }

    public function get_columns(): array
    {
        return [
            'id' => __('ID', 'xtec-maintenance'),
            'name' => __('Blog', 'xtec-maintenance'),
            'users' => __('Users', 'xtec-maintenance'),
            'date' => __('Date of deletion', 'xtec-maintenance'),
        ];
    }

    public function prepare_items(): void
    {
        global $wpdb;

        $total = (int)$wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $wpdb->delblocs WHERE status = %d",
            XTEC_MAINTENANCE_STATUS_INACTIVITY
        ));
        $blogs = $wpdb->get_results($wpdb->prepare(
            "SELECT site_id, site_path, blogname, del_date FROM $wpdb->delblocs
            WHERE status = %d ORDER BY del_date DESC, id DESC LIMIT %d OFFSET %d",
            XTEC_MAINTENANCE_STATUS_INACTIVITY,
            self::PER_PAGE,
            ($this->get_pagenum() - 1) * self::PER_PAGE
        ), ARRAY_A);

        // The users of all the blogs of the page, with a single query
        $users = [];
        $blog_ids = array_map('intval', array_column($blogs, 'site_id'));
        if ($blog_ids) {
            $placeholders = implode(', ', array_fill(0, count($blog_ids), '%d'));
            $rows = $wpdb->get_results($wpdb->prepare(
                "SELECT blog_id, user_id, display_name, user_email FROM $wpdb->delblocs_users
                WHERE blog_id IN ($placeholders) ORDER BY id",
                $blog_ids
            ), ARRAY_A);
            foreach ($rows as $row) {
                $users[(int)$row['blog_id']][] = $row;
            }
        }

        $this->items = [];
        foreach ($blogs as $blog) {
            $blog['users'] = $users[(int)$blog['site_id']] ?? [];
            $this->items[] = $blog;
        }

        $this->set_pagination_args(['total_items' => $total, 'per_page' => self::PER_PAGE]);
        $this->_column_headers = [$this->get_columns(), [], []];
    }

    public function no_items(): void
    {
        esc_html_e('No blog has been deleted for inactivity.', 'xtec-maintenance');
    }

    protected function column_id(array $item): string
    {
        return esc_html($item['site_id']);
    }

    protected function column_name(array $item): string
    {
        return '<strong>' . esc_html($item['blogname']) . '</strong><br />' . esc_html($item['site_path']);
    }

    protected function column_users(array $item): string
    {
        $html = [];
        foreach ($item['users'] as $user) {
            $name = esc_html($user['display_name']) . ' (' . esc_html($user['user_email']) . ')';
            // The users may have been deleted after the blog
            if (get_userdata((int)$user['user_id'])) {
                $url = network_admin_url('user-edit.php?user_id=' . (int)$user['user_id']);
                $name = '<a href="' . esc_url($url) . '">' . $name . '</a>';
            }
            $html[] = $name;
        }

        return implode('<br />', $html);
    }

    protected function column_date(array $item): string
    {
        // The dates are kept in UTC, and wp_date() shows them in the time zone and the language of the site
        $format = get_option('date_format') . ' ' . get_option('time_format');

        return esc_html((string)wp_date($format, strtotime($item['del_date'] . ' UTC')));
    }
}
