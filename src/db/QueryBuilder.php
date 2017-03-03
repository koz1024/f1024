<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\db;

/**
 * Query Builder
 */
class QueryBuilder{
    
    private $db;
    private $table;
    private $conditions = '';
    private $ordering = '';
    private $limit = '';
    private $insdata = '';
    private $sql = '';
    
    private $params = [];
    
    public function __construct(){
        $this->getDB();
    }
    
    protected function getDB(){
        $this->db = \f1024\core\ServiceLocator::getInstance()->get('DB');
    }
    
    /**
     * Sets the name of table
     * 
     * @param string $tableName Name of table
     * @return \f1024\db\QueryBuilder
     */
    public function table($tableName){
        $this->table = $tableName;
        return $this;
    }
    
    /**
     * Sets the conditions
     * For more details please see documentation
     * 
     * @param array $conditions
     * @return type
     */
    public function where($conditions = []){
        if ($this->db->getType()=='mysql_pdo'){
            return $this->wherePDO($conditions);
        }elseif ($this->db->getType()=='mysql'){
            return $this->whereOLD($conditions);
        }
    }
    
    /**
     * Sets conditions for old mysql driver
     * This is a dangerous method. You should controll all passed params
     * 
     * @access private
     * @param array $conditions
     * @return \f1024\db\QueryBuilder
     */
    private function whereOLD($conditions = []){
        foreach ($conditions as $cond => $val){
            if (!is_array($val)){
                if (intval($cond)){
                    $this->conditions .= $val.' AND';
                }else{
                    $this->conditions .= $cond . ' = "' . $val . '" AND ';
                }
            }else{
                $this->conditions .= $cond . ' IN (';
                foreach ($val as $inval){
                    $this->conditions .= $inval.', ';
                }
                $this->conditions = substr($this->conditions, 0, -2) . ') AND';
            }
        }
        $this->conditions = substr($this->conditions, 0, -4);
        return $this;
    }
    
    /**
     * Sets conditions for PDO-like drivers
     * 
     * @access private
     * @param array $conditions
     * @return \f1024\db\QueryBuilder
     */
    private function wherePDO($conditions = []){
        foreach ($conditions as $cond => $val){
            if (!is_array($val)){
                if (intval($cond)){
                    $this->conditions .= '? AND';
                }else{
                    $this->conditions .= $cond . ' = ? AND ';
                }
                $this->params[] = $val;
            }else{
                $this->conditions .= $cond . ' IN (';
                foreach ($val as $inval){
                    $this->conditions .= '?, ';
                    $this->params[] = $intval;
                }
                $this->conditions = substr($this->conditions, 0, -2) . ') AND';
            }
        }
        $this->conditions = substr($this->conditions, 0, -4);
        return $this;
    }
    
    /**
     * Add conditions with OR
     * @param array $conditions
     * @return \f1024\db\QueryBuilder
     */
    public function orWhere($conditions = []){
        $this->conditions .= ' OR ';
        $this->where($conditions);
        return $this;
    }
    
    /**
     * Sets sorting (ordering)
     * 
     * @param string $by
     * @param boolean $isAsc
     * @return \f1024\db\QueryBuilder
     */
    public function order($by, $isAsc = true){
        if (!empty($by)){
            $this->ordering = 'ORDER BY `' . $by . '` '. ($isAsc ? 'ASC' : 'DESC');
        }
        return $this;
    }
    
    /**
     * Sets limiting
     * 
     * @param integer $from
     * @param mixed $to
     * @return \f1024\db\QueryBuilder
     */
    public function limit($from, $to = false){
        if (!empty($from)){
            $this->limit = 'LIMIT '.intval($from) . ($to ? ', '.intval($to) : '');
        }
        return $this;
    }
    
//    public function join($table, $on, $type = 'inner'){
//        if (!in_array(strtolower($type), ['left', 'inner', 'right'])){
//            $type = 'inner';
//        }
//    }
    
    /**
     * Generates and returns SQL string
     * @return string SQL
     */
    public function getSQL(){
        if (empty($this->sql)){
            $this->sql = "SELECT * FROM `" . $this->table . "` " . (!empty($this->conditions) ? " WHERE " . $this->conditions : '') . ' ' . $this->ordering . ' ' . $this->limit;
        }
        return $this->sql;
    }
    
    /**
     * Sets the SQL string (instead of generating)
     * You probaly shouldn't use this method
     * 
     * @param string $sql
     * @return \f1024\db\QueryBuilder
     */
    public function setSQL($sql){
        $this->sql = $sql;
        return $this;
    }
    
    /**
     * Gets results of all of this.
     * This method must be last in the chain
     * 
     * @return array
     */
    public function get(){
        $this->getSQL();
        $result = $this->db->query($this->sql, $this->params);
        $error  = $this->db->getError();
        if (!empty($error)){
            //once we're using PDO, $error has never been empty
            //throw new \Exception('Error on query: '.$this->sql.' ('.$error.')');
        }
        return $result->resultArray();
    }
    
    /**
     * Sets insert/update data
     * 
     * @param array $data
     * @return \f1024\db\QueryBuilder
     */
    public function set($data = []){
        $this->insdata = '';
        foreach ($data as $key => $val){
            $this->insdata .= "`" . $key . "` = ?, ";
            $this->params[] = $val;
        }
        $this->insdata = substr($this->insdata, 0, -2);
        return $this;
    }
    
    /**
     * Make UPDATE query.
     * This method must be last in the chain.
     * 
     * @return boolean
     */
    public function update(){
        if (empty($this->sql)){
            $this->sql = "UPDATE `" . $this->table . "` SET " . $this->insdata . " "  . (!empty($this->conditions) ? " WHERE " . $this->conditions : '');
        }
        $this->db->query($this->sql, $this->params);
        $error  = $this->db->getError();
        if (!empty($error)){
            throw new \Exception('Error on query: '.$this->sql.' ('.$error.')');
        }
        return true;
    }
    
    /**
     * Make INSERT query. Returns inserted id
     * This method must be last in the chain.
     * 
     * @return integer Inserted ID
     */
    public function insert(){
        if (empty($this->sql)){
            $this->sql = "INSERT INTO  `" . $this->table . "` SET " . $this->insdata . " ";
        }
        $this->db->query($this->sql, $this->params);
        $error  = $this->db->getError();
        if (!empty($error)){
            throw new \Exception('Error on query: '.$this->sql.' ('.$error.')');
        }
        return $this->db->lastId();
    }
    
    /**
     * Make DELETE query.
     * This method must be last in the chain.
     * 
     * @return boolean
     */
    public function delete(){
        if (empty($this->sql)){
            $this->sql = "DELETE FROM `" . $this->table . "` ". (!empty($this->conditions) ? " WHERE " . $this->conditions : '') . ' ' . $this->ordering . ' ' . $this->limit;
        }
        $this->db->query($this->sql, $this->params);
        
        return true;
    }
    
}
