<?php

/**
 * Plugin Name:       XTEC WeekBlog 2
 * Description:       Allows network admins to manage WeekBlogs, a new custom post type.
 * Version:           1.0
 * Requires at least: 5.3
 * Requires PHP:      7.4
 * Author:            Francesc Bassas i Bullich
 * License:           GPL v2
 * License URI:       https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain:       xtecweekblog
 * Domain Path:       /languages
 */

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

defined('ABSPATH') || exit;

// Minimum size of the image of the weekblog
const XTECWEEKBLOG_IMAGE_WIDTH = 363;
const XTECWEEKBLOG_IMAGE_HEIGHT = 98;

add_action('after_setup_theme', 'xtecweekblog_setup');
add_action('init', 'xtecweekblog_create_post_type');

register_activation_hook(__FILE__, 'xtecweekblog_activation_hook');
register_deactivation_hook(__FILE__, 'xtecweekblog_deactivation_hook');

/**
 * Registers the weekblog custom post type and regenerates the rewrite rules, so that the weekblog URLs work.
 * The 'init' action has already run when the plugin is activated, so the post type must be registered here.
 */
function xtecweekblog_activation_hook(): void
{
    xtecweekblog_create_post_type();
    flush_rewrite_rules();
}

/**
 * Removes the rewrite rules of the weekblog custom post type.
 */
function xtecweekblog_deactivation_hook(): void
{
    unregister_post_type('xtecweekblog');
    flush_rewrite_rules();
}

/**
 * Adds the support of featured images to the weekblogs and the size of their image.
 */
function xtecweekblog_setup(): void
{
    add_theme_support('post-thumbnails', ['xtecweekblog']);
    add_image_size('xtecweekblog', XTECWEEKBLOG_IMAGE_WIDTH, XTECWEEKBLOG_IMAGE_HEIGHT, true);
}

/**
 * Creates weekblog custom post type.
 */
function xtecweekblog_create_post_type(): void
{
    // loads plugin textdomain
    load_plugin_textdomain('xtecweekblog', false, dirname(plugin_basename(__FILE__)) . '/languages');

    // register xtecweekblog post type
    register_post_type(
        'xtecweekblog',
        [
            'labels' => [
                'name' => __('WeekBlogs', 'xtecweekblog'),
                'singular_name' => __('WeekBlog', 'xtecweekblog'),
                'add_new_item' => __('Add new WeekBlog', 'xtecweekblog'),
                'edit_item' => __('Edit WeekBlog', 'xtecweekblog'),
                'new_item' => __('New WeekBlog', 'xtecweekblog'),
                'view_item' => __('View WeekBlog', 'xtecweekblog'),
                'search_items' => __('Search WeekBlogs', 'xtecweekblog'),
                'not_found_in_trash' => __('No WeekBlogs found in Trash', 'xtecweekblog'),
            ],
            'description' => __('The outstanding blog of the week.', 'xtecweekblog'),
            'public' => true,
            'has_archive' => true,
            'capabilities' => [
                'edit_post' => 'manage_network',
                'edit_posts' => 'manage_network',
                'edit_others_posts' => 'manage_network',
                'publish_posts' => 'manage_network',
                'read_post' => 'manage_network',
                'read_private_posts' => 'manage_network',
                'delete_post' => 'manage_network',
                'delete_posts' => 'manage_network',
            ],
            'supports' => ['thumbnail'],
            'menu_icon' => 'dashicons-star-filled',
            'register_meta_box_cb' => 'xtecweekblog_meta_box_cb',
        ]
    );
}

add_action('admin_enqueue_scripts', 'xtecweekblog_enqueue_list_table_style');

/**
 * Adds CSS to fix the week column width on the xtecweekblogs list table.
 *
 * @param string $hook_suffix The current admin page.
 */
function xtecweekblog_enqueue_list_table_style(string $hook_suffix): void
{
    if ($hook_suffix === 'edit.php' && get_current_screen()->post_type === 'xtecweekblog') {
        $path = 'css/xtecweekblogs_list_table.css';
        $version = (string)filemtime(plugin_dir_path(__FILE__) . $path);
        wp_enqueue_style('xtecweekblogs_list_table', plugins_url($path, __FILE__), [], $version);
    }
}

/**
 * Adds xtecweekblog meta boxes.
 */
function xtecweekblog_meta_box_cb(): void
{
    add_meta_box('xtecweekblog-meta', __('Params', 'xtecweekblog'), 'xtecweekblog_meta', 'xtecweekblog', 'normal');
    remove_meta_box('postimagediv', 'xtecweekblog', 'side');
    add_meta_box(
        'postimagediv',
        __('Custom Image', 'xtecweekblog'),
        'post_thumbnail_meta_box',
        'xtecweekblog',
        'normal',
        'low'
    );
}

/**
 * Displays weekblog meta box.
 */
function xtecweekblog_meta(): void
{
    wp_nonce_field('xtecweekblog_meta_box', 'xtecweekblog_meta_box_nonce', false);

    global $post;
    $name = get_post_meta($post->ID, '_xtecweekblog-name', true);
    $description = get_post_meta($post->ID, '_xtecweekblog-description', true);
    ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">
                <label for="xtecweekblog-name"><?php esc_html_e('WeekBlog URL', 'xtecweekblog') ?></label>
            </th>
            <td>
                <?php echo esc_html(network_site_url()); ?>
                <br>
                <input type='text' name='_xtecweekblog-name' id='xtecweekblog-name' style='width:98%'
                       value='<?php echo esc_attr($name) ?>'/>
                <br>
                <?php
                // alert if blog of weekblog not exists
                if ($post->post_status !== 'auto-draft' && !xtecweekblog_validate_name($post->ID)) {
                    echo '<div class="notice notice-error inline"><p>' .
                        esc_html__('WeekBlog URL is wrong.', 'xtecweekblog') . '</p></div>';
                }
                ?>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <label for="xtecweekblog-description"><?php esc_html_e('Description', 'xtecweekblog') ?></label>
            </th>
            <td>
                <textarea name="_xtecweekblog-description" id="xtecweekblog-description" maxlength="175" rows="5"
                          style="width:98%"><?php echo esc_textarea($description) ?></textarea>
                <p class="howto">
                    <?php esc_html_e('Description max length: 175 chars', 'xtecweekblog') ?>
                </p>
                <?php
                // alert if description of weekblog is not defined
                if ($post->post_status !== 'auto-draft' && !xtecweekblog_validate_description($post->ID)) {
                    echo '<div class="notice notice-error inline"><p>' .
                        esc_html__('WeekBlog description is not defined.', 'xtecweekblog') . '</p></div>';
                }
                ?>
            </td>
        </tr>
    </table>
    <?php
}

add_action('save_post_xtecweekblog', 'xtecweekblog_save');

/**
 * Saves weekblog data.
 *
 * @param int $post_id Post ID.
 */
function xtecweekblog_save($post_id): void
{
    // Check nonce.
    if (
        !isset($_POST['xtecweekblog_meta_box_nonce'])
        || !wp_verify_nonce($_POST['xtecweekblog_meta_box_nonce'], 'xtecweekblog_meta_box')
    ) {
        return;
    }

    // Exit on autosave.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check capabilities.
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save weekblog name.
    if (isset($_POST['_xtecweekblog-name'])) {
        // The values of $_POST are slashed, as update_post_meta() expects
        update_post_meta($post_id, '_xtecweekblog-name', sanitize_text_field($_POST['_xtecweekblog-name']));
    } else {
        delete_post_meta($post_id, '_xtecweekblog-name');
    }

    // Save weekblog description.
    if (isset($_POST['_xtecweekblog-description'])) {
        update_post_meta(
            $post_id,
            '_xtecweekblog-description',
            wp_filter_post_kses($_POST['_xtecweekblog-description'])
        );
    } else {
        delete_post_meta($post_id, '_xtecweekblog-description');
    }
}

add_filter('post_updated_messages', 'xtecweekblog_updated_messages');

/**
 * Customizes weekblog updated messages.
 *
 * @param array $messages Default updated messages.
 * @return array Udated messages.
 */
function xtecweekblog_updated_messages($messages): array
{
    global $post, $post_ID;

    $messages['xtecweekblog'] = [
        0 => '', // Unused. Messages start at index 1.
        1 => sprintf(
            __('Weekblog updated. <a href="%s">View weekblog</a>', 'xtecweekblog'),
            esc_url(get_permalink($post_ID))
        ),
        2 => __('Custom field updated.', 'xtecweekblog'),
        3 => __('Custom field deleted.', 'xtecweekblog'),
        4 => __('Weekblog updated.', 'xtecweekblog'),
        5 => isset($_GET['revision'])
            ? sprintf(
                __('Weekblog restored to revision from %s', 'xtecweekblog'),
                wp_post_revision_title((int)$_GET['revision'], false)
            )
            : false,
        6 => sprintf(
            __('Weekblog published. <a href="%s">View weekblog</a>', 'xtecweekblog'),
            esc_url(get_permalink($post_ID))
        ),
        7 => __('Weekblog saved.', 'xtecweekblog'),
        8 => sprintf(
            __('Weekblog submitted. <a target="_blank" href="%s">Preview weekblog</a>', 'xtecweekblog'),
            esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))
        ),
        9 => sprintf(
            __(
                'Weekblog scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview weekblog</a>',
                'xtecweekblog'
            ),
            date_i18n(__('M j, Y @ G:i', 'xtecweekblog'), strtotime($post->post_date)),
            esc_url(get_permalink($post_ID))
        ),
        10 => sprintf(
            __('Weekblog draft updated. <a target="_blank" href="%s">Preview weekblog</a>', 'xtecweekblog'),
            esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))
        ),
    ];

    return $messages;
}

add_filter('admin_post_thumbnail_html', 'xtecweekblog_thumbnail_html');

/**
 * Output HTML for the xtecweekblog thumbnail meta-box.
 *
 * @param string $content Default HTML for the post thumbnail meta-box.
 * @return string html
 */
function xtecweekblog_thumbnail_html($content): string
{
    global $post;

    // shows info message
    if ($post !== null && $post->post_type === 'xtecweekblog') {
        $content .= '<p class="howto">' .
            esc_html__(
                'Image size must be at least of 363 x 98 px. If the image is bigger than the minimum size then, when it be displayed, it will be automatically cropped.',
                'xtecweekblog'
            ) .
            '</p>';
    }

    // checks image
    if (!is_null($post) && ($post->post_status !== 'auto-draft')) {
        if (!xtecweekblog_validate_image($post->ID)) {
            $content .= '<div class="notice notice-error inline"><p>' .
                esc_html__('Custom Image is not defined.', 'xtecweekblog') . '</p></div>';
        } else {
            // checks thumbnail
            $thumbnail_id = get_post_meta($post->ID, '_thumbnail_id', true);
            $image_attributes = wp_get_attachment_image_src($thumbnail_id, 'full');

            // shows cropped image
            if (
                $image_attributes
                && ($image_attributes[1] > XTECWEEKBLOG_IMAGE_WIDTH || $image_attributes[2] > XTECWEEKBLOG_IMAGE_HEIGHT)
            ) {
                $content .= '<p>' . esc_html__('Cropped Image:', 'xtecweekblog') . '</p><p>' .
                    get_the_post_thumbnail($post->ID, 'xtecweekblog') . '</p>';
            }

            // checks image size
            if (!xtecweekblog_validate_image_size($post->ID)) {
                $content .= '<div class="notice notice-error inline"><p>' .
                    esc_html__('Image size is too small', 'xtecweekblog') . '</p></div>';
            }
        }
    }

    return $content;
}

add_filter('manage_edit-xtecweekblog_columns', 'xtecweekblog_edit_columns');

/**
 * Customizes xtecweekblogs list table columns.
 *
 * @return array Xtecweekblogs list table columns.
 */
function xtecweekblog_edit_columns(): array
{
    return [
        'cb' => "<input type=\"checkbox\" />",
        '_xtecweekblog-name' => __('WeekBlog URL', 'xtecweekblog'),
        'thumbnail' => __('Image', 'xtecweekblog'),
        '_xtecweekblog-description' => __('Description', 'xtecweekblog'),
        'week' => __('Week', 'xtecweekblog'),
        'date' => __('Date', 'xtecweekblog'),
    ];
}

add_action('manage_xtecweekblog_posts_custom_column', 'xtecweekblog_custom_columns', 10, 2);

/**
 * Output HTML for the column of a specific xtecweekblog.
 *
 * @param string $column Column name.
 * @param string $post_id Post ID.
 */
function xtecweekblog_custom_columns($column, $post_id): void
{
    global $post;

    switch ($column) {
        case '_xtecweekblog-name':
            $name = get_post_meta($post_id, '_xtecweekblog-name', true);
            echo '<strong>';
            echo '<span class="row-title" style="color:#21759B">' . esc_html($name) . '</span>';
            _post_states($post);
            echo '</strong>';
            if (xtecweekblog_validate_name($post_id)) {
                // valid weekblog, print name and URL
                $url = network_site_url() . $name;
                echo "<p><a href='" . esc_url($url) . "'>" . esc_html($url) . "</a></p>";
            } else {
                // invalid weekblog, print invalid name and notify
                echo '<p style="color:#FF0000">' . esc_html__('Invalid name', 'xtecweekblog') . '</p>';
            }
            // Data for the quick edit, which WordPress only prints in the title column
            get_inline_data($post);
            break;

        case '_xtecweekblog-description':
            $custom = get_post_custom($post_id);
            if (xtecweekblog_validate_description($post_id)) {
                echo wpautop(wp_kses_post($custom['_xtecweekblog-description'][0]));
            } else {
                echo '<p style="color:#FF0000">' . esc_html__('Description is not defined', 'xtecweekblog') . '</p>';
            }
            break;

        case 'thumbnail':
            if (xtecweekblog_validate_image($post_id)) {
                if (!xtecweekblog_validate_image_size($post_id)) {
                    echo '<p style="color:#FF0000">' . esc_html__('Image size is too small', 'xtecweekblog') . '</p>';
                }
                echo get_the_post_thumbnail($post_id, 'xtecweekblog');
            } else {
                echo '<p style="color:#FF0000">' . esc_html__('Custom Image is not defined', 'xtecweekblog') . '</p>';
            }
            break;

        case 'week':
            echo mysql2date('W', $post->post_date);
            break;
    }
}

add_filter('list_table_primary_column', 'xtecweekblog_primary_column', 10, 2);

/**
 * Sets the name as the primary column of the xtecweekblogs list table, where WordPress adds the row actions.
 *
 * @param string $default Default primary column.
 * @param string $screen_id ID of the current screen.
 * @return string Primary column.
 */
function xtecweekblog_primary_column(string $default, string $screen_id): string
{
    return $screen_id === 'edit-xtecweekblog' ? '_xtecweekblog-name' : $default;
}

add_filter('manage_edit-xtecweekblog_sortable_columns', 'xtecweekblog_sortable_columns');

/**
 * Defines new sortable xtecweekblogs list table columns .
 *
 * @param array $columns Default sortable columns.
 * @return array Sortable columns.
 */
function xtecweekblog_sortable_columns($columns): array
{
    $columns['_xtecweekblog-name'] = '_xtecweekblog-name';
    return $columns;
}

/**
 * Gets the data of a weekblog to show it.
 *
 * @param WP_Post $weekblog The weekblog.
 * @return array The URL and the title of the blog of the week ('url' and 'blog_title') and the description of the
 *     weekblog ('description').
 */
function xtecweekblog_get_data(WP_Post $weekblog): array
{
    $name = get_post_meta($weekblog->ID, '_xtecweekblog-name', true);
    $blogId = get_id_from_blogname($name);

    return [
        'url' => get_blogaddress_by_name($name),
        'blog_title' => $blogId ? get_blog_option($blogId, 'blogname') : '',
        'description' => get_post_meta($weekblog->ID, '_xtecweekblog-description', true),
    ];
}

/**
 * Gets the message to show when there isn't a weekblog for the current week.
 *
 * @return string The message, which can contain HTML.
 */
function xtecweekblog_default_message(): string
{
    return (string)get_option('xtecweekblog_default_msg');
}

/**
 * Gets the URL of the banner to show when there isn't a weekblog for the current week.
 *
 * @return string URL of the banner.
 */
function xtecweekblog_default_banner_url(): string
{
    return plugins_url('images/banner.jpg', __FILE__);
}

/**
 * Gets the current weekblog.
 *
 * @return WP_Post|null Current weekblog post.
 */
function xtecweekblog_current_weekblog(): ?WP_Post
{
    // From Monday to Sunday of the current week, in the timezone of the site like the dates of the posts
    $monday = new DateTimeImmutable('monday this week', wp_timezone());
    $week = [
        'after' => $monday->format('Y-m-d 00:00:00'),
        'before' => $monday->modify('+6 days')->format('Y-m-d 23:59:59'),
        'inclusive' => true,
    ];
    $args = [
        'post_type' => 'xtecweekblog',
        'posts_per_page' => 1,
        'orderby' => 'date',
        'date_query' => [$week],
    ];

    //Check if there is a blog published this week
    $weekblog = get_posts($args + ['post_status' => 'publish', 'order' => 'DESC']);
    if (!$weekblog) {
        //Check if there is a blog scheduled for this week
        $weekblog = get_posts($args + ['post_status' => 'future', 'order' => 'ASC']);
    }

    return array_shift($weekblog);
}

/**
 * Validates xtecweekblog.
 *
 * @param int $post_id Post ID.
 *
 * @return bool True if xtecweekblog validates, false otherwise.
 */
function xtecweekblog_validate($post_id): bool
{
    return xtecweekblog_validate_name($post_id)
        && xtecweekblog_validate_description($post_id)
        && xtecweekblog_validate_image($post_id)
        && xtecweekblog_validate_image_size($post_id);
}

/**
 * Validates xtecweekblog name.
 *
 * @param int $post_id Post ID.
 *
 * @return bool True if xtecweekblog name validates, false otherwise.
 */
function xtecweekblog_validate_name($post_id): bool
{
    return (bool)get_id_from_blogname(get_post_meta($post_id, '_xtecweekblog-name', true));
}

/**
 * Validates xtecweekblog description.
 *
 * @param int $post_id Post ID.
 *
 * @return bool True if xtecweekblog description validates, false otherwise.
 */
function xtecweekblog_validate_description($post_id): bool
{
    return (bool)get_post_meta($post_id, '_xtecweekblog-description', true);
}

/**
 * Validates xtecweekblog image.
 *
 * @param int $post_id Post ID.
 *
 * @return bool True if xtecweekblog image validates, false otherwise.
 */
function xtecweekblog_validate_image($post_id): bool
{
    return (bool)get_post_meta($post_id, '_thumbnail_id', true);
}

/**
 * Validates xtecweekblog image size.
 *
 * @param int $post_id Post ID.
 *
 * @return bool True if xtecweekblog image size validates, false otherwise.
 */
function xtecweekblog_validate_image_size($post_id): bool
{
    $thumbnail_id = get_post_meta($post_id, '_thumbnail_id', true);
    $image_attributes = wp_get_attachment_image_src($thumbnail_id, 'full');

    return $image_attributes !== false
        && $image_attributes[1] >= XTECWEEKBLOG_IMAGE_WIDTH
        && $image_attributes[2] >= XTECWEEKBLOG_IMAGE_HEIGHT;
}

add_action('admin_init', 'xtecweekblog_register_settings');
add_action('admin_menu', 'xtecweekblog_admin_menu');

/**
 * Registers the options of the plugin, which are saved by options.php.
 */
function xtecweekblog_register_settings(): void
{
    register_setting('xtecweekblog', 'xtecweekblog_default_msg', [
        'type' => 'string',
        'sanitize_callback' => 'wp_kses_post',
        'default' => '',
    ]);
}

/**
 * Adds plugin options menu.
 */
function xtecweekblog_admin_menu(): void
{
    $page = add_submenu_page(
        'options-general.php',
        __('WeekBlog', 'xtecweekblog'),
        __('WeekBlog', 'xtecweekblog'),
        'manage_options',
        'ms-weekblog',
        'xtecweekblog_options'
    );
    if ($page) {
        add_action('load-' . $page, 'xtecweekblog_add_help_tab');
    }
}

/**
 * Adds the help tab to the options page of the plugin. The current screen is only available once the page is loaded.
 */
function xtecweekblog_add_help_tab(): void
{
    get_current_screen()->add_help_tab([
        'title' => '',
        'id' => 'id1',
        'content' => __('This screen helps you manage the blog of the week.', 'xtecweekblog'),
    ]);
}

/**
 * Output HTML for plugin options page.
 */
function xtecweekblog_options(): void
{
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Weekblog Settings', 'xtecweekblog') ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('xtecweekblog'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="xtecweekblog_default_msg">
                            <?php esc_html_e('Default Message', 'xtecweekblog') ?>
                        </label>
                    </th>
                    <td>
                        <textarea name="xtecweekblog_default_msg" id="xtecweekblog_default_msg" cols="45" rows="4"><?php
                            echo esc_textarea(get_option('xtecweekblog_default_msg')); ?></textarea>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Save Changes', 'xtecweekblog')); ?>
        </form>
    </div>
    <?php
}
