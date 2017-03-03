<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\web;

/**
 * Abstract Controller for WEB applications
 */
abstract class Controller{
    
    /**
     * @access protected
     * @var object ServiceLocator
     */
    protected $SL       = null;
    
    /**
     * @var boolean if not setted to true, then parent controller will initialize request, response and view classes by itself
     */
    protected $isOverwrite = false;
    
    /**
     * @var object Request class
     */
    protected $request     = null;
    
    /**
     * @var object Response class
     */
    protected $response    = null;
    
    /**
     * @var object View class
     */
    public    $view        = null;

    /**
     * Constructor
     */
    public function __construct(){
        $this->SL = \f1024\core\ServiceLocator::getInstance();
        
        if (!$this->isOverwrite){
            $this->request  = new \f1024\web\Request();
            $this->response = new \f1024\web\Response();
            $this->view     = new \f1024\web\Htmlview();
            
            $this->SL->addInstance('request', $this->request);
            $this->SL->addInstance('response', $this->response);
            $this->SL->addInstance('view', $this->view);
        }
    }

    
}