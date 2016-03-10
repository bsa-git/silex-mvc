<?php

// app/Controllers/UbkiController.php

namespace Controllers;

use Silex\Application;

/**
 * Class - UbkiController
 * UBKI - test operations
 * 
 * @category Controller
 * @package  app\Controllers
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 */
class UbkiController extends BaseController {

    //-----------------------------
    /**
     * Конструктор 
     * 
     * @param Application $app
     */
    public function __construct(Application $app) {
        parent::__construct($app);
    }
    
    /**
     * Routes initialization
     * 
     * @return void
     */
    protected function iniRoutes() {
        $self = $this;
        $this->app->get('/ubki', function () use ($self) {
            return $self->indexAction();
        })->bind('ubki_index');
        
        $this->app->post('/ubki/auth', function () use ($self) {
            return $self->authAction();
        })->bind('ubki_auth');
        
        $this->app->post('/ubki/info', function () use ($self) {
            return $self->infoAction();
        })->bind('ubki_info');
        
        $this->app->post('/ubki/data', function () use ($self) {
            return $self->dataAction();
        })->bind('ubki_data');
        
        $this->app->post('/ubki/registry', function () use ($self) {
            return $self->registryAction();
        })->bind('ubki_registry');
    }

    /**
     * Action - ubki/index
     * 
     * @return string
     */
    public function indexAction() {
        
        // Initialization
        $this->init(__CLASS__ . "/" . __FUNCTION__);
        
        return $this->showView();
    }

    /**
     * Action - ubki/auth
     * UBKI - authorization
     * 
     * @return string
     */
    public function authAction() {
        $http = $this->app["my"]->get('http');
        $crXml = $this->app["my"]->get('xml');
        $strBox = $this->app['my']->get('string');
        $mylogin = $this->app['config']['parameters']['ubki_test_login'];
        $mypass = $this->app['config']['parameters']['ubki_test_pass'];
        $login = "";
        $pass = "";
        $sessid = '3B91A9B73A0E4D49B9CE4FCA6F2AA4E2';
        $xml = "";
        //------------------

        try {
            
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);
            
            // Get request
            $xmlStr = $http->getInputPHP();

            // Unpack and decode
            $xmlStr = $strBox->set($xmlStr)->base64Decode()->get();

            // Validate XML
            if (!$crXml->isValidXml($xmlStr)) {
                $this->app->abort(406, "Invalid format XML");
            }

            // Load XML
            $crXml->loadXML($xmlStr);

            // Get login
            $login = $crXml->doc->auth['login'];
            // Get password
            $pass = $crXml->doc->auth['pass'];

            // Check authorization
            if ($mylogin !== $login || $mypass !== $pass) {// ERR
                $this->app->abort(401, "Authorization error");
            }

            // Get response
            $file = $this->getLibPath() . '/resAuth.xml';
            $resXml = file_get_contents($file);

            // Load XML
            $crXml->loadXML($resXml);

            $crXml->doc->auth['sessid'] = $sessid;
            $xml = $crXml->xml();
        } catch (\Exception $exc) {

            // Get error code
            $code = $this->getCodeException($exc);

            // Get response
            $file = $this->getLibPath() . '/resError.xml';
            $resXml = file_get_contents($file);

            // Load XML
            $crXml->loadXML($resXml);
            $crXml->doc->auth['errcode'] = $code;
            $crXml->doc->auth['errtext'] = $exc->getMessage();
            $xml = $crXml->xml();
        }

        return $this->sendXml($xml);
    }

    /**
     * Action - ubki/info
     * Get information from UBKI service
     * 
     * @return string
     */
    public function infoAction() {
        $http = $this->app["my"]->get('http');
        $crXml = $this->app["my"]->get('xml');
        //------------------

        try {
            
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);
            
            // Get XML request
            $xmlStr = $http->getInputPHP();

            // Validate XML request
            if (!$crXml->isValidXml($xmlStr)) {
                $this->app->abort(406, "Invalid format XML");
            }

            // Load XML
            $crXml->loadXML($xmlStr);

            // Define the number of the request
            $encodeRequest = (string) $crXml->doc->ubki->req_envelope->req_xml;
            $decodeRequest = base64_decode($encodeRequest);
            $strRequest = '<?xml version="1.0" encoding="UTF-8"?>' . $decodeRequest;
            $crXml->loadXML($strRequest);

            // Get XML response
            $file = $this->getLibPath() . "/resGetInfo.xml";
            $resXml = file_get_contents($file);

            // Load XML response
            $crXml->loadXML($resXml);
            $xml = $crXml->xml();
        } catch (\Exception $exc) {
            // Get error code
            $code = $this->getCodeException($exc);

            // Get XML response for error
            $file = $this->getLibPath() . '/resError.xml';
            $resXml = file_get_contents($file);

            // Load XML response for error
            $crXml->loadXML($resXml);
            // Set error code and error message
            $crXml->ubkidata->tech->error['errtype'] = $code;
            $crXml->ubkidata->tech->error['errtext'] = $exc->getMessage();
            $xml = $crXml->xml();
        }
        // Send XML response
        return $this->sendXml($xml);
    }

    /**
     * Action - ubki/data
     * Send data
     * 
     * @return string
     */
    public function dataAction() {
        $http = $this->app["my"]->get('http');
        $crXml = $this->app["my"]->get('xml');
        $strBox = $this->app['my']->get('string');
        //------------------

        try {

            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);
            
            // Get XML request
            $gzdata = $http->getInputPHP();

            // Unpack and decode
            $xmlStr = $strBox->set($gzdata)->gzUnpack()->base64Decode()->get();

            // Validate XML request
            if (!$crXml->isValidXml($xmlStr)) {
                $this->app->abort(406, "Invalid format XML");
            }

            // Load XML
            $crXml->loadXML($xmlStr);

            // Get XML response
            $file = $this->getLibPath() . "/resSendData.xml";
            $resXml = file_get_contents($file);

            // Load XML response
            $crXml->loadXML($resXml);
            $xml = $crXml->xml();
        } catch (\Exception $exc) {
            // Get error code
            $code = $this->getCodeException($exc);

            // Get XML response for error
            $file = $this->getLibPath() . '/resError.xml';
            $resXml = file_get_contents($file);

            // Load XML response for error
            $crXml->loadXML($resXml);
            // Set error code and error message
            $crXml->ubkidata->tech->error['errtype'] = $code;
            $crXml->ubkidata->tech->error['errtext'] = $exc->getMessage();
            $xml = $crXml->xml();
        }
        // Send XML response
        return $this->sendXml($xml);
    }

    /**
     * Action - ubki/registry
     * Get registry UBKI data
     * 
     * @return string
     */
    public function registryAction() {
        $http = $this->app["my"]->get('http');
        $crXml = $this->app["my"]->get('xml');
        $strBox = $this->app['my']->get('string');
        //------------------

        try {

            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);
            
            // Get encode XML request
            $encodeData = $http->getInputPHP();

            // Decode this XML request
            $xmlStr = $strBox->set($encodeData)->base64Decode()->get();

            // Validate XML request
            if (!$crXml->isValidXml($xmlStr)) {
                $this->app->abort(406, "Invalid format XML");
            }

            // Load XML
            $crXml->loadXML($xmlStr);
            $sessid = $crXml->doc->prot['sessid'];
            $idout = $crXml->doc->prot['idout'];
            $idalien = $crXml->doc->prot['idalien'];
            $zip = $crXml->doc->prot['zip'];
            $todo = $crXml->doc->prot['todo'];
            $todo = $strBox->set($todo)->toLower()->ucfirst()->get();

            // Get XML response
            $file = $this->getLibPath() . "/resGetRegistry{$todo}.xml";
            $resXml = file_get_contents($file);

            // Validate XML response
            if (!$crXml->isValidXml($resXml)) {
                $this->app->abort(406, "Invalid format XML response");
            }
            
            // Load XML
            $crXml->loadXML($resXml);
            
            // Set XML values
            $crXml->doc->prot['sessid'] = $sessid;
            $crXml->doc->prot['indate'] =date("Ymd");
            $crXml->doc->prot['idout'] =$idout;
            $crXml->doc->prot['idalien'] =$idalien;
            
            // Get XML
            $resXml = $crXml->xml();
            
            // Compress XML response
            if ($zip == 'ZLIB') {
                $gzRes = $strBox->set($resXml)->gzCompress()->get();
            } else {
                $gzRes = $strBox->set($resXml)->gzPack()->get();
            }

            // Send XML response
            return $this->sendGzip($gzRes);
            
        } catch (\Exception $exc) {
            // Get error code
            $code = $this->getCodeException($exc);

            // Get XML response for error
            $file = $this->getLibPath() . '/resError.xml';
            $resXml = file_get_contents($file);

            // Load XML response for error
            $crXml->loadXML($resXml);
            // Set error code and error message
            $crXml->ubkidata->tech->error['errtype'] = $code;
            $crXml->ubkidata->tech->error['errtext'] = $exc->getMessage();
            $xml = $crXml->xml();
            // Send XML response
            return $this->sendXml($xml);
        }
    }

}
