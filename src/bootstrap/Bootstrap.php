<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\bootstrap;

class Bootstrap{
    
    public static function postInstallCmd($e = null){
        \f1024\console\base\Console::e("Done", [\f1024\console\base\Console::FG_GREEN]);
    }
}
