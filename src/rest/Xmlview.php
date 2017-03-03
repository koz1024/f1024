<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\rest;

use \f1024\web\View;
use \f1024\helpers\ArrayHelper;

class Xmlview implements View{
    
    /**
     * Outputs data as XML
     * @param array $data
     */
    public function render($data = []){
        if ($data !== false){
            $response = \f1024\core\ServiceLocator::getInstance()->get('response');
            $response->type = 'application/xml';
            
            $xml = new \SimpleXMLElement('<xml/>');
            
            $this->recoursiveTraversing($data, $xml);
            
            $response->response();
            echo $xml->asXML();
        }
    }
    
    private function recoursiveTraversing($subArray, &$node){
        if (is_array($subArray)){
            if (ArrayHelper::isNumeric($subArray)){
                foreach ($subArray as $row){
                    $subnode = $node->addChild('node');
                    $this->recoursiveTraversing($row, $subnode);
                }
            }else{
                foreach ($subArray as $key => $value){
                    if (!is_array($value)){
                        $node->addChild($key, $value);
                    }else{
                        $subnode = $node->addChild($key);
                        $this->recoursiveTraversing($value, $subnode);
                    }
                }
            }
        }else{
            $node->addChild('value', $subArray);
        }
    }
    
}
