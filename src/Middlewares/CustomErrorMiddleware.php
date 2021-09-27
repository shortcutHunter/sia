<?php

namespace App\Middlewares;

use Slim\Interfaces\ErrorRendererInterface;
use Throwable;
use DI\ContainerBuilder;
use Slim\Views\Twig;

class CustomErrorMiddleware implements ErrorRendererInterface
{

    public function __invoke(Throwable $exception, bool $displayErrorDetails): string
    {
        $settings = require __DIR__ . '/../../config/settings.php';
        $template_path = $settings['template_path'];

        // $rendered = $twig->render('root/maintenance.twig');
        $renderer = new \Slim\Views\PhpRenderer($template_path);

        return '<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
            <div style="display: flex; justify-content: center;margin-top: 15px;">
                <img src="/public/src/frontend-sia/dist/images/maintenance.jpg" width="80%">
            </div>
        </div>';
    }

}

