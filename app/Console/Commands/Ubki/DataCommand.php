<?php

// app/Console/Commands/Ubki/DataCommand.php

namespace Console\Commands\Ubki;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class - DataCommand
 * UBKI - send data
 * 
 * @category Command
 * @package  app\Console\Commands
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class DataCommand extends \Console\Commands\BaseCommand {

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

        $models = $this->app['models'];
        $http = $this->app["my"]->get('http');
        $parameters = $this->app['config']['parameters'];
        $results = array();
        //-----------------

        try {

            // Initialization
            $this->init($input);

            // Get test url
            $url_test = $parameters['url_test'];

            if ($this->opts['environment'] == 'production') {
                if ($this->app['debug']) {
                    $url = "https://secure.ubki.ua:4040/upload/data/xml";
                    $path = "/upload/data/xml";
                } else {
                    $url = "https://secure.ubki.ua/upload/data/xml";
                    $path = "/upload/data/xml";
                }
            } else {
                if ($this->app['debug']) {
                    $url = "{$url_test}/ubki/data?XDEBUG_SESSION_START=netbeans-xdebug";
                    $path = "/ubki/data";
                } else {
                    $url = "{$url_test}/ubki/data";
                    $path = "/ubki/data";
                }
            }

            // Get Sess ID
            $idSess = $models->load('Ubki', 'getSessId');
            // Get XML request
            $data = array();
            $data['sessid'] = $idSess;
            $xmlData = $models->load('Ubki', 'getReqSendData', $data);
            
            // Set options
            $options = array(
                CURLOPT_HTTPHEADER => Array(
                    "POST " . $path . " HTTP/1.0",
                    "Content-type: text/xml;charset=\"utf-8\"",
                    "Accept: text/xml",
                    "Content­Encoding: gzip",
                    "Content-length: " . strlen($xmlData),
                ),
                CURLOPT_PROXY => $parameters['proxy']? "{$parameters['proxy.host']}:{$parameters['proxy.port']}":'',
                CURLOPT_PROXYUSERPWD => $parameters['proxy']? "{$parameters['proxy.user']}:{$parameters['proxy.pass']}":'',
            );
            
            // Create HttpBox object
            $http->setData($xmlData);
            // Send post request
            $response = $http->post($url, $options);

            // Get http info
            $arHttpInfo = $http->getInfo();

            // Communications error
            if ($response === FALSE && !$response) {// ERR
                $err_code = $arHttpInfo['errno'];
                $err_message = $arHttpInfo['errmsg'];
                $this->app->abort($err_code, $err_message);
            }

            // Handle the response
            $data = array();
            $data['response'] = $response;
            $models->load('Ubki', 'handleResSendData', $data);

            // Save results 
            $results['url'] = $url;
            $results['message'] = "Send UBKI data successful.";
            $this->saveResults($results);

            // Show results
            $output->writeln($this->showResults($results));


        } catch (\Exception $exc) {
            $this->showError($exc, $input, $output);
        }
    }

}
