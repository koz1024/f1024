<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\rest;

use \f1024\web\View;

class Jsonview implements View{

    /**
     * Outputs data as json array
     * @param array $data
     */
    public function render($data = []){
        if ($data !== false){
            $response = \f1024\core\ServiceLocator::getInstance()->get('response');
            $response->type = 'application/json';
            $response->response();
            echo json_encode($data);
        }
    }
    
}
