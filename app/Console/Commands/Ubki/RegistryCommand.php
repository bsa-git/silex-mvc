<?php

// app/Console/Commands/Ubki/RegistryCommand.php

namespace Console\Commands\Ubki;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class - RegistryCommand
 * Get registry UBKI data
 * 
 * @category Command
 * @package  app\Console\Commands
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class RegistryCommand extends \Console\Commands\BaseCommand {

    
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
        $crXml = $this->app["my"]->get('xml');
        $models = $this->app['models'];
        $http = $this->app["my"]->get('http');
        $strBox = $this->app['my']->get('string');
        $parameters = $this->app['config']['parameters'];
        $results = array();
        //-----------------

        try {

            // Initialization
            $this->init($input);

            // Get test url
            $url_test = $parameters['url_test'];
            // Get url
            if ($this->opts['environment'] == 'production') {
                if ($this->app['debug']) {
                    $url = "https://secure.ubki.ua:4040/upload/in/reestrs.php";
                    $path = "/upload/in/reestrs.php";
                } else {
                    $url = "https://secure.ubki.ua/upload/in/reestrs.php";
                    $path = "/upload/in/reestrs.php";
                }
            } else {
                if ($this->app['debug']) {
                    $url = "{$url_test}/ubki/registry?XDEBUG_SESSION_START=netbeans-xdebug";
                    $path = "/ubki/registry";
                } else {
                    $url = "{$url_test}/ubki/registry";
                    $path = "/ubki/registry";
                }
            }

            // Get Sess ID
            $idSess = $models->load('Ubki', 'getSessId');
            // Get XML request
            $data = array();
            $data['sessid'] = $idSess;
            $data += $this->params;
            $xmlRegistry = $models->load('Ubki', 'getReqGetRegistry', $data);
            
            // Set http options
            $options = array(
                CURLOPT_HTTPHEADER => Array(
                    "POST " . $path . " HTTP/1.0",
                    "Content-type: text/xml;charset=\"utf-8\"",
                    "Accept: text/xml",
                    "Content-length: " . strlen($xmlRegistry),
                ),
                CURLOPT_PROXY => $parameters['proxy']? "{$parameters['proxy.host']}:{$parameters['proxy.port']}":'',
                CURLOPT_PROXYUSERPWD => $parameters['proxy']? "{$parameters['proxy.user']}:{$parameters['proxy.pass']}":'',
            );
            
            // Create HttpBox object
            $http->setData($xmlRegistry);
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

            // Get compression type
            $xmlDecode = $strBox->set($xmlRegistry)->base64Decode()->get();
            $crXml->loadXML($xmlDecode);
            $zip = $crXml->doc->prot['zip'];
            // Handle the response
            $data = array();
            $data['response'] = $response;
            $data['zip'] = $zip;
            $models->load('Ubki', 'handleResGetRegistry', $data);

            // Save results 
            $results['url'] = $url;
            $results['message'] = "Get UBKI registry successful.";
            $this->saveResults($results);

            // Show results
            $output->writeln($this->showResults($results));


        } catch (\Exception $exc) {
            $this->showError($exc, $input, $output);
        }
    }

}
