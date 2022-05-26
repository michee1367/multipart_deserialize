<?php
// api/src/Serializer/ApiNormalizer

namespace Mink67\MultiPartDeserialize\Serializer;

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

final class MultiPartNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    private $decorated;
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

    public function __construct(NormalizerInterface $decorated, ReaderFile $readerFile, FileUploader $fileUploader)
    {
        if (!$decorated instanceof DenormalizerInterface) {
            throw new \InvalidArgumentException(sprintf('The decorated normalizer must implement the %s.', DenormalizerInterface::class));
        }

        $this->decorated = $decorated;
        $this->readerFile = $readerFile;
        $this->fileUploader = $fileUploader;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        //dd($object);
        $data = $this->decorated->normalize($object, $format, $context);
        
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

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $this->decorated->supportsDenormalization($data, $type, $format);
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return $this->decorated->denormalize($data, $class, $format, $context);
        
    }

    public function setSerializer(SerializerInterface $serializer)
    {
        if($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
}