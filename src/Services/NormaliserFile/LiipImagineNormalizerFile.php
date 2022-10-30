<?php

namespace Mink67\MultiPartDeserialize\Services\NormaliserFile;

use Mink67\MultiPartDeserialize\Annotations\File;
use Mink67\MultiPartDeserialize\Commons\Normalizer\Context;
use Mink67\MultiPartDeserialize\Contracts\NormaliserFile;
use Mink67\MultiPartDeserialize\Services\FileUploader;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use League\Uri\Uri;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LiipImagineNormalizerFile implements NormaliserFile
{
    /**
     * @var FileUploader
     */
    private $fileUploader;
    /**
     * @var CacheManager
     */
    private $cacheManager;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * 
     */
    public function __construct(
        FileUploader $fileUploader, 
        CacheManager $cacheManager,
        ContainerInterface $container
    ) 
    {
        $this->fileUploader = $fileUploader;
        $this->cacheManager = $cacheManager;
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function normalize(array $data, File $file, Context $context)
    {
        $res = !!$data[$file->getPropertyName()]? [$data[$file->getPropertyName()]]:[];
        $filters = $this->getNormalFilters($context->getParam("filter"));


        if (isset($data[$file->getPropertyName()])) {
            $value = $data[$file->getPropertyName()];

            if (!empty($value)) {

                $me = $this;
                $res = [];

                foreach ($filters as $key => $filter) {
                    $res[$filter] = $this->transformUri($me->cacheManager->getBrowserPath($value, $filter));
                }

                //$normalValue = $this->fileUploader->getUrl($value);
                //$res = $normalValue;

            }
            
        }
        //$this->cacheManager->getBrowserPath($item->getIncumbentPhoto(), "pic_producer")

        return $res;
    }

    private function getNormalFilters($filter) : array
    {
        $normalFilters = [];

        if (is_string($filter)) {
            $normalFilters = [$filter];
        } elseif (is_array($filter)) {
            $normalFilters = $filter;          
        }

        return $normalFilters;
    }
    /**
     * 
     */
    public function transformUri(?string $url) : ?string
    {
        if (is_null($url)) {
            return $url;
        }
        
        $uri = Uri::createFromString($url);
        $host = $this->container->getParameter("mink67.multipart_deserializer.host_name");

        if (is_null($host)) {
            return $url;
        }

        return $host."/".$uri->getPath();
    }
}
