<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\cache;

class Redis extends \f1024\cache\CacheAbstract{
    
    private $conn;
    
    /**
     * @param array $settings Connection settings to storage
     */
    protected function  __construct($settings){
        $this->conn = new \Redis();
        if (!$this->conn){
            trigger_error('Cannot start Redis', E_USER_ERROR);
        }
        $this->conn->connect(
                (isset($settings['host'])) ? $settings['host'] : 'localhost',
                (isset($settings['port'])) ? $settings['port'] : 6379
        );
        $this->conn->select(isset($settings['database']) ? $settings['database'] : 0);
        
    }
    
    /**
     * Gets value from storage by key
     * @param string $key Key
     * @param mixed $default The returned default value if key isn't present in storage
     * @return mixed Value
     */
    public function get($key, $default = ''){
        $_key = md5($key);
        return ($this->conn->exists($_key)) ? $this->_get($_key) : $default;
    }
    
    /**
     * @access private
     * @param string $key
     * @return mixed value
     */
    private function _get($key){
        $value  = $this->conn->get($key);
        $type   = substr($value, 0, 1);
        $_value = substr($value, 1);
        switch ($type){
            case 'b':
                $return = (strtolower($_value)=='true') ? true : false;
                break;
            case 'i':
                $return = intval($_value);
                break;
            case 'd':
                $return = floatval($_value);
                break;
            case 's':
                $return = $_value;
                break;
            case 'a':
            case 'o':
            case 'r':
                $return = unserialize($_value);
                break;
            case 'N':
            default:
                $return = null;
        }
        return $return;
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
        
        $_value = substr(gettype($value), 0, 1);
        if (is_array($value) || is_object($value)){
            $_value .= serialize($value);
        }else{
            $_value .= (string)$value;
        }
        
        $_key = md5($key);
        
        if ($ttl > 0){
            $this->conn->setex($_key, $ttl, $_value);
        }else{
            $this->conn->set($_key, $_value);
        }
        
        if (!empty($tags)){
            if ($this->conn->exists('tags')){
                $_tags = unserialize($this->conn->get('tags'));
            }else{
                $_tags = [];
            }
            foreach ($tags as $tag){
                if (!isset($_tags[$tag])){
                    $_tags[$tag] = [];
                }
                $_tags[$tag][] = $_key;
            }
            $this->conn->set('tags', serialize($_tags));
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
        if ($this->conn->exists(md5($key))){
            return $this->get($key);
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
        return $this->conn->del(md5($key));
    }
    
    /**
     * Deletes values from storage by tag
     * @param array $tags Array of tags that should be deleted
     */
    public function invalidate($tags) {
        if ($this->conn->exists('tags')){
            $_tags = unserialize($this->conn->get('tags'));
        }else{
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
        $this->conn->set('tags', serialize($_tags));
    }
}
