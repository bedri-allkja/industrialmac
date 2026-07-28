<?php

namespace App\Classes;

/**
 * Deliver mail to addresses that the hosting SMTP cannot reach (e.g. Microsoft 365)
 * via FormSubmit HTTPS API.
 */
class FormSubmitMailer
{
    /**
     * FormSubmit form IDs (from activation email) mapped by recipient.
     * Prefer hash over raw email so delivery works after Activate.
     */
    protected array $formIds = [
        'info@industrialmac.it' => '89506b8e3c6432034efba9a85b5e822d',
    ];

    public function send(array $recipients, string $subject, string $htmlBody, string $fromEmail, string $fromName): void
    {
        $errors = [];
        $plain = trim(html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $htmlBody))));

        foreach ($recipients as $to) {
            $toKey = strtolower(trim($to));
            $endpoint = $this->formIds[$toKey] ?? $to;

            $payload = [
                'name' => $fromName !== '' ? $fromName : 'IndustrialMac',
                'email' => $fromEmail,
                '_replyto' => $fromEmail,
                '_subject' => $subject,
                'message' => $plain !== '' ? $plain : $htmlBody,
            ];

            $ch = curl_init('https://formsubmit.co/ajax/' . rawurlencode($endpoint));
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Origin: https://industrialmac.com',
                    'Referer: https://industrialmac.com/',
                ],
            ]);
            $resp = curl_exec($ch);
            $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_error($ch);
            curl_close($ch);

            $json = json_decode((string) $resp, true);
            $success = is_array($json) && (
                ($json['success'] ?? null) === true
                || ($json['success'] ?? null) === 'true'
            );

            $needsActivation = is_array($json) && stripos((string) ($json['message'] ?? ''), 'Activation') !== false;
            $msg = is_array($json) ? (string) ($json['message'] ?? '') : (string) $resp;

            if ($success) {
                continue;
            }

            if ($needsActivation) {
                $errors[] = $to . ' => needs FormSubmit Activate (check inbox): ' . $msg;
                continue;
            }

            if ($code >= 200 && $code < 300 && $err === '' && $msg === '') {
                continue;
            }

            $errors[] = $to . ' => HTTP ' . $code . ' ' . ($err ?: substr($msg !== '' ? $msg : (string) $resp, 0, 250));
        }

        if ($errors) {
            throw new \RuntimeException(implode(' | ', $errors));
        }
    }
}
