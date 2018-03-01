<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\console;

use f1024\console\base\Console as C;

/**
 * Router for console-based applications
 */
class Router{
    
    private $SL;
    
    private $systemRoutes = [
        
    ];
    
    public function __construct(){
        $this->SL = \f1024\core\ServiceLocator::getInstance();
        
        $files = glob($this->SL->get('config')['basePath'] . '/commands/*.php');
        foreach ($files as $file){
            //todo: checkings
            
            $name = explode('/', $file);
            $name = $name[sizeof($name)-1];
            $name = substr($name, 0, strpos($name, '.'));
            
            $this->SL->addClass('commands\\'.$name, $this->SL);
        }
    }
    
    public function route($params){
        if (sizeof($params) > 1){
            if (isset($this->systemRoutes[$params[1]])){
                $route  = $this->systemRoutes[$params[1]];
                $class  = $this->SL->get($route[0]);
                $action = $route[1];
                $param  = isset($params[2]) ? $params[2] : null;
                if (method_exists($class, $action)){
                    $class->$action($param);
                }else{
                    $class->index($param);
                }
            }elseif($this->SL->has('commands\\'.$params[1])){
                $class  = $this->SL->get('commands\\'.$params[1]);
                $action = isset($params[2]) ? $params[2] : 'index';
                $param  = isset($params[3]) ? $params[3] : null;
                if (method_exists($class, $action)){
                    $class->$action($param);
                }else{
                    $class->index($param);
                }
            }else{
                echo C::format("No Route for ", [C::FG_RED]) . C::format($params[1], [C::FG_RED, C::BOLD]) . "\r\n";
            }
        }else{
            echo C::format("You should pass at least one param", [C::FG_RED]) . "\r\n";
        }
    }
}
