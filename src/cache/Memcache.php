<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\cache;

use \f1024\cache\CacheAbstract;

class Memcache extends CacheAbstract{
    
    private $conn;
    
    /**
     * @param array $settings Connection settings to storage
     */
    public function  __construct($settings){
        try{
            $this->conn = new \Memcache();
            $this->conn->connect(isset($settings['host']) ? $settings['host'] : '127.0.0.1', isset($settings['port']) ? $settings['port'] : 11211);
        }catch(\Exception $e){
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
    }
    
    /**
     * Gets value from storage by key
     * @param string $key Key
     * @param mixed $default The returned default value if key isn't present in storage
     * @return mixed Value
     */
    public function get($key, $default = ''){
        $return = $this->conn->get($key);
        return ($return) ? $return : $default;
    }
    
    /**
     * Sets value to storage
     * @param string $key Key
     * @param mixed $value Value
     * @param integer $ttl Time to live in seconds. Once TTL passed, cache automatically will dropped
     * @param array $tags Array of tags. You may use tags for mass removing @see tags
     * @return mixed Value
     */
    public function set($key, $value, $ttl = 0, $tags = []){
        
        $this->conn->set(md5($key), $value, false, $ttl);
        
        if (!empty($tags)){
            $_tags = $this->conn->get('tags');
            if (!$_tags){
                $_tags = [];
            }
            foreach ($tags as $tag){
                if (!isset($_tags[$tag])){
                    $_tags[$tag] = [];
                }
                $_tags[$tag][] = md5($key);
            }
            $this->conn->set('tags', serialize($_tags), false, 0);
        }
        return $value;
    }
    
    /**
     * Gets the value from storage and if it isn't exist then sets it.
     * @param string $key Key
     * @param mixed $value Value
     * @param integer $ttl Time to live in seconds. Once TTL passed, cache automatically will dropped
     * @param array $tags Array of tags. You may use tags for mass removing @see tags
     * @return mixed Value
     */
    public function value($key, $value, $ttl = 0, $tags = []) {
        $_value = $this->conn->get(md5($key));
        if ($_value){
            return $_value;
        }else{
            return $this->set($key, $value, $ttl, $tags);
        }
    }
    
    /**
     * Deletes value from storage by key
     * @param string $key Key
     * @return
     */
    public function delete($key) {
        return $this->conn->delete(md5($key), 0);
    }
    
    /**
     * Deletes values from storage by tag
     * @param array $tags Array of tags that should be deleted
     */
    public function invalidate($tags) {
        $_tags = $this->conn->get('tags');
        if (!$_tags){
            $_tags = [];
        }
        foreach ($tags as $tag){
            if (isset($_tags[$tag])){
                foreach ($_tags[$tag] as $key){
                    $this->conn->delete($key);
                }
                unset($_tags[$tag]);
            }
        }
        $this->conn->set('tags', serialize($_tags), false, 0);
    }
}
