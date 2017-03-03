<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\web;

class Response{
    
    /**
     * @var int HTTP status code
     */
    protected $code     = 200;
    
    /**
     * @var string Content Type
     */
    protected $type     = 'text/html';
    
    /**
     * @var string Charset
     */
    protected $charset  = 'utf-8';
    
    /**
     * @var array Headers
     */
    protected $headers  = [];
    
    /**
     * Magic method
     * Gets the header
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name){
        if (property_exists(__CLASS__, $name)){
            return $this->$name;
        }else{
            return false;
        }
    }
    
    /**
     * Magic method
     * Sets the header
     * 
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value){
        if (property_exists(__CLASS__, $name)){
            $this->$name = $value;
        }else{
            $this->headers[$name] = $value;
        }
    }
    
    /**
     * Outputs headers
     */
    public function response(){
        header('HTTP/1.1 '.$this->code);
        header('Content-Type: '.$this->type.'; charset='.$this->charset);
        foreach ($this->headers as $name => $value){
            header($name.': '.$value);
        }
    }
    
    
}
