<?php

declare(strict_types=1);

$directory = rtrim(str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? ''))), '/');
$target = ($directory === '' || $directory === '.') ? '/public/' : $directory . '/public/';

header('Location: ' . $target, true, 302);
exit;
