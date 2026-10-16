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

add_action('wp_ajax_xtec_users_check', 'xtec_users_ajax_check');

/**
 * Returns the roles that can be given to a user in the blog, with their translated names. Only XTEC users can be
 * administrators.
 *
 * @param bool $xtec True if the user is a XTEC user.
 * @return array The names of the roles, indexed by role.
 */
function xtec_users_roles(bool $xtec): array
{
    $roles = [
        'editor' => _x('Editor', 'User role'),
        'author' => _x('Author', 'User role'),
        'contributor' => _x('Contributor', 'User role'),
        'subscriber' => _x('Subscriber', 'User role'),
    ];

    return $xtec ? ['administrator' => _x('Administrator', 'User role')] + $roles : $roles;
}

/**
 * Checks if a user can be added to the current blog and how. Usernames shorter than
 * XTEC_USERS_MIN_LOCAL_USERNAME_LENGTH characters belong to XTEC users, who must exist and are invited. Longer
 * usernames belong to local users, who are added directly if they exist or created otherwise.
 *
 * @param string $username The username, in lowercase.
 * @return array The status (empty, invalid, xtec_not_found, not_xtec, member, xtec, local or new) and the user, if it
 *               exists.
 */
function xtec_users_classify(string $username): array
{
    if ($username === '') {
        return ['status' => 'empty', 'user' => null];
    }

    if (!xtec_users_is_valid_username($username)) {
        return ['status' => 'invalid', 'user' => null];
    }

    $user = get_user_by('login', $username) ?: null;

    $is_xtec_username = strlen($username) < XTEC_USERS_MIN_LOCAL_USERNAME_LENGTH;

    if ($is_xtec_username) {
        if (!$user) {
            return ['status' => 'xtec_not_found', 'user' => null];
        }
        if (!xtec_users_is_xtec_user($user->ID)) {
            return ['status' => 'not_xtec', 'user' => $user];
        }
    } elseif (!$user) {
        return ['status' => 'new', 'user' => null];
    }

    if (is_user_member_of_blog($user->ID, get_current_blog_id())) {
        return ['status' => 'member', 'user' => $user];
    }

    return ['status' => $is_xtec_username ? 'xtec' : 'local', 'user' => $user];
}

/**
 * Hides most of an e-mail address, so that the check of a username doesn't reveal the e-mail of the user.
 *
 * @param string $email The e-mail address.
 * @return string The masked address, for example a••••••@xtec.cat.
 */
function xtec_users_mask_email(string $email): string
{
    $at = strpos($email, '@');
    if ($at === false) {
        return '';
    }

    return $email[0] . str_repeat('•', max($at - 1, 3)) . substr($email, $at);
}

/**
 * Returns the HTML of the selector of the role.
 *
 * @param array $roles The roles that can be selected, as returned by xtec_users_roles().
 * @param string $selected The selected role. The default is contributor.
 * @return string The HTML of the row of the table of the form.
 */
function xtec_users_role_row(array $roles, string $selected): string
{
    if (!isset($roles[$selected])) {
        $selected = 'contributor';
    }

    $html = "<tr><th scope='row'><label for='xtec-users-role'>" . esc_html__('Role') . '</label></th>'
        . "<td><select id='xtec-users-role' name='user[role]'>";
    foreach ($roles as $role => $name) {
        $html .= "<option value='" . esc_attr($role) . "'" . selected($role, $selected, false) . '>'
            . esc_html($name) . '</option>';
    }

    return $html . '</select></td></tr>';
}

/**
 * Returns the HTML of the result of checking a username: a notice and, if the user can be added to the blog, the
 * fields and the button to do it. It is used both in the page and in the check through AJAX.
 *
 * @param string $username The username, in lowercase.
 * @param array $values The values of the fields of the form, to keep them after an error.
 * @param string $errors The HTML of the errors of the form, if any.
 * @return string The HTML of the result.
 */
function xtec_users_render_check(string $username, array $values = [], string $errors = ''): string
{
    $check = xtec_users_classify($username);
    $user = $check['user'];
    $name = '<strong>' . esc_html($username) . '</strong>';
    $role = xtec_users_field($values, 'role');

    switch ($check['status']) {
        case 'invalid':
            return xtec_users_notice(
                'error',
                "<p>El nom d'usuari $name no és vàlid. Només s'admeten lletres minúscules, números, punts, guions i "
                . 'guions baixos.</p>'
            );
        case 'xtec_not_found':
            return xtec_users_notice(
                'error',
                "<p>No s'ha trobat cap usuari XTEC amb el nom $name. Recordeu que l'usuari ha d'haver entrat a "
                . 'XTECBlocs almenys una vegada.</p>'
                . "<p>Si no és un usuari XTEC, el nom d'usuari ha de tenir un mínim de "
                . XTEC_USERS_MIN_LOCAL_USERNAME_LENGTH . ' caràcters.</p>'
            );
        case 'not_xtec':
            return xtec_users_notice(
                'error',
                "<p>L'usuari $name no és un usuari XTEC i no es pot afegir al bloc. Els noms dels usuaris que no són "
                . 'XTEC han de tenir un mínim de ' . XTEC_USERS_MIN_LOCAL_USERNAME_LENGTH . ' caràcters.</p>'
            );
        case 'member':
            return xtec_users_notice('info', "<p>L'usuari $name ja és membre d'aquest bloc.</p>");
        case 'xtec':
            $html = xtec_users_notice(
                'info',
                "<p>Usuari XTEC: $name (" . esc_html($user->display_name) . ', '
                . esc_html(xtec_users_mask_email($user->user_email)) . ').</p>'
                . '<p>Rebrà un correu electrònic per confirmar la invitació. Quan la confirmi, '
                . "s'afegirà com a membre del bloc.</p>"
            );
            $rows = xtec_users_role_row(xtec_users_roles(true), $role);
            $button = ['invite', "Envia la invitació"];
            break;
        case 'local':
            $html = xtec_users_notice(
                'info',
                "<p>Usuari existent: $name (" . esc_html($user->display_name) . ').</p>'
                . "<p>S'afegirà directament com a membre del bloc.</p>"
            );
            $rows = xtec_users_role_row(xtec_users_roles(false), $role);
            $button = ['add', 'Afegeix al bloc'];
            break;
        case 'new':
            $password = array_key_exists('password', $values)
                ? xtec_users_field($values, 'password')
                : wp_generate_password(12, false);
            $html = xtec_users_notice(
                'info',
                "<p>L'usuari $name no existeix. Podeu crear-lo i afegir-lo al bloc.</p>"
            );
            $rows = "<tr><th scope='row'><label for='xtec-users-password'>" . esc_html__('Password') . '</label></th>'
                . '<td>' . xtec_users_password_field($password)
                . "<p class='description'>Feu arribar el nom d'usuari i la contrasenya a l'usuari perquè pugui entrar "
                . 'a XTECBlocs.</p></td></tr>'
                . "<tr><th scope='row'><label for='xtec-users-email'>" . esc_html__('Email')
                . ' (opcional)</label></th>'
                . "<td><input type='text' id='xtec-users-email' name='user[email]' class='regular-text' value='"
                . esc_attr(xtec_users_field($values, 'email')) . "' />"
                . "<p class='description'>Si l'usuari no disposa de correu electrònic, deixeu aquest camp en blanc. "
                . 'No es poden fer servir adreces @xtec.cat.</p></td></tr>'
                . xtec_users_role_row(xtec_users_roles(false), $role);
            $button = ['create', "Crea l'usuari i afegeix-lo al bloc"];
            break;
        default:
            return '';
    }

    if ($errors !== '') {
        $html .= xtec_users_notice('error', $errors);
    }

    return $html . "<table class='form-table' role='presentation'>$rows</table>"
        . "<p class='submit'><button type='submit' name='xtec_users_action' value='$button[0]' "
        . "class='button button-primary'>" . esc_html($button[1]) . '</button></p>';
}

/**
 * Checks a username through AJAX and returns the HTML of the result.
 */
function xtec_users_ajax_check(): void
{
    check_ajax_referer('xtec-add-users');

    // The same permissions as the page
    if (!current_user_can('promote_users') || !xtec_users_current_user_can('add_users')) {
        wp_send_json_error(null, 403);
    }

    $username = (isset($_POST['username']) && is_string($_POST['username']))
        ? strtolower(trim(wp_unslash($_POST['username'])))
        : '';

    wp_send_json_success(['html' => xtec_users_render_check($username)]);
}

/**
 * Returns the message shown after inviting or adding a user to the blog.
 *
 * @param string $done The action done: invited or added.
 * @param string $username The username.
 * @param string $role The role of the user in the blog.
 * @return string The HTML of the message, or an empty string if the action or the role are unknown.
 */
function xtec_users_done_message(string $done, string $username, string $role): string
{
    $roles = xtec_users_roles(true);
    if (!isset($roles[$role])) {
        return '';
    }

    $name = '<strong>' . esc_html($username) . '</strong>';
    $role_name = '<strong>' . esc_html($roles[$role]) . '</strong>';

    if ($done === 'invited') {
        return "<p>S'ha enviat una invitació al correu electrònic de l'usuari $name per afegir-se com a $role_name "
            . "d'aquest bloc.</p><p>Quan l'usuari confirmi la invitació s'afegirà com a membre del bloc.</p>";
    }
    if ($done === 'added') {
        return "<p>Heu afegit l'usuari $name com a $role_name d'aquest bloc.</p>";
    }

    return '';
}

/**
 * Invites, adds or creates a user, after checking again that it can be done.
 *
 * @param string $action The action: invite, add or create.
 * @param string $username The username, in lowercase.
 * @param array $data The data of the form, already unslashed.
 * @return array The HTML of the notice of success, or the HTML of the errors. Both are empty if the user can't be
 *               added any more in this way, so that the new result of the check is shown.
 */
function xtec_users_do_action(string $action, string $username, array $data): array
{
    $statuses = ['invite' => 'xtec', 'add' => 'local', 'create' => 'new'];
    if (!isset($statuses[$action])) {
        xtec_users_invalid_request();
    }

    $check = xtec_users_classify($username);
    $user = $check['user'];

    // The situation may have changed since the check, for example if another administrator has added the user
    if ($check['status'] !== $statuses[$action]) {
        return ['success' => '', 'errors' => ''];
    }

    $roles = xtec_users_roles($action === 'invite');
    $role = xtec_users_field($data, 'role');
    if (!isset($roles[$role])) {
        xtec_users_invalid_request();
    }

    $name = '<strong>' . esc_html($username) . '</strong>';
    $role_name = '<strong>' . esc_html($roles[$role]) . '</strong>';

    if ($action === 'invite') {
        // The user is added to the blog when the invitation is accepted (see maybe_add_existing_user_to_blog()). The
        // invitation is only read then, so it isn't loaded with the rest of the options in every request.
        $newuser_key = wp_generate_password(20, false);
        add_option(
            'new_user_' . $newuser_key,
            ['user_id' => $user->ID, 'email' => $user->user_email, 'role' => $role],
            '',
            false
        );
        /* translators: 1: Site title, 2: Site URL, 3: User role, 4: Activation URL. */
        $invitation = __(
            'Hi,

You\'ve been invited to join \'%1$s\' at
%2$s with the role of %3$s.

Please click the following link to confirm the invite:
%4$s'
        );
        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        wp_mail(
            $user->user_email,
            sprintf(__('[%s] Joining Confirmation'), $blogname),
            sprintf($invitation, $blogname, home_url(), $roles[$role], home_url("/newbloguser/$newuser_key/"))
        );

        return ['success' => xtec_users_done_message('invited', $username, $role), 'errors' => ''];
    }

    if ($action === 'add') {
        add_user_to_blog(get_current_blog_id(), $user->ID, $role);

        return ['success' => xtec_users_done_message('added', $username, $role), 'errors' => ''];
    }

    $password = xtec_users_field($data, 'password');
    $email = trim(xtec_users_field($data, 'email'));
    if ($email === '') {
        // WordPress requires an e-mail. The domain .invalid is reserved and can never receive mail (RFC 2606).
        $email = $username . '@xtec.invalid';
    }

    $errors = xtec_users_password_error($password);
    if (!is_email($email)) {
        $errors .= '<p>L\'adreça de correu electrònic que heu introduït no és vàlida.</p>';
    } elseif (preg_match('/^.+@xtec\.cat$/i', $email)) {
        // Only XTEC users can use addresses of the domain xtec.cat
        $errors .= '<p>L\'adreça de correu electrònic que heu introduït no és vàlida. Només els usuaris XTEC poden '
            . 'fer servir adreces de correu electrònic @xtec.cat.</p>';
    } elseif (email_exists($email)) {
        $errors .= "<p>L'adreça de correu electrònic que heu introduït ja s'utilitza.</p>";
    }
    if ($errors !== '') {
        return ['success' => '', 'errors' => $errors];
    }

    $user_id = wpmu_create_user($username, $password, $email);
    if (!$user_id) {
        return ['success' => '', 'errors' => "<p>No s'ha pogut crear l'usuari. Torneu-ho a provar.</p>"];
    }

    update_user_meta($user_id, 'xtec_user_creator', wp_get_current_user()->user_login);
    add_user_to_blog(get_current_blog_id(), $user_id, $role);

    return [
        'success' => "<p>Heu donat d'alta al servei de blocs l'usuari $name amb contrasenya <strong>"
            . esc_html($password) . "</strong>. Feu arribar a l'usuari el nom d'usuari i la contrasenya perquè pugui "
            . 'accedir al servei de blocs.</p>'
            . "<p>Heu afegit l'usuari $name com a $role_name d'aquest bloc.</p>",
        'errors' => '',
    ];
}

/**
 * Invites or adds a user when the form of the page to add users is sent. It is done before the page is shown, so that
 * the browser can be redirected afterwards and reloading the page doesn't send another invitation. Creating a user is
 * done when the page is shown, as the message of the result contains the password, which can't be kept for after the
 * redirection. Reloading the page then doesn't create the user again, as it already exists.
 */
function xtec_users_add_users_actions(): void
{
    $action = (isset($_POST['xtec_users_action']) && is_string($_POST['xtec_users_action']))
        ? $_POST['xtec_users_action']
        : '';
    if ($action !== 'invite' && $action !== 'add') {
        return;
    }

    check_admin_referer('xtec-add-users');
    $values = (isset($_POST['user']) && is_array($_POST['user'])) ? wp_unslash($_POST['user']) : [];
    $username = strtolower(trim(xtec_users_field($values, 'username')));

    // If the user can't be invited or added any more, the page shows the new result of the check
    if (xtec_users_do_action($action, $username, $values)['success'] !== '') {
        $args = [
            'done' => ($action === 'invite') ? 'invited' : 'added',
            'user' => $username,
            'role' => xtec_users_field($values, 'role'),
        ];
        wp_safe_redirect(add_query_arg($args, admin_url('users.php?page=xtec-add-users')));
        exit;
    }
}

/**
 * Shows the page to add users to the blog. The username is checked through AJAX while it is typed, and the result
 * shows the fields to add the user. Without JavaScript, the button to check the username does the same.
 */
function xtec_users_add_users_page(): void
{
    $username = '';
    $values = [];
    $success = '';
    $errors = '';

    // Result of an invitation or an addition, after the redirection of xtec_users_add_users_actions()
    if (
        isset($_GET['done'], $_GET['user'], $_GET['role'])
        && is_string($_GET['done']) && is_string($_GET['user']) && is_string($_GET['role'])
    ) {
        $success = xtec_users_done_message($_GET['done'], wp_unslash($_GET['user']), $_GET['role']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        check_admin_referer('xtec-add-users');

        $values = (isset($_POST['user']) && is_array($_POST['user'])) ? wp_unslash($_POST['user']) : [];
        $username = strtolower(trim(xtec_users_field($values, 'username')));
        $action = (isset($_POST['xtec_users_action']) && is_string($_POST['xtec_users_action']))
            ? $_POST['xtec_users_action']
            : '';

        // Invitations and additions are done in xtec_users_add_users_actions(). If one gets here, the user can't be
        // invited or added any more, and the new result of the check is shown.
        if ($action === 'invite' || $action === 'add') {
            $values = [];
        } elseif ($action !== '') {
            $result = xtec_users_do_action($action, $username, $values);
            $success = $result['success'];
            $errors = $result['errors'];
            if ($errors === '') {
                // After an action, the fields are shown again from scratch
                $values = [];
            }
            if ($success !== '') {
                $username = '';
            }
        }
    }
    ?>
    <div class="wrap">
        <h1>Afegeix un nou usuari</h1>

        <?php
        if ($success !== '') {
            echo xtec_users_notice('success', $success);
        }
        ?>

        <div class="notice notice-info inline">
            <p>Escriviu el nom d'usuari de la persona que voleu afegir al vostre bloc:</p>
            <ul>
                <li><strong>Usuaris XTEC</strong>: el seu nom d'usuari de la XTEC. Rebran una invitació per correu
                    electrònic. Han d'haver entrat a XTECBlocs almenys una vegada.
                </li>
                <li><strong>Altres usuaris</strong>: un nom d'usuari d'un mínim de
                    <?php echo XTEC_USERS_MIN_LOCAL_USERNAME_LENGTH; ?> caràcters. Si ja existeix,
                    s'afegirà al bloc. Si no existeix, el podreu crear.
                </li>
            </ul>
        </div>

        <form id="xtec-users-form" action="?page=xtec-add-users" method="post">
            <?php
            wp_nonce_field('xtec-add-users'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="xtec-users-username">Nom d'usuari</label></th>
                    <td>
                        <input type="text" id="xtec-users-username" name="user[username]" class="regular-text"
                               value="<?php
                                echo esc_attr($username); ?>" maxlength="60" autocomplete="off"
                               autocapitalize="none" spellcheck="false"/>
                        <button type="submit" name="xtec_users_check" value="1" class="button">Comprova</button>
                    </td>
                </tr>
            </table>
            <div id="xtec-users-result" aria-live="polite">
                <?php
                echo xtec_users_render_check($username, $values, $errors); ?>
            </div>
        </form>
    </div>
    <?php
}
