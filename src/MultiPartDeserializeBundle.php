<?php

namespace Mink67\MultiPartDeserialize;

use Mink67\MultiPartDeserialize\Services\NormaliserFile\LiipImagineNormalizerFile;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Config\LiipImagineConfig;

/**
 * Perment de créer un un kafka connect
 */
class MultiPartDeserializeBundle extends Bundle
{
    /**
     * @var LiipImagineNormalizerFile
     */
    protected $liipImagineNormalizerFile;

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
        //$container->set(LiipImagineNormalizerFile::class, $this->liipImagineNormalizerFile);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__. "/Resources/config")
        );

        $loader->load('services.yaml');

    }
}