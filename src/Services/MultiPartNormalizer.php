<?php
// api/src/Serializer/ApiNormalizer

namespace Mink67\MultiPartDeserialize\Services;

use App\Serializer\UnexpectedValueException as SerializerUnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use JsonPath\JsonObject;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Mink67\MultiPartDeserialize\Annotations\Readers\ReaderFile;
use Mink67\MultiPartDeserialize\Services\FileUploader;
use Doctrine\Common\Util\ClassUtils;
use Mink67\MultiPartDeserialize\Annotations\File;
use Mink67\MultiPartDeserialize\Commons\Normalizer\Context;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Mink67\MultiPartDeserialize\Services\NormaliserFile\DefaultNormalizerFile;
use Mink67\MultiPartDeserialize\Contracts\NormaliserFile;

final class MultiPartNormalizer
{
    /**
     * @var ReaderFile
     */
    private $readerFile;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var FileUploader
     */
    private $fileUploader;
    /**
     * @var DefaultNormalizerFile
     */
    private $defaultNormalizerFile;

    public function __construct(
        ReaderFile $readerFile, 
        FileUploader $fileUploader, 
        ContainerInterface $container,
        DefaultNormalizerFile $defaultNormalizerFile
    )
    {
        $this->readerFile = $readerFile;
        $this->fileUploader = $fileUploader;
        $this->container = $container;
        $this->defaultNormalizerFile = $defaultNormalizerFile;
    }


    public function normalize($object, $dataOrg)
    {
        //dd($object);
        $data = array_reduce(
            array_keys($dataOrg),
            function ($prev, $key) use ($dataOrg)
            {
                $prev[$key] = $dataOrg[$key];
                return $prev;
            },
            []
        )
        ;
        
        $accessor = PropertyAccess::createPropertyAccessor();


        $files = $this->readerFile->getFiles(ClassUtils::getClass($object));

        foreach ($files as $key => $file) {
            $normalizer = $this->getNormalizer($file) ;
            $context = $this->getContextNormalizer($data, $object, $file);

            $normalValue = $normalizer->normalize($data, $file, $context);
            $data[$file->getPropertyName()] = $normalValue;
        }

        return $data;
    }

    /**
     * 
     */
    private function getNormalizer(File $file) : NormaliserFile
    {
        $className = $file->getNormaliserClassName();
        //dd($className);
        if (!$className) {
            $normalizer = $this->defaultNormalizerFile;
        }else{
            $normalizer = $this->container->get($className);
            //Mink67\MultiPartDeserialize\Services\NormaliserFile\DefaultNormalizerFile
        }

        return $normalizer;
    }

    /**
     * @return File[]
     */
    public function getFilesToObject($object)
    {
        $files = $this->readerFile->getFiles(ClassUtils::getClass($object));

        return $files;
    }

    /**
     * @return array
     */
    public function getDataFiles($object)
    {
        $files = $this->getFilesToObject($object);
        $data = [];
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($files as $key => $file) {
            $data[$file->getPropertyName()] = $accessor->getValue($object, $file->getPropertyName()); 
        }

        return $data;
    }

    /**
     * 
     */
    public function getDataFilesNormalize($object)
    {
        $dataFiles = $this->getDataFiles($object);
        $dataFilesNormalize = $this->normalize($object, $dataFiles);
        return $dataFilesNormalize;
    }

    /**
     * 
     */
    private function getContextNormalizer(array $dataArg, $object, File $file)
    {
        $data = $data = array_reduce(
            array_keys($dataArg),
            function ($prev, $key) use ($dataArg)
            {
                $prev[$key] = $dataArg[$key];
                return $prev;
            },
            []
        )
        ;

        $data["object"] = $object;
        $params = $file->getNormalizerParams();

        return new Context($data, $params);
    }

    

    public function getPropertyFile($object, $dataOrg)
    {
        //dd($object);
        $data = array_reduce(
            array_keys($dataOrg),
            function ($prev, $key) use ($dataOrg)
            {
                $prev[$key] = $dataOrg[$key];
                return $prev;
            },
            []
        )
        ;
        
        $accessor = PropertyAccess::createPropertyAccessor();

        $files = $this->readerFile->getFiles(ClassUtils::getClass($object));

        foreach ($files as $key => $file) {
            if (isset( $data[$file->getPropertyName()])) {
                $value = $accessor->getValue($object, $file->getPropertyName());

                if (empty($value)) {
                    $data[$file->getPropertyName()] = null;
                }else {
                    $normalValue = $this->fileUploader->getUrl($value);
                    $data[$file->getPropertyName()] = $normalValue;
                }
                
            }
        }

        return $data;
    }


}