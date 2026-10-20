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

add_action('init', 'xtec_plugin_manager_load_textdomain');
add_action('network_admin_menu', 'xtec_plugin_manager_menu');
add_action('network_admin_edit_xtec-plugin-manager', 'xtec_plugin_manager_save');
add_action('wp_ajax_xtec_plugin_manager_mass', 'xtec_plugin_manager_ajax_mass');

// Number of blogs processed in each request of a mass activation or deactivation
const XTEC_PLUGIN_MANAGER_BATCH = 50;

/**
 * Loads the translations of the plugin.
 */
function xtec_plugin_manager_load_textdomain(): void
{
    load_plugin_textdomain(
        'xtec-plugin-manager',
        false,
        dirname(plugin_basename(XTEC_PLUGIN_MANAGER_FILE)) . '/languages'
    );
}

/**
 * Adds the page to manage the plugins to the menu Plugins of the administration of the network.
 */
function xtec_plugin_manager_menu(): void
{
    $page = add_submenu_page(
        'plugins.php',
        __('Plugin management', 'xtec-plugin-manager'),
        __('Plugin management', 'xtec-plugin-manager'),
        'manage_network_plugins',
        'xtec-plugin-manager',
        'xtec_plugin_manager_page'
    );
    add_action("load-$page", static fn() => add_action('admin_enqueue_scripts', 'xtec_plugin_manager_enqueue'));
}

/**
 * Loads the JavaScript of the mass activation and deactivation.
 */
function xtec_plugin_manager_enqueue(): void
{
    $file = dirname(XTEC_PLUGIN_MANAGER_FILE) . '/xtec-plugin-manager.js';
    // The date of the file is used as version so that browsers load it again when it changes
    wp_enqueue_script(
        'xtec-plugin-manager_js',
        plugin_dir_url(XTEC_PLUGIN_MANAGER_FILE) . 'xtec-plugin-manager.js',
        [],
        (string)filemtime($file),
        true
    );
    wp_localize_script('xtec-plugin-manager_js', 'xtecPluginManager', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('xtec-plugin-manager-mass'),
        /* translators: %s: Name of the plugin. */
        'confirmActivate' => __('Activate %s in all the blogs?', 'xtec-plugin-manager'),
        /* translators: %s: Name of the plugin. */
        'confirmDeactivate' => __('Deactivate %s in all the blogs?', 'xtec-plugin-manager'),
        /* translators: 1: Number of blogs processed, 2: Total number of blogs. */
        'progress' => __('Processed %1$d of %2$d blogs...', 'xtec-plugin-manager'),
        /* translators: 1: Name of the plugin, 2: Number of blogs. */
        'activated' => __('%1$s has been activated in %2$d blogs.', 'xtec-plugin-manager'),
        /* translators: 1: Name of the plugin, 2: Number of blogs. */
        'deactivated' => __('%1$s has been deactivated in %2$d blogs.', 'xtec-plugin-manager'),
        /* translators: %d: Number of blogs. */
        'failed' => __('It could not be done in %d blogs.', 'xtec-plugin-manager'),
        'error' => __('The process has stopped because of an error. Please try again.', 'xtec-plugin-manager'),
    ]);
}

/**
 * Returns the plugins that can be managed in the blogs: the ones that aren't network only nor network activated.
 *
 * @return array The plugins, by their file.
 */
function xtec_plugin_manager_plugins(): array
{
    return array_filter(
        get_plugins(),
        static fn(string $plugin): bool => !is_network_only_plugin($plugin) && !is_plugin_active_for_network($plugin),
        ARRAY_FILTER_USE_KEY
    );
}

/**
 * Shows the page to manage the plugins.
 */
function xtec_plugin_manager_page(): void
{
    $options = [
        XTEC_PLUGIN_MANAGER_NONE => __('None', 'xtec-plugin-manager'),
        XTEC_PLUGIN_MANAGER_USERS => __('Administrators of the blogs', 'xtec-plugin-manager'),
        XTEC_PLUGIN_MANAGER_AUTO => __('Automatic activation', 'xtec-plugin-manager'),
    ];
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Plugin management', 'xtec-plugin-manager'); ?></h1>

        <?php
        if (isset($_GET['updated'])) {
            wp_admin_notice(esc_html__('Settings saved.'), ['type' => 'success', 'dismissible' => true]);
        }
        ?>
        <div id="xtec-plugin-manager-result" aria-live="polite"></div>

        <div class="notice notice-info inline">
            <p>
                <?php
                esc_html_e(
                    'The plugins activated in the network work in all the blogs, and the administrators of the blogs cannot see them. For the other plugins, choose who can activate them:',
                    'xtec-plugin-manager'
                );
                ?>
            </p>
            <ul>
                <li>
                    <strong><?php echo esc_html($options[XTEC_PLUGIN_MANAGER_NONE]); ?></strong>:
                    <?php
                    esc_html_e(
                        'the administrators of the blogs cannot see the plugin nor activate it. Only super admins can activate it in a blog.',
                        'xtec-plugin-manager'
                    );
                    ?>
                </li>
                <li>
                    <strong><?php echo esc_html($options[XTEC_PLUGIN_MANAGER_USERS]); ?></strong>:
                    <?php
                    esc_html_e(
                        'the administrators of the blogs can activate and deactivate the plugin in their blogs.',
                        'xtec-plugin-manager'
                    );
                    ?>
                </li>
                <li>
                    <strong><?php echo esc_html($options[XTEC_PLUGIN_MANAGER_AUTO]); ?></strong>:
                    <?php
                    esc_html_e(
                        'the same, and the plugin is also activated in the new blogs.',
                        'xtec-plugin-manager'
                    );
                    ?>
                </li>
            </ul>
            <p>
                <?php
                esc_html_e(
                    'The blogs where a plugin is active keep it when the access changes. The mass activation and deactivation change the plugin in all the blogs, whatever its access.',
                    'xtec-plugin-manager'
                );
                ?>
            </p>
        </div>

        <form method="post" action="<?php echo esc_url(network_admin_url('edit.php?action=xtec-plugin-manager')); ?>">
            <?php wp_nonce_field('xtec-plugin-manager'); ?>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th scope="col"><?php esc_html_e('Plugin', 'xtec-plugin-manager'); ?></th>
                        <th scope="col"><?php esc_html_e('Who can activate it', 'xtec-plugin-manager'); ?></th>
                        <th scope="col"><?php esc_html_e('All the blogs', 'xtec-plugin-manager'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach (xtec_plugin_manager_plugins() as $plugin => $data) { ?>
                    <tr>
                        <td>
                            <label for="<?php echo esc_attr('xtec-plugin-manager-' . md5($plugin)); ?>">
                                <strong><?php echo esc_html($data['Name']); ?></strong>
                            </label>
                            <br />
                            <?php
                            /* translators: 1: Version of the plugin, 2: Author of the plugin. */
                            $text = __('Version %1$s by %2$s', 'xtec-plugin-manager');
                            echo esc_html(sprintf($text, $data['Version'], wp_strip_all_tags($data['Author'])));
                            ?>
                        </td>
                        <td>
                            <select id="<?php echo esc_attr('xtec-plugin-manager-' . md5($plugin)); ?>"
                                name="access[<?php echo esc_attr($plugin); ?>]">
                                <?php foreach ($options as $value => $label) { ?>
                                    <option value="<?php echo esc_attr($value); ?>"
                                        <?php selected($value, xtec_plugin_manager_access($plugin)); ?>>
                                        <?php echo esc_html($label); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <?php foreach (['activate', 'deactivate'] as $mode) { ?>
                                <button type="button" class="button xtec-plugin-manager-mass"
                                    data-plugin="<?php echo esc_attr($plugin); ?>"
                                    data-name="<?php echo esc_attr($data['Name']); ?>"
                                    data-mode="<?php echo esc_attr($mode); ?>">
                                    <?php
                                    if ($mode === 'activate') {
                                        esc_html_e('Activate in all', 'xtec-plugin-manager');
                                    } else {
                                        esc_html_e('Deactivate in all', 'xtec-plugin-manager');
                                    }
                                    ?>
                                </button>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Saves the access to the plugins when the form of the page is sent, and goes back to the page.
 */
function xtec_plugin_manager_save(): void
{
    check_admin_referer('xtec-plugin-manager');
    if (!current_user_can('manage_network_plugins')) {
        wp_die(esc_html__('Sorry, you are not allowed to access this page.'), '', ['response' => 403]);
    }

    $access = isset($_POST['access']) && is_array($_POST['access']) ? wp_unslash($_POST['access']) : [];
    $options = [
        XTEC_PLUGIN_MANAGER_USERS => XTEC_PLUGIN_MANAGER_USERS_OPTION,
        XTEC_PLUGIN_MANAGER_AUTO => XTEC_PLUGIN_MANAGER_AUTO_OPTION,
    ];
    $plugins = array_keys(xtec_plugin_manager_plugins());

    // The plugins that aren't in the page keep their access, like the ones activated in the network or the ones that
    // aren't installed yet when migrating
    $lists = [];
    foreach ($options as $value => $option) {
        $lists[$value] = array_diff(xtec_plugin_manager_list($option), $plugins);
    }

    // Only the plugins of the page are taken from the form, so that the lists can't have other values
    foreach ($plugins as $plugin) {
        $value = $access[$plugin] ?? '';
        if (is_string($value) && isset($lists[$value])) {
            $lists[$value][] = $plugin;
        }
    }

    foreach ($options as $value => $option) {
        update_site_option($option, array_values($lists[$value]));
    }

    wp_safe_redirect(network_admin_url('plugins.php?page=xtec-plugin-manager&updated=true'));
    exit;
}

/**
 * Activates or deactivates a plugin in a batch of blogs of the network. The page calls it again with the next batch
 * until all the blogs are done, so that a single request doesn't have to process all of them.
 */
function xtec_plugin_manager_ajax_mass(): void
{
    check_ajax_referer('xtec-plugin-manager-mass');
    if (!current_user_can('manage_network_plugins')) {
        wp_send_json_error(null, 403);
    }

    $plugin = isset($_POST['plugin']) && is_string($_POST['plugin']) ? wp_unslash($_POST['plugin']) : '';
    $mode = $_POST['mode'] ?? '';
    $offset = isset($_POST['offset']) ? absint($_POST['offset']) : 0;
    if (!array_key_exists($plugin, xtec_plugin_manager_plugins()) || ($mode !== 'activate' && $mode !== 'deactivate')) {
        wp_send_json_error(null, 400);
    }

    // The same blogs as Multisite Plugin Manager: all the ones of the network that aren't spam
    $query = ['network_id' => get_current_network_id(), 'spam' => 0];
    $total = get_sites($query + ['count' => true]);
    $blogs = get_sites($query + [
        'fields' => 'ids',
        'number' => XTEC_PLUGIN_MANAGER_BATCH,
        'offset' => $offset,
        'orderby' => 'id',
        'order' => 'ASC',
    ]);

    $failed = 0;
    foreach ($blogs as $blog_id) {
        switch_to_blog($blog_id);
        if ($mode === 'activate') {
            // The activation runs the hook of the plugin, which may prepare the blog
            activate_plugin($plugin);
            $failed += is_plugin_active($plugin) ? 0 : 1;
        } else {
            deactivate_plugins($plugin, true);
            $failed += is_plugin_active($plugin) ? 1 : 0;
        }
        restore_current_blog();
    }

    wp_send_json_success([
        'next' => $blogs ? $offset + count($blogs) : $total,
        'total' => $total,
        'failed' => $failed,
    ]);
}
