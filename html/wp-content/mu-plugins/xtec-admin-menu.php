<?php

/**
 * Plugin Name:       XTEC Admin Menu
 * Description:       Shows the menu of the administration always expanded.
 * Version:           1.0
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            Toni Ginard
 * License:           GPL v2
 * License URI:       https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
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

add_action('admin_head', 'xtec_admin_menu_expand');
add_action('enqueue_block_editor_assets', 'xtec_admin_menu_exit_fullscreen');

/**
 * Expands the menu of the administration. WordPress folds it when the user has collapsed it (setting 'mfold') and,
 * unless the setting 'unfold' is set, when the window is between 783 and 960 pixels wide. Both settings are changed
 * only for the current request, right before WordPress uses them to set the classes of the body, so the preferences
 * of the user aren't saved.
 */
function xtec_admin_menu_expand(): void
{
    global $_updated_user_settings;

    $settings = get_all_user_settings();
    $settings['mfold'] = 'o';
    $settings['unfold'] = '1';
    $_updated_user_settings = $settings;
}

/**
 * Turns off the fullscreen mode of the block editor of the posts, which hides the menu of the administration. The mode
 * is a preference of the user, so it is turned off once the editor is loaded only if it is on.
 */
function xtec_admin_menu_exit_fullscreen(): void
{
    $script = <<<'JS'
wp.domReady(function () {
    window._wpLoadBlockEditor.then(function () {
        if (wp.data.select('core/preferences').get('core/edit-post', 'fullscreenMode')) {
            wp.data.dispatch('core/preferences').set('core/edit-post', 'fullscreenMode', false);
        }
    });
});
JS;

    // The script of the post editor is only loaded in the post editor, not in the site editor
    wp_add_inline_script('wp-edit-post', $script);
}
