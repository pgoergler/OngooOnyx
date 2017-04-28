<?php

namespace Ongoo\Onyx\Utils;

/**
 * Description of OptionalObject
 *
 * @author paul
 */
class OptionalObject
{
    
    protected $object;
    protected $defaultValue = null;
    
    public static function of($object = null) {
        return new OptionalObject($object);
    }
    
    protected function __construct(&$object = null) {
        $this->object = $object;
    }
    
    public function defaultAs($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }
    
    public function getObject()
    {
        return $object;
    }
    
    public function __call($methodName, $args) {
        if( is_null($this->object) )
        {
            return $this->defaultValue;
        }
        
        return call_user_func_array(array($this->object, $methodName), $args);
    }
}
