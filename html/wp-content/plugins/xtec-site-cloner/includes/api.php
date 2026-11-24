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

add_action('init', 'xtec_site_cloner_load_textdomain');
// After WordPress creates the tables of the new blog (10) and before the plugins of automatic activation are activated
// (100), so that they are activated in the copied blog
add_action('wp_initialize_site', 'xtec_site_cloner_initialize_site', 50, 2);

// The global $xtec_site_cloner_request keeps the creation of a blog by xtec_site_cloner_create_site() that is in
// progress: the template and the error of the copy. It isn't initialized here because WP-CLI loads the plugins inside
// a function, where the variables of this file wouldn't be global.

/**
 * Loads the translations of the plugin.
 */
function xtec_site_cloner_load_textdomain(): void
{
    load_plugin_textdomain('xtec-site-cloner', false, dirname(plugin_basename(XTEC_SITE_CLONER_FILE)) . '/languages');
}

/**
 * Creates a blog from a template blog. The administrator of the new blog is the given user, and the users of the
 * template aren't copied. If the copy fails, the new blog is deleted.
 *
 * The permissions of the current user aren't checked: it is up to the caller. The visibility of the new blog is the
 * one of the template, whatever $options says.
 *
 * @param int $template_id The ID of the template blog.
 * @param string $slug The name of the new blog in its address.
 * @param string $title The title of the new blog.
 * @param int $admin_id The ID of the administrator of the new blog.
 * @param array $options The options of the new blog, as in wpmu_create_blog().
 * @return int|WP_Error The ID of the new blog, or the error.
 */
function xtec_site_cloner_create_site(
    int $template_id,
    string $slug,
    string $title,
    int $admin_id,
    array $options = []
): int|WP_Error {
    global $xtec_site_cloner_request;

    $slug = strtolower(trim($slug));
    $error = xtec_site_cloner_validate_slug($slug);
    if ($error !== null) {
        return $error;
    }

    $template = get_site($template_id);
    if ($template === null || is_main_site($template_id)) {
        return new WP_Error('xtec_site_cloner_template', __('The template blog does not exist.', 'xtec-site-cloner'));
    }

    if (get_userdata($admin_id) === false) {
        return new WP_Error('xtec_site_cloner_admin', __('The administrator does not exist.', 'xtec-site-cloner'));
    }

    [$domain, $path] = xtec_site_cloner_site_address($slug);

    // The copy is done by xtec_site_cloner_initialize_site() while WordPress initializes the blog
    $xtec_site_cloner_request = ['template_id' => $template_id, 'error' => null];
    $blog_id = wpmu_create_blog($domain, $path, $title, $admin_id, $options, get_current_network_id());
    $error = $xtec_site_cloner_request['error'];
    $xtec_site_cloner_request = null;

    if (is_wp_error($blog_id)) {
        return $blog_id;
    }

    if ($error !== null) {
        xtec_site_cloner_delete_site($blog_id);
        return $error;
    }

    return $blog_id;
}

/**
 * Copies the template into a new blog while WordPress initializes it, if there is a template for the blog: the one of
 * xtec_site_cloner_create_site() or the one of the filter xtec_site_cloner_template, which allows other plugins to copy
 * a template into the blogs created by WordPress, like the ones of the sign-up.
 *
 * If the copy fails, xtec_site_cloner_create_site() gets the error. Otherwise, as the code that creates the blog
 * can't know it, the blog is deleted and the request is finished with an error.
 *
 * @param WP_Site $site The new blog.
 * @param array $args The arguments of the initialization of the blog.
 */
function xtec_site_cloner_initialize_site(WP_Site $site, array $args): void
{
    global $xtec_site_cloner_request;

    /**
     * Filters the template blog that is copied into a new blog.
     *
     * @param int $template_id The ID of the template blog, or 0 if no template is copied.
     * @param WP_Site $site The new blog.
     * @param array $args The arguments of the initialization of the blog.
     */
    $template_id = (int)apply_filters(
        'xtec_site_cloner_template',
        (int)($xtec_site_cloner_request['template_id'] ?? 0),
        $site,
        $args
    );
    if ($template_id === 0) {
        return;
    }

    $error = xtec_site_cloner_copy($template_id, (int)$site->id);
    if ($error === null) {
        return;
    }

    error_log(sprintf(
        'XTEC Site Cloner: could not copy the blog %d into the blog %d: %s',
        $template_id,
        $site->id,
        $error->get_error_message()
    ));

    if ($xtec_site_cloner_request !== null) {
        $xtec_site_cloner_request['error'] = $error;
        return;
    }

    xtec_site_cloner_delete_site((int)$site->id);
    wp_die(
        esc_html__('The blog could not be created. Please try again later.', 'xtec-site-cloner'),
        esc_html__('Error creating the blog', 'xtec-site-cloner'),
        ['response' => 500, 'back_link' => true]
    );
}

/**
 * Deletes a blog whose copy has failed, with all its tables. WordPress only drops the tables that it knows, and the
 * copy may have created tables of plugins.
 *
 * WordPress also deletes the folder of the files of the blog, the one of its option upload_path. It is only deleted if
 * it is the folder of the blog, whose name ends with its ID (blogs.dir/ID/files or uploads/sites/ID), so that a wrong
 * upload_path can't delete the files of another blog, like the template.
 *
 * @param int $blog_id The ID of the blog.
 */
function xtec_site_cloner_delete_site(int $blog_id): void
{
    $drop_all_tables = static fn(array $tables, int $id): array => $id === $blog_id
        ? array_values(array_unique(array_merge($tables, xtec_site_cloner_get_tables($blog_id))))
        : $tables;

    // An empty folder makes WordPress delete nothing
    $only_own_folder = static function (string $dir, int $id) use ($blog_id): string {
        if ($id !== $blog_id || preg_match('#/' . $blog_id . '(/files)?/?$#', $dir)) {
            return $dir;
        }

        error_log("XTEC Site Cloner: the folder $dir has not been deleted with the blog $blog_id, as it isn't its own");

        return '';
    };

    add_filter('wpmu_drop_tables', $drop_all_tables, 10, 2);
    add_filter('wpmu_delete_blog_upload_dir', $only_own_folder, 10, 2);
    wp_delete_site($blog_id);
    remove_filter('wpmu_delete_blog_upload_dir', $only_own_folder);
    remove_filter('wpmu_drop_tables', $drop_all_tables);
}

/**
 * Checks the name of a new blog: lowercase letters, numbers and hyphens, and not a name reserved by WordPress or by
 * the settings of the network.
 *
 * @param string $slug The name of the blog in its address.
 * @return WP_Error|null The error, or null if the name is valid.
 */
function xtec_site_cloner_validate_slug(string $slug): ?WP_Error
{
    if (!preg_match('/^[a-z0-9][a-z0-9-]*$/', $slug)) {
        return new WP_Error(
            'xtec_site_cloner_slug',
            __('The name of the blog can only contain lowercase letters, numbers and hyphens.', 'xtec-site-cloner')
        );
    }

    $reserved = array_merge(
        (array)get_site_option('illegal_names', []),
        is_subdomain_install() ? [] : get_subdirectory_reserved_names()
    );
    if (in_array($slug, $reserved, true)) {
        return new WP_Error('xtec_site_cloner_slug', __('That name is not allowed.', 'xtec-site-cloner'));
    }

    return null;
}

/**
 * Returns the domain and the path of a blog of the network from its name.
 *
 * @param string $slug The name of the blog in its address.
 * @return string[] The domain and the path.
 */
function xtec_site_cloner_site_address(string $slug): array
{
    $network = get_network();

    if (is_subdomain_install()) {
        return [$slug . '.' . preg_replace('|^www\.|', '', $network->domain), $network->path];
    }

    return [$network->domain, $network->path . $slug . '/'];
}

/**
 * Returns a blog from its ID or its name.
 *
 * @param string $site The ID or the name of the blog.
 * @return WP_Site|null The blog, or null if it doesn't exist.
 */
function xtec_site_cloner_find_site(string $site): ?WP_Site
{
    $site = strtolower(trim($site, " \n\r\t\v\0/"));
    if ($site === '') {
        return null;
    }

    if (ctype_digit($site)) {
        return get_site((int)$site);
    }

    [$domain, $path] = xtec_site_cloner_site_address($site);
    $sites = get_sites(['domain' => $domain, 'path' => $path, 'number' => 1]);

    return $sites[0] ?? null;
}

/**
 * Returns a user from their username or their e-mail address.
 *
 * @param string $user The username or the e-mail address.
 * @return WP_User|null The user, or null if they don't exist.
 */
function xtec_site_cloner_find_user(string $user): ?WP_User
{
    $user = trim($user);
    if ($user === '') {
        return null;
    }

    $found = get_user_by(is_email($user) ? 'email' : 'login', $user);

    return $found === false ? null : $found;
}
