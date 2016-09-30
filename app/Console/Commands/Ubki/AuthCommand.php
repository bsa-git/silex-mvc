<?php

// app/Console/Commands/Ubki/AuthCommand.php

namespace Console\Commands\Ubki;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class - AuthCommand
 * UBKI - authorization
 * 
 * @category Command
 * @package  app\Console\Commands
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class AuthCommand extends \Console\Commands\BaseCommand {

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
        //----------------

        try {
            // Initialization
            $this->init($input);

            // Get the test url
            $url_test = $parameters['url_test'];;

            if ($this->opts['environment'] == 'production') {
                if ($this->app['debug']) {
                    $url = "https://secure.ubki.ua:4040/b2_api_xml/ubki/auth";
                    $path = "/b2_api_xml/ubki/auth";
                } else {
                    $url = "https://secure.ubki.ua:443/b2_api_xml/ubki/auth";
                    $path = "/b2_api_xml/ubki/auth";
                }
            } else {
                if ($this->app['debug']) {
                    $url = "{$url_test}/ubki/auth?XDEBUG_SESSION_START=netbeans-xdebug";
                    $path = "/ubki/auth";
                } else {
                    $url = "{$url_test}/ubki/auth";
                    $path = "/ubki/auth";
                }
            }

            // Get XML request
            $data = array();
            $data['env'] = $this->app['my.opts']['environment'];
            $xmlAuth = $models->load('Ubki', 'getReqForAuth', $data);

            // Set options
            $options = array(
//                CURLOPT_VERBOSE => $this->app['debug'], // TRUE To display more information.
                CURLOPT_PROXY => $parameters['proxy']? "{$parameters['proxy.host']}:{$parameters['proxy.port']}":'',
                CURLOPT_PROXYUSERPWD => $parameters['proxy']? "{$parameters['proxy.user']}:{$parameters['proxy.pass']}":'',
            );
            
            // Send post request
            $http->setData($xmlAuth);
            $response = $http->post($url, $options);

            // Get http info
            $arHttpInfo = $http->getInfo();

            // Communications error
            if ($response === FALSE && !$response) {// ERR
                $err_code = $arHttpInfo['errno'];
                $err_message = $arHttpInfo['errmsg'];
                $this->app->abort($err_code, $err_message);
            }

            // Get the data after processing
            $arResponse = $models->load('Ubki', 'handleResForAuth', $response);
            $results['url'] = $url;
            $results += $arResponse;
            $results['message'] = "UBKI authorization successful.";

            // Save results
            $this->saveResults($results);

            // Save results to log
            $this->saveLog($results);

            // Show results
            $output->writeln($this->showResults($results));
        } catch (\Exception $exc) {
            $this->showError($exc, $input, $output);
        }
    }

}
