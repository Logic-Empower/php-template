<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';

use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Router;
use App\Http\Session;

$router = new Router();
$pages = new PageController();
$contact = new ContactController();

$router->get('/', fn () => $pages->show('home', 'Home'));
$router->get('/about', fn () => $pages->show('about', 'About'));
$router->get('/contact', fn () => $pages->show('contact', 'Contact'));
$router->post('/contact', fn () => $contact->store());

$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/'
);

Session::clearTransient();
