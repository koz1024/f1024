<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\db\drivers;

/**
 * Class for working with MySQL database (no PDO, no mysqli, just MySQL)
 * 
 * @deprecated
 * This class is deprecated and not supported
 * You should use it carefully
 */

class DbMysqlOld{
    private $r;
    private $conn_id;
    public  $queries = 0;
    protected static $instance;
    
    private function __construct($settings){
        if (isset($settings['server']) && isset($settings['user']) && isset($settings['password']) && isset($settings['name'])){
            $this->conn_id = mysql_connect($settings['server'], $settings['user'], $settings['password']);
            mysql_select_db($settings['name'], $this->conn_id);
            mysql_query("SET NAMES utf8", $this->conn_id);
        }else{
            trigger_error('Not found requierd DATABASE CONNECT params', E_USER_ERROR);
        }
    }
    
    function query($sql = '', $params = []){
        $this->queries++;
        $this->r = mysql_query($sql, $this->conn_id);
        return $this;
    }
    
    function numRows(){
        return mysql_num_rows($this->r);
    }
    
    function resultArray(){
        $return = array();
        while ($arr = mysql_fetch_assoc($this->r)){
            $return[] = $arr;
        }
        return $return;
    }
    
    function rowArray(){
        return mysql_fetch_assoc($this->r);
    }
    
    function cellsArray($cell = ''){
        $return = array();
        while ($arr = mysql_fetch_assoc($this->r)){
            $return[] = $arr[$cell];
        }
        return $return;
    }
    
    function result($field = ''){
        if ($this->numRows()>0){
            return mysql_result($this->r, 0, $field);
        }else{
            return null;
        }
    }
    
    
    function getError(){
        return mysql_error($this->conn_id);
    }
    
    function lastId(){
        return mysql_insert_id($this->conn_id);
    }
    
    public static function getInstance($settings){
        if (is_null(self::$instance)){
            self::$instance = new self($settings);
        }
        return self::$instance;
    }

    public static function escape($val = ''){
        return mysql_real_escape_string($val);
    }

    public static function escapeField($field = ''){
        if (preg_match('#^([A-z0-9\.]+)$#is', $field)){
            return '`'.$field.'`';
        }else{
            return '';
        }
    }
}