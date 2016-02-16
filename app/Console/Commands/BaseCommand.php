<?php

// app/Console/Commands/BaseCommand.php

namespace Console\Commands;

use Providers\Console\Events;
use Symfony\Component\Console\Event;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpKernel\Exception;
use Providers\Console\Command as ConsoleCommand;

/**
 * Class - BaseCommand
 * 
 *
 * @category Command
 * @package  app\Console\Commands
 * @author   Sergii Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 */
class BaseCommand extends ConsoleCommand {

    use \Controllers\Helper\ValidatorTrait;
    use \Controllers\Helper\TranslateTrait;
    use \Controllers\Helper\MailTrait;
    use \Controllers\Helper\MonologTrait;
    
    /**
     * Config name
     *
     * @var string
     */
    public $config_name;

    /**
     * Query parameters
     *
     * @var array
     */
    protected $params;

    /**
     * Options
     *
     * @var array
     */
    protected $opts;

    //----------------------

    /**
     * Constructor
     * 
     * @param string $name
     */
    public function __construct($name = null) {
        parent::__construct($name);
    }

    /**
     * Initialization
     */
    public function init(InputInterface $input) {

        // Get params
        $params = $this->_getParams($input);

        // Application initialization
        $this->_initApplication($params);

        // Set properties
        $this->_setProperties();
    }

    /**
     * Get params and options for command
     * 
     * @param InputInterface $input
     * @return array
     */
    protected function _getParams(InputInterface $input) {
        $params = array();
        $optNames = array();
        $arBox = $this->app['my']->get('array');
        $strBox = $this->app['my']->get('string');
        //-----------------
        //---- Get arguments ----
        $arguments = $input->getArguments();
        $arguments = $arBox->set($arguments)->delRows()->get();


        // Get command configuration
        if (isset($this->config_name)) {
            $config = $this->app['config']['commands'][$this->config_name];

            // Get command parameters
            if ($config['configure']['delimiter']) {
                $delimiter = $config['configure']['delimiter'];
                // Transform command arguments, in accordance with the separator
                foreach ($arguments as $key => $value) {
                    $is_delimiter = $strBox->set($value)->contains($delimiter);
                    if ($is_delimiter) {
                        $params += $arBox->set($value, $delimiter)->get();
                    } else {
                        $params[$key] = $value;
                    }
                }
            } else {
                $params += $arguments;
            }
            
            // Get only those options that are present in the configuration
            if(isset($config['options'])){
                $optNames = $arBox->set($config['options'])->slice('name')->get();
            }
        }else{
            $params += $arguments;
        }


        //---- Get opts ----
        $opts = $arBox->set($input->getOptions())->trimArray("=")->delRows($optNames, true, true);

        // Set value for environment
        $environment = $this->app['config']['parameters']['environment'];
        $opts = $opts->setVal('environment', $environment);

        // Set value for data_dir
        $data_dir = $this->app['config']['parameters']['data_dir'];
        $opts = $opts->setVal('data_dir', $data_dir);
        // Set value for debug
        $opts = $opts->addToFirstAssoc('debug', $this->app['debug'])->get();

        return array("opts" => $opts, "params" => $params);
    }

    /**
     * Application initialization
     * 
     * @param array $params 
     */
    protected function _initApplication($params = array()) {

        // Set "params" and "opts" as services
        $this->app["my.opts"] = $params["opts"];
        $this->app["my.params"] = $params["params"];

        // Add a message about using commands in the log
        $this->_addCommandLoadedToMonolog();

        // Create data dir
        $data_dir = $params["opts"]["data_dir"];
        $this->app['my']->get('system')->createDataDir($data_dir);
    }

    /**
     * Set object properties
     */
    protected function _setProperties() {
        $this->params = $this->app["my.params"];
        $this->opts = $this->app["my.opts"];
    }

    //============== Additional functions ==============

    /**
     * Run command
     * 
     * @param array $arguments 
     * @param InputInterface $input 
     * @param OutputInterface $output 
     * @return bool|int  
     */
    public function runCommand($arguments, InputInterface $input, OutputInterface $output) {

        $name = $arguments["command"];

        $command = $this->getApplication()->find($name);

        $input = new Input\ArrayInput($arguments);
        $returnCode = $command->run($input, $output);

        // Run console:error command
        if ($name == "console:error") {
            return FALSE;
        }

        if (isset($this->opts['data_dir'])) {
            return $this->isSuccessCommand();
        }

        return $returnCode;
    }

    /**
     * Is command successfully?
     * 
     * @return bool 
     */
    public function isSuccessCommand() {
        $arBox = $this->app['my']->get('array');
        //------------------------
        $results = $this->getResults();
        $result = $arBox->set($results)->trimArray()->get('result');
        $result = strtoupper($result);
        $result = $result == 'OK' ? TRUE : FALSE;
        return $result;
    }

    //============= CONFIGURATION COMMANDS =======//

    /**
     * Set command options/arguments
     * 
     * @return array
     */
    public function setCommandConfig() {
        $name = $this->config_name; // Конфиг. имя команды (пр. "my_greet")
        $definition = new Input\InputDefinition();
        //---------------------------
        $config = $this->app['config']['commands'][$name];

        // Set command description
        if ($config['configure']['description']) {
            $this->setDescription($config['configure']['description']);
        }

        // Set arguments
        if ($config['arguments']) {
            $arguments = $config['arguments'];
            foreach ($arguments as $argument) {
                $mode = $this->_getArgumentMode($argument['mode']);
                $newArgument = new InputArgument(
                        $argument['name'], $mode, $argument['description'], $argument['default']);
                $definition->addArgument($newArgument);
            }
        }

        // Set options
        if (isset($config['options'])) {
            $options = $config['options'];
            foreach ($options as $option) {
                $mode = $this->_getOptionMode($option['mode']);
                $newOption = new InputOption(
                        $option['name'], $option['shortcut'], $mode, $option['description'], $option['default']);
                $definition->addOption($newOption);
            }
        }
        $this->setDefinition($definition);
    }

    /**
     * Get command delimiter
     * 
     * @param string $name Command name
     * @return string
     */
    public function getCommandDelimiter($name) {
        $config = $this->app['config']['commands'][$name];
        return $config['configure']['delimiter'];
    }

    /**
     * Get argument value
     * 
     * @param string $strValue
     * @return int
     */
    private function _getArgumentMode($strValue) {
        switch ($strValue) {
            case 'REQUIRED':
                return 1;
            case 'OPTIONAL':
                return 2;
            case 'IS_ARRAY':
                return 4;
            default:
                $this->app->abort(403, "The mode value '{$strValue}' of the class 'Input\\InputArgument' is not.");
                break;
        }
    }

    /**
     * Get option value
     * 
     * @param string $strValue
     * @return int
     */
    private function _getOptionMode($strValue) {
        switch ($strValue) {
            case 'VALUE_NONE':
                return 1;
            case 'VALUE_REQUIRED':
                return 2;
            case 'VALUE_OPTIONAL':
                return 4;
            case 'VALUE_IS_ARRAY':
                return 8;
            default:
                $this->app->abort(403, "The mode value '{$strValue}' of the class 'Input\\InputOption' is not.");
                break;
        }
    }

    //============== OUTPUT/SAVE RESULTS ==============

    /**
     * Show results
     * 
     * @param  array $msgs 
     * @param  bool $result
     * @return string
     */
    protected function showResults($msgs = array(), $result = true) {
        $results = array();
        $twig = $this->app["twig"];
        $sysBox = $this->app['my']->get('system');
        //----------------------
        // Get result value 
        $strResult = $result ? "OK" : "ERROR";
        $results["results"][] = "===== {$strResult} =====";

        // Set date
        $date = date("Y-m-d H:i:s");
        $results["results"][] = "date = \"{$date}\"";

        // Set command
        if ($result) {
            $command = $this->params['command'];
            $results["results"][] = "command = \"{$command}\"";
        }

        // Add options and params
        if (!$result && !array_key_exists("opts", $msgs)) {
            $msgs["opts"] = $this->opts;
        }

        if (!$result && !array_key_exists("params", $msgs)) {
            $msgs["params"] = $this->params;
        }

        // Transform array "$msgs" for view
        $arr = $sysBox->ArrData2View($msgs);
        $results["results"] = array_merge($results["results"], $arr);

        // Get the message content through the template
        $content = $twig->render('inc/view.twig', $results);
        // HTML sequence transform into characters
        $content = htmlspecialchars_decode($content, ENT_QUOTES);

        return $content;
    }

    /**
     * Save results
     * 
     * @param  array $msgs 
     * @param  bool $result
     * @return void
     */
    protected function saveResults($msgs = array(), $result = true) {
        $results = array();
        $twig = $this->app["twig"];
        $config = $this->app["my"]->get('config');
        $sysBox = $this->app["my"]->get('system');
        //----------------------
        // Get result value
        $result = $result ? "OK" : "ERROR";
        $results["results"][] = "result = {$result}";

        // Set date
        $date = date("Y-m-d H:i:s");
        $results["results"][] = "date = \"{$date}\"";

        // Set command
        if ($result) {
            $command = $this->params['command'];
            $results["results"][] = "command = \"{$command}\"";
        }


        // Transform array "$msgs" for view
        $arr = $sysBox->ArrData2View($msgs, FALSE);
        $results["results"] = array_merge($results["results"], $arr);

        // Get the message content through the template
        $content = $twig->render('inc/results.twig', $results);
        // HTML sequence transform into characters
        $content = htmlspecialchars_decode($content, ENT_QUOTES);
        // Save results to file
        $dir = $config->getProjectPath("download");
        $filePath = $dir . "/result.txt";
        file_put_contents($filePath, $content, LOCK_EX); //FILE_APPEND | LOCK_EX
    }

    /**
     * Get results
     * 
     * @return array
     */
    protected function getResults() {
        $results = array();
        $config = $this->app["my"]->get('config');
        //----------------------
        // Get file content
        $dir = $config->getProjectPath("download");
        $filePath = $dir . "/result.txt";
        if (!is_file($filePath)) {
            $this->app->abort(404, "No file '{$filePath}'.");
        }
        // Parse this content
        $results = parse_ini_file($filePath);
        if ($results === FALSE) {
            $this->app->abort(403, "Syntax error in the file '{$filePath}'.");
        }
        return $results;
    }

    //============== LOGGING ==============

    /**
     * Get log message
     * 
     * @param array $arData 
     * @return string
     *  
     */
    protected function getLogMsg($arData = array()) {
        $results = array();
        $twig = $this->app["twig"];
        $sysBox = $this->app['my']->get('system');
        //----------------
        $date = date("Y-m-d H:i:s");

        if (!array_key_exists("opts", $arData)) {
            $arData["opts"] = $this->opts;
        }

        if (!array_key_exists("params", $arData)) {
            $arData["params"] = $this->params;
        }

        $results["results"] = $sysBox->ArrData2View($arData);

        $results["date"] = $date;
        $command = $this->params['command'];
        $commands = explode(":", $command);
        $results["controller"] = $commands[0];
        $results["action"] = $commands[1];
        $results["env"] = $this->opts["environment"];


        $content = $twig->render('inc/log.twig', $results);
        $content = htmlspecialchars_decode($content, ENT_QUOTES);

        return $content;
    }

    /**
     * Save log
     * 
     * @param  array $msgs 
     * @return void
     */
    protected function saveLog($msgs = array()) {
        $sysBox = $this->app['my']->get('system');
        $config = $this->app["my"]->get('config');
        //------------------------
        // Save log
        $msg = $this->getLogMsg($msgs);
        $file = $config->getProjectPath("logs") . "/script.log";
        $sysBox->saveLog($msg, $file);
    }

    /**
     * Add user info to monolog
     *  
     * @param string $message User message
     */
    protected function addUserInfoToMonolog($message) {
        // Get command
        $command = $this->getApplication()->find($this->app['my.params']['command']);
        // Output message to log
        $input = new Input\ArrayInput(array("user_info" => $message));
        $output = new Output\ConsoleOutput();
        $event = new Event\ConsoleEvent($command, $input, $output);
        $this->app['dispatcher']->dispatch(Events::USER_INFO, $event);
    }

    /**
     * Add command info to monolog
     *  
     */
    private function _addCommandLoadedToMonolog() {
        // Get command
        $command = $this->getApplication()->find($this->app['my.params']['command']);
        // Output message to log
        $input = new Input\ArrayInput(array());
        $output = new Output\ConsoleOutput();
        $event = new Event\ConsoleEvent($command, $input, $output);
        $this->app['dispatcher']->dispatch(Events::COMMAND_LOADED, $event);
    }

    /**
     * Add error info to monolog
     *  
     * @param \Exception $exc 
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function _addErrorToMonolog(\Exception $exc, InputInterface $input, OutputInterface $output) {

        // Get error code
        if ($exc instanceof Exception\HttpException) {
            $code = $exc->getStatusCode();
        } else {
            $code = $exc->getCode();
        }
        // Get command
        $command = $this->getApplication()->find($this->app['my.params']['command']);
        // Output message to log
        $event = new Event\ConsoleExceptionEvent($command, $input, $output, $exc, $code);
        $this->app['dispatcher']->dispatch(Events::EXCEPTION, $event);
    }

    //============== ERRORS ==============

    /**
     * Show error
     *  
     * @param \Exception $exc Обьект ошибки
     * @param InputInterface $input аргументы для команды
     * @param OutputInterface $output аргументы для команды
     * @return array
     */
    protected function showError(\Exception $exc, InputInterface $input, OutputInterface $output) {

        // Save to log
        $this->_addErrorToMonolog($exc, $input, $output);

        // Save $opts and $params
        $opts = $this->app['my.opts'];
        $params = $this->app['my.params'];

        // Get error message
        $message = $this->_errorHandler($exc);
        $message = base64_encode($message);

        $arguments = array(
            'command' => 'console:error',
            'message' => $message,
            '--color' => true
        );

        $this->runCommand($arguments, $input, $output);

        // Update $opts and $params
        $this->app['my.opts'] = $opts;
        $this->app['my.params'] = $params;
    }

    /**
     * Error handler
     * 
     * @param \Exception $exc
     * @return string 
     */
    private function _errorHandler(\Exception $exc) {
        $results = array();
        $httpCodes = $this->app["my"]->get('http')->getHttpCodes();
        //--------------------
        // Prepary output data
        if ($exc instanceof Exception\HttpException) {
            $code = (int) $exc->getStatusCode();
            if (isset($httpCodes[$code])) {
                $code .= " {$httpCodes[$code]}";
            }
        } else {
            $code = $exc->getCode();
        }

        $results["code"] = $code;
        $results["message"] = $exc->getMessage();

        // Save results
        $this->saveResults($results, false);


        // Output error to log
        $this->saveErrorLog($results);

        $message = $this->showResults($results, false);
        return $message;
    }

    /**
     * Save error to log
     * 
     * @param  array $msgs 
     */
    protected function saveErrorLog($msgs = array()) {
        $sysBox = $this->app['my']->get('system');
        $config = $this->app["my"]->get('config');
        //------------------------
        // Save to log
        $msg = $this->getLogMsg($msgs);
        $file = $config->getProjectPath("logs") . "/error.log";
        $sysBox->saveLog($msg, $file);
    }

}
