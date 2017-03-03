<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\web;

/**
 * Request Class.
 */
class Request{
    

    /**
     * Returns specified GET param or default value
     * @param string $name
     * @param mixed $default Value that will be returned if param is absent
     * @return mixed
     */
    public function get($name, $default = null){
        return (isset($_GET[$name])) ? $_GET[$name] : $default;
    }

    /**
     * Returns specified POST param or default value
     * @param string $name
     * @param mixed $default Value that will be returned if param is absent
     * @return mixed
     */    
    public function post($name, $default = null){
        return (isset($_POST[$name])) ? $_POST[$name] : $default;
    }
    
    /**
     * Always returns false :)
     * 
     * @todo
     * @param string @name
     * @return boolean false
     */
    public function fileUpload($name){
        //not implemented yet
        return false;
    }
    
    /**
     * Returns specified HEADER
     * @param string $name
     * @return mixed
     */
    public function header($name){
        $headers = getallheaders();
        return (isset($headers[$name])) ? $headers[$name] : false;
    }
    
    /**
     * Returns specified GET or POST param or default value
     * @param string $name
     * @param mixed $default Value that will be returned if param is absent
     * @return mixed
     */
    public function req($name = false, $default = false){
        if ($name){
            return (isset($_REQUEST[$name])) ? $_REQUEST[$name] : $default;
        }else{
            return $_REQUEST;
        }
    }
}
