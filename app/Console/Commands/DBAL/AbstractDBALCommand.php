<?php

// app/Console/Commands/DBAL/AbstractDBALCommand.php
/**
 * PHP version ~5.5
 *
 * @category Command
 * @package  Fluency\Silex\Doctrine\DBAL\Tools\Console\Command
 * @author   Rafael Ernesto Espinosa Santiesteban <ralphlnx@gmail.com>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://fluency.inc.com
 */

namespace Console\Commands\DBAL;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Providers\Console\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DBALCommand
 * @category Command
 * @package  Fluency\Silex\Doctrine\DBAL\Tools\Console\Command
 * @author   Rafael Ernesto Espinosa Santiesteban <ralphlnx@gmail.com>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://fluency.inc.com
 */
abstract class AbstractDBALCommand extends ConsoleCommand {

    /**
     * DBAL Connection instance
     * @var Connection
     */
    private $_connection;

    /**
     * Gets DBAL Connection instance
     * @return Connection
     */
    protected function getConnection() {
        return $this->_connection;
    }

    /**
     * Sets DBAL Connection instance
     * @param Connection $connection DBAL Connection instance
     * @return $this
     */
    protected function setConnection(Connection $connection) {
        $this->_connection = $connection;
        return $this;
    }

    /**
     * Processes input options, sets connection without database selected.
     * If default connection change overrides existing connection helper.
     * @param InputInterface $input Console input instance
     * @throws \Doctrine\DBAL\DBALException
     * @return void
     */
    protected function processOptions(InputInterface $input) {

        if ($this->getContainer()->offsetExists('dbs')) {
            $params = $this->getContainer()['dbs'][$input->getOption('connection')]
                    ->getParams();
            $this->getHelperSet()->set(
                    new ConnectionHelper(
                    $this->getContainer()['dbs'][$input->getOption('connection')], 'db'
                    )
            );
        } else {
            $params = $this->getHelper('db')->getConnection()->getParams();
        }

        if ($input->getOption('user') != null) {
            $params['user'] = $input->getOption('user');
            $params['password'] = $input->getOption('password');
        }

        unset($params['dbname']);
        unset($params['path']);

        $this->setConnection(DriverManager::getConnection($params));
    }

    /**
     * Gets connection params
     * @param string|null $connectionName Connection name like 'default'
     * @return array
     */
    protected function getConnectionParams($connectionName = null) {
        return ($connectionName != null ) ?
                $this->getContainer()['dbs'][$connectionName]->getParams() :
                $this->getHelper('db')->getConnection()->getParams();
    }

    /**
     * Configure input options for DBALCommand instances
     * @return void
     */
    protected function configure() {
        $this
                ->addOption(
                        'connection', 'c', InputOption::VALUE_OPTIONAL, 'The connection to use for this command', 'default'
                )
                ->addOption(
                        'user', '-u', InputOption::VALUE_OPTIONAL, 'Database user with administrative rights'
                )
                ->addOption(
                        'password', '-p', InputOption::VALUE_OPTIONAL, 'Database password for --user'
        );
    }

    /**
     * Calls process options for DBALCommand objects
     * @param InputInterface  $input  Command input
     * @param OutputInterface $output Console output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->processOptions($input);
    }

}
