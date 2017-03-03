<?php
namespace koz1024\framework\db;

class DB extends \koz1024\framework\db\DB\core{
    
    protected $config;
    
    public function __construct($config) {
        $this->config = $config['database'];
        parent::__construct();
    }
    
}
