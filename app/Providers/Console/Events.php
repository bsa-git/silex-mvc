<?php
/**
 * PHP version ~5.5
 *
 * @category Application
 * @package  Fluency\Silex\Console
 * @author   Rafael Ernesto Espinosa Santiesteban <ralphlnx@gmail.com>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://fluency.inc.com
 */

namespace Providers\Console;

/**
 * Class Events, holds some extra event constants for console application
 *
 * @category Application
 * @package  Fluency\Silex\Console
 * @author   Rafael Ernesto Espinosa Santiesteban <ralphlnx@gmail.com>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://fluency.inc.com
 */
final class Events
{
    const COMMAND_LOADED = 'console.command.loaded';
    const EXCEPTION = 'console.command.exception';
    const USER_INFO = 'console.user.info';
}