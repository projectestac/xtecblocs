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
 * List of the blogs that meet the criteria of inactivity, with the actions to delete them or to change the date of
 * their last update to today.
 */
class InactiveBlogsTable extends \WP_List_Table
{
    private const PER_PAGE = 25;

    /**
     * @param array $criteria The criteria, as returned by xtec_maintenance_criteria().
     */
    public function __construct(private array $criteria)
    {
        parent::__construct(['singular' => 'blog', 'plural' => 'blogs']);
    }

    public function get_columns(): array
    {
        return [
            'cb' => '<input type="checkbox" />',
            'name' => __('Blog', 'xtec-maintenance'),
            'days' => __('Days since the last update', 'xtec-maintenance'),
            'posts' => __('Posts and pages', 'xtec-maintenance'),
            'files' => __('Files in the media library', 'xtec-maintenance'),
        ];
    }

    protected function get_bulk_actions(): array
    {
        return [
            'touch' => __('Change the date of the last update to today', 'xtec-maintenance'),
            'delete' => __('Delete permanently', 'xtec-maintenance'),
        ];
    }

    public function prepare_items(): void
    {
        $blog_ids = xtec_maintenance_find_inactive_blogs($this->criteria);
        $page_ids = array_slice($blog_ids, ($this->get_pagenum() - 1) * self::PER_PAGE, self::PER_PAGE);
        $content = xtec_maintenance_count_content($page_ids);

        $this->items = [];
        foreach ($page_ids as $blog_id) {
            $site = get_site($blog_id);
            if (!$site) {
                continue;
            }
            $this->items[] = [
                'id' => $blog_id,
                'name' => (string)get_blog_option($blog_id, 'blogname'),
                'url' => get_home_url($blog_id),
                'address' => $site->domain . $site->path,
                // The date of the last update is in UTC
                'days' => (int)floor((time() - strtotime($site->last_updated . ' UTC')) / DAY_IN_SECONDS),
                'posts' => $content[$blog_id]['posts'],
                'files' => $content[$blog_id]['files'],
            ];
        }

        $this->set_pagination_args(['total_items' => count($blog_ids), 'per_page' => self::PER_PAGE]);
        $this->_column_headers = [$this->get_columns(), [], []];
    }

    public function no_items(): void
    {
        esc_html_e('No blog meets the criteria.', 'xtec-maintenance');
    }

    protected function column_cb($item): string
    {
        return '<input type="checkbox" name="blogs[]" value="' . esc_attr((string)$item['id']) . '" />';
    }

    protected function column_name(array $item): string
    {
        return '<strong><a href="' . esc_url($item['url']) . '">' . esc_html($item['name'] ?: $item['address'])
            . '</a></strong><br />' . esc_html($item['address']);
    }

    protected function column_default($item, $column_name): string
    {
        return esc_html(number_format_i18n($item[$column_name]));
    }
}
