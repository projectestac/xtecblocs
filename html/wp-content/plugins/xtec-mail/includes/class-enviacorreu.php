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

/**
 * Client of the SOAP service EnviaCorreu of the Departament d'Educació, which sends mails on behalf of the
 * applications. The service is only reachable from the network of the Departament.
 */
class EnviaCorreu
{
    // Accounts that the service allows as sender, by their alias
    public const SENDERS = [
        'gencat' => 'correus_aplicacions.educacio@gencat.cat',
        'xtec' => 'correus_aplicacions.educacio@xtec.cat',
        'educacio' => 'apligest@correueducacio.xtec.cat',
    ];

    public const ENVIRONMENTS = ['INT', 'PRE', 'PRO'];

    // Seconds to wait for the service, so that a service that doesn't answer doesn't block the pages
    private const TIMEOUT = 20;

    private ?\SoapClient $client = null;

    /**
     * @param string $environment The environment of the service: INT, PRE or PRO.
     * @param string $id_app The identifier of the application in the service.
     */
    public function __construct(private string $environment, private string $id_app)
    {
    }

    /**
     * Sends a mail.
     *
     * @param array $mail The mail: sender (alias of SENDERS), reply_to, to, cc and bcc (lists of addresses), subject,
     *                    body, body_type (text/plain or text/html) and attachments (list with name, content and type).
     * @return array The answer of the service: status (OK or KO), id of the message and message.
     * @throws \SoapFault If the service can't be reached or it rejects the request.
     */
    public function send(array $mail): array
    {
        $destinations = [];
        foreach (['to' => 'TO', 'cc' => 'CC', 'bcc' => 'BCC'] as $field => $type) {
            foreach ($mail[$field] as $address) {
                $destinations[] = ['address' => $address, 'type' => $type];
            }
        }

        $attachments = [];
        foreach ($mail['attachments'] as $attachment) {
            // SoapClient encodes the content in base64, as the type of the field is base64Binary
            $attachments[] = [
                'fileName' => $attachment['name'],
                'attachmentContent' => ['fileContent' => $attachment['content'], 'mimeType' => $attachment['type']],
            ];
        }

        $correu = [
            'from' => self::SENDERS[$mail['sender']],
            'destinationAddresses' => ['destination' => $destinations],
            'subject' => $mail['subject'],
            'mailBody' => ['bodyType' => $mail['body_type'], 'bodyContent' => $mail['body']],
        ];
        if ($mail['reply_to']) {
            $correu['replyAddresses'] = ['address' => $mail['reply_to']];
        }
        if ($attachments) {
            $correu['attachments'] = ['attachment' => $attachments];
        }

        $response = $this->client()->enviament(['idApp' => $this->id_app, 'correu' => $correu]);

        // The answer of the request (status and message) and of the mail (id, status and message)
        $answer = $response->respostaCorreu ?? null;
        if (is_array($answer)) {
            $answer = $answer[0] ?? null;
        }

        return [
            'status' => ($response->status === 'OK' && ($answer->status ?? '') === 'OK') ? 'OK' : 'KO',
            'id' => (string)($answer->id ?? ''),
            'message' => (string)($answer->message ?? $response->message ?? ''),
        ];
    }

    /**
     * Returns the SOAP client of the environment. The description of the service (WSDL) is the one of the plugin, as
     * it has the address of the service that works.
     *
     * @return \SoapClient The client.
     */
    private function client(): \SoapClient
    {
        if ($this->client === null) {
            $wsdl = dirname(__DIR__) . '/wsdl/' . strtolower($this->environment) . '.wsdl';
            $this->client = new \SoapClient($wsdl, [
                'exceptions' => true,
                'cache_wsdl' => WSDL_CACHE_DISK,
                'connection_timeout' => self::TIMEOUT,
                'stream_context' => stream_context_create(['http' => ['timeout' => self::TIMEOUT]]),
                'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
                'encoding' => 'UTF-8',
            ]);
        }

        return $this->client;
    }
}
