<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\rest;

use \f1024\data\ActiveRecord;

class Model extends ActiveRecord{
    
    /**
     * @var array Validation rules
     */
    public $validation = [];
    
    /**
     * @var array Validation Failed Messages
     */
    private $validationMessage = [];
    
    /**
     * @var integer User ID
     */
    protected $userId;
    
 
    /**
     * List all records (limited by pagination)
     * 
     * @param integer $limit
     * @param integer $offset
     * @return array
     */
    public function listAll($limit, $offset){
        $entities = $this->getCollection([], $this->primaryKey, true, $limit, $offset);
        $return   = [];
        foreach ($entities as $entity){
            $return[] = $entity->asArray();
        }
        return $return;
    }
    
    /**
     * Returns one record by ID (actually, by primary key)
     * 
     * @param intger $id
     * @return array
     */
    public function show($id){
        if ($this->checkAccess($id, $this->userId)){
            return $this->getEntity([$this->primaryKey => $id])->asArray();
        }else{
            return ['error' => true, 'code' => 403];
        }
    }
    
    /**
     * Edits entity
     * 
     * @param intger $id
     * @param array $data
     * @return array
     */
    public function edit($id, $data){
        if ($cleanData = $this->validation($data)){
            if ($this->checkAccess($id, $this->userId)){
                $entity = $this->getEntity([$this->primaryKey => $id]);
                foreach ($cleanData as $key => $value){
                    $entity->$key = $value;
                }
                $entity->save();
            }else{
                return ['error' => true, 'code' => 403];
            }
        }else{
            return ['error' => true, 'code' => 400, 'message' => $this->getValidationMessage()];
        }
    }
    
    /**
     * Creates new entity
     * 
     * @param array $data
     * @return array
     */
    public function create($data){
        if ($cleanData = $this->validation($data)){
            if ($this->checkAccess($id, $this->userId)){
                $entity = $this->getEntity([$this->primaryKey => $id]);
                foreach ($cleanData as $key => $value){
                    $entity->$key = $value;
                }
                $entity->save();
            }else{
                return ['error' => true, 'code' => 403];
            }
        }else{
            return ['error' => true, 'code' => 400, 'message' => $this->getValidationMessage()];
        }
    }
    
    /**
     * Removes entity
     * @param integer $id
     * @return array
     */
    public function rm($id){
        if ($this->checkAccess($id, $this->userId)){
            $entity = $this->getEntity([$this->primaryKey => $id]);
            $entity->delete();
            return ['error' => false];
        }else{
            return ['error' => true, 'code' => 403];
        }
    }
    
    /**
     * simple validation. To Be Extended.
     * @todo
     * @param array $data
     * @return mixed Cleaned data or false if validation failed
     */
    public function validation($data){
//        if (!isset($this->validation['mode']) || isset($this->validation['mode']) && !in_array(strtolower($this->validation['mode']), ['pass', 'strict'])){
//            $this->validation['mode'] = 'strict';
//        }
        if (!isset($this->validation['_required'])){
            $this->validation['_required'] = [];
        }
        $cleanData = [];
        foreach ($data as $key => $value){
            if (isset($this->validation[$key])){
                $cleanData[$key] = $value;
            }
        }
        $fError = false;
        foreach ($this->validation['_required'] as $key){
            if (!isset($cleanData[$key])){
                $fError = true;
                $this->validationMessage[] = $key.' is absent';
            }
        }
        return (!$fError) ? $cleanData : false;
    }
    
    /**
     * Returns the validation error messages
     * @return string
     */
    public function getValidationMessage(){
        return implode(', ', $this->validationMessage);
    }
    
    /**
     * Check Access
     * To Be Done
     * @todo
     * @param integer $id
     * @param integer $userId
     * @return boolean
     */
    public function checkAccess($id, $userId){
        return true;
    }
    
}
