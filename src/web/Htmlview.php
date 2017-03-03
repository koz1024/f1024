<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\web;

use f1024\web\View;

class Htmlview implements View{
    
    private $template = null;
    private $layout   = null;
    
    /**
     * @todo
     * @var boolean isCacheable 
     */
    public $isCacheable = false;
    
    public $params = [];
    
    
    public function flashMessage($message, $class = ''){
        $_SESSION['flash']['message'] = $message;
        $_SESSION['flash']['class']   = $class;
        return true;
    }
    
    /**
     * Sets template
     * @param string $tpl Filename of template
     */
    public function template($tpl){
        $this->template = $tpl;
    }
    
    /**
     * Sets layout
     * @param string $tpl Filename of layout
     */
    public function layout($tpl){
        $this->layout = $tpl;
    }
    
    /**
     * Renders and outputs everything
     * @param mixed $data Data to be rendered or false if no need to render
     */
    public function render($data = []){
        if ($data !== false){
            $config   = \f1024\core\ServiceLocator::getInstance()->get('config');
            $response = \f1024\core\ServiceLocator::getInstance()->get('response');
            
            if (!is_null($this->template)){
                
                if (isset($config['views']) && isset($config['views']['path'])){
                    $path = $config['basePath'] . $config['views']['path'];
                }else{
                    $path = $config['basePath'] . '/views';
                }
                
                ob_start();
                include($path . '/'.$this->template.'.php');
                $content = ob_get_clean();

                ob_start();

                include($path . '/' . (!is_null($this->layout) ? $this->layout : 'layout') . '.php');

                $output = ob_get_clean();
                
                //if ($this->isCacheable){
                    //todo
                //}
                
                $response->response();
                echo $output;    
            }
        }
        $_SESSION['flash'] = false;
    }
    
    /**
     * Partial render
     * @param string $tpl
     * @param mixed $data
     */
    public function renderPartial($tpl, $data = []){
        $config   = \f1024\core\ServiceLocator::getInstance()->get('config');
        if (isset($config['views']) && isset($config['views']['path'])){
            $path = $config['basePath'] . $config['views']['path'];
        }else{
            $path = $config['basePath'] . '/views';
        }
        include($path . '/' . $tpl . '.php');
        $_SESSION['flash'] = false;
    }
    
}
