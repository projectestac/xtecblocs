<?php

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

namespace XtecMail;

use PHPMailer\PHPMailer\Exception;

/**
 * Mailer of WordPress that sends the mails through the service EnviaCorreu instead of the function mail() of PHP.
 * wp_mail() prepares the mail as usual, and PHPMailer calls xtecSend() when the mailer is 'xtec'.
 */
class Mailer extends \WP_PHPMailer
{
    public const NAME = 'xtec';

    /**
     * Sends the mail through the service EnviaCorreu. The headers and the body built by PHPMailer aren't used, as the
     * service builds the mail from its parts.
     *
     * @param string $header The headers built by PHPMailer.
     * @param string $body The body built by PHPMailer.
     * @return bool True, as the errors throw an exception.
     * @throws Exception If the mail hasn't been sent, so that wp_mail() fires the action wp_mail_failed.
     */
    public function xtecSend(string $header, string $body): bool
    {
        $config = xtec_mail_config();
        $mail = [
            'sender' => xtec_mail_sender(),
            'reply_to' => array_column($this->getReplyToAddresses(), 0) ?: xtec_mail_default_reply_to(),
            'to' => array_column($this->getToAddresses(), 0),
            'cc' => array_column($this->getCcAddresses(), 0),
            'bcc' => array_column($this->getBccAddresses(), 0),
            // The same as PHPMailer does, so that the subject can't add headers to the mail
            'subject' => $this->secureHeader($this->Subject),
            'body' => $this->Body,
            'body_type' => $this->ContentType === static::CONTENT_TYPE_TEXT_HTML ? 'text/html' : 'text/plain',
            'attachments' => [],
        ];

        try {
            $mail['attachments'] = $this->attachmentsContent();
            $result = (new EnviaCorreu($config['environment'], $config['id_app']))->send($mail);
        } catch (Exception | \SoapFault $e) {
            $result = ['status' => 'KO', 'id' => '', 'message' => $e->getMessage()];
        }

        $error = '';
        if ($result['status'] !== 'OK') {
            /* translators: %s: The cause of the error. */
            $error = sprintf(__('The mail has not been sent through EnviaCorreu: %s', 'xtec-mail'), $result['message']);
        }

        xtec_mail_log($config['environment'], $mail, $result, $error);

        if ($error !== '') {
            throw new Exception($error);
        }

        return true;
    }

    /**
     * Returns the attachments of the mail with their content. The service doesn't show the images embedded in the
     * body, so they are sent as attachments too.
     *
     * @return array The attachments, with name, content and type.
     * @throws Exception If the file of an attachment can't be read.
     */
    private function attachmentsContent(): array
    {
        $attachments = [];
        foreach ($this->getAttachments() as $attachment) {
            // The attachments added as a string have the content instead of the path of the file
            $content = $attachment[5] ? $attachment[0] : file_get_contents($attachment[0]);
            if ($content === false) {
                throw new Exception($this->lang('file_access') . $attachment[0]);
            }
            $attachments[] = ['name' => $attachment[2], 'content' => $content, 'type' => $attachment[4]];
        }

        return $attachments;
    }
}
