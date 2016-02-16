<?php

namespace Providers\Console\EventListener;

use Providers\Console\Events;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LoggerListener, used for console application logs
 *
 * @category Listener
 * @package  Fluency\Silex\Console\EventListener
 * @author   Rafael Ernesto Espinosa Santiesteban <ralphlnx@gmail.com>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://fluency.inc.com
 */
class LoggerListener implements EventSubscriberInterface {

    /**
     * Fires when an exception throws on command execution scope ($command->run())
     *
     * @param ConsoleExceptionEvent $event ConsoleExceptionEvent instance
     *
     * @return void
     */
    public function onConsoleCommandException(ConsoleExceptionEvent $event) {
        $command = $event->getCommand();
        $container = $command->getApplication()->getContainer();
        $container['monolog']->addError(
                sprintf(
                        'Command \'%s\' ErrorMsg: \'%s\'', $command->getName(), $event->getException()->getMessage()
                )
        );
    }

    /**
     * Fires when command is loaded into console application
     *
     * @param ConsoleEvent $event ConsoleEvent instance
     *
     * @return void
     */
    public function onConsoleCommandLoaded(ConsoleEvent $event) {
        $command = $event->getCommand();
        $container = $command->getApplication()->getContainer();
        $container['monolog']->addInfo(
                sprintf(
                        'Command \'%s\' Loaded as \'%s\'', get_class($command), $command->getName()
                )
        );
    }

    /**
     * Fires when user send info to log
     *
     * @param ConsoleEvent $event ConsoleEvent instance
     *
     * @return void
     */
    public function onConsoleCommandUserInfo(ConsoleEvent $event) {
        $input = $event->getInput();
        $user_info = $input->getParameterOption("user_info");
        $command = $event->getCommand();
        $container = $command->getApplication()->getContainer();
        $container['monolog']->addInfo(
                sprintf('Command \'%s\' UserInfo: \'%s\'', $command->getName(), $user_info)
        );
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents() {
        return array(
            Events::COMMAND_LOADED => 'onConsoleCommandLoaded',
            Events::EXCEPTION => 'onConsoleCommandException',
            Events::USER_INFO => 'onConsoleCommandUserInfo'
        );
    }

}
