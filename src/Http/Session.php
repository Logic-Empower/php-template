<?php

declare(strict_types=1);

namespace App\Http;

final class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_name('phptemplate_session');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => self::isHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
    }

    public static function csrfToken(): string
    {
        self::start();

        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION['_csrf_token'];
    }

    public static function verifyCsrf(?string $token): bool
    {
        self::start();

        return is_string($token)
            && isset($_SESSION['_csrf_token'])
            && hash_equals((string) $_SESSION['_csrf_token'], $token);
    }

    public static function flash(string $key, string $message): void
    {
        self::start();
        $_SESSION['_flash'][$key] = $message;
    }

    public static function flashOld(array $input): void
    {
        self::start();
        $_SESSION['_old'] = $input;
    }

    public static function clearTransient(): void
    {
        self::start();
        unset($_SESSION['_flash'], $_SESSION['_old']);
    }

    private static function isHttps(): bool
    {
        return (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    }
}
