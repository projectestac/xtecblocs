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

add_action('init', 'xtec_mail_load_textdomain');
add_action('wpmu_options', 'xtec_mail_network_options');
add_action('update_wpmu_options', 'xtec_mail_update_network_options');
add_action('network_admin_notices', 'xtec_mail_show_notices');

/**
 * Loads the translations of the plugin.
 */
function xtec_mail_load_textdomain(): void
{
    load_plugin_textdomain('xtec-mail', false, dirname(plugin_basename(XTEC_MAIL_FILE)) . '/languages');
}

/**
 * Shows the options of the mail in the settings of the network.
 */
function xtec_mail_network_options(): void
{
    $config = xtec_mail_config();
    $log_file = (string)get_site_option('xtec_mail_logpath', '');
    ?>
    <h2><?php esc_html_e('Mail', 'xtec-mail'); ?></h2>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><?php esc_html_e('Service EnviaCorreu', 'xtec-mail'); ?></th>
            <td>
                <?php
                if ($config !== null) {
                    printf(
                        /* translators: 1: The environment of the service (INT, PRE or PRO). 2: The application. */
                        esc_html__('The mails are sent through the environment %1$s as the application %2$s.', 'xtec-mail'),
                        '<strong>' . esc_html($config['environment']) . '</strong>',
                        '<strong>' . esc_html($config['id_app']) . '</strong>'
                    );
                } else {
                    printf(
                        /* translators: 1: XTEC_MAIL_ENVIRONMENT. 2: XTEC_MAIL_IDAPP. 3: wp-config.php. */
                        esc_html__('The service is not configured, so WordPress sends the mails itself. To use it, set the constants %1$s (INT, PRE or PRO) and %2$s in %3$s. PHP needs the extension SOAP.', 'xtec-mail'),
                        '<code>XTEC_MAIL_ENVIRONMENT</code>',
                        '<code>XTEC_MAIL_IDAPP</code>',
                        '<code>wp-config.php</code>'
                    );
                }
                ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="xtec_mail_sender"><?php esc_html_e('Sender', 'xtec-mail'); ?></label></th>
            <td>
                <select id="xtec_mail_sender" name="xtec_mail_sender">
                    <?php foreach (XtecMail\EnviaCorreu::SENDERS as $alias => $address) { ?>
                        <option value="<?php echo esc_attr($alias); ?>" <?php selected($alias, xtec_mail_sender()); ?>>
                            <?php echo esc_html($address); ?>
                        </option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="xtec_mail_replyto"><?php esc_html_e('Reply to', 'xtec-mail'); ?></label></th>
            <td>
                <input type="email" id="xtec_mail_replyto" name="xtec_mail_replyto" class="regular-text"
                    value="<?php echo esc_attr((string)get_site_option('xtec_mail_replyto', '')); ?>" />
                <p class="description">
                    <?php esc_html_e('Address to reply to the mails that do not set one.', 'xtec-mail'); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('Log', 'xtec-mail'); ?></th>
            <td>
                <label for="xtec_mail_log">
                    <input type="checkbox" id="xtec_mail_log" name="xtec_mail_log" value="1"
                        <?php checked((bool)get_site_option('xtec_mail_log', false)); ?> />
                    <?php esc_html_e('Write all the mails sent through the service in a file', 'xtec-mail'); ?>
                </label>
                <p>
                    <label for="xtec_mail_logpath"><?php esc_html_e('File', 'xtec-mail'); ?></label>
                    <input type="text" id="xtec_mail_logpath" name="xtec_mail_logpath" class="regular-text code"
                        value="<?php echo esc_attr($log_file); ?>" />
                </p>
                <p class="description">
                    <?php
                    esc_html_e(
                        'The file must be outside the folder of WordPress. The log keeps the whole content of the mails, including the links to reset the passwords, so enable it only while needed. When it is disabled, only the errors are written in the log of PHP.',
                        'xtec-mail'
                    );
                    ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('Test', 'xtec-mail'); ?></th>
            <td>
                <?php
                submit_button(__('Save and send a test mail', 'xtec-mail'), 'secondary', 'xtec_mail_test', false);
                ?>
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: The e-mail address of the current user. */
                        esc_html__('Saves the settings and sends a mail to %s.', 'xtec-mail'),
                        '<strong>' . esc_html(wp_get_current_user()->user_email) . '</strong>'
                    );
                    ?>
                </p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Saves the options of the mail and, if requested, sends the test mail. WordPress has already checked the nonce and
 * the permissions.
 */
function xtec_mail_update_network_options(): void
{
    $notices = [];

    $sender = isset($_POST['xtec_mail_sender']) && is_string($_POST['xtec_mail_sender'])
        ? sanitize_key(wp_unslash($_POST['xtec_mail_sender'])) : '';
    if (array_key_exists($sender, XtecMail\EnviaCorreu::SENDERS)) {
        update_site_option('xtec_mail_sender', $sender);
    }

    if (isset($_POST['xtec_mail_replyto']) && is_string($_POST['xtec_mail_replyto'])) {
        $reply_to = sanitize_email(wp_unslash($_POST['xtec_mail_replyto']));
        update_site_option('xtec_mail_replyto', $reply_to);
    }

    if (isset($_POST['xtec_mail_logpath']) && is_string($_POST['xtec_mail_logpath'])) {
        update_site_option('xtec_mail_logpath', trim(sanitize_text_field(wp_unslash($_POST['xtec_mail_logpath']))));
    }

    // The log is only enabled if it can be written
    $log = !empty($_POST['xtec_mail_log']);
    if ($log) {
        $error = xtec_mail_log_file_error((string)get_site_option('xtec_mail_logpath', ''));
        if ($error !== '') {
            $log = false;
            $notices[] = ['error', __('The log has not been enabled.', 'xtec-mail') . ' ' . $error];
        }
    }
    update_site_option('xtec_mail_log', $log ? 1 : 0);

    if (isset($_POST['xtec_mail_test'])) {
        $notices[] = xtec_mail_send_test();
    }

    if ($notices) {
        set_site_transient('xtec_mail_notices_' . get_current_user_id(), $notices, MINUTE_IN_SECONDS);
    }
}

/**
 * Sends a test mail to the current user.
 *
 * @return array The notice with the result: its type and its message.
 */
function xtec_mail_send_test(): array
{
    $email = wp_get_current_user()->user_email;
    $config = xtec_mail_config();

    $error = '';
    $catch_error = static function (WP_Error $wp_error) use (&$error): void {
        $error = $wp_error->get_error_message();
    };
    add_action('wp_mail_failed', $catch_error);

    $sent = wp_mail(
        $email,
        __('Test mail', 'xtec-mail'),
        sprintf(
            /* translators: 1: The name of the network. 2: The environment of the service, or "none". */
            __("This mail has been sent from the settings of %1\$s to check that the mails are sent.\n\nEnvironment of the service EnviaCorreu: %2\$s", 'xtec-mail'),
            get_network()->site_name,
            $config['environment'] ?? __('none', 'xtec-mail')
        )
    );

    remove_action('wp_mail_failed', $catch_error);

    if ($sent) {
        /* translators: %s: The e-mail address. */
        return ['success', sprintf(__('The test mail has been sent to %s.', 'xtec-mail'), $email)];
    }

    /* translators: 1: The e-mail address. 2: The error. */
    return ['error', sprintf(__('The test mail has not been sent to %1$s: %2$s', 'xtec-mail'), $email, $error)];
}

/**
 * Shows the notices of the last save of the options of the mail.
 */
function xtec_mail_show_notices(): void
{
    $key = 'xtec_mail_notices_' . get_current_user_id();
    $notices = get_site_transient($key);
    if (!is_array($notices)) {
        return;
    }

    delete_site_transient($key);
    foreach ($notices as [$type, $message]) {
        wp_admin_notice(esc_html($message), ['type' => $type, 'dismissible' => true]);
    }
}
