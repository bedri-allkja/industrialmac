<?php

namespace App\Classes;

use App\Models\Generalsetting;

class SendGridMailer
{
    protected Generalsetting $gs;

    public function __construct(?Generalsetting $gs = null)
    {
        $this->gs = $gs ?: Generalsetting::findOrFail(1);
    }

    public function isConfigured(): bool
    {
        return trim((string) ($this->gs->sendgrid_api_key ?? '')) !== '';
    }

    public function send(array $recipients, string $subject, string $htmlBody, ?string $fromEmail = null, ?string $fromName = null): void
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('SendGrid is not configured');
        }

        $fromEmail = $fromEmail ?: $this->gs->from_email;
        $fromName = $fromName ?: $this->gs->from_name;

        $to = [];
        foreach ($recipients as $email) {
            $to[] = ['email' => $email];
        }

        $payload = [
            'personalizations' => [['to' => $to]],
            'from' => ['email' => $fromEmail, 'name' => $fromName],
            'subject' => $subject,
            'content' => [
                ['type' => 'text/html', 'value' => $htmlBody],
            ],
        ];

        $ch = curl_init('https://api.sendgrid.com/v3/mail/send');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . trim((string) $this->gs->sendgrid_api_key),
                'Content-Type: application/json',
            ],
        ]);
        $resp = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($code < 200 || $code >= 300) {
            throw new \RuntimeException('SendGrid HTTP ' . $code . ': ' . ($err ?: substr((string) $resp, 0, 400)));
        }
    }
}
