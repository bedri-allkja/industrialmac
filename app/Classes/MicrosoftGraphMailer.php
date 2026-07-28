<?php

namespace App\Classes;

use App\Models\Generalsetting;

class MicrosoftGraphMailer
{
    protected Generalsetting $gs;

    public function __construct(?Generalsetting $gs = null)
    {
        $this->gs = $gs ?: Generalsetting::findOrFail(1);
    }

    public function isConfigured(): bool
    {
        return trim((string) $this->gs->ms_tenant_id) !== ''
            && trim((string) $this->gs->ms_client_id) !== ''
            && trim((string) $this->gs->ms_client_secret) !== '';
    }

    public function send(array $recipients, string $subject, string $htmlBody, ?string $fromEmail = null, ?string $fromName = null): void
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('Microsoft Graph is not configured');
        }

        $fromEmail = $fromEmail ?: $this->gs->from_email;
        $fromName = $fromName ?: $this->gs->from_name;
        $token = $this->getAccessToken();

        $toRecipients = [];
        foreach ($recipients as $email) {
            $toRecipients[] = ['emailAddress' => ['address' => $email]];
        }

        $payload = [
            'message' => [
                'subject' => $subject,
                'body' => [
                    'contentType' => 'HTML',
                    'content' => $htmlBody,
                ],
                'toRecipients' => $toRecipients,
            ],
            'saveToSentItems' => true,
        ];

        $url = 'https://graph.microsoft.com/v1.0/users/' . rawurlencode($fromEmail) . '/sendMail';
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ],
        ]);
        $resp = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($code < 200 || $code >= 300) {
            throw new \RuntimeException('Graph sendMail HTTP ' . $code . ': ' . ($err ?: substr((string) $resp, 0, 400)));
        }
    }

    protected function getAccessToken(): string
    {
        $tenant = trim((string) $this->gs->ms_tenant_id);
        $url = 'https://login.microsoftonline.com/' . rawurlencode($tenant) . '/oauth2/v2.0/token';
        $post = http_build_query([
            'client_id' => trim((string) $this->gs->ms_client_id),
            'client_secret' => (string) $this->gs->ms_client_secret,
            'scope' => 'https://graph.microsoft.com/.default',
            'grant_type' => 'client_credentials',
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        ]);
        $resp = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        $json = json_decode((string) $resp, true);
        if (empty($json['access_token'])) {
            $msg = $json['error_description'] ?? $json['error'] ?? ($err ?: substr((string) $resp, 0, 300));
            throw new \RuntimeException('Graph token HTTP ' . $code . ': ' . $msg);
        }

        return $json['access_token'];
    }
}
