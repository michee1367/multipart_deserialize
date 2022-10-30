<?php

namespace Mink67\MultiPartDeserialize\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use function Symfony\Component\String\u;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Mink67\MultiPartDeserialize\Services\NormaliserFile\LiipImagineNormalizerFile;

/**
 * 
 */
class MultiPartDeserializeExtension extends Extension {

    /**
     * 
     */
    public function __construct() 
    {
        
        //$this->name = "mink67.security.bundle";
    }

    /**
     * Loads a specific configuration.
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container){



        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        //dd($config);
        $hostName = isset($config["host_name"]) ? $config["host_name"] : null ;
        $container->setParameter("mink67.multipart_deserializer.public_path", $config["public_path"]);
        $container->setParameter("mink67.multipart_deserializer.upload_path", $config["upload_path"]);
        $container->setParameter("mink67.multipart_deserializer.host_name", $hostName);
        

    }

}
