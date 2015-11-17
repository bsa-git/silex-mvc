<?php

// app/Console/Commands/Ubki/IndexCommand.php

namespace Console\Commands\Ubki;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

/**
 * Class - IndexCommand
 * UBKI operations
 * 
 * @category Command
 * @package  app\Console\Commands
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class IndexCommand extends \Console\Commands\BaseCommand {

    //---------------------
    /**
     * Configures command
     *
     * @return void
     */
    protected function configure() {
        
    }

    /**
     * Executes command
     *
     * @param InputInterface  $input  Command input
     * @param OutputInterface $output Console output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
            $sysBox = $this->app['my']->get('system');
            //--------------------
            // Init
            $this->init($input);

            // Delete files from download dir
            $sysBox->deleteDownloadFiles();

            // Select type operation
            switch ($this->opts['type']) {
                case 'info':
                    $this->_getInfo($input, $output);
                    break;
                case 'data':
                    $this->_sendData($input, $output);
                    break;
                case 'registry':
                    $this->_getRegistry($input, $output);
                    break;
                default:
                    $this->app->abort(406, "Wrong type of operation '{$this->opts['type']}'.");
                    break;
            }
        } catch (\Exception $exc) {
            $this->showError($exc, $input, $output);
        }
    }

    /**
     * getInfo - Get info from UBKI
     *
     * @param InputInterface  $input  Command input
     * @param OutputInterface $output Console output
     *
     * @return void
     */
    private function _getInfo(InputInterface $input, OutputInterface $output) {
        $models = $this->app['models'];
        $results = array();
        $commands = array(// default
            'isSessId' => 'nodone',
            'ubki:auth' => 'nodone',
            'ubki:info' => 'nodone'
        );
        //-------------------------
        //----- Get sess ID ----
        $idSess = $models->load('Ubki', 'getSessId');
        // Set results
        $commands['isSessId'] = var_export(isset($idSess), TRUE);

        //----- Authorize ----
        if (!$idSess) {
            $arguments = array(
                'command' => 'ubki:auth',
                'args' => ""
            );

            // Run command
            $result = $this->runCommand($arguments, $input, $output);
            // Set results
            $commands['ubki:auth'] = var_export($result, TRUE);
            if (!$result) {
                // Show results
                $this->_showTableGetInfo($output, $commands);
                return;
            }
        }

        //----- Get info ----
        $arguments = array(
            'command' => 'ubki:info',
            'args' => ""
        );

        // Run command
        $result = $this->runCommand($arguments, $input, $output);
        // Set results
        $commands['ubki:info'] = var_export($result, TRUE);
        if (!$result) {
            // Show results
            $this->_showTableGetInfo($output, $commands);
            return;
        }


        $results["results"]["request"] = "Get information from UBKI.";
        $results["results"]["response"] = "Successful.";


        // Save results
        $this->saveResults($results);

        // Save results to log
        $this->saveLog($results);

        // Show results
        $output->writeln($this->showResults($results));

        // Show table results
        $this->_showTableGetInfo($output, $commands);
    }

    /**
     * sendData - Send data to UBKI
     *
     * @param InputInterface  $input  Command input
     * @param OutputInterface $output Console output
     *
     * @return void
     */
    private function _sendData(InputInterface $input, OutputInterface $output) {
        $models = $this->app['models'];
        $results = array();
        $commands = array(// default
            'isSessId' => 'nodone',
            'ubki:auth' => 'nodone',
            'ubki:data' => 'nodone'
        );
        //-------------------------
        //----- Get sess ID ----
        $idSess = $models->load('Ubki', 'getSessId');
        // Set results
        $commands['isSessId'] = var_export(isset($idSess), TRUE);

        //----- Authorize ----
        if (!$idSess) {
            $arguments = array(
                'command' => 'ubki:auth',
                'args' => ""
            );

            // Run command
            $result = $this->runCommand($arguments, $input, $output);
            // Set results
            $commands['ubki:auth'] = var_export($result, TRUE);
            if (!$result) {
                // Show results
                $this->_showTableSendData($output, $commands);
                return;
            }
        }



        //----- Send data ----
        $arguments = array(
            'command' => 'ubki:data',
            'args' => ""
        );

        // Run command
        $result = $this->runCommand($arguments, $input, $output);
        // Set results
        $commands['ubki:data'] = var_export($result, TRUE);
        if (!$result) {
            // Show results
            $this->_showTableSendData($output, $commands);
            return;
        }

        $results["results"]["request"] = "Send data to UBKI.";
        $results["results"]["response"] = "Successful.";


        // Save results
        $this->saveResults($results);

        // Save results to log
        $this->saveLog($results);

        // Show results
        $output->writeln($this->showResults($results));

        // Show table results
        $this->_showTableSendData($output, $commands);
    }

    /**
     * getRegistry - Get registry UBKI data
     *
     * @param InputInterface  $input  Command input
     * @param OutputInterface $output Console output
     *
     * @return void
     */
    private function _getRegistry(InputInterface $input, OutputInterface $output) {
        $models = $this->app['models'];
        $results = array();
        $commands = array(// default
            'isSessId' => 'nodone',
            'ubki:auth' => 'nodone',
            'ubki:registry' => 'nodone'
        );
        //-------------------------
        //----- Get sess ID ----
        $idSess = $models->load('Ubki', 'getSessId');
        // Set results
        $commands['isSessId'] = var_export(isset($idSess), TRUE);

        //----- Authorize ----
        if (!$idSess) {
            $arguments = array(
                'command' => 'ubki:auth',
                'args' => ""
            );

            // Run command
            $result = $this->runCommand($arguments, $input, $output);
            // Set results
            $commands['ubki:auth'] = var_export($result, TRUE);
            if (!$result) {
                // Show results
                $this->_showTableGetRegistry($output, $commands);
                return;
            }
        }



        //----- Get registry ----
        $arguments = array(
            'command' => 'ubki:registry',
            'args' => isset($this->params['args']) ? $this->params['args'] : ""
        );

        // Run command
        $result = $this->runCommand($arguments, $input, $output);
        // Set results
        $commands['ubki:registry'] = var_export($result, TRUE);
        if (!$result) {
            // Show results
            $this->_showTableGetRegistry($output, $commands);
            return;
        }

        $results["results"]["request"] = "Get registry UBKI data.";
        $results["results"]["response"] = "Successful.";


        // Save results
        $this->saveResults($results);

        // Save results to log
        $this->saveLog($results);

        // Show results
        $output->writeln($this->showResults($results));

        // Show table results
        $this->_showTableGetRegistry($output, $commands);
    }

    /**
     * showTableCreditInfo - Show results information
     * 
     * @param OutputInterface $output Console output
     * @param array  $results
     * @return void
     */
    private function _showTableGetInfo(OutputInterface $output, $results = array()) {

        $table = new Table($output);
        $table
                ->setHeaders(array('##', 'Title', 'Result'))
                ->setRows(array(
                    array('1', 'Function-isSessId', $results['isSessId']),
                    array('2', 'Command-ubki:auth', $results['ubki:auth']),
                    array('3', 'Command-ubki:info', $results['ubki:info'])
                ))
        ;
        $table->render();
    }

    /**
     * showTableCreditInfo - Show results information
     * 
     * @param OutputInterface $output Console output
     * @param array  $results
     * @return void
     */
    private function _showTableSendData(OutputInterface $output, $results = array()) {

        $table = new Table($output);
        $table
                ->setHeaders(array('##', 'Title', 'Result'))
                ->setRows(array(
                    array('1', 'Function-isSessId', $results['isSessId']),
                    array('2', 'Command-ubki:auth', $results['ubki:auth']),
                    array('3', 'Command-ubki:data', $results['ubki:data']),
                ))
        ;
        $table->render();
    }

    /**
     * _showTableGetRegistry - Show results information
     * 
     * @param OutputInterface $output Console output
     * @param array  $results
     * @return void
     */
    private function _showTableGetRegistry(OutputInterface $output, $results = array()) {

        $table = new Table($output);
        $table
                ->setHeaders(array('##', 'Title', 'Result'))
                ->setRows(array(
                    array('1', 'Function-isSessId', $results['isSessId']),
                    array('2', 'Command-ubki:auth', $results['ubki:auth']),
                    array('3', 'Command-ubki:registry', $results['ubki:registry']),
                ))
        ;
        $table->render();
    }

}
