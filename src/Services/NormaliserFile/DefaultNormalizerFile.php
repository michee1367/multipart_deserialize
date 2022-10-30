<?php

namespace Mink67\MultiPartDeserialize\Services\NormaliserFile;

use Mink67\MultiPartDeserialize\Annotations\File;
use Mink67\MultiPartDeserialize\Commons\Normalizer\Context;
use Mink67\MultiPartDeserialize\Contracts\NormaliserFile;
use Mink67\MultiPartDeserialize\Services\FileUploader;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DefaultNormalizerFile implements NormaliserFile
{
    /**
     * @var FileUploader
     */
    private $fileUploader;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * 
     */
    public function __construct(
        FileUploader $fileUploader,
        ContainerInterface $container
    ) 
    {
        $this->fileUploader = $fileUploader;
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function normalize(array $data, File $file, Context $context)
    {
        $res = $data[$file->getPropertyName()];

        if (isset($data[$file->getPropertyName()])) {
            $value = $data[$file->getPropertyName()];

            if (!empty($value)) {

                $normalValue = $this->fileUploader->getUrl($value);
                $res = $this->transformUri($normalValue);

            }
            
        }

        return $res;
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
