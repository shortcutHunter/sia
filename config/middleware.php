<?php

use Selective\BasePath\BasePathMiddleware;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;
use App\Middlewares\CustomErrorMiddleware;

return function (App $app) {
    // Parse json, form data and xml
    $app->addBodyParsingMiddleware();

    // Add the Slim built-in routing middleware
    $app->addRoutingMiddleware();

    $app->add(BasePathMiddleware::class);

    // Add Error Middleware
    // $errorMiddleware = $app->addErrorMiddleware(true, true, true);

    // Get the default error handler and register my custom error renderer.
    // $errorHandler = $errorMiddleware->getDefaultErrorHandler();
    // $errorHandler->registerErrorRenderer('text/html', CustomErrorMiddleware::class);

    // Catch exceptions and errors
    $app->add(ErrorMiddleware::class);

};
