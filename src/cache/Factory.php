<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\cache;

/**
 * Factory for caching system
  */
class Factory extends \f1024\core\AbstractFactory{
    
    protected $provider;
    
    public function __construct(){
        $SL = \f1024\core\ServiceLocator::getInstance()->get('config');
        if (isset($SL['cache']) && isset($SL['cache']['type'])){
            switch (strtolower($SL['cache']['type'])){
                case 'redis':
                    $this->provider = \f1024\cache\Redis::getInstance($SL['cache']['settings']);
                    break;
                case 'memcache':
                    $this->provider = \f1024\cache\Memcache::getInstance($SL['cache']['settings']);
                    break;
                default:
                    throw new \Exception('Cache type '.$SL['cache']['type'].' not found');
            }
        }
    }
    
}