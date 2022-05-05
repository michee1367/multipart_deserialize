<?php

namespace Mink67\MultiPartDeserialize\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * 
 */
class MultiPartDeserializeExtension extends Extension {


    /**
     * Loads a specific configuration.
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container){



        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        //dd($config);

        $container->setParameter("mink67.multipart_deserializer.public_path", $config["public_path"]);
        $container->setParameter("mink67.multipart_deserializer.upload_path", $config["upload_path"]);
        

    }

}
