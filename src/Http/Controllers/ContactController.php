<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Database;
use App\Http\ContactRequest;
use App\Http\Session;
use App\Mailer;
use Throwable;

final class ContactController
{
    public function store(): void
    {
        [$data, $errors] = (new ContactRequest())->validate($_POST);

        if (! Session::verifyCsrf($_POST['_token'] ?? null)) {
            $errors['_token'] = 'Your session expired. Please try again.';
        }

        if ($errors !== []) {
            $this->redirectBackWithError(reset($errors), $data);
        }

        try {
            $data['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? null;
            $data['user_agent'] = isset($_SERVER['HTTP_USER_AGENT'])
                ? mb_substr((string) $_SERVER['HTTP_USER_AGENT'], 0, 255)
                : null;

            $submissionId = (new Database())->insertContactSubmission($data);

            $body = sprintf(
                '<h2>New contact submission</h2>
                <p><strong>Name:</strong> %s</p>
                <p><strong>Email:</strong> %s</p>
                <p><strong>Phone:</strong> %s</p>
                <p><strong>Message:</strong><br>%s</p>
                <p><strong>Submission ID:</strong> %d</p>',
                e($data['name']),
                e($data['email']),
                e($data['phone'] ?? 'Not provided'),
                nl2br(e($data['message'])),
                $submissionId
            );

            (new Mailer())->sendEmail(
                (string) env_value('CONTACT_TO_EMAIL', 'owner@example.com'),
                'New website contact submission',
                $body,
                $data['email']
            );

            Session::flash('success', 'Thanks, your message has been sent.');
        } catch (Throwable $exception) {
            error_log('Contact submission failed: ' . $exception->getMessage());
            $this->redirectBackWithError('Sorry, we could not send your message right now.', $data);
        }

        $this->redirectToContact();
    }

    private function redirectBackWithError(string $message, array $data): never
    {
        Session::flash('error', $message);
        Session::flashOld([
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
            'message' => $data['message'] ?? '',
        ]);

        $this->redirectToContact();
    }

    private function redirectToContact(): never
    {
        header('Location: ' . url('/contact'), true, 303);
        exit;
    }
}
