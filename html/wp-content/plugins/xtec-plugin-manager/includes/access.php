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

add_filter('all_plugins', 'xtec_plugin_manager_hide_plugins');
add_filter('map_meta_cap', 'xtec_plugin_manager_map_meta_cap', 10, 4);
add_filter('plugin_row_meta', 'xtec_plugin_manager_hide_plugin_meta');
// The REST API lists the plugins without the filter all_plugins
add_filter('rest_request_before_callbacks', 'xtec_plugin_manager_rest_hide_plugin', 10, 3);
add_filter('rest_post_dispatch', 'xtec_plugin_manager_rest_hide_plugins', 10, 3);
// After WordPress has created the tables of the new blog
add_action('wp_initialize_site', 'xtec_plugin_manager_activate_in_new_blog', 100);

// The options are the ones of Multisite Plugin Manager, so that its settings are kept
const XTEC_PLUGIN_MANAGER_USERS_OPTION = 'pm_user_control_list';
const XTEC_PLUGIN_MANAGER_AUTO_OPTION = 'pm_auto_activate_list';

// Access of the administrators of the blogs to a plugin
const XTEC_PLUGIN_MANAGER_NONE = 'none';
const XTEC_PLUGIN_MANAGER_USERS = 'all';
const XTEC_PLUGIN_MANAGER_AUTO = 'auto';

/**
 * Returns a list of plugins kept in a network option.
 *
 * @param string $option The name of the option.
 * @return string[] The files of the plugins. Multisite Plugin Manager kept the value EMPTY for an empty list.
 */
function xtec_plugin_manager_list(string $option): array
{
    $list = get_site_option($option, []);

    return is_array($list) ? array_values(array_filter($list, 'is_string')) : [];
}

/**
 * Returns the access of the administrators of the blogs to a plugin.
 *
 * @param string $plugin The file of the plugin.
 * @return string XTEC_PLUGIN_MANAGER_NONE, XTEC_PLUGIN_MANAGER_USERS or XTEC_PLUGIN_MANAGER_AUTO.
 */
function xtec_plugin_manager_access(string $plugin): string
{
    if (in_array($plugin, xtec_plugin_manager_list(XTEC_PLUGIN_MANAGER_AUTO_OPTION), true)) {
        return XTEC_PLUGIN_MANAGER_AUTO;
    }
    if (in_array($plugin, xtec_plugin_manager_list(XTEC_PLUGIN_MANAGER_USERS_OPTION), true)) {
        return XTEC_PLUGIN_MANAGER_USERS;
    }

    return XTEC_PLUGIN_MANAGER_NONE;
}

/**
 * Removes the plugins that the administrators of the blogs can't activate from the list of plugins of the blog.
 * Super admins see all the plugins.
 *
 * @param array $plugins The plugins, by their file.
 * @return array The plugins that the current user can activate.
 */
function xtec_plugin_manager_hide_plugins(array $plugins): array
{
    if (is_super_admin()) {
        return $plugins;
    }

    return array_filter(
        $plugins,
        static fn(string $plugin): bool => xtec_plugin_manager_access($plugin) !== XTEC_PLUGIN_MANAGER_NONE,
        ARRAY_FILTER_USE_KEY
    );
}

/**
 * Prevents the administrators of the blogs from activating or deactivating the plugins that they can't manage, also
 * with a request made by hand, as hiding them from the list doesn't prevent it.
 *
 * @param string[] $caps The primitive capabilities required.
 * @param string $cap The capability checked.
 * @param int $user_id The user ID.
 * @param array $args The arguments of the check: the file of the plugin.
 * @return string[] The primitive capabilities required.
 */
function xtec_plugin_manager_map_meta_cap(array $caps, string $cap, int $user_id, array $args): array
{
    if (
        ($cap === 'activate_plugin' || $cap === 'deactivate_plugin')
        && isset($args[0]) && is_string($args[0])
        && !is_super_admin($user_id)
        && xtec_plugin_manager_access($args[0]) === XTEC_PLUGIN_MANAGER_NONE
    ) {
        $caps[] = 'do_not_allow';
    }

    return $caps;
}

/**
 * Hides the version, the author and the link to the details of the plugins from the administrators of the blogs.
 *
 * @param string[] $meta The links of the plugin.
 * @return string[] The links shown.
 */
function xtec_plugin_manager_hide_plugin_meta(array $meta): array
{
    return is_super_admin() ? $meta : [];
}

/**
 * Checks if the current user can see a plugin in the REST API. The lists name the plugins by their file without the
 * extension, while the parameter of the routes of a plugin already has it.
 *
 * @param string $plugin The name of the plugin in the REST API.
 * @return bool True if the user can see it.
 */
function xtec_plugin_manager_rest_can_see(string $plugin): bool
{
    $file = str_ends_with($plugin, '.php') ? $plugin : $plugin . '.php';

    return is_super_admin() || xtec_plugin_manager_access($file) !== XTEC_PLUGIN_MANAGER_NONE;
}

/**
 * Answers that a plugin doesn't exist when the administrator of a blog asks for one that is hidden in the REST API,
 * before it is read, activated or deactivated.
 *
 * @param mixed $response The response, or null to go on with the request.
 * @param array $handler The handler of the route.
 * @param WP_REST_Request $request The request.
 * @return mixed The same response, or the error if the plugin is hidden.
 */
function xtec_plugin_manager_rest_hide_plugin(mixed $response, array $handler, WP_REST_Request $request): mixed
{
    $plugin = $request['plugin'];
    if (
        str_starts_with($request->get_route(), '/wp/v2/plugins/')
        && is_string($plugin) && !xtec_plugin_manager_rest_can_see($plugin)
    ) {
        return new WP_Error('rest_plugin_not_found', __('Plugin not found.'), ['status' => 404]);
    }

    return $response;
}

/**
 * Removes the hidden plugins from the list of plugins of the REST API.
 *
 * @param WP_HTTP_Response $response The response.
 * @param WP_REST_Server $server The server.
 * @param WP_REST_Request $request The request.
 * @return WP_HTTP_Response The response without the hidden plugins.
 */
function xtec_plugin_manager_rest_hide_plugins(
    WP_HTTP_Response $response,
    WP_REST_Server $server,
    WP_REST_Request $request
): WP_HTTP_Response {
    $plugins = $response->get_data();
    if (
        $request->get_route() !== '/wp/v2/plugins' || is_super_admin()
        || $response->get_status() >= 400 || !is_array($plugins)
    ) {
        return $response;
    }

    $response->set_data(array_values(array_filter(
        $plugins,
        static fn(mixed $plugin): bool => is_array($plugin) && is_string($plugin['plugin'] ?? null)
            && xtec_plugin_manager_rest_can_see($plugin['plugin'])
    )));

    return $response;
}

/**
 * Activates the plugins of automatic activation in a new blog.
 *
 * @param WP_Site $site The new blog.
 */
function xtec_plugin_manager_activate_in_new_blog(WP_Site $site): void
{
    require_once ABSPATH . 'wp-admin/includes/plugin.php';

    // Only the plugins that are installed, as the list may have plugins that have been removed
    $plugins = array_intersect(xtec_plugin_manager_list(XTEC_PLUGIN_MANAGER_AUTO_OPTION), array_keys(get_plugins()));
    if (!$plugins) {
        return;
    }

    switch_to_blog($site->id);
    activate_plugins($plugins);
    restore_current_blog();
}
