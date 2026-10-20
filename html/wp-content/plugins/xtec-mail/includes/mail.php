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

add_filter('pre_wp_mail', 'xtec_mail_install_mailer');
add_action('phpmailer_init', 'xtec_mail_use_service');

const XTEC_MAIL_DEFAULT_SENDER = 'gencat';

require_once __DIR__ . '/class-enviacorreu.php';

/**
 * Returns the configuration of the service, which is in wp-config.php.
 *
 * @return array|null The environment (INT, PRE or PRO) and the identifier of the application, or null if the
 *                    service isn't configured or PHP can't use it, so that WordPress sends the mails itself.
 */
function xtec_mail_config(): ?array
{
    $environment = defined('XTEC_MAIL_ENVIRONMENT') ? XTEC_MAIL_ENVIRONMENT : '';
    $id_app = defined('XTEC_MAIL_IDAPP') ? XTEC_MAIL_IDAPP : '';

    if (
        !in_array($environment, XtecMail\EnviaCorreu::ENVIRONMENTS, true)
        || !is_string($id_app) || $id_app === ''
        || !class_exists('SoapClient')
    ) {
        return null;
    }

    return ['environment' => $environment, 'id_app' => $id_app];
}

/**
 * Returns the alias of the account that sends the mails.
 *
 * @return string The alias, one of the keys of XtecMail\EnviaCorreu::SENDERS.
 */
function xtec_mail_sender(): string
{
    $sender = (string)get_site_option('xtec_mail_sender', XTEC_MAIL_DEFAULT_SENDER);

    return array_key_exists($sender, XtecMail\EnviaCorreu::SENDERS) ? $sender : XTEC_MAIL_DEFAULT_SENDER;
}

/**
 * Returns the address to reply to the mails that don't set one.
 *
 * @return string[] The address, or none if it isn't set.
 */
function xtec_mail_default_reply_to(): array
{
    $reply_to = (string)get_site_option('xtec_mail_replyto', '');

    return is_email($reply_to) ? [$reply_to] : [];
}

/**
 * Replaces the mailer of WordPress by the one that uses the service, before wp_mail() prepares the mail. WordPress
 * keeps the mailer in the global $phpmailer and reuses it for all the mails.
 *
 * @param mixed $return The result of the mail if another plugin has already sent it, or null.
 * @return mixed The same result.
 */
function xtec_mail_install_mailer(mixed $return): mixed
{
    global $phpmailer;

    if ($return === null && !($phpmailer instanceof XtecMail\Mailer) && xtec_mail_config() !== null) {
        require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
        require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
        require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
        require_once ABSPATH . WPINC . '/class-wp-phpmailer.php';
        require_once __DIR__ . '/class-mailer.php';

        // The same as wp_mail() does when it creates the mailer
        $phpmailer = new XtecMail\Mailer(true);
        $phpmailer::$validator = static fn(string $email): bool => (bool)is_email($email);
    }

    return $return;
}

/**
 * Sends the mail through the service. wp_mail() sets the mailer to mail() before this action.
 *
 * @param PHPMailer\PHPMailer\PHPMailer $phpmailer The mailer.
 */
function xtec_mail_use_service(PHPMailer\PHPMailer\PHPMailer $phpmailer): void
{
    if ($phpmailer instanceof XtecMail\Mailer) {
        $phpmailer->Mailer = XtecMail\Mailer::NAME;
    }
}
