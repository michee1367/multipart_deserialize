<?php
namespace Mink67\MultiPartDeserialize\Annotations;

use Attribute;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class File {
    /**
     * @var string
     */
    protected $propertyName;
    

    public function __construct(array $options = [])
    {

        if (!empty($options['propertyName'])) {
            $this->propertyName = $options['propertyName'];
        }
        
    }

    /**
     * Get the value of propertyName
     *
     * @return  string
     */ 
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * Set the value of propertyName
     *
     * @param  string  $propertyName
     *
     * @return  self
     */ 
    public function setPropertyName(string $propertyName)
    {
        $this->propertyName = $propertyName;

        return $this;
    }
}