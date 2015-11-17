<?php

// app/Controllers/Helper/DebugTrait.php

namespace Controllers\Helper;

use Symfony\Component\VarDumper\VarDumper;


/**
 * Trait - DebugTrait
 * debug operations
 *
 * @category Helper
 * @package  app\Controllers\Helper
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
trait DebugTrait {

    /**
     * Print var information
     *
     * @param mixed   $var       The var name
     *
     * @return void
     */
    public function dump($var) {
        VarDumper::dump($var);
    }
}
