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

add_action('network_admin_menu', 'xtec_site_cloner_menu');
add_filter('manage_sites_action_links', 'xtec_site_cloner_site_action', 10, 2);

// Fields of the form to clone a blog
const XTEC_SITE_CLONER_FIELDS = ['template', 'slug', 'title', 'admin', 'public'];

/**
 * Adds the page to clone a blog to the menu Sites of the administration of the network.
 */
function xtec_site_cloner_menu(): void
{
    add_submenu_page(
        'sites.php',
        __('Clone a site', 'xtec-site-cloner'),
        __('Clone a site', 'xtec-site-cloner'),
        'manage_sites',
        'xtec-site-cloner',
        'xtec_site_cloner_page'
    );
}

/**
 * Adds the link to clone a blog to the actions of the list of blogs of the network.
 *
 * @param string[] $actions The links of the actions.
 * @param mixed $blog_id The ID of the blog.
 * @return string[] The links of the actions.
 */
function xtec_site_cloner_site_action(array $actions, mixed $blog_id): array
{
    $blog_id = (int)$blog_id;

    if (!is_main_site($blog_id) && current_user_can('manage_sites')) {
        $url = add_query_arg(['page' => 'xtec-site-cloner', 'template' => $blog_id], network_admin_url('sites.php'));
        $actions['xtec-site-cloner'] = '<a href="' . esc_url($url) . '">' . esc_html__('Clone', 'xtec-site-cloner')
            . '</a>';
    }

    return $actions;
}

/**
 * Shows the page to clone a blog and creates the blog when the form is sent.
 */
function xtec_site_cloner_page(): void
{
    if (!current_user_can('manage_sites')) {
        wp_die(esc_html__('Sorry, you are not allowed to access this page.'), '', ['response' => 403]);
    }

    $values = array_fill_keys(XTEC_SITE_CLONER_FIELDS, '');
    $values['template'] = isset($_GET['template']) ? (string)absint($_GET['template']) : '';
    $result = null;

    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
        check_admin_referer('xtec-site-cloner');

        foreach (XTEC_SITE_CLONER_FIELDS as $field) {
            $value = $_POST[$field] ?? '';
            $values[$field] = is_string($value) ? sanitize_text_field(wp_unslash($value)) : '';
        }

        $result = xtec_site_cloner_create_from_form($values);
        if (!is_wp_error($result)) {
            $values = array_fill_keys(XTEC_SITE_CLONER_FIELDS, '');
        }
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Clone a site', 'xtec-site-cloner'); ?></h1>

        <?php
        if (is_wp_error($result)) {
            wp_admin_notice(esc_html($result->get_error_message()), ['type' => 'error']);
        } elseif ($result !== null) {
            $site = get_site($result);
            wp_admin_notice(
                sprintf(
                    /* translators: 1: the link to the new blog, 2: the link to its dashboard */
                    esc_html__('The blog %1$s has been created. %2$s', 'xtec-site-cloner'),
                    '<a href="' . esc_url($site->siteurl) . '">' . esc_html($site->blogname) . '</a>',
                    '<a href="' . esc_url(get_admin_url($result)) . '">' . esc_html__('Dashboard') . '</a>'
                ),
                ['type' => 'success', 'dismissible' => true]
            );
        }
        ?>

        <p>
            <?php
            esc_html_e(
                'The new blog will have the content, the settings, the theme, the plugins and the files of the template. The users of the template are not copied: the administrator of the new blog will be the chosen user.',
                'xtec-site-cloner'
            );
            ?>
        </p>

        <form method="post">
            <?php wp_nonce_field('xtec-site-cloner'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="template"><?php esc_html_e('Template', 'xtec-site-cloner'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="template" name="template" class="regular-text" required
                            value="<?php echo esc_attr($values['template']); ?>" />
                        <p class="description">
                            <?php esc_html_e('ID or name of the blog to clone.', 'xtec-site-cloner'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="slug"><?php esc_html_e('Site Address (URL)'); ?></label></th>
                    <td>
                        <?php
                        $network = get_network();
                        echo is_subdomain_install()
                            ? ''
                            : '<code>' . esc_html($network->domain . $network->path) . '</code>';
                        ?>
                        <input type="text" id="slug" name="slug" class="regular-text" required
                            value="<?php echo esc_attr($values['slug']); ?>" />
                        <?php
                        echo is_subdomain_install()
                            ? '<code>.' . esc_html(preg_replace('|^www\.|', '', $network->domain)) . '</code>'
                            : '';
                        ?>
                        <p class="description">
                            <?php esc_html_e('Only lowercase letters, numbers and hyphens.', 'xtec-site-cloner'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="title"><?php esc_html_e('Site Title'); ?></label></th>
                    <td>
                        <input type="text" id="title" name="title" class="regular-text" required
                            value="<?php echo esc_attr($values['title']); ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="admin"><?php esc_html_e('Administrator', 'xtec-site-cloner'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="admin" name="admin" class="regular-text" required
                            value="<?php echo esc_attr($values['admin']); ?>" />
                        <p class="description">
                            <?php esc_html_e('Username or e-mail address of an existing user.', 'xtec-site-cloner'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="public"><?php esc_html_e('Visibility', 'xtec-site-cloner'); ?></label>
                    </th>
                    <td>
                        <select id="public" name="public">
                            <option value="" <?php selected($values['public'], ''); ?>>
                                <?php esc_html_e('Same as the template', 'xtec-site-cloner'); ?>
                            </option>
                            <option value="1" <?php selected($values['public'], '1'); ?>>
                                <?php esc_html_e('Public', 'xtec-site-cloner'); ?>
                            </option>
                            <option value="0" <?php selected($values['public'], '0'); ?>>
                                <?php esc_html_e('Not public', 'xtec-site-cloner'); ?>
                            </option>
                        </select>
                        <p class="description">
                            <?php
                            esc_html_e(
                                'Search engines are asked not to index the blogs that are not public.',
                                'xtec-site-cloner'
                            );
                            ?>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Clone', 'xtec-site-cloner')); ?>
        </form>
    </div>
    <?php
}

/**
 * Creates a blog with the data of the form. The visibility of the template is copied, unless another one is chosen.
 *
 * @param string[] $values The fields of the form: template, slug, title, admin and public.
 * @return int|WP_Error The ID of the new blog, or the error.
 */
function xtec_site_cloner_create_from_form(array $values): int|WP_Error
{
    $template = xtec_site_cloner_find_site($values['template']);
    if ($template === null) {
        return new WP_Error('xtec_site_cloner_template', __('The template blog does not exist.', 'xtec-site-cloner'));
    }

    $admin = xtec_site_cloner_find_user($values['admin']);
    if ($admin === null) {
        return new WP_Error('xtec_site_cloner_admin', __('The administrator does not exist.', 'xtec-site-cloner'));
    }

    if ($values['title'] === '') {
        return new WP_Error('xtec_site_cloner_title', __('The title of the blog is required.', 'xtec-site-cloner'));
    }

    $blog_id = xtec_site_cloner_create_site((int)$template->id, $values['slug'], $values['title'], $admin->ID);

    // The visibility is kept both in the table of the blogs and in the options of the blog, which WordPress updates
    if (!is_wp_error($blog_id) && in_array($values['public'], ['0', '1'], true)) {
        wp_update_site($blog_id, ['public' => (int)$values['public']]);
    }

    return $blog_id;
}
