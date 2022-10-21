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

final class MultiPartNormalizer
{
    /**
     * @var ReaderFile
     */
    private $readerFile;
    /**
     * @var PropertyAccess
     */
    private $accessor;
    /**
     * @var FileUploader
     */
    private $fileUploader;

    public function __construct(ReaderFile $readerFile, FileUploader $fileUploader)
    {
        $this->readerFile = $readerFile;
        $this->fileUploader = $fileUploader;
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