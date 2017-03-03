<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\core;

/**
 * Base Abstract Factory Class
 */
abstract class AbstractFactory{
    
    protected $provider;
    
    abstract public function __construct();
    
    public function __call($name, $arguments) {
        $return = null;
        switch (sizeof($arguments)){
            case 0:
                $return = $this->provider->$name();
                break;
            case 1:
                $return = $this->provider->$name($arguments[0]);
                break;
            case 2:
                $return = $this->provider->$name($arguments[0], $arguments[1]);
                break;
            case 3:
                $return = $this->provider->$name($arguments[0], $arguments[1], $arguments[2]);
                break;
            case 4:
                $return = $this->provider->$name($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
                break;
            default:
                throw new \Exception('Too much params');
        }
        return $return;
    }
}
