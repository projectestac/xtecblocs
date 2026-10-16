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

// Creation of blogs in wp-signup.php
add_action('preprocess_signup_form', 'xtec_users_preprocess_signup_form');
add_filter('wpmu_validate_blog_signup', 'xtec_users_validate_blog_signup');
add_action('wpmu_options', 'xtec_users_network_options');
add_action('update_wpmu_options', 'xtec_users_update_network_options');

/**
 * Returns the maximum number of blogs that a user can create in 24 hours. With 0, only super admins can create blogs.
 *
 * @return int The maximum number of blogs per day.
 */
function xtec_users_max_blogs_per_day(): int
{
    return (int)get_site_option('xtec_signup_maxblogsday', 0);
}

/**
 * Returns the blogs of a user that have been created in the last 24 hours.
 *
 * @param int $user_id The user ID.
 * @return int[] The IDs of the blogs.
 */
function xtec_users_blogs_created_today(int $user_id): array
{
    // The date of registration of the blogs is in UTC
    $since = gmdate('Y-m-d H:i:s', time() - DAY_IN_SECONDS);
    $blogs = [];

    // get_blogs_of_user() leaves the blogs in the cache, so get_site() doesn't query the database
    foreach (get_blogs_of_user($user_id, true) as $blog) {
        if (get_site($blog->userblog_id)->registered > $since) {
            $blogs[] = (int)$blog->userblog_id;
        }
    }

    return $blogs;
}

/**
 * Checks if the current user can create a blog. Only XTEC users can, and only up to a maximum of blogs per day.
 *
 * @return array The reason why the user can't create a blog (not_xtec or limit, or empty if the user can create it)
 *               and, if the limit has been reached, the IDs of the blogs created in the last 24 hours.
 */
function xtec_users_signup_restriction(): array
{
    $limit = xtec_users_max_blogs_per_day();

    if (!is_user_logged_in() || !xtec_users_current_user_can('create_blogs', $limit)) {
        return ['reason' => 'not_xtec', 'blogs' => []];
    }

    $blogs = xtec_users_current_user_can('create_blog_today', $limit);

    return ['reason' => $blogs ? 'limit' : '', 'blogs' => $blogs];
}

/**
 * Shows why the current user can't create a blog instead of the form of wp-signup.php, which is not shown.
 */
function xtec_users_preprocess_signup_form(): void
{
    $restriction = xtec_users_signup_restriction();
    if ($restriction['reason'] === '') {
        return;
    }

    if ($restriction['reason'] === 'not_xtec') {
        echo '<p class="mu_alert">Només poden crear blocs els usuaris XTEC.</p>';
    } else {
        $limit = xtec_users_max_blogs_per_day();
        echo '<h2>Límit de creació excedit</h2>';
        echo '<p>El límit de creació de blocs diari és <strong>' . $limit . '</strong>. Si voleu crear-ne més avui, '
            . "abans n'haureu d'eliminar algun.</p>";
        echo '<p>Blocs creats en les darreres 24 hores:</p><ul>';
        foreach ($restriction['blogs'] as $blog_id) {
            echo "<li><a href='" . esc_url(get_blog_option($blog_id, 'siteurl')) . "'>"
                . esc_html(get_blog_option($blog_id, 'blogname')) . '</a></li>';
        }
        echo '</ul>';
    }

    // The rest of wp-signup.php is skipped, so it is finished in the same way: closing the containers of the form and
    // showing the footer
    echo '</div></div>';
    do_action('after_signup_form');
    get_footer('wp-signup');
    exit;
}

/**
 * Prevents the creation of a blog when the current user can't create it. The form of wp-signup.php isn't shown in
 * that case, but the request to create the blog could be sent anyway.
 *
 * @param array $result The result of the validation of the new blog, with the errors in a WP_Error.
 * @return array The result, with an error if the user can't create the blog.
 */
function xtec_users_validate_blog_signup(array $result): array
{
    $restriction = xtec_users_signup_restriction()['reason'];

    if ($restriction === 'not_xtec') {
        $result['errors']->add('blogname', 'Només poden crear blocs els usuaris XTEC.');
    } elseif ($restriction === 'limit') {
        $result['errors']->add(
            'blogname',
            sprintf(
                "Heu arribat al límit de blocs que podeu crear en 24 hores (%d). Abans n'haureu d'eliminar algun.",
                xtec_users_max_blogs_per_day()
            )
        );
    }

    return $result;
}

/**
 * Shows the option of the maximum of blogs per day in the settings of the network.
 */
function xtec_users_network_options(): void
{
    ?>
    <h2>Creació de blocs</h2>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><label for="xtec_signup_maxblogsday">Nombre màxim de blocs per dia</label></th>
            <td>
                <input type="number" id="xtec_signup_maxblogsday" name="xtec_signup_maxblogsday" class="small-text"
                    min="0" value="<?php echo esc_attr((string)xtec_users_max_blogs_per_day()); ?>" />
                <p class="description">
                    Nombre de blocs que cada usuari XTEC pot crear en 24 hores. Amb 0, només els superadministradors
                    poden crear blocs.
                </p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Saves the option of the maximum of blogs per day. WordPress has already checked the nonce and the permissions.
 */
function xtec_users_update_network_options(): void
{
    if (isset($_POST['xtec_signup_maxblogsday']) && is_string($_POST['xtec_signup_maxblogsday'])) {
        update_site_option('xtec_signup_maxblogsday', absint(wp_unslash($_POST['xtec_signup_maxblogsday'])));
    }
}
