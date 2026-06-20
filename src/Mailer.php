<?php

declare(strict_types=1);

namespace App;

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;
use RuntimeException;

final class Mailer
{
    public function sendEmail(string $to, string $subject, string $body, ?string $replyTo = null): void
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = (string) env_value('SMTP_HOST', '');
            $mail->Port = (int) env_value('SMTP_PORT', 587);
            $mail->SMTPAuth = (bool) env_value('SMTP_AUTH', true);
            $mail->Username = (string) env_value('SMTP_USERNAME', '');
            $mail->Password = (string) env_value('SMTP_PASSWORD', '');

            $encryption = strtolower((string) env_value('SMTP_ENCRYPTION', 'tls'));
            if ($encryption === 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } elseif ($encryption === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            }

            $fromAddress = (string) env_value('MAIL_FROM_ADDRESS', 'noreply@example.com');
            $fromName = (string) env_value('MAIL_FROM_NAME', 'Website');

            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($to);

            if ($replyTo !== null && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
                $mail->addReplyTo($replyTo);
            }

            $mail->isHTML(true);
            $mail->Subject = $this->stripHeaderInput($subject);
            $mail->Body = $body;
            $mail->AltBody = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $body)));

            $mail->send();
        } catch (MailException $exception) {
            error_log('Mail delivery failed: ' . $exception->getMessage());

            throw new RuntimeException('Unable to send email.');
        }
    }

    private function stripHeaderInput(string $value): string
    {
        return trim(str_replace(["\r", "\n"], '', $value));
    }
}
