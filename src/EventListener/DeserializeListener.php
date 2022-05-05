<?php

namespace Mink67\MultiPartDeserialize\EventListener;

use ApiPlatform\Core\EventListener\DeserializeListener as DecoratedListerner;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use Mink67\MultiPartDeserialize\Services\FileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DeserializeListener  
{
    /**
     *@var DecoratedListerner
     */
    private $decorated;
    /**
     *@var SerializerContextBuilderInterface
     */
    private $serializerContextBuilder;
    /**
     *@var DenormalizerInterface
     */
    private $denormalizer;
    /**
     *@var FileUploader
     */
    private $fileUploader;

    public function __construct(
        DecoratedListerner $decorated,
        SerializerContextBuilderInterface $serializerContextBuilder,
        DenormalizerInterface $denormalizer,
        FileUploader $fileUploader
    ) {
        $this->decorated = $decorated;
        $this->serializerContextBuilder = $serializerContextBuilder;
        $this->denormalizer = $denormalizer;
        $this->fileUploader = $fileUploader;
    }


    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (
            $request->isMethodCacheable() ||
            $request->isMethod(Request::METHOD_DELETE)
        ) {
            return;            
        }

        //form 
        $contentType = $request->getContentType();

        if (
            $contentType == "multipart" ||
            $contentType == "form" 
        ) {
            $this->denormalizeFormMultipart($request);
            
        }else {
            $this->decorated->onKernelRequest($event);
        }
    }

    public function denormalizeFormMultipart(Request $request)
    {
        $atts = RequestAttributesExtractor::extractAttributes($request);
        //dd($atts);
        $populated = $request->attributes->get("data");

        if (! is_null($populated)) {
            $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $populated;
        }

        $context = $this->serializerContextBuilder->createFromRequest($request, false, $atts);

        $dataReq = $request->request->all();
        $dataFiles = $request->files->all();
        $fileUploader = $this->fileUploader;

        foreach ($dataFiles as $key => $value) {
            $dataFiles[$key] = $fileUploader->upload($value);
        }

        $object = $this->denormalizer->denormalize(
            array_merge($dataFiles, $dataReq),
            $atts["resource_class"],
            null,
            $context
        );

        //dd($object);
        $populated = $request->attributes->set("data", $object);

    }
}
