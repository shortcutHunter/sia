<?php

namespace App\Controllers;

use App\Controllers\BaseController;

final class ComponentController extends BaseController
{

    public function components($request, $response, $args)
   {
       $container = $this->container;
       $view_name = $args['view_name'];
       return $container->get('twig')->render($response, 'components/'.$view_name.'.twig');
   }

}
