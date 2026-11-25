<?php

/**
 * Plugin Name:       XTEC Eduhack
 * Description:       Theme and settings of the blogs of the EduHack projects.
 * Version:           2.0
 * Requires at least: 6.1
 * Requires PHP:      8.0
 * Author:            Joan Sala Soler
 * Text Domain:       xtec-eduhack
 * Domain Path:       /languages
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Rewritten for WordPress 7.x by the Departament d'Educació i Formació Professional. The blogs of the projects are no
 * longer created, so the form and the copy of the template have been removed. The plugin keeps what the existing
 * blogs use: the theme eduhack, the image and the color of the categories, the classes of the widgets of each phase,
 * which the plugin Widget Options added, and the restrictions of the administration and of the template.
 */

/**
 * Copyright 2026 Departament d'Educació i Formació Professional
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

defined('ABSPATH') || exit;

const XTEC_EDUHACK_FILE = __FILE__;
const XTEC_EDUHACK_VERSION = '2.0';

// The theme eduhack is in the plugin, where the blogs have always found it (option stylesheet_root)
register_theme_directory(__DIR__ . '/themes');

add_action('init', 'xtec_eduhack_load_textdomain');
add_action('wp_loaded', 'xtec_eduhack_protect_template');
add_action('admin_menu', 'xtec_eduhack_admin_menu', 1000);
add_action('customize_register', 'xtec_eduhack_customize_register', 1000);
add_action('admin_enqueue_scripts', 'xtec_eduhack_admin_scripts');
add_action('category_edit_form_fields', 'xtec_eduhack_category_fields');
add_action('edited_category', 'xtec_eduhack_save_category_fields');
add_filter('dynamic_sidebar_params', 'xtec_eduhack_widget_classes');
add_filter('widget_update_callback', 'xtec_eduhack_keep_widget_classes', 10, 3);

/**
 * Loads the translations of the plugin and of the theme.
 */
function xtec_eduhack_load_textdomain(): void
{
    load_plugin_textdomain('xtec-eduhack', false, dirname(plugin_basename(XTEC_EDUHACK_FILE)) . '/languages');
}

/**
 * Checks if the current blog is the template of the projects.
 *
 * @return bool True if it is the template.
 */
function xtec_eduhack_is_template(): bool
{
    return (int)get_site_option('eduhack_template_id') === get_current_blog_id();
}

/**
 * Sends the visitors of the template who aren't members of it nor super admins to the main page of the network.
 */
function xtec_eduhack_protect_template(): void
{
    if (xtec_eduhack_is_template() && !is_super_admin() && !is_user_member_of_blog()) {
        wp_safe_redirect(network_home_url());
        exit;
    }
}

/**
 * Hides the menus to change the theme and the categories from the administrators of the blogs. The template keeps
 * them, as its members prepare the categories.
 */
function xtec_eduhack_admin_menu(): void
{
    if (is_main_site() || is_super_admin() || xtec_eduhack_is_template()) {
        return;
    }

    remove_submenu_page('themes.php', 'themes.php');
    remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=category');
}

/**
 * Removes the section to change the theme from the customizer, except for super admins.
 *
 * @param WP_Customize_Manager $wp_customize The customizer.
 */
function xtec_eduhack_customize_register(WP_Customize_Manager $wp_customize): void
{
    if (!is_main_site() && !is_super_admin()) {
        $wp_customize->remove_section('themes');
    }
}

/**
 * Loads the color picker of the color of the categories.
 *
 * @param string $hook_suffix The page of the administration.
 */
function xtec_eduhack_admin_scripts(string $hook_suffix): void
{
    if ($hook_suffix !== 'term.php' || get_current_screen()?->taxonomy !== 'category') {
        return;
    }

    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script(
        'xtec-eduhack-color-picker',
        plugins_url('scripts/color-picker.js', XTEC_EDUHACK_FILE),
        ['wp-color-picker'],
        XTEC_EDUHACK_VERSION,
        true
    );
}

/**
 * Shows the fields of the image and the color of a category, which the theme shows with the posts of the category.
 *
 * @param WP_Term $term The category.
 */
function xtec_eduhack_category_fields(WP_Term $term): void
{
    $image = get_term_meta($term->term_id, 'xtec_image', true);
    $color = get_term_meta($term->term_id, 'xtec_color', true);
    ?>
    <tr class="form-field">
        <th scope="row"><label for="xtec_image"><?php esc_html_e('Image URL', 'xtec-eduhack'); ?></label></th>
        <td>
            <input id="xtec_image" name="xtec_image" value="<?php echo esc_attr($image); ?>" placeholder="http://"
                type="text">
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="xtec_color"><?php esc_html_e('Color', 'xtec-eduhack'); ?></label></th>
        <td>
            <input id="xtec_color" name="xtec_color" value="<?php echo esc_attr($color); ?>" type="text"
                class="eduhak-color-picker">
        </td>
    </tr>
    <?php
}

/**
 * Saves the image and the color of a category. WordPress has already checked the nonce and the permissions.
 *
 * @param int $term_id The ID of the category.
 */
function xtec_eduhack_save_category_fields(int $term_id): void
{
    if (isset($_POST['xtec_image']) && is_string($_POST['xtec_image'])) {
        update_term_meta($term_id, 'xtec_image', esc_url_raw(wp_unslash($_POST['xtec_image']), ['http', 'https']));
    }

    if (isset($_POST['xtec_color']) && is_string($_POST['xtec_color'])) {
        $color = wp_unslash($_POST['xtec_color']);
        if ($color === '' || sanitize_hex_color($color) !== null) {
            update_term_meta($term_id, 'xtec_color', $color);
        }
    }
}

/**
 * Adds to a widget the classes that were chosen with the plugin Widget Options, which the theme uses to show the menu
 * of each phase with its color. The settings of the plugin are kept in each widget.
 *
 * @param array $params The parameters of the widget: the arguments of the sidebar and the number of the widget.
 * @return array The parameters, with the classes added to the container of the widget.
 */
function xtec_eduhack_widget_classes(array $params): array
{
    global $wp_registered_widgets;

    $widget_id = $params[0]['widget_id'] ?? '';
    $widget = $wp_registered_widgets[$widget_id]['callback'][0] ?? null;
    if (!$widget instanceof WP_Widget) {
        return $params;
    }

    $instances = $widget->get_settings();
    $number = $wp_registered_widgets[$widget_id]['params'][0]['number'] ?? null;
    $settings = $instances[$number]['extended_widget_opts-' . $widget_id] ?? [];
    $classes = $settings['class']['predefined'] ?? [];

    $classes = is_array($classes) ? array_filter(array_map('sanitize_html_class', $classes)) : [];
    if ($classes) {
        $params[0]['before_widget'] = preg_replace(
            '/class="/',
            'class="' . esc_attr(implode(' ', $classes)) . ' ',
            $params[0]['before_widget'],
            1
        );
    }

    return $params;
}

/**
 * Keeps the settings of the plugin Widget Options when a widget is saved, as the widgets only save their own settings
 * and the plugin isn't active any more.
 *
 * @param array|false $instance The settings to save, or false to cancel the saving.
 * @param array $new_instance The settings sent by the form.
 * @param array $old_instance The previous settings.
 * @return array|false The settings to save.
 */
function xtec_eduhack_keep_widget_classes(
    array|false $instance,
    array $new_instance,
    array $old_instance = []
): array|false {
    if ($instance === false) {
        return false;
    }

    foreach ($old_instance as $key => $value) {
        if (is_string($key) && str_starts_with($key, 'extended_widget_opts-') && !isset($instance[$key])) {
            $instance[$key] = $value;
        }
    }

    return $instance;
}
