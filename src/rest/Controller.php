<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\rest;

/**
 * Abstract controller for REST applications
 */
abstract class Controller extends \f1024\web\Controller{
    
    /**
     * @access protected
     * @var \f1024\rest\Model
     */
    protected $model;
    
    /**
     * Constructor
     */
    public function __construct(){
        $this->SL = \f1024\core\ServiceLocator::getInstance();
        
        if (!$this->isOverwrite){
            $this->request  = new \f1024\web\Request();
            $this->response = new \f1024\web\Response();
            
            $type = $this->request->header('Accept');
            if (strpos($type, 'application/json')!==false){
                $this->view = new \f1024\rest\Jsonview();
            }elseif (strpos($type, 'application/xml')!==false){
                $this->view = new \f1024\rest\Xmlview();
            }else{
                $this->view = new \f1024\web\Htmlview();
            }
            
            $this->SL->addInstance('request', $this->request);
            $this->SL->addInstance('response', $this->response);
            $this->SL->addInstance('view', $this->view);
        }
    }
    
    /**
     * List all or show action
     * @param integer|false $id
     * @return mixed output data
     */
    public function index($id = false){
        if ($id===false){
            return $this->listAll();
        }else{
            return $this->show($id);
        }
    }
    
    /**
     * List all - action (GET without id)
     * @return mixed output data
     */
    public function listAll(){
        if ($this->model && $this->model instanceof \f1024\rest\Model){
            $limit  = $this->request->req('limit', 20);
            $offset = $this->request->req('offset', 0);
            return $this->afterAction($this->model->listAll($limit, $offset));
        }
    }
    
    /**
     * Show - action (GET)
     * @param integer $id
     * @return mixed output data
     */
    public function show($id){
        if ($this->model && $this->model instanceof \f1024\rest\Model){
            return $this->afterAction($this->model->show($id));
        }
    }
    
    /**
     * Edit - action (POST)
     * @param integer $id
     * @return mixed output data
     */
    public function edit($id){
        if ($this->model && $this->model instanceof \f1024\rest\Model){
            $data = $this->request->req();
            return $this->afterAction($this->model->edit($id, $data));
        }
    }
    
    /**
     * Create - action (PUT)
     * @return mixed output data
     */
    public function create(){
        if ($this->model && $this->model instanceof \f1024\rest\Model){
            
            $data = $this->request->req();
            return $this->afterAction($this->model->create($data));
            
        }
    }
    
    /**
     * Delete - action (DELETE)
     * @param integer $id
     * @return mixed output data
     */
    public function delete($id){
        if ($this->model && $this->model instanceof \f1024\rest\Model){
            return $this->afterAction($this->model->rm($id));
        }
    }
    
    /**
     * This method calls after action
     * @param mixed $data output data
     * @return mixed output data
     */
    protected function afterAction($data){
        if (is_array($data) && isset($data['error'])){
            if (isset($data['code'])){
                $this->response->code = $data['code'];
            }else{
                $this->response->code = 500;
            }
        }
        return $data;
    }
    
    /**
     * This method calls before action
     * @param mixed $data output data
     * @return mixed output data
     */
    protected function beforeAction(){}
    
}