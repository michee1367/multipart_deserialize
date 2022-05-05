<?php

namespace Mink67\MultiPartDeserialize;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * Perment de créer un un kafka connect
 */
class MultiPartDeserializeBundle extends Bundle
{

    /**
     * 
     */
    public function __construct() 
    {
        //$this->name = "mink67.security.bundle";
    }

    /**
     * 
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);


        $container->set(ContainerBuilder::class, $container);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__. "/Resources/config")
        );

        $loader->load('services.yaml');

    }
}