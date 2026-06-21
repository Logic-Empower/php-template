<?php

declare(strict_types=1);

namespace App\Http\Controllers;

final class PageController
{
    public function show(string $page, string $pageTitle): void
    {
        $pageFile = app_path('views/pages/' . $page . '.php');

        if (! is_file($pageFile)) {
            http_response_code(404);
            $page = '404';
            $pageTitle = 'Page Not Found';
            $pageFile = app_path('views/pages/404.php');
        }

        require app_path('views/components/header.php');
        require $pageFile;
        require app_path('views/components/footer.php');
    }
}
