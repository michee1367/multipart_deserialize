<?php
namespace Mink67\MultiPartDeserialize\Annotations\Readers;

use Doctrine\Common\Annotations\Reader;
use Mink67\MultiPartDeserialize\Annotations\File;

class ReaderFile {

    /**
     * @var Reader
     */
    private $reader;
    

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }


    /**
     * @param string $className
     * @return File[]
     */
    private function getFileAnnotation(string $className): ?array
    {
        $reflection = new \ReflectionClass($className);
        /**
         * @var \ReflectionProperty[]
         */
        $properties = $reflection->getProperties();
        
        $filesAnn = [];
        
        foreach ($properties as $property) {
                //dump($className);
                //dump($property->getName());
                /**
                 * @var File
                 */
                $ann = $this->reader->getPropertyAnnotation($property, File::class);
                
                
                if (!is_null($ann)) {
                    $ann->setPropertyName($property->getName());
                   array_push($filesAnn, $ann);
                }

        }

        return $filesAnn;
    }
    /**
     * 
     */

    /**
     * @param string $className
     * @return File[] 
     */
    private function getFileAttribute(string $className):?array
    {
        $reflection = new \ReflectionClass($className);

        /**
         * @var \ReflectionProperty[]
         */
        $properties = $reflection->getProperties();
        
        $filesAnn = [];

        
        foreach ($properties as $property) {
                $atts = $property->getAttributes();

                foreach ($atts as $key => $att) {
                    //dump($property->getName());
                    //dd($att->getArguments());
                    if ($att->getName() == File::class) {
                        $options = $att->getArguments();
                        $options["propertyName"] = $property->getName();
                        $ann = new File(
                            $options
                        );
                        array_push($filesAnn, $ann);
                    }
                }

        }

        return $filesAnn;

    }
    /**
     * @param string $className
     * @return File[] 
     */
    public function getFiles(string $className):?array
    {
        
        $configs = [
            ... $this->getFileAttribute($className),
            ... $this->getFileAnnotation($className)
        ];

        return $configs;
    }
    

}