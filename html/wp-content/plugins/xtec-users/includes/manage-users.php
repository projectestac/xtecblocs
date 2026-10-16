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

/**
 * Returns the user of the forms to change the password or delete a user of the page to manage users, after checking
 * that the current user can do the action. The request is stopped otherwise.
 *
 * @param string $action The action: change_user_password or delete_user.
 * @param int $user_id The ID of the user.
 * @return WP_User The user.
 */
function xtec_users_manage_users_target(string $action, int $user_id): WP_User
{
    $user = get_userdata($user_id);
    if (!$user || !xtec_users_current_user_can($action, $user_id)) {
        xtec_users_invalid_request();
    }

    return $user;
}

/**
 * Changes the password of a user or deletes a user, when the forms of the page to manage users are sent. It is done
 * before the page is shown, so that the browser can be redirected afterwards and reloading the page doesn't send the
 * form again. The result is shown from the parameters of the URL, which never contain the password.
 */
function xtec_users_manage_users_actions(): void
{
    // The forms are checked before the page is shown, so that an invalid request gets the right status code
    $form = (isset($_GET['action']) && is_string($_GET['action'])) ? $_GET['action'] : '';
    if ($form === 'change_user_password' || $form === 'delete_user') {
        xtec_users_manage_users_target($form, isset($_GET['user_id']) ? absint($_GET['user_id']) : 0);
    }

    $action = (isset($_POST['xtec_users_action']) && is_string($_POST['xtec_users_action']))
        ? $_POST['xtec_users_action']
        : '';
    if ($action !== 'change_password' && $action !== 'delete') {
        return;
    }

    check_admin_referer('xtec-manage-users');
    $user_id = isset($_POST['user_id']) ? absint($_POST['user_id']) : 0;
    $userdata = xtec_users_manage_users_target(
        ($action === 'change_password') ? 'change_user_password' : 'delete_user',
        $user_id
    );

    $url = admin_url('users.php?page=xtec-manage-users');

    if ($action === 'change_password') {
        $password = (isset($_POST['user']['password']) && is_string($_POST['user']['password']))
            ? wp_unslash($_POST['user']['password'])
            : '';
        if (xtec_users_password_error($password) !== '') {
            // The form is shown again with the error
            $args = ['action' => 'change_user_password', 'user_id' => $user_id, 'error' => 'password'];
        } else {
            wp_set_password($password, $user_id);
            // Whoever had logged in with the old password can't go on using the user
            WP_Session_Tokens::get_instance($user_id)->destroy_all();
            $args = ['done' => 'password', 'user' => $userdata->user_login];
        }
    } else {
        wpmu_delete_user($user_id);
        $args = ['done' => 'deleted', 'user' => $userdata->user_login];
    }

    wp_safe_redirect(add_query_arg($args, $url));
    exit;
}

/**
 * Shows the page to manage the local users: the list of users and the forms to change their password or delete them.
 */
function xtec_users_manage_users_page(): void
{
    // Result of an action, after the redirection of xtec_users_manage_users_actions()
    $done = isset($_GET['done']) && is_string($_GET['done']) ? $_GET['done'] : '';
    $done_user = isset($_GET['user']) && is_string($_GET['user']) ? wp_unslash($_GET['user']) : '';
    $message = '';
    $done_name = '<strong>' . esc_html($done_user) . '</strong>';
    if ($done === 'password') {
        /* translators: %s: Username. */
        $text = esc_html__('The password of the user %s has been changed.', 'xtec-users');
        $message = '<p>' . sprintf($text, $done_name) . '</p>';
    } elseif ($done === 'deleted') {
        /* translators: %s: Username. */
        $text = esc_html__('The user %s has been deleted from XTECBlocs.', 'xtec-users');
        $message = '<p>' . sprintf($text, $done_name) . '</p>';
    }
    $list_url = admin_url('users.php?page=xtec-manage-users');
    $doaction = isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : '';
    if ($doaction === 'change_user_password' || $doaction === 'delete_user') {
        // Already checked in xtec_users_manage_users_actions()
        $user_id = isset($_GET['user_id']) ? absint($_GET['user_id']) : 0;
        $name = '<strong>' . esc_html(xtec_users_manage_users_target($doaction, $user_id)->user_login) . '</strong>';
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('User management', 'xtec-users'); ?></h1>

        <?php
        if ($doaction === 'change_user_password') {
            ?>
            <h2><?php esc_html_e('Change the password', 'xtec-users'); ?></h2>
            <p>
                <?php
                /* translators: %s: Username. */
                printf(esc_html__('Enter the new password of the user %s.', 'xtec-users'), $name);
                ?>
            </p>
            <?php
            if (isset($_GET['error'])) {
                // The password is too short or empty
                echo xtec_users_notice('error', xtec_users_password_error('-'));
            }
            ?>
            <form action="<?php echo esc_url($list_url); ?>" method="post">
                <?php wp_nonce_field('xtec-manage-users'); ?>
                <input type="hidden" name="user_id" value="<?php echo esc_attr((string)$user_id); ?>" />
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="xtec-users-password"><?php esc_html_e('New password', 'xtec-users'); ?></label>
                        </th>
                        <td><?php echo xtec_users_password_field(wp_generate_password(12, false)); ?></td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" name="xtec_users_action" value="change_password"
                        class="button button-primary"><?php esc_html_e('Change the password', 'xtec-users'); ?></button>
                    <a href="<?php echo esc_url($list_url); ?>" class="button">
                        <?php esc_html_e('Cancel', 'xtec-users'); ?>
                    </a>
                </p>
            </form>
            <?php
        } elseif ($doaction === 'delete_user') {
            ?>
            <h2><?php esc_html_e('Delete the user', 'xtec-users'); ?></h2>
            <p>
                <?php
                /* translators: %s: Username. */
                printf(esc_html__('Are you sure you want to delete the user %s from XTECBlocs?', 'xtec-users'), $name);
                ?>
            </p>
            <form action="<?php echo esc_url($list_url); ?>" method="post">
                <?php wp_nonce_field('xtec-manage-users'); ?>
                <input type="hidden" name="user_id" value="<?php echo esc_attr((string)$user_id); ?>" />
                <p class="submit">
                    <button type="submit" name="xtec_users_action" value="delete" class="button button-primary">
                        <?php esc_html_e('Yes, I want to delete it', 'xtec-users'); ?>
                    </button>
                    <a href="<?php echo esc_url($list_url); ?>" class="button">
                        <?php esc_html_e('No, never mind', 'xtec-users'); ?>
                    </a>
                </p>
            </form>
            <?php
        } else { ?>
            <?php
            // Super admins can manage all the local users. The rest, only the users that they have created.
            $is_super_admin = is_super_admin();
            ?>
            <div class="notice notice-info inline">
                <?php
                if ($is_super_admin) { ?>
                    <p>
                        <?php
                        esc_html_e(
                            'This page lists all the local users, who created them and the blogs they belong to.',
                            'xtec-users'
                        );
                        ?>
                    </p>
                    <?php
                } else { ?>
                    <p>
                        <?php
                        esc_html_e(
                            'This page lists all the users that you have created and the blogs they belong to.',
                            'xtec-users'
                        );
                        ?>
                    </p>
                    <?php
                } ?>
                <p>
                    <?php
                    esc_html_e(
                        'From here you can change the password of these users and, if they do not belong to any blog, you can also delete them permanently.',
                        'xtec-users'
                    );
                    ?>
                </p>
            </div>

            <?php

            if ($message !== '') {
                echo xtec_users_notice('success', $message);
            }

            // Local users created with this plugin have the login of their creator in xtec_user_creator
            $per_page = 20;
            $paged = isset($_GET['paged']) ? max(1, absint($_GET['paged'])) : 1;
            $args = [
                'blog_id' => 0, // All the users of the network, not only the members of the current blog
                'orderby' => 'login',
                'number' => $per_page,
                'paged' => $paged,
                'count_total' => true,
            ];
            if ($is_super_admin) {
                // All the users that aren't XTEC users nor super admins, also the ones created by other means
                $args['meta_query'] = [
                    'relation' => 'OR',
                    ['key' => 'xtec_user_creator', 'compare' => 'NOT EXISTS'],
                    [
                        'key' => 'xtec_user_creator',
                        'value' => ['LDAP_XTEC', 'OAUTH_XTEC'],
                        'compare' => 'NOT IN',
                    ],
                ];
                $args['login__not_in'] = get_super_admins();
                add_action('pre_user_query', 'xtec_users_exclude_xtec_emails');
            } else {
                $args['meta_query'] = [['key' => 'xtec_user_creator', 'value' => wp_get_current_user()->user_login]];
            }

            $query = new WP_User_Query($args);
            $total = $query->get_total();

            // A page beyond the last one, for example after deleting users, shows the last page
            if ($paged > 1 && $paged > ceil($total / $per_page)) {
                $paged = max(1, (int)ceil($total / $per_page));
                $query = new WP_User_Query(['paged' => $paged] + $args);
            }
            $users = $query->get_results();
            remove_action('pre_user_query', 'xtec_users_exclude_xtec_emails');

            if ($total > 0) {
                $pagination = xtec_users_pagination($total, $per_page, $paged);
                echo $pagination;
                ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                    <tr>
                        <th scope="col" class="manage-column column-username column-primary">
                            <?php
                            echo esc_html__('Username') ?>
                        </th>
                        <?php
                        if ($is_super_admin) { ?>
                            <th scope="col" class="manage-column"><?php esc_html_e('Created by', 'xtec-users'); ?></th>
                            <?php
                        } ?>
                        <th scope="col" class="manage-column"><?php esc_html_e('Member of...', 'xtec-users'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($users as $user) {
                        $blogs = get_blogs_of_user($user->ID, true);
                        ?>
                        <tr>
                            <td class="column-username column-primary">
                                <strong><?php
                                    echo esc_html($user->user_login) ?></strong>
                                <div class="row-actions">
                                    <?php
                                    printf(
                                        '<a href="%s">%s</a>',
                                        esc_url(add_query_arg(
                                            ['action' => 'change_user_password', 'user_id' => $user->ID],
                                            $list_url
                                        )),
                                        esc_html__('Change the password', 'xtec-users')
                                    );
                                    if (xtec_users_can_delete_user($user->ID, $blogs)) {
                                        printf(
                                            ' | <a href="%s">%s</a>',
                                            esc_url(add_query_arg(
                                                ['action' => 'delete_user', 'user_id' => $user->ID],
                                                $list_url
                                            )),
                                            esc_html__('Delete the user', 'xtec-users')
                                        );
                                    }
                                    ?>
                                </div>
                            </td>
                            <?php
                            if ($is_super_admin) { ?>
                                <td>
                                    <?php
                                    echo esc_html(get_user_meta($user->ID, 'xtec_user_creator', true) ?: '—'); ?>
                                </td>
                                <?php
                            } ?>
                            <td>
                                <?php
                                foreach ($blogs as $blog) {
                                    echo "<a href='" . esc_url($blog->siteurl) . "'>" . esc_html($blog->blogname)
                                        . '</a><br>';
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <?php
                echo str_replace('tablenav top', 'tablenav bottom', $pagination);
            } else {
                echo "<div class='notice notice-info inline'>";
                echo '<p>' . ($is_super_admin
                    ? esc_html__('There are currently no local users.', 'xtec-users')
                    : esc_html__('There are currently no users created by you.', 'xtec-users')) . '</p>';
                echo "</div>";
            }
        }
        ?>
    </div>
    <?php
}

/**
 * Excludes the users with an e-mail of XTEC from a query of users. Together with the value of xtec_user_creator, it
 * identifies XTEC users in the same way as the plugin XTEC LDAP Login, which also considers XTEC users the ones
 * registered before that value existed.
 *
 * @param WP_User_Query $query The query of users.
 */
function xtec_users_exclude_xtec_emails(WP_User_Query $query): void
{
    global $wpdb;

    $query->query_where .= $wpdb->prepare(
        " AND $wpdb->users.user_email NOT LIKE %s",
        '%' . $wpdb->esc_like('@xtec.cat')
    );
}

/**
 * Returns the HTML of the pagination of the list of users, with the same appearance as the lists of WordPress.
 *
 * @param int $total The number of users.
 * @param int $per_page The number of users per page.
 * @param int $paged The current page.
 * @return string The HTML of the pagination.
 */
function xtec_users_pagination(int $total, int $per_page, int $paged): string
{
    $pages = max(1, (int)ceil($total / $per_page));
    $paged = min($paged, $pages);

    // Same markup as WP_List_Table::pagination()
    $link = function (int $page, string $class, string $label, string $symbol) use ($paged, $pages): string {
        $disabled = ($class === 'first-page' || $class === 'prev-page') ? $paged <= 1 : $paged >= $pages;
        if ($disabled) {
            return "<span class='tablenav-pages-navspan button disabled' aria-hidden='true'>$symbol</span>";
        }
        $url = esc_url(add_query_arg('paged', $page, admin_url('users.php?page=xtec-manage-users')));

        return "<a class='$class button' href='$url'><span class='screen-reader-text'>" . esc_html($label)
            . "</span><span aria-hidden='true'>$symbol</span></a>";
    };

    /* translators: %s: Number of items. */
    $items = sprintf(_n('%s item', '%s items', $total), number_format_i18n($total));
    /* translators: 1: Current page, 2: Total pages. */
    $paging = sprintf(
        _x('%1$s of %2$s', 'paging'),
        number_format_i18n($paged),
        "<span class='total-pages'>" . number_format_i18n($pages) . '</span>'
    );

    return "<div class='tablenav top'><div class='tablenav-pages" . ($pages === 1 ? ' one-page' : '') . "'>"
        . "<span class='displaying-num'>" . esc_html($items) . '</span> '
        . "<span class='pagination-links'>"
        . $link(1, 'first-page', __('First page'), '&laquo;') . ' '
        . $link(max(1, $paged - 1), 'prev-page', __('Previous page'), '&lsaquo;') . ' '
        . "<span class='paging-input'><span class='tablenav-paging-text'>$paging</span></span> "
        . $link(min($pages, $paged + 1), 'next-page', __('Next page'), '&rsaquo;') . ' '
        . $link($pages, 'last-page', __('Last page'), '&raquo;')
        . "</span></div><br class='clear' /></div>";
}
