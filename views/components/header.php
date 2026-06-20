<?php

declare(strict_types=1);

$appName = (string) env_value('APP_NAME', 'PHP Template');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= e($appName) ?>">
    <title><?= e($pageTitle) ?> | <?= e($appName) ?></title>
    <link rel="stylesheet" href="<?= e(url('/assets/css/style.css')) ?>">
</head>
<body>
    <header class="site-header">
        <a class="brand" href="<?= e(url('/')) ?>"><?= e($appName) ?></a>
        <nav class="nav" aria-label="Primary navigation">
            <a href="<?= e(url('/')) ?>">Home</a>
            <a href="<?= e(url('/about')) ?>">About</a>
            <a href="<?= e(url('/contact')) ?>">Contact</a>
        </nav>
    </header>
    <main class="site-main">
