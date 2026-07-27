<?php

namespace App\Classes;

use App\{
    Models\Order,
    Models\EmailLog,
    Models\EmailTemplate,
    Models\Generalsetting
};
use Barryvdh\DomPDF\Facade\Pdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Str;

class GeniusMailer
{

    public $mail;
    public $gs;

    public function __construct()
    {
        $this->gs = Generalsetting::findOrFail(1);

        $this->mail = new PHPMailer(true);

        if ($this->gs->is_smtp == 1) {

            $this->mail->isSMTP();
            $this->mail->Host = $this->gs->mail_host;
            $this->mail->Port = (int) $this->gs->mail_port;
            $this->mail->Timeout = 15;

            $host = strtolower(trim((string) $this->gs->mail_host));
            $isLocal = in_array($host, ['localhost', '127.0.0.1', '::1'], true)
                || str_ends_with($host, '.secureserver.net') && str_starts_with($host, 'relay-hosting');

            // Shared hosts (GoDaddy) often block outbound SMTP to Office365/Gmail.
            // Local Exim relay usually needs no auth and no TLS.
            if ($isLocal || (int) $this->gs->mail_port === 25 && $host === 'localhost') {
                $this->mail->SMTPAuth = false;
                $this->mail->SMTPSecure = false;
                $this->mail->SMTPAutoTLS = false;
            } else {
                $this->mail->SMTPAuth = true;
                $this->mail->Username = $this->gs->mail_user;
                $this->mail->Password = $this->gs->mail_pass;
                $enc = strtolower(trim((string) $this->gs->mail_encryption));
                if (in_array($enc, ['tls', 'ssl'], true)) {
                    $this->mail->SMTPSecure = $enc;
                } else {
                    $this->mail->SMTPSecure = false;
                    $this->mail->SMTPAutoTLS = false;
                }
            }
        }
    }

    protected function logMail(string $type, $to, ?string $subject, string $status, ?string $error = null): void
    {
        try {
            if (is_array($to)) {
                $to = implode(', ', $to);
            }

            EmailLog::create([
                'type' => $type,
                'to_email' => Str::limit((string) $to, 490, ''),
                'from_email' => $this->gs->from_email,
                'subject' => $subject ? Str::limit($subject, 490, '') : null,
                'status' => $status,
                'error' => $error ? Str::limit($error, 2000, '') : null,
            ]);
        } catch (\Throwable $e) {
            // Never break mail sending because logging failed.
        }
    }

    protected function normalizeRecipients($to): array
    {
        $recipients = is_array($to) ? $to : preg_split('/[,;]+/', (string) $to);
        $clean = [];
        foreach ($recipients as $email) {
            $email = trim((string) $email);
            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $clean[] = $email;
            }
        }
        return $clean;
    }


    public function sendAutoOrderMail(array $mailData, $id)
    {
        $temp = EmailTemplate::where('email_type', '=', $mailData['type'])->first();
        $order = Order::findOrFail($id);
        $cart = json_decode($order->cart, true);
        $to = $mailData['to'] ?? '';
        $subject = $temp->email_subject ?? ($mailData['type'] ?? 'order');

        try {

            $body = preg_replace("/{customer_name}/", $mailData['cname'], $temp->email_body);
            $body = preg_replace("/{order_amount}/", $mailData['oamount'], $body);
            $body = preg_replace("/{admin_name}/", $mailData['aname'], $body);
            $body = preg_replace("/{admin_email}/", $mailData['aemail'], $body);
            $body = preg_replace("/{order_number}/", $mailData['onumber'], $body);
            $body = preg_replace("/{website_title}/", $this->gs->title, $body);


            $fileName = 'assets/temp_files/' . Str::random(4) . time() . '.pdf';

            $pdf = PDF::loadView('pdf.order', compact('order', 'cart'))->save($fileName);

            //Recipients
            $this->mail->clearAddresses();
            $this->mail->setFrom($this->gs->from_email, $this->gs->from_name);
            $this->mail->addAddress($to);

            // Attachments
            $this->mail->addAttachment($fileName);

            // Content
            $this->mail->isHTML(true);

            $this->mail->Subject = $temp->email_subject;

            $this->mail->Body = $body;

            $this->mail->send();
            $this->logMail('order', $to, $subject, 'sent');
        } catch (Exception $e) {
            $this->logMail('order', $to, $subject, 'failed', $e->getMessage());
        }

        $files = glob('assets/temp_files/*'); //get all file names
        foreach ($files as $file) {
            if (is_file($file))
                unlink($file); //delete file
        }

        return true;
    }

    public function sendAutoMail(array $mailData)
    {

        $temp = EmailTemplate::where('email_type', '=', $mailData['type'])->first();
        $to = $mailData['to'] ?? '';
        $subject = $temp->email_subject ?? ($mailData['type'] ?? 'auto');

        try {

            $body = preg_replace("/{customer_name}/", $mailData['cname'], $temp->email_body);
            $body = preg_replace("/{order_amount}/", $mailData['oamount'], $body);
            $body = preg_replace("/{admin_name}/", $mailData['aname'], $body);
            $body = preg_replace("/{admin_email}/", $mailData['aemail'], $body);
            $body = preg_replace("/{order_number}/", $mailData['onumber'], $body);
            $body = preg_replace("/{website_title}/", $this->gs->title, $body);

            //Recipients
            $this->mail->clearAddresses();
            $this->mail->setFrom($this->gs->from_email, $this->gs->from_name);
            $this->mail->addAddress($to);

            // Content
            $this->mail->isHTML(true);

            $this->mail->Subject = $temp->email_subject;

            $this->mail->Body = $body;

            $this->mail->send();
            $this->logMail('auto', $to, $subject, 'sent');
        } catch (Exception $e) {
            $this->logMail('auto', $to, $subject, 'failed', $e->getMessage());
        }

        return true;
    }

    public function sendCustomMail(array $mailData)
    {
        $type = $mailData['type'] ?? 'custom';
        $subject = $mailData['subject'] ?? null;
        $recipients = $this->normalizeRecipients($mailData['to'] ?? '');
        $toLabel = implode(', ', $recipients);

        try {

            //Recipients
            $this->mail->clearAddresses();
            $this->mail->setFrom($this->gs->from_email, $this->gs->from_name);

            foreach ($recipients as $to) {
                $this->mail->addAddress($to);
            }

            if (count($recipients) === 0) {
                $this->logMail($type, $toLabel ?: '(none)', $subject, 'failed', 'No valid recipient email');
                return false;
            }

            // Content
            $this->mail->isHTML(true);

            $this->mail->Subject = $mailData['subject'];

            $this->mail->Body = $mailData['body'];

            $this->mail->send();
            $this->logMail($type, $toLabel, $subject, 'sent');
        } catch (Exception $e) {
            $this->logMail($type, $toLabel, $subject, 'failed', $e->getMessage());
            return false;
        }

        return true;
    }
}
