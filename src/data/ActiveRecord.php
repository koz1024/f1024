<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\data;

use f1024\db\QueryBuilder;

/**
 * Active Record
 * 
 */
class ActiveRecord extends QueryBuilder{
    
    /**
     * @var string Name of table
     */
    public $tableName;
    
    /**
     * @var string Primary key (field name) 
     */
    protected $primaryKey;
    
    /**
     * @access private
     * @var array Array of entities
     */
    private $collection;
    
    /**
     * @access private
     * @var array Array of values of attrubutes 
     */
    private $attributes;
    
    /**
     * @access private
     * @var array Array of values of "dirty" (changed) attrubutes 
     */
    private $dirtyAttributes;
    
    /**
     * @access private
     * @var array Array of relations
     */
    private $relations;
    
    /**
     * @access private
     * @var boolean Is this collection present in database?
     */
    private $isNew = true;
    
    /**
     * 
     * @param string $name Table name
     * @param string $primaryKey Field defined as primary key
     */
    public function __construct($name = false, $primaryKey = false){
        
        if ($name){
            $this->tableName = $name;
        }
        
        if ($primaryKey){
            $this->primaryKey = $primaryKey;
        }
        
        if (empty($this->tableName)){
            throw new \Exception('Active Record does not have setted $name property');
        }
        
        $this->getDB();
    }
    
    /**
     * Sets the primary key
     * @param string $primaryKey Field defined as primary key
     */
    public function setPrimaryKey($primaryKey){
        $this->primaryKey = $primaryKey;
    }
    
    /**
     * Returns the array of entites (named "collection")
     *
     * @param array $condition
     * @param string $orderBy
     * @param boolen $orderIsAsc
     * @param integer $limitFrom
     * @param integer $limitTo
     * @return array collection (array of entities)
     */
    public function getCollection($condition, $orderBy = '', $orderIsAsc = false, $limitFrom = '', $limitTo = ''){
        $aCollection = $this
                        ->table($this->tableName)
                        ->where($condition)
                        ->order($orderBy, $orderIsAsc)
                        ->limit($limitFrom, $limitTo)
                        ->get();
        $this->collection = [];
        $aCollection = $this->getRelations($aCollection);
        foreach ($aCollection as $row){
            $_entity = new static($this->tableName);
            $_entity->setEntity($row, false);
            $this->collection[] = $_entity;
        }
        return $this->collection;
    }
    
    /**
     * Returns an entity
     * 
     * @param array $condition
     * @return \f1024\data\ActiveRecord
     */
    public function getEntity($condition){
        $aEntity = $this
                    ->table($this->tableName)
                    ->where($condition)
                    ->limit(1)
                    ->get();
        if (isset($aEntity[0])){
            $aEntity = $this->getRelations($aEntity);
            $this->setEntity($aEntity[0], false);
            return $this;
        }
    }
    
    /**
     * Returns collection or entity directly by SQL
     * You should use this method carefully
     * 
     * @param string $sql
     * @return mixed Collection or Entity or null
     */
    public function getBySql($sql){
        
        $return = $this->setSQL($sql)->get();
        
        $return = $this->getRelations($return);
        
        if (sizeof($return) > 1){
            
            //collection
            
            $this->collection = [];
            foreach ($return as $row){
                $_entity = new static($this->tableName);
                $_entity->setEntity($row, false);
                $this->collection[] = $_entity;
            }
            
            return $this->collection;
            
        }elseif (sizeof($return) == 1){
            
            //entity
            
            if (isset($return[0])){
                $this->setEntity($return[0], false);
                return $this;
            }
            
        }else{
            
            //nothing to show
            
            return null;
        }
    }
    
    /**
     * Sets entity
     * 
     * @param object $entity
     * @param boolean $isNew
     */
    public function setEntity($entity, $isNew = true){
        if ($isNew){
            $this->dirtyAttributes = $entity;
        }else{
            $this->attributes = $this->fields($entity);
        }
        $this->isNew = $isNew;
    }
    
    /**
     * Saves changes on current entity
     * 
     * @param string $primaryKey
     * @return boolean
     */
    public function save($primaryKey = false){
        if ($this->isNew){
            $this
                ->reset()
                ->table($this->tableName)
                ->set($this->dirtyAttributes)
                ->insert();
        }else{
            
            $where = $this->prepareWhere($primaryKey);
            
            if ($where){
                $this
                    ->table($this->tableName)
                    ->set($this->dirtyAttributes)
                    ->where($where)
                    ->update();
            }else{
                throw new \Exception('Primary key for table '.$this->tableName.' on model '.__CLASS__.' is not defined');
            }
        }
        return true;
    }
    
    /**
     * Removes current entity
     * 
     * @param string $primaryKey
     * @return boolean
     */
    public function remove($primaryKey = false){
        $where = $this->prepareWhere($primaryKey);
        if ($where){
            $this
                ->table($this->tableName)
                ->where($where)
                ->limit(1)
                ->delete();
            return true;
        }else{
            return false;
        }
    }
    
    private function prepareWhere($primaryKey){
        $where = false;
        if ($primaryKey && isset($this->attributes[$primaryKey])){
            $where = [$primaryKey => $this->attributes[$primaryKey]];
        }elseif (!is_null($this->primaryKey) && isset($this->attributes[$this->primaryKey])){
            $where = [$this->primaryKey => $this->attributes[$this->primaryKey]];
        }
        return $where;
    }
    
    /**
     * Binds two entities by keys (relation one-to-one)
     * 
     * @param object $model Foreign entity
     * @param string $hereKey Inner key
     * @param string $thereKey Foreign key
     * @return \f1024\data\ActiveRecord
     */
    public function hasOne($model, $hereKey, $thereKey){
        if (!isset($this->relations['one'])){
            $this->relations['one'] = [];
        }
        $this->relations['one'][] = [
            'model'         => $model,
            'relations'     => [
                'inner'     => $hereKey,
                'foreigen'  => $thereKey,
            ],
        ];
        return $this;
    }
    
    /**
     * Binds two entities by keys (relation one-to-many)
     * 
     * @param object $model Foreign entity
     * @param string $hereKey Inner key
     * @param string $thereKey Foreign key
     * @return \f1024\data\ActiveRecord
     */
    public function hasMany($model, $hereKey, $thereKey){
        if (!isset($this->relations['many'])){
            $this->relations['many'] = [];
        }
        $this->relations['many'][] = [
            'model'         => $model,
            'relations'     => [
                'inner'     => $hereKey,
                'foreigen'  => $thereKey,
            ],
        ];
        return $this;
    }
    
    private function getRelations($data){
        
        if (isset($this->relations['one'])){
            foreach ($this->relations['one'] as $rel){
                
                $innerVals = [];
                
                $key = $rel['relations']['inner'];
                foreach ($data as $i => $row){
                    if (isset($row[$key])){
                        $innerVals[$i] = $row[$key];
                    }
                }
                $related = $rel['model']->getCollection([$rel['relations']['foreigen'] => $innerVals]);
                foreach ($related as $row){
                    $i = array_search($row->$key, $innerVals);
                    if (!isset($data[$i]['__relations'])){
                        $data[$i]['__relations'] = [];
                    }
                    $data[$i]['__relations'][$rel['model']->tableName] = $row;
                }
            }
        }
        
        //almost same for "many" relation
        if (isset($this->relations['many'])){
            foreach ($this->relations['many'] as $rel){
                
                $innerVals = [];
                
                $key = $rel['relations']['inner'];
                foreach ($data as $i => $row){
                    if (isset($row[$key])){
                        $innerVals[$i] = $row[$key];
                    }
                }
                $related = $rel['model']->getCollection([$rel['relations']['foreigen'] => $innerVals]);
                foreach ($related as $row){
                    $i = array_search($row->$key, $innerVals);
                    if (!isset($data[$i]['__relations'])){
                        $data[$i]['__relations'] = [];
                    }
                    $data[$i]['__relations'][$rel['model']->tableName][] = $row;
                }
            }
        }
        
        return $data;
       
        
    }
    
    /**
     * Magic method.
     * Provides constructions like $foo = $entity->attributename;
     * 
     * @param string $name
     * @return mixed attribute's value
     */
    public function __get($name) {
        if (isset($this->dirtyAttributes[$name])){
            return $this->dirtyAttributes[$name];
        }elseif (isset($this->attributes[$name])){
            return $this->attributes[$name];
        }elseif (isset($this->attributes['__relations']) && isset($this->attributes['__relations'][$name])){
            return $this->attributes['__relations'][$name];
        }else{
            return null;
        }
    }
    
    /**
     * Magic method.
     * Provides constructions like $entity->attributename = 'foo';
     * 
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        if (isset($this->attributes[$name]) || $this->isNew){
            $this->dirtyAttributes[$name] = $value;
        }
    }
    
    /**
     * Magic method.
     * Provides constructions like isset($entity->attributename)
     * 
     * @param string $name
     * @return boolean
     */
    public function __isset($name) {
        return isset($this->attributes[$name]) || isset($this->dirtyAttributes[$name]);
    }
    
    /**
     * Returns entity's attributes.
     * You may inherit this method for redefining some entity's attributes (remove, add or chage)
     * 
     * @param array $attributes
     * @return array
     */
    protected function fields($attributes){
        return $attributes;
    }
    
    /**
     * Returns entity's attributes' values
     * @return array
     */
    public function asArray(){
        return $this->attributes;
    }
    
}
