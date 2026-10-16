<?php

/**
 * Plugin Name:       XTEC Users
 * Description:       Adds XTECBlocs user system with two types of users: LDAP users from XTEC and the others, and
 *                    limits the creation of blogs to XTEC users and to a maximum per day.
 * Version:           1.2
 * Requires at least: 5.5
 * Requires PHP:      8.0
 * Network:           true
 * Author:            Francesc Bassas i Bullich, Toni Ginard
 * License:           GPL v2
 * License URI:       https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Depends on the plugin XTEC LDAP Login, which identifies the XTEC users.
 */

defined('ABSPATH') || exit;

// Minimum length of the passwords of the local users
const XTEC_USERS_MIN_PASSWORD_LENGTH = 8;

add_action('admin_menu', 'xtec_users_menu');
add_action('wp_ajax_xtec_users_check', 'xtec_users_ajax_check');
add_action('load-user-new.php', 'xtec_users_redirect_user_new');
add_action('admin_bar_menu', 'xtec_users_admin_bar_new_user', 100);

// Creation of blogs in wp-signup.php
add_action('preprocess_signup_form', 'xtec_users_preprocess_signup_form');
add_filter('wpmu_validate_blog_signup', 'xtec_users_validate_blog_signup');
add_action('wpmu_options', 'xtec_users_network_options');
add_action('update_wpmu_options', 'xtec_users_update_network_options');

/**
 * Adds plugin menu.
 */
function xtec_users_menu(): void
{
    if (xtec_users_current_user_can('add_users')) {
        $add_users = add_submenu_page(
            'users.php',
            'Afegeix',
            'Afegeix',
            'promote_users',
            'xtec-add-users',
            'xtec_users_add_users_page'
        );
        remove_submenu_page('users.php', 'user-new.php');
        add_action("load-$add_users", 'xtec_users_add_users_actions');
        add_action("load-$add_users", 'xtec_users_enqueue_assets');
    }
    if (xtec_users_current_user_can('manage_users')) {
        $manage_users = add_submenu_page(
            'users.php',
            'Gestió d\'usuaris',
            'Gestió d\'usuaris',
            'promote_users',
            'xtec-manage-users',
            'xtec_users_manage_users_page'
        );
        add_action("load-$manage_users", 'xtec_users_manage_users_actions');
        add_action("load-$manage_users", 'xtec_users_enqueue_assets');
    }
}

/**
 * Replaces the page of WordPress to add users (user-new.php) with the page of the plugin, so that its rules can't be
 * skipped. Only super admins can still use the page of WordPress.
 */
function xtec_users_redirect_user_new(): void
{
    if (is_super_admin()) {
        return;
    }

    if (xtec_users_current_user_can('add_users')) {
        wp_safe_redirect(admin_url('users.php?page=xtec-add-users'));
        exit;
    }

    wp_die('Només els usuaris XTEC poden afegir usuaris als blocs.', '', ['response' => 403, 'back_link' => true]);
}

/**
 * Makes the link to add a user of the toolbar point to the page of the plugin, or removes it if the user can't use
 * that page. Super admins keep the link of WordPress.
 *
 * @param WP_Admin_Bar $wp_admin_bar The toolbar.
 */
function xtec_users_admin_bar_new_user(WP_Admin_Bar $wp_admin_bar): void
{
    if (is_super_admin() || !$wp_admin_bar->get_node('new-user')) {
        return;
    }

    if (xtec_users_current_user_can('add_users')) {
        $wp_admin_bar->add_node(['id' => 'new-user', 'href' => admin_url('users.php?page=xtec-add-users')]);
    } else {
        $wp_admin_bar->remove_node('new-user');
    }
}

/**
 * Includes JavaScript file and CSS stylesheet.
 */
function xtec_users_enqueue_assets(): void
{
    $plugindir = plugin_dir_url(__FILE__);
    // The date of the files is used as version so that browsers load them again when they change
    wp_enqueue_script(
        'xtec-users_js',
        $plugindir . 'xtec-users.js',
        ['password-strength-meter'],
        (string)filemtime(__DIR__ . '/xtec-users.js'),
        true
    );
    wp_enqueue_style(
        'xtec-users_css',
        $plugindir . 'xtec-users.css',
        [],
        (string)filemtime(__DIR__ . '/xtec-users.css')
    );
}

/**
 * Stops a request that isn't valid, for example with data that has been manipulated or a user that doesn't exist.
 */
function xtec_users_invalid_request(): void
{
    wp_die('La petició no és vàlida.', '', ['response' => 403, 'back_link' => true]);
}

/**
 * Returns a text field of the data of the form, or an empty string if it is missing or it isn't a text.
 *
 * @param array $data The data of the form, already unslashed.
 * @param string $key The name of the field.
 * @return string The value of the field.
 */
function xtec_users_field(array $data, string $key): string
{
    return (isset($data[$key]) && is_string($data[$key])) ? $data[$key] : '';
}

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
 * Checks if a user can be added to the current blog and how. Usernames shorter than 9 characters belong to XTEC users,
 * who must exist and are invited. Longer usernames belong to local users, who are added directly if they exist or
 * created otherwise.
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

    if (strlen($username) < 9) {
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

    return ['status' => (strlen($username) < 9) ? 'xtec' : 'local', 'user' => $user];
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
 * Returns the HTML of a notice of WordPress shown inside the content of the page.
 *
 * @param string $type The type of notice: info, success, warning or error.
 * @param string $html The content of the notice, already escaped.
 * @return string The HTML of the notice.
 */
function xtec_users_notice(string $type, string $html): string
{
    return "<div class='notice notice-$type inline'>$html</div>";
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
 * Returns the HTML of the field of a password, with a button to generate another one and the strength of the password.
 * The password is shown, as it has to be given to the user.
 *
 * @param string $password The password shown in the field.
 * @return string The HTML of the field and its description.
 */
function xtec_users_password_field(string $password): string
{
    return "<input type='text' id='xtec-users-password' name='user[password]' class='regular-text' value='"
        . esc_attr($password) . "' autocomplete='off' spellcheck='false' /> "
        . "<button type='button' id='xtec-users-generate' class='button hide-if-no-js'>Genera'n una altra</button>"
        . "<p class='description'><span id='xtec-users-strength' aria-live='polite'></span> Ha de tenir un mínim de "
        . XTEC_USERS_MIN_PASSWORD_LENGTH . ' caràcters.</p>';
}

/**
 * Checks the length of a password.
 *
 * @param string $password The password.
 * @return string The HTML of the error, or an empty string if the password is valid.
 */
function xtec_users_password_error(string $password): string
{
    if ($password === '') {
        return "<p>Heu d'introduir una contrasenya.</p>";
    }
    if (mb_strlen($password) < XTEC_USERS_MIN_PASSWORD_LENGTH) {
        return '<p>La contrasenya ha de tenir un mínim de ' . XTEC_USERS_MIN_PASSWORD_LENGTH . ' caràcters.</p>';
    }

    return '';
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
                . "<p>Si no és un usuari XTEC, el nom d'usuari ha de tenir un mínim de 9 caràcters.</p>"
            );
        case 'not_xtec':
            return xtec_users_notice(
                'error',
                "<p>L'usuari $name no és un usuari XTEC i no es pot afegir al bloc. Els noms dels usuaris que no són "
                . 'XTEC han de tenir un mínim de 9 caràcters.</p>'
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
                <li><strong>Altres usuaris</strong>: un nom d'usuari d'un mínim de 9 caràcters. Si ja existeix,
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
    if ($done === 'password') {
        $message = "<p>S'ha canviat la contrasenya de l'usuari <strong>" . esc_html($done_user) . '</strong>.</p>';
    } elseif ($done === 'deleted') {
        $message = "<p>S'ha eliminat l'usuari <strong>" . esc_html($done_user) . "</strong> d'XTECBlocs.</p>";
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
        <h1>Gestió d'usuaris</h1>

        <?php
        if ($doaction === 'change_user_password') {
            ?>
            <h2>Canvia la contrasenya</h2>
            <p>Escriviu la nova contrasenya de l'usuari <?php echo $name; ?>.</p>
            <?php
            if (isset($_GET['error'])) {
                echo xtec_users_notice(
                    'error',
                    '<p>La contrasenya ha de tenir un mínim de ' . XTEC_USERS_MIN_PASSWORD_LENGTH . ' caràcters.</p>'
                );
            }
            ?>
            <form action="<?php echo esc_url($list_url); ?>" method="post">
                <?php wp_nonce_field('xtec-manage-users'); ?>
                <input type="hidden" name="user_id" value="<?php echo esc_attr((string)$user_id); ?>" />
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="xtec-users-password">Nova contrasenya</label></th>
                        <td><?php echo xtec_users_password_field(wp_generate_password(12, false)); ?></td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" name="xtec_users_action" value="change_password"
                        class="button button-primary">Canvia la contrasenya</button>
                    <a href="<?php echo esc_url($list_url); ?>" class="button">Cancel·la</a>
                </p>
            </form>
            <?php
        } elseif ($doaction === 'delete_user') {
            ?>
            <h2>Elimina l'usuari</h2>
            <p>Esteu segur que voleu eliminar l'usuari <?php echo $name; ?> d'XTECBlocs?</p>
            <form action="<?php echo esc_url($list_url); ?>" method="post">
                <?php wp_nonce_field('xtec-manage-users'); ?>
                <input type="hidden" name="user_id" value="<?php echo esc_attr((string)$user_id); ?>" />
                <p class="submit">
                    <button type="submit" name="xtec_users_action" value="delete"
                        class="button button-primary">Sí, vull eliminar-lo</button>
                    <a href="<?php echo esc_url($list_url); ?>" class="button">No, deixa-ho córrer</a>
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
                    <p>En aquesta pàgina es llisten tots els usuaris locals, qui els ha creat i els blocs als quals
                        pertanyen.</p>
                    <?php
                } else { ?>
                    <p>En aquesta pàgina es llisten tots els usuaris que heu creat i els blocs als quals pertanyen.</p>
                    <?php
                } ?>
                <p>Des d'aquí podeu canviar la contrasenya d'aquests usuaris i en el cas que no pertanyin a cap bloc
                    també podeu eliminar-los definitivament.</p>
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
                            <th scope="col" class="manage-column">Creat per</th>
                            <?php
                        } ?>
                        <th scope="col" class="manage-column">És membre de ...</th>
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
                                    <a href="?page=xtec-manage-users&amp;action=change_user_password&amp;<?php
                                    echo 'user_id=' . $user->ID; ?>">Canvia la contrasenya</a>
                                    <?php
                                    if (xtec_users_current_user_can('delete_user', $user->ID)) { ?>
                                        | <a href="?page=xtec-manage-users&amp;action=delete_user&amp;user_id=<?php
                                        echo $user->ID; ?>">Elimina l'usuari</a>
                                        <?php
                                    } ?>
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
                echo $is_super_admin
                    ? '<p>Actualment no existeix cap usuari local.</p>'
                    : '<p>Actualment no existeix cap usuari creat per vós.</p>';
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
 * Checks if the current user can create a blog. Only XTEC users can, and only up to a maximum of blogs per day.
 *
 * @return string The reason why the user can't create a blog: not_xtec or limit. Empty if the user can create it.
 */
function xtec_users_signup_restriction(): string
{
    $limit = xtec_users_max_blogs_per_day();

    if (!is_user_logged_in() || !xtec_users_current_user_can('create_blogs', $limit)) {
        return 'not_xtec';
    }

    return xtec_users_current_user_can('create_blog_today', $limit) ? 'limit' : '';
}

/**
 * Shows why the current user can't create a blog instead of the form of wp-signup.php, which is not shown.
 */
function xtec_users_preprocess_signup_form(): void
{
    $restriction = xtec_users_signup_restriction();
    if ($restriction === '') {
        return;
    }

    if ($restriction === 'not_xtec') {
        echo '<p class="mu_alert">Només poden crear blocs els usuaris XTEC.</p>';
    } else {
        $limit = xtec_users_max_blogs_per_day();
        echo '<h2>Límit de creació excedit</h2>';
        echo '<p>El límit de creació de blocs diari és <strong>' . $limit . '</strong>. Si voleu crear-ne més avui, '
            . "abans n'haureu d'eliminar algun.</p>";
        echo '<p>Blocs creats en les darreres 24 hores:</p><ul>';
        foreach (xtec_users_current_user_can('create_blog_today', $limit) as $blog_id) {
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
    $restriction = xtec_users_signup_restriction();

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

/**
 * Checks if the current user can do an specific action.
 *
 * @param string $action The action to check.
 * @param mixed $param Extra argument: the limit of blogs per day (create_blogs and create_blog_today) or the user ID
 *                     (delete_user and change_user_password).
 * @return bool|array True if the user can do the action false otherwise. For create_blog_today, an empty array if the
 *                    user can create a blog today, or the IDs of the blogs created today otherwise.
 */
function xtec_users_current_user_can(string $action, mixed $param = ''): bool|array
{
    // Site admins can do it all except delete users who are still members of some blog
    if (is_super_admin()) {
        if ($action === 'delete_user') {
            return !xtec_users_is_member_of_blogs((int)$param);
        }

        if ($action === 'create_blog_today') {
            return array();
        }

        return true;
    }

    switch ($action) {
        case 'add_users':
            // Can add users: XTEC users from LDAP who can edit users

            global $current_user;
            wp_get_current_user();
            return current_user_can('add_users') && xtec_users_is_xtec_user($current_user->ID);

        case 'manage_users':
            // Can manage users: XTEC users from LDAP

            global $current_user;
            wp_get_current_user();
            return xtec_users_is_xtec_user($current_user->ID);

        case 'create_blogs':
            // Can create blogs: XTEC users from LDAP.
            // $param -> limit per day blogs creation

            $limit = $param;
            if ((int)$limit === 0) {
                return false;
            }


            global $current_user;
            wp_get_current_user();
            return xtec_users_is_xtec_user($current_user->ID);

        case 'create_blog_today':
            // Can create blog today: users who have not exceeded the maximum blog daily creation.
            // Returns: empty array, if user can create blog today.
            //          array with blogs created today for user, if user exceeds the maximum blog daily creation.
            // $param -> limit per day blogs creation

            $limit = $param;
            $today_created_blogs = array();

            global $current_user;
            wp_get_current_user();
            $blogs = get_blogs_of_user($current_user->ID, true);

            foreach ($blogs as $blog) {
                $ahir = date("Y-m-d H:i:s", time() - 86400);
                $registered = get_blog_details($blog->userblog_id)->registered;
                if ($ahir < $registered) {
                    array_push($today_created_blogs, $blog->userblog_id);
                }
            }

            if (count($today_created_blogs) < $limit) {
                return array();
            }

            return $today_created_blogs;

        case 'delete_user':
            // Can delete user: user who have created whenever the user to delete is not member of any bloc other than
            // the portal
            // $param -> user_id
            $user_id = (int)$param;
            global $current_user;
            if ($current_user->user_login === get_user_meta($user_id, 'xtec_user_creator', true)) {
                return !xtec_users_is_member_of_blogs($user_id);
            }

            return false;

        case 'change_user_password':
            // Can change user password: user who have created
            // $param -> user_id
            $user_id = (int)$param;
            global $current_user;

            return $current_user->user_login === get_user_meta($user_id, 'xtec_user_creator', true);
    }

    return false;
}

/**
 * Checks if a user is member of any blog other than the portal, which is the main site.
 *
 * @param int $user_id The user ID.
 * @return bool True if the user is member of any blog other than the portal.
 */
function xtec_users_is_member_of_blogs(int $user_id): bool
{
    foreach (get_blogs_of_user($user_id, true) as $blog) {
        if (!is_main_site($blog->userblog_id)) {
            return true;
        }
    }
    return false;
}

/**
 * Checks if is a XTEC user: registered through LDAP or OAuth, or with an e-mail of XTEC. It is the same criterion as
 * the plugin XTEC LDAP Login, which also considers XTEC users the ones registered before xtec_user_creator existed.
 *
 * @param int $user_id The user ID.
 * @return bool True if is a XTEC user.
 */
function xtec_users_is_xtec_user(int $user_id): bool
{
    $creator = get_user_meta($user_id, 'xtec_user_creator', true);
    if ($creator === 'LDAP_XTEC' || $creator === 'OAUTH_XTEC') {
        return true;
    }

    $user = get_userdata($user_id);

    return $user && str_ends_with(strtolower($user->user_email), '@xtec.cat');
}

/**
 * Checks if is a valid username.
 *
 * @param string $username The username.
 * @return bool True if is a valid username false otherwise.
 */
function xtec_users_is_valid_username(string $username): bool
{
    return !preg_match('/[^a-z0-9_.\-]/', $username);
}
