<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\core;

/**
 * Core class
 */
class Core{
    
    private static $config;
    
    public function __construct($config){
        
        $SL = \f1024\core\ServiceLocator::getInstance();
        
        $SL->addInstance('config', $config);
        
        self::$config = $config;
        
    }
    
    public function run($type = 'web'){
        
        if (strtolower($type) == 'web'){
            
            $this->runWeb();
            
        }elseif (strtolower($type) == 'console'){
            
            $this->runConsole();
            
        }else{
            trigger_error('Unknown type of application');
        }
    }
    
    private function runWeb(){
        
        $uri = $_SERVER['REQUEST_URI'];

        if (($_pos = strpos($uri, '?'))!==false){
            $uri = substr($uri, 0, $_pos);
        }
        
        $router = new \f1024\core\Router();
        
        $router->route($uri);
    }
    
    private function runConsole(){
        global $argv;
        
        $router = new \f1024\console\Router();
        
        $router->route($argv);
        
    }
    
    public static function autoload($className){
        
        $basePath = (isset(self::$config['basePath'])) ? self::$config['basePath'] : __DIR__;
        
        $path = $basePath . '/'. str_replace('\\', '/', $className) . '.php';
        
        if (file_exists($path)){
            include($path);
        }
    }
}
