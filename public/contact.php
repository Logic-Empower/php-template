<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';

use App\Database;
use App\Http\ContactRequest;
use App\Http\Session;
use App\Mailer;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

[$data, $errors] = (new ContactRequest())->validate($_POST);

if (! Session::verifyCsrf($_POST['_token'] ?? null)) {
    $errors['_token'] = 'Your session expired. Please try again.';
}

if ($errors !== []) {
    Session::flash('error', reset($errors));
    Session::flashOld([
        'name' => $data['name'] ?? '',
        'email' => $data['email'] ?? '',
        'phone' => $data['phone'] ?? '',
        'message' => $data['message'] ?? '',
    ]);

    header('Location: ' . url('/contact'), true, 303);
    exit;
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
    Session::flash('error', 'Sorry, we could not send your message right now.');
    Session::flashOld([
        'name' => $data['name'],
        'email' => $data['email'],
        'phone' => $data['phone'] ?? '',
        'message' => $data['message'],
    ]);
}

header('Location: ' . url('/contact'), true, 303);
exit;
