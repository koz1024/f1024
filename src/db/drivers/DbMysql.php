<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\db\drivers;

/**
 * PDO MySQL driver
 */
class DbMysql{
    
    /**
     * @access protected
     * @var object For singleton
     */
    protected static $instance;
    
    /**
     * @access private
     * @var resource
     */
    private $pdo;
    
    /**
     * @access private
     * @var resource
     */
    private $statement;
    
    /**
     * @var integer Count of queries
     */
    public  $queries = 0;
    
    /**
     * Constructor
     * @param array $settings Connection settings to database
     */
    private function __construct($settings){
        if (isset($settings['dsn']) && isset($settings['user']) && isset($settings['password'])){
            $this->pdo = new \PDO($settings['dsn'], $settings['user'], $settings['password'], [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]);
        }else{
            trigger_error('Not found requierd DATABASE CONNECT params', E_USER_ERROR);
        }
    }
    
    /**
     * Prepares and executes a query
     * 
     * @param string $query
     * @param array $params
     * @return \f1024\db\drivers\DbMysql
     */
    public function query($query, $params = []){
        if (empty($params)){
            $this->statement = $this->pdo->query($query);
        }else{
            $this->statement = $this->pdo->prepare($query);
            $this->statement->execute($params);
        }
        return $this;
    }
    
    /**
     * Returns count of found rows
     * @return integer Count of rows
     */
    public function numRows(){
        return $this->statement->rowCount();
    }
    
    /**
     * Returns an associative array - result of query
     * @return array
     */
    public function resultArray(){
        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Returns one (first) row from result of query
     * @return array
     */
    public function rowArray(){
        return $this->statement->fetch();
    }
    
    /**
     * Returns array of first cells from result of query
     * @return array
     */
    public function cellsArray(){
        return $this->statement->fetchAll(\PDO::FETCH_COLUMN);
    }
    
    /**
     * Returns associative array where keys are first cells and values are second cells of result of query
     * @return array
     */
    public function cellsAssocArray(){
        return $this->statement->fetchAll(\PDO::FETCH_KEY_PAIR);
    }
    
    /**
     * Return result of query (cell)
     * @param integer $fieldNumber
     * @return mixed
     */
    public function result($fieldNumber = 0){
        return $this->statement->fetchColumn($fieldNumber);
    }
    
    /**
     * Returns error description
     * @return string
     */
    public function getError(){
        return $this->pdo->errorInfo();
    }
    
    /**
     * Returns last inserted id
     * @return integer
     */
    public function lastId(){
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Singleton implementation
     */
    public static function getInstance($settings){
        if (is_null(self::$instance)){
            self::$instance = new self($settings);
        }
        return self::$instance;
    }
}
