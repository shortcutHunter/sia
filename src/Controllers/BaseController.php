<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;

class BaseController
{
    protected $container;
    protected $settings;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->settings = $container->get('settings');
    }

    public function get_object($object_name)
    {
        $object = $this->container->get('getObject')($object_name);
        $object = new $object;
        return $object;
    }
}
