<?php

// app/Console/Commands/Ubki/InfoCommand.php

namespace Console\Commands\Ubki;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class - InfoCommand
 * UBKI get credit information
 * 
 * @category Command
 * @package  app\Console\Commands
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class InfoCommand extends \Console\Commands\BaseCommand {

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
                    $url = "https://secure.ubki.ua:4040/b2_api_xml/ubki/xml";
                    $path = "/b2_api_xml/ubki/xml";
                } else {
                    $url = "https://secure.ubki.ua/b2_api_xml/ubki/xml";
                    $path = "/b2_api_xml/ubki/xml";
                }
            } else {
                if ($this->app['debug']) {
                    $url = "{$url_test}/ubki/info?XDEBUG_SESSION_START=netbeans-xdebug";
                    $path = "/ubki/info";
                } else {
                    $url = "{$url_test}/ubki/info";
                    $path = "/ubki/info";
                }
            }

            // Get Sess ID
            $idSess = $models->load('Ubki', 'getSessId');
            // Get XML request
            $data = array();
            $data['sessid'] = $idSess;
            $xmlInfo = $models->load('Ubki', 'getReqForInfo', $data);

            $options = array(
                CURLOPT_HTTPHEADER => Array(
                    "POST " . $path . " HTTP/1.0",
                    "Content-type: text/xml;charset=\"utf-8\"",
                    "Accept: text/xml",
                    "Content-length: " . strlen($xmlInfo),
                ),
                CURLOPT_PROXY => $parameters['proxy']? "{$parameters['proxy.host']}:{$parameters['proxy.port']}":'',
                CURLOPT_PROXYUSERPWD => $parameters['proxy']? "{$parameters['proxy.user']}:{$parameters['proxy.pass']}":'',
            );
            
            // Create HttpBox object
            $http->setData($xmlInfo);
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
            $models->load('Ubki', 'handleResForInfo', $data);

            // Save results 
            $results['url'] = $url;
            $results['message'] = "Get UBKI information successful.";
            $this->saveResults($results);

            // Show results
            $output->writeln($this->showResults($results));


        } catch (\Exception $exc) {
            $this->showError($exc, $input, $output);
        }
    }

}
