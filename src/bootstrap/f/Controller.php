<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\bootstrap\f;

/**
 * Controller for bootstrap
 * @todo
 * 
 */
class Controller extends \f1024\web\Controller{
    
    /**
     * Constructor. Initializiation of view here.
     * @return null Nothing to return
     */
    public function __construct(){
        parent::__construct();
        $this->view = new \f1024\bootstrap\f\View();
    }
    
    /**
     * Index method
     * @return array void
     */
    public function index(){
        $this->view->template = 'index';
        return [];
    }
}
