<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';

use App\Http\Session;

$routes = [
    '/' => 'home',
    '/about' => 'about',
    '/contact' => 'contact',
];

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath = base_path_url();

if ($basePath !== '' && str_starts_with($path, $basePath)) {
    $path = substr($path, strlen($basePath)) ?: '/';
}

$path = rtrim($path, '/') ?: '/';
$page = $routes[$path] ?? null;

if ($page === null) {
    http_response_code(404);
    $page = '404';
}

$pageTitle = match ($page) {
    'home' => 'Home',
    'about' => 'About',
    'contact' => 'Contact',
    default => 'Page Not Found',
};

require app_path('views/components/header.php');
require app_path('views/pages/' . $page . '.php');
require app_path('views/components/footer.php');

Session::clearTransient();
