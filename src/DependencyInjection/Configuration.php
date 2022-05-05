<?php

namespace Mink67\MultiPartDeserialize\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * 
 */
class Configuration implements ConfigurationInterface {


    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder(){

        $treepBuilder = new TreeBuilder("mink67_multipart_serialize");

        
        $root = $treepBuilder->getRootNode();


        if($root instanceof ArrayNodeDefinition){
            $root
                ->children()
                        ->scalarNode("public_path")
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                            ->scalarNode("upload_path")
                            ->isRequired()
                        ->cannotBeEmpty()
                ->end()
            ;

        }else{

            throw new InvalidConfigurationException("Root must be an instance to ArrayNodeDefinition");
        }

        return $treepBuilder;

        
    }

}