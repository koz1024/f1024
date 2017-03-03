<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\db;

/**
 * Extending factory of database drivers
 */
class Db extends \f1024\db\drivers\Factory{
    
    protected $config;
    
    public function __construct($config) {
        $this->config = $config['database'];
        parent::__construct();
    }
    
    /**
     * Returns type of database
     * @return string
     */
    public function getType(){
        return $this->config['type'];
    }
    
}
