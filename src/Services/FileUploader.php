<?php
 
namespace Mink67\MultiPartDeserialize\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\UrlHelper;
 
class FileUploader
{
    private $uploadPath;
    private $slugger;
    private $urlHelper;
    private $relativeUploadsDir;
 
    public function __construct(ContainerBagInterface $containerBag, SluggerInterface $slugger, UrlHelper $urlHelper)
    {

        $this->uploadPath = $containerBag->get("mink67.multipart_deserializer.upload_path");
        $this->slugger = $slugger;
        $this->urlHelper = $urlHelper;
 
        // get uploads directory relative to public path //  "/uploads/"
        $this->relativeUploadsDir = str_replace(
            $containerBag->get("mink67.multipart_deserializer.public_path"), 
            '', 
            $this->uploadPath).'/';
    }
 
    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
 
        try {
            $file->move($this->getuploadPath(), $fileName);
        } catch (FileException $e) {
            throw $e;
            
            // ... handle exception if something happens during file upload
        }
 
        return $fileName;
    }
 
    public function getuploadPath()
    {
        return $this->uploadPath;
    }
 
    public function getUrl(?string $fileName, bool $absolute = true)
    {
        if (empty($fileName)) return null;
 
        if ($absolute) {
            return $this->urlHelper->getAbsoluteUrl($this->relativeUploadsDir.$fileName);
        }
 
        return $this->urlHelper->getRelativePath($this->relativeUploadsDir.$fileName);
    }
}