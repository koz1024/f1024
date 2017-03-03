<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\db\drivers;

/**
 * Factory of database driveres
 */
class Factory extends \f1024\core\AbstractFactory{
    
    protected $provider;
    
    public function __construct(){
        if ($this->config['type'] == 'mysql'){
            $this->provider = \f1024\db\drivers\DbMysqlOld::getInstance($this->config['connection']);
        }elseif ($this->config['type'] == 'mysql_pdo'){
            $this->provider = \f1024\db\drivers\DbMysql::getInstance($this->config['connection']);
        }
    }
    
}