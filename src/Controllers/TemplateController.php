<?php

namespace App\Controllers;

use App\Controllers\BaseController;

final class TemplateController extends BaseController
{

    public function table($request, $response, $args)
    {
        $object    = $args['object'];
        $container = $this->container;
        return $container->get('twig')->render($response, 'pages/'.$object.'/table.twig');
    }

    public function detail($request, $response, $args)
    {
        $object    = $args['object'];
        $container = $this->container;
        return $container->get('twig')->render($response, 'pages/'.$object.'/detail.twig');
    }

    public function form($request, $response, $args)
    {
        $object    = $args['object'];
        $container = $this->container;
        return $container->get('twig')->render($response, 'pages/'.$object.'/form.twig');
    }

    public function dynamicTemplate($request, $response, $args)
    {
        $folder_name = $args['folder_name'];
        $file        = $args['file'];
        $container = $this->container;
        return $container->get('twig')->render($response, $folder_name.'/'.$file.'.twig');
    }

    public function component($request, $response, $args)
    {
        $component_name = $args['component_name'];
        $container      = $this->container;
        return $container->get('twig')->render($response, 'pages/components/'.$component_name.'.twig');
    }

    public function menu($request, $response, $args)
    {
        $container = $this->container;
        return $container->get('twig')->render($response, 'pages/components/menu.twig');
    }

    public function home($request, $response)
    {
        $container = $this->container;
        if (!$container->get('session')->get('user')) {
            return $response->withHeader('Location', '/login');
        }
        // return $container->get('twig')->render($response, 'root/admin.twig');
        return $container->get('twig')->render($response, 'template/template.twig');
    }

    public function login($request, $response)
    {
        $container = $this->container;

        if ($container->get('session')->get('user')) {
            return $response->withHeader('Location', '/');
        }

        return $container->get('twig')->render($response, 'root/login.twig');
    }


    public function newTemplate($request, $response)
    {
        $container = $this->container;
        return $container->get('twig')->render($response, 'root/template.twig');
    }

}
