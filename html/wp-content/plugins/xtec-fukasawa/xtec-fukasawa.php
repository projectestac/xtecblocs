<?php

/**
 * Plugin Name:       XTEC Fukasawa
 * Description:       Customizations of XTECBlocs for the theme Fukasawa, without changing the files of the theme.
 * Version:           1.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Network:           true
 * Text Domain:       xtec-fukasawa
 * Domain Path:       /languages
 * Author:            Toni Ginard
 * License:           GPL v2
 * License URI:       https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * The same customizations as the version 1.10 of the theme had in XTECBlocs: the widgets of WordPress instead of the
 * ones of the theme, no main menu in the sidebar, custom background, categories and tags in the list of posts and the
 * styles of the project DOSSIER. They only apply to the blogs that use the theme. The blogs of the project DOSSIER have
 * the option xtec_blogtype set to 'epa', as they are copies of a template blog: they also have the link to the page
 * "About this space" and the item "Pendents" of the menu shown as an ellipsis.
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

const XTEC_FUKASAWA_FILE = __FILE__;

add_action('init', 'xtec_fukasawa_load_textdomain');
add_action('after_setup_theme', 'xtec_fukasawa_setup', 11);
// The theme loads its stylesheet in this action, so the one of the plugin is loaded after it
add_action('wp_print_styles', 'xtec_fukasawa_enqueue_style', 11);
add_filter('the_excerpt', 'xtec_fukasawa_add_terms', 20);
add_filter('wp_nav_menu_objects', 'xtec_fukasawa_pending_menu_item');
// The theme doesn't allow adding content to its credits, so the link is added after the widgets of the sidebar
add_action('dynamic_sidebar_after', 'xtec_fukasawa_about_link');

/**
 * The theme only defines this function if it doesn't exist, and the plugins are loaded before the theme. So the
 * widgets of the theme aren't registered.
 */
function fukasawa_widget_registration(): void
{
}

/**
 * The theme only defines this function if it doesn't exist. So the widgets of WordPress for the recent posts and
 * comments aren't replaced by the ones of the theme, and the widgets that the blogs already have are still shown.
 */
function fukasawa_unregister_default_widgets(): void
{
}

/**
 * Loads the translations of the plugin.
 */
function xtec_fukasawa_load_textdomain(): void
{
    load_plugin_textdomain('xtec-fukasawa', false, dirname(plugin_basename(XTEC_FUKASAWA_FILE)) . '/languages');
}

/**
 * Checks if the blog uses the theme Fukasawa, either directly or as the parent of a child theme.
 *
 * @return bool True if the blog uses the theme.
 */
function xtec_fukasawa_is_active(): bool
{
    return get_template() === 'fukasawa';
}

/**
 * Checks if the blog is one of the project DOSSIER, which uses the theme Fukasawa.
 *
 * @return bool True if the blog is one of the project.
 */
function xtec_fukasawa_is_dossier(): bool
{
    return xtec_fukasawa_is_active() && get_option('xtec_blogtype') === 'epa';
}

/**
 * Allows the blogs to change the background, with the same default color as the theme.
 */
function xtec_fukasawa_setup(): void
{
    if (xtec_fukasawa_is_active()) {
        add_theme_support('custom-background', ['default-color' => 'f2f2f2']);
    }
}

/**
 * Loads the styles of the plugin, with the accent color of the theme for the categories and the tags.
 */
function xtec_fukasawa_enqueue_style(): void
{
    if (is_admin() || !xtec_fukasawa_is_active()) {
        return;
    }

    $file = 'xtec-fukasawa.css';
    // The date of the file is used as version so that browsers load it again when it changes
    wp_enqueue_style(
        'xtec-fukasawa',
        plugin_dir_url(XTEC_FUKASAWA_FILE) . $file,
        ['fukasawa_style'],
        (string)filemtime(dirname(XTEC_FUKASAWA_FILE) . '/' . $file)
    );

    $color = sanitize_hex_color((string)get_theme_mod('accent_color', ''));
    if ($color) {
        wp_add_inline_style(
            'xtec-fukasawa',
            '.entry-categories, .entry-tags, .entry-categories:before, .entry-tags:before, '
            . '.widget_nav_menu .menu .current-menu-item:before, .widget_nav_menu .menu .current_page_item:before '
            . '{ color: ' . $color . '; }'
        );
    }
}

/**
 * Adds the categories and the tags of the post after its excerpt, in the list of posts.
 *
 * @param string $excerpt The HTML of the excerpt.
 * @return string The HTML of the excerpt with the categories and the tags.
 */
function xtec_fukasawa_add_terms(string $excerpt): string
{
    if (is_admin() || is_feed() || is_singular() || !in_the_loop() || !xtec_fukasawa_is_active()) {
        return $excerpt;
    }

    $terms = '';
    $categories = get_the_category_list(', ');
    if ($categories) {
        $terms .= '<span class="entry-categories">' . $categories . '</span> ';
    }
    $tags = get_the_tag_list('', ', ');
    if (is_string($tags) && $tags !== '') {
        $terms .= '<span class="entry-tags">' . $tags . '</span>';
    }

    return $terms === '' ? $excerpt : $excerpt . '<p class="entry-terms">' . trim($terms) . '</p>';
}

/**
 * Shows the item "Pendents" of the menus as an ellipsis in the blogs of the project DOSSIER. The name is kept for the
 * screen readers.
 *
 * @param array $items The items of the menu.
 * @return array The items of the menu.
 */
function xtec_fukasawa_pending_menu_item(array $items): array
{
    if (!xtec_fukasawa_is_dossier()) {
        return $items;
    }

    foreach ($items as $item) {
        if (strcasecmp(trim($item->title), 'pendents') === 0) {
            $item->title = '<span class="genericon genericon-ellipsis" aria-hidden="true"></span>'
                . '<span class="screen-reader-text">' . esc_html($item->title) . '</span>';
        }
    }

    return $items;
}

/**
 * Shows the link to the page "About this space" after the widgets of the sidebar in the blogs of the project DOSSIER.
 *
 * @param int|string $index The identifier of the sidebar.
 */
function xtec_fukasawa_about_link(int|string $index): void
{
    // The action also runs in the page of the widgets of the administration
    if ($index !== 'sidebar' || is_admin() || !xtec_fukasawa_is_dossier()) {
        return;
    }

    echo '<div class="xtec-fukasawa-about"><p>'
        . '<span class="genericon genericon-info" aria-hidden="true"></span> '
        . '<a href="' . esc_url(home_url('/sobre-aquest-espai/')) . '">'
        . esc_html__('About this space', 'xtec-fukasawa')
        . '</a></p></div>';
}
