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
    /**
     * @var string
     */
    protected $normalizerClassName;
    /**
     * @var array
     */
    protected $normalizerParams;
    

    public function __construct(array $options = [])
    {
        //dd($options);

        if (!empty($options['propertyName'])) {
            $this->propertyName = $options['propertyName'];
        }

        if (!empty($options['normalizerClassName'])) {
            $this->normalizerClassName = $options['normalizerClassName'];            
        }

        $this->normalizerParams = [];
        
        if (isset($options['normalizerParams']) && !empty($options['normalizerParams']) && is_array($options['normalizerParams'])) {

            $this->normalizerParams = $options['normalizerParams'];

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
     * Get the value of propertyName
     *
     * @return  string
     */ 
    public function getNormaliserClassName()
    {
        return $this->normalizerClassName;
    }
    /**
     * Get the value of propertyName
     *
     * @return  array
     */ 
    public function getNormalizerParams()
    {
        return $this->normalizerParams;
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