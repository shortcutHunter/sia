<?php

use Selective\BasePath\BasePathMiddleware;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;
use Slim\Psr7\UploadedFile;
use Spipu\Html2Pdf\Html2Pdf;
use Slim\Views\Twig;

use App\Objects\Session;

return [
    'settings' => function () {
        return require __DIR__ . '/settings.php';
    },

    'renderer' => function (ContainerInterface $container) {
        $template_path = $container->get('settings')['template_path'];
        return new \Slim\Views\PhpRenderer($template_path);
    },

    'twig' => function (ContainerInterface $container) {
        $settings = $container->get('settings');
        $twigSettings = $settings['twig'];
        $options = $twigSettings['options'];
        $options['cache'] = $options['cache_enabled'] ? $options['cache_path'] : false;

        $twig = Twig::create($twigSettings['paths'], $options);
        return $twig;
    },

    'db' => function (ContainerInterface $container) {
        $capsule = new \Illuminate\Database\Capsule\Manager;
        $capsule->addConnection($container->get('settings')['db']);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        return $capsule;
    },

    'moveUploadedFile' => function(ContainerInterface $container) {
        return function($directory, UploadedFile $uploadedFile, $fname) use ($container) {
            $directory = $container->get('settings')['uploaded'].$directory;
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $fname);
            return $fname;
        };
    },

    'getModel' => function(ContainerInterface $container) {
        return function($class_name) use ($container) {
            $class_name = join("", array_map("ucfirst", explode("_", $class_name)));
            $class_name = "App\\Models\\".$class_name."Model";
            $model = new $class_name($container);
            return $model;
        };
    },

    'getObject' => function(ContainerInterface $container) {
        return function($class_name) use ($container) {
            $class_name = join("", array_map("ucfirst", explode("_", $class_name)));
            $class_name = "App\\Objects\\".$class_name;
            $model = new $class_name();
            return $model;
        };
    },

    'renderPDF' => function(ContainerInterface $container) {
        return function($template_name, $value) use ($container) {
            $html2pdf = new Html2Pdf();
            $rendered_template = $container->get('renderer')->fetch($template_name, $value);
            $html2pdf->writeHTML($rendered_template);
            $pdfContent = $html2pdf->output('my_doc.pdf', 'S');
            return $pdfContent;
        };
    },

    'session' => function(ContainerInterface $container) {
        $session = new Session();
        return $session;
    },

    BasePathMiddleware::class => function (ContainerInterface $container) {
        return new BasePathMiddleware($container->get(App::class));
    },

    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);

        return AppFactory::create();
    },

    ErrorMiddleware::class => function (ContainerInterface $container) {
        $app = $container->get(App::class);
        $settings = $container->get('settings')['error'];

        return new ErrorMiddleware(
            $app->getCallableResolver(),
            $app->getResponseFactory(),
            (bool)$settings['display_error_details'],
            (bool)$settings['log_errors'],
            (bool)$settings['log_error_details']
        );
    },

];
