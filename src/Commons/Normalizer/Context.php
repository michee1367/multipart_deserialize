<?php
 
namespace Mink67\MultiPartDeserialize\Commons\Normalizer;
 
class Context {

    /**
     * @var array
     */
    private $data;
    /**
     * @var array
     */
    private $params;

    /**
     * 
     */
    public function __construct(array $data = [], array $params = []) {
        $this->data = $data;
        $this->params = $params;
    }

    /**
     * @return 
     */
    public function get(string $key, $default=null) {
        return $this->has($key) ? $this->data[$key] : $default;
    }
    /**
     * @return 
     */
    public function getParam(string $key, $default=null) {
        return $this->hasParam($key)? $this->params[$key]: $default;
    }
    
    /**
     * @return bool
     */
    public function has(string $key) {
        return isset($this->data[$key]) ;
    }
    /**
     * @return bool
     */
    public function hasParam(string $key) {
        return isset($this->params[$key]);
    }
    

}