<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\bootstrap\f;

class F{
    
    public function route($uri){
        $segments   = explode('/', strtolower($uri));
        $action     = (isset($segments[2]) && !empty($segments[2])) ? $segments[2] : 'index';
        $controller = new \f1024\bootstrap\f\Controller();
        $data = $controller->$action();
        $controller->view->render($data);
    }
}
