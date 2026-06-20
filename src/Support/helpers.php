<?php

declare(strict_types=1);

use App\Http\Session;

if (! function_exists('app_path')) {
    function app_path(string $path = ''): string
    {
        $base = defined('APP_ROOT') ? APP_ROOT : dirname(__DIR__, 2);

        return $path === '' ? $base : $base . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }
}

if (! function_exists('env_value')) {
    function env_value(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false || $value === null || $value === '') {
            return $default;
        }

        return match (strtolower((string) $value)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'null', '(null)' => null,
            default => $value,
        };
    }
}

if (! function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (! function_exists('base_path_url')) {
    function base_path_url(): string
    {
        $configured = trim((string) env_value('APP_BASE_PATH', ''), '/');

        if ($configured !== '') {
            return '/' . $configured;
        }

        $scriptDirectory = str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '')));
        $scriptDirectory = rtrim($scriptDirectory, '/');

        if ($scriptDirectory === '' || $scriptDirectory === '.') {
            return '';
        }

        if (str_ends_with($scriptDirectory, '/public')) {
            $scriptDirectory = substr($scriptDirectory, 0, -7);
        }

        return $scriptDirectory === '' ? '' : $scriptDirectory;
    }
}

if (! function_exists('url')) {
    function url(string $path = ''): string
    {
        $path = '/' . ltrim($path, '/');

        if ($path === '/') {
            return base_path_url() ?: '/';
        }

        return base_path_url() . $path;
    }
}

if (! function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Session::csrfToken();
    }
}

if (! function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
    }
}

if (! function_exists('old')) {
    function old(string $key, string $default = ''): string
    {
        $old = $_SESSION['_old'] ?? [];

        return isset($old[$key]) ? (string) $old[$key] : $default;
    }
}

if (! function_exists('flash')) {
    function flash(string $key): ?string
    {
        return isset($_SESSION['_flash'][$key]) ? (string) $_SESSION['_flash'][$key] : null;
    }
}
