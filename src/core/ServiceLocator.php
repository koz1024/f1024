<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\core;

/**
 * Service Locator Class
 */

class ServiceLocator {
    
    /**
     * singleton
     */
    private static $instance;

    private $services = [];
    
    private $classes = [];


    public function addInstance($name, $service) {
        $this->services[$name] = $service;
    }
    
    public function addClass($class, $params) {
        $this->classes[$class] = $params;
    }


    public function has($interface){
        return (isset($this->services[$interface]) || isset($this->classes[$interface]));
    }

    /**
     * @param string $class
     *
     * @return object
     */
    public function get($class) {
        if (isset($this->services[$class])) {

            return $this->services[$class];

        }elseif (isset($this->classes[$class])){
            
            $params = $this->classes[$class];
            
            //only 1 param can be used
            $obj = new $class($params);
            
            return $obj;
            
        }else{
            
            return null;
            
        }
    }
    
    //singleton
    
    public static function getInstance(){
        if (is_null(self::$instance)){
            self::$instance = new \f1024\core\ServiceLocator();
        }
        return self::$instance;
    }
    
    private function __construct() {}

}
