<?php

// app/Controllers/Helper/FormTrait.php

namespace Controllers\Helper;

use Monolog\Logger;

/**
 * Trait - MonologTrait
 * monolog operations
 *
 * @category Helper
 * @package  app\Controllers\Helper
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
trait MonologTrait
{
    /**
     * Adds a log record.
     *
     * @param string $message The log message
     * @param array  $context The log context
     * @param int    $level   The logging level
     *
     * @return bool Whether the record has been processed
     */
    public function log($message, array $context = array(), $level = Logger::INFO) {
        if (!isset($this->app['monolog'])) {
            throw new \LogicException('The \"MonologServiceProvider\" is not registered in your application.');
        }
        
        return $this->app['monolog']->addRecord($level, $message, $context);
    }
}
