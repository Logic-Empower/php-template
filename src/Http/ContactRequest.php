<?php

declare(strict_types=1);

namespace App\Http;

final class ContactRequest
{
    public function validate(array $input): array
    {
        $data = [
            'name' => $this->cleanText($input['name'] ?? '', 100),
            'email' => strtolower($this->cleanText($input['email'] ?? '', 255)),
            'phone' => $this->cleanText($input['phone'] ?? '', 40),
            'message' => $this->cleanTextArea($input['message'] ?? '', 5000),
        ];

        $errors = [];

        if ($data['name'] === '') {
            $errors['name'] = 'Please enter your name.';
        }

        if (! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($data['message'] === '') {
            $errors['message'] = 'Please enter a message.';
        }

        if ($data['phone'] === '') {
            $data['phone'] = null;
        }

        return [$data, $errors];
    }

    private function cleanText(mixed $value, int $maxLength): string
    {
        $clean = trim(strip_tags((string) $value));
        $clean = preg_replace('/[[:cntrl:]]/', '', $clean) ?? '';

        return mb_substr($clean, 0, $maxLength);
    }

    private function cleanTextArea(mixed $value, int $maxLength): string
    {
        $clean = trim(strip_tags((string) $value));
        $clean = preg_replace("/\r\n|\r/", "\n", $clean) ?? '';
        $clean = preg_replace('/[^\P{C}\n\t]/u', '', $clean) ?? '';

        return mb_substr($clean, 0, $maxLength);
    }
}
