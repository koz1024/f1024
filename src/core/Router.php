<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\core;

/**
 * Router class
 */
class Router{
    
    private $SL = null;
    protected $DB = null;
    
    /**
     * Constructor
     * Crawling controllers folder in search of php classes (controllers)
     * Adding found controllers to Service Location
     */
    public function __construct(){
        $this->SL = \f1024\core\ServiceLocator::getInstance();
        
        $files = glob($this->SL->get('config')['basePath'] . '/controllers/*.php');
        foreach ($files as $file){
            //todo: checkings
            
            $name = explode('/', $file);
            $name = $name[sizeof($name)-1];
            $name = substr($name, 0, strpos($name, '.'));
            
            $this->SL->addClass('controllers\\'.$name, $this->SL);
        }
        
    }
    
    /**
     * Route.
     * Getting instance of db class
     * Checking for fitting route (routing)
     * 
     * @param string $uri
     * @return null
     */
    public function route($uri){
        $config = $this->SL->get('config');
        
        $this->DB = new \f1024\db\Db($config);
        $this->SL->addInstance('DB', $this->DB);
        
        if (isset($config['global']['use_codegenerator']) && $config['global']['use_codegenerator'] == true){
            if (strpos($uri, '/f/')===0){
                $f = new \f1024\bootstrap\f\F();
                $f->route($uri);
                return ;
            }
        }
        
        $isProceeded = $this->checkDatabase($config, $uri);
        if (!$isProceeded){
            $isProceeded = $isProceeded || $this->checkRules($config, $uri);
        }
        
        if (!$isProceeded){
            $isProceeded = $isProceeded || $this->checkRoutes($config, $uri);
        }
        
        if (!$isProceeded){
            $isProceeded = $isProceeded || $this->checkRestSegments($config, $uri);
        }
        
        if (!$isProceeded){
            $isProceeded = $isProceeded || $this->checkSegments($config, $uri);
        }
        
        if (!$isProceeded){
            $isProceeded = $isProceeded || $this->checkDefault();
        }
        
        if (!$isProceeded){
            $this->showNotFound();
        }
        
    }
    
    /**
     * Match URI with routes on database
     * @param array $config
     * @param string $uri
     * @return boolean
     */
    protected function checkDatabase($config, $uri){
        
        $return = false;
        
        if ($config['global']['prettyurl']=='db'){
            
            $r = $this->DB->query("SELECT * FROM url WHERE alias = ?", [$uri])->resultArray();
            
            if (!empty($r) && isset($r[0])){
                
                $routing = $r[0];
                
                if ($routing['status']=='200'){
                
                    $controller = $routing['controller'];
                    $action     = strtolower($routing['action']);
                    $params     = $routing['params'];

                    $class = $this->SL->get($controller);
                    $data  = $class->$action($params);
                    $class->view->render($data);
                    
                    $return = true;
                    
                }else{
                    $this->showStatus($routing['status'], $routing['status_text']);
                    $return = true;
                }
            }
        }
        
        return $return;
    }
    
    /**
     * Match URI with rules in config file
     * @param array $config
     * @param string $uri
     * @return boolean
     */
    protected function checkRules($config, $uri){
        $return = false;
        
        if (isset($config['global']['rules']) && is_array($config['global']['rules'])){
            
            $routing = $config['global']['rules'];
            
            foreach ($routing as $rule => $scenario){
                
                if (preg_match('#'.$rule.'#is', $uri, $params)){
                    
                    if (isset($scenario['status']) && $scenario['status']!=200){
                        
                        $this->showStatus($scenario['status'], (isset($scenario['status_text']) ? $scenario['status_text'] : ''));
                        
                        $return = true;
                        
                        break;
                        
                    }else{
                    
                        if ($this->SL->has($scenario['controller'])){

                            $action = $scenario['action'];
                            if (sizeof($params)==2){
                                $params = $params[1];
                            }else{
                                unset($params[0]);
                                $params = array_values($params);
                            }
                            //$params = (isset($scenario['params'])) ? $scenario['params'] : false;

                            $class  = $this->SL->get($scenario['controller']);
                            if ($params){
                                $data = $class->$action($params);
                            }else{
                                $data = $class->$action();
                            }
                            $class->view->render($data);
                            $return = true;
                            break;
                        }
                        
                    }
                }
            }
        }
        
        return $return;
    }
    
    /**
     * Match URI with routes in config file
     * @param array $config
     * @param string $uri
     * @return boolean
     */
    protected function checkRoutes($config, $uri){
        
        $return = false;
        
        if (isset($config['global']['routing']) && is_array($config['global']['routing'])){
            
            $routing = $config['global']['routing'];
            
            foreach ($routing as $suri => $scenario){
                
                if ($uri == $suri){
                    
                    if (isset($scenario['status']) && $scenario['status']!=200){
                        
                        $this->showStatus($scenario['status'], (isset($scenario['status_text']) ? $scenario['status_text'] : ''));
                        
                        $return = true;
                        
                        break;
                        
                    }else{
                    
                        $action = $scenario['action'];
                        $params = (isset($scenario['params'])) ? $scenario['params'] : false;

                        $class  = $this->SL->get($scenario['controller']);
                        if ($params){
                            $data = $class->$action($params);
                        }else{
                            $data = $class->$action();
                        }
                        $class->view->render($data);
                        
                        $return = true;
                        break;
                        
                    }
                }
            }
        }
        
        return $return;
    }
    
    /**
     * Parse URI on segments and find matching controller/action for REST APPLICATIONS
     * @param array $config
     * @param string $uri
     * @return boolean
     */
    protected function checkRestSegments($config, $uri){
        $return = false;
        if (!isset($config['global']['disableRestRouting']) || isset($config['global']['disableRestRouting']) && $config['global']['disableRestRouting'] === false){
            $method     = $_SERVER['REQUEST_METHOD'];
            $segments   = explode('/', strtolower($uri));
            $controller = '\\controllers\\' . ucfirst($segments[1]) . 'Controller';
            switch (strtolower($method)){
                case 'get':
                    $action = 'index';
                    if (isset($segments[2])){
                        $params = $segments[2];
                    }
                    break;
                case 'post':
                    $action = 'update';
                    if (isset($segments[2])){
                        $params = $segments[2];
                    }
                    break;
                case 'put':
                    $action = 'create';
                    break;
                case 'delete':
                    $action = 'delete';
                    if (isset($segments[2])){
                        $params = $segments[2];
                    }
                    break;
                default:
                    $action = (isset($segments[2])) ? $segments[2] : 'index';
                    if (isset($segments[3])){
                        $params = $segments[3];
                    }
            }

            if ($this->SL->has($controller)){
                $class = $this->SL->get($controller);
                if (isset($params)){
                    $data = $class->$action($params);
                }else{
                    $data = $class->$action();
                }
                $class->view->render($data);
                $return = true;
            }
        }
        return $return;
    }
    
    /**
     * Parse URI on segments and find matching controller/action
     * @param array $config
     * @param string $uri
     * @return boolean
     */
    protected function checkSegments($config, $uri){
        $return = false;
        if (!isset($config['global']['disableSegmentRouting']) || isset($config['global']['disableSegmentRouting']) && $config['global']['disableSegmentRouting'] === false){
            $segments   = explode('/', strtolower($uri));
            $controller = '\\controllers\\' . ucfirst($segments[1]) . 'Controller';
            $action     = (isset($segments[2])) ? $segments[2] : 'index';
            $params     = (isset($segments[3])) ? $segments[3] : false;
            if ($this->SL->has($controller)){
                $class = $this->SL->get($controller);
                if ($params){
                    $data = $class->$action($params);
                }else{
                    $data = $class->$action();
                }
                $class->view->render($data);
                $return = true;
            }
        }
        
        return $return;
    }
    
    /**
     * Call default controller/action if it present
     * @return boolean
     */
    protected function checkDefault(){
        try{
            $class = new \controllers\MainController($this->SL);
            if (method_exists($class, 'index')){
                $data = $class->index();
                $class->view->render($data);
                $return = true;
            }
        }catch(\Exception $e){
            $return = false;
        }
        
        return $return;
    }
    
    /**
     * Show hard "not found" error (404)
     */
    protected function showNotFound(){
        header('HTTP/1.1 404 Not Found');
        exit();
    }
    
    /**
     * Output custom status and headers
     * @param integer $code
     * @param string $text
     */
    protected function showStatus($code = 200, $text = ''){
        
        $reason = new \f1024\helpers\Reasons();
        
        header('HTTP/1.1 ' . $code . ' ' . (isset($reason[$code]) ? $reason[$code] : ''));
        
        if (!empty($text)){
            header($text);
        }
        
        exit();
    }
}