<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\cache;

/**
 * Abstract class for caching system
 * If you implement the new cache type you MUST extend this class
 */
abstract class CacheAbstract{
    
    /**
     * Instance. Need for singleton implementation
     * @access private
     * @var resource
     */
    protected static $instance;
    
    /**
     * @param array $settings Connection settings to storage
     */
    abstract protected function __construct($settings);
    
    /**
     * Gets value from storage by key
     * @param string $key Key
     * @param mixed $default The returned default value if key isn't present in storage
     * @return mixed Value
     */
    abstract public function get($key, $default = '');
    
    /**
     * Sets value to storage
     * @param string $key Key
     * @param mixed $value Value
     * @param integer $ttl Time to live in seconds. Once TTL passed, cache automatically will dropped
     * @param array $tags Array of tags. You may use tags for mass removing @see tags
     * @return mixed Value
     */
    abstract public function set($key, $value, $ttl = 0, $tags = []);
    
    /**
     * Gets the value from storage and if it isn't exist then sets it.
     * @param string $key Key
     * @param mixed $value Value
     * @param integer $ttl Time to live in seconds. Once TTL passed, cache automatically will dropped
     * @param array $tags Array of tags. You may use tags for mass removing @see tags
     * @return mixed Value
     */
    abstract public function value($key, $value, $ttl = 0, $tags = []);
    
    /**
     * Deletes value from storage by key
     * @param string $key Key
     * @return
     */
    abstract public function delete($key);
    
    /**
     * Deletes values from storage by tag
     * @param array $tags Array of tags that should be deleted
     */
    abstract public function invalidate($tags);
    
    /**
     * singleton implementation
     * @param array $settings
     * @return resource instance
     */
    public static function getInstance($settings){
        if (is_null(self::$instance)){
            self::$instance = new static($settings);
        }
        return self::$instance;
    }
    
}
