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
 * Returns the file of the log of the mails. The file is checked again, as the options could have been set without
 * the page of the options.
 *
 * @return string The path of the file, or empty if the log is disabled or can't be written in the file.
 */
function xtec_mail_log_file(): string
{
    if (!get_site_option('xtec_mail_log', false)) {
        return '';
    }

    $file = (string)get_site_option('xtec_mail_logpath', '');

    return xtec_mail_log_file_error($file) === '' ? $file : '';
}

/**
 * Checks that the log can be written in a file. It must be outside WordPress, so that it can't be downloaded, as it
 * has the content of the mails.
 *
 * @param string $path The path of the file.
 * @return string The reason why the log can't be written in the file, or empty if it can.
 */
function xtec_mail_log_file_error(string $path): string
{
    if ($path === '' || !path_is_absolute($path)) {
        return __('The file of the log must have an absolute path.', 'xtec-mail');
    }

    $dir = realpath(dirname($path));
    $root = realpath(ABSPATH);
    if ($dir === false || !is_dir($dir)) {
        return __('The folder of the file of the log does not exist.', 'xtec-mail');
    }
    if (str_starts_with(trailingslashit($dir), trailingslashit($root))) {
        return __('The file of the log must be outside the folder of WordPress.', 'xtec-mail');
    }
    if (file_exists($path) ? !is_file($path) || !is_writable($path) : !is_writable($dir)) {
        return __('The web server cannot write the file of the log.', 'xtec-mail');
    }

    return '';
}

/**
 * Returns the current date in the local time of the server. WordPress sets the time zone of PHP to UTC, so the one of
 * the configuration of PHP is used. PHP already sets it to UTC if it isn't set or isn't valid.
 *
 * @return string The date, with its time zone.
 */
function xtec_mail_log_date(): string
{
    $timezone = new DateTimeZone(ini_get('date.timezone') ?: 'UTC');

    return (new DateTimeImmutable('now', $timezone))->format('Y-m-d H:i:s T');
}

/**
 * Logs a mail sent through the service. If the log is enabled, it writes the whole mail and the answer of the
 * service. Otherwise, it only writes the errors in the log of PHP.
 *
 * @param string $environment The environment of the service.
 * @param array $mail The mail, as it has been given to XtecMail\EnviaCorreu::send().
 * @param array $result The answer of the service.
 * @param string $error The error, or empty if the mail has been sent.
 */
function xtec_mail_log(string $environment, array $mail, array $result, string $error): void
{
    $file = xtec_mail_log_file();

    if ($file === '') {
        if ($error !== '') {
            error_log('XTEC Mail: ' . $error);
        }
        return;
    }

    $attachments = [];
    foreach ($mail['attachments'] as $attachment) {
        $size = strlen($attachment['content']);
        $attachments[] = sprintf('%s (%s, %d bytes)', $attachment['name'], $attachment['type'], $size);
    }

    $lines = [
        '==== ' . xtec_mail_log_date() . ' ====',
        'Environment: ' . $environment,
        'Sender: ' . $mail['sender'] . ' (' . XtecMail\EnviaCorreu::SENDERS[$mail['sender']] . ')',
        'Reply-To: ' . implode(', ', $mail['reply_to']),
        'To: ' . implode(', ', $mail['to']),
        'Cc: ' . implode(', ', $mail['cc']),
        'Bcc: ' . implode(', ', $mail['bcc']),
        'Subject: ' . $mail['subject'],
        'Attachments: ' . implode('; ', $attachments),
        'Result: ' . $result['status'] . ' (id: ' . $result['id'] . ') ' . $result['message'],
    ];
    if ($error !== '') {
        $lines[] = 'Error: ' . $error;
    }
    $lines[] = 'Body (' . $mail['body_type'] . '):';
    $lines[] = $mail['body'];

    if (file_put_contents($file, implode("\n", $lines) . "\n\n", FILE_APPEND | LOCK_EX) === false) {
        error_log('XTEC Mail: the log cannot be written in ' . $file . ($error !== '' ? '. ' . $error : ''));
    }
}
