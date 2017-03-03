<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\helpers;

class ArrayHelper{
    
    /**
     * Checks if array is associative
     * @param array $arr
     * @return boolean
     */
    public static function isAssociative($arr){
        if (!empty($arr) && is_array($arr)){
            return array_keys($arr) !== range(0, sizeof($arr) - 1);
        }else{
            return false;
        }
    }
    
    /**
     * Checks if array is numeric
     * @param array $arr
     * @return boolean
     */
    public static function isNumeric($arr){
        return !ArrayHelper::isAssociative($arr);
    }
    
}
