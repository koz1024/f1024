<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\bootstrap\f;

/**
 * View for bootsrap
 */
class View implements \f1024\web\View{
    
    public $template = null;
    
    public function render($data = []){
        $response = \f1024\core\ServiceLocator::getInstance()->get('response');
        
        if ($data !== false && !is_null($this->template)){
            
            ob_start();
            include(__DIR__ . '/views/' . $this->template . '.php');
            $content = ob_get_clean();
            
            ob_start();
            include(__DIR__ . '/views/layout.php');
            $output = ob_get_clean();
            
            $response->response();
            echo $output;
        }
    }
}
