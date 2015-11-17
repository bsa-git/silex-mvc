<?php

// app/Models/UbkiModel.php

namespace Models;

/**
 * Class - UbkiModel
 * Model for UBKI service
 * 
 * @category Model
 * @package  app\Models
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class UbkiModel extends BaseModel {


    //============ SESS IDS ==========

    /**
     * Get sessions id
     * 
     * @return string
     */
    public function getSessId() {
        $result = NULL;
        $arBox = $this->app['my']->get('array');
        $sysBox = $this->app['my']->get('system');
        $config = $this->app["my"]->get('config');
        //-------------------
        // Get file path with "sessid"
        $path = $config->getProjectPath("sessid");
        $files = $sysBox->getNameFilesSortDesc($path, "sessid_");
        if (count($files)) {// sessid_20150524.txt
            // Check the relevance of the "sessid"
            // "sessid" should be updated every day
            $date_now = floatval(date("Ymd"));
            $date_last = floatval($arBox->set($arBox->set($files[0], ".")->get(0), "_")->get(1));
            if ($date_last < $date_now) {// ERR
                return $result;
            }
            // Get "sessid"
            $file = "{$path}/{$files[0]}";
            $content = file_get_contents($file);
            $result = $this->app->escape($content);
        }

        return $result;
    }

    /**
     * Set "sessid"
     * ex. sessid_20150524.txt
     * 
     * @param string $sessid 
     * @return void
     */
    private function _setSessId($sessid) {
        $config = $this->app["my"]->get('config');
        //-------------------
        $path = $config->getProjectPath("sessid");
        $date_now = date("Ymd");
        $filePath = $path . "/sessid_{$date_now}.txt";
        file_put_contents($filePath, $sessid, LOCK_EX);
    }

    //============ REQUEST DATA ==========

    /**
     * Get XML request for authorization
     * 
     * @param string $env Environment: production, test
     * @return string
     */
    public function getReqForAuth($data = array()) {
        $crXml = $this->app["my"]->get('xml');
        $config = $this->app["my"]->get('config');
        $strBox = $this->app['my']->get('string');
        $debug = $this->app['debug'];
        //----------------------------
        // Get login/passport
        $env = $data['env'];
        if ($env == 'production') {
            if ($debug) {
                $login = $this->app['config']['parameters']['ubki_test_login'];
                $pass = $this->app['config']['parameters']['ubki_test_pass'];
            } else {
                $login = $this->app['config']['parameters']['ubki_login'];
                $pass = $this->app['config']['parameters']['ubki_pass'];
            }
        } else {
            $login = $this->app['config']['parameters']['ubki_test_login'];
            $pass = $this->app['config']['parameters']['ubki_test_pass'];
        }


        $file = $config->getProjectPath('views') . '/ubki/lib/auth/reqAuth.xml';
        $xmlStr = file_get_contents($file);

        // Valid this XML
        if (!$crXml->isValidXml($xmlStr)) {
            $this->app->abort(406, "Invalid format XML - '{$file}'");
        }
        // Load this XML
        $crXml->loadXML($xmlStr);

        // Set login
        $crXml->doc->auth['login'] = $login;
        // Set password
        $crXml->doc->auth['pass'] = $pass;

        $xmlAuth = $crXml->xml();

        // Encode string
        $xmlAuth = $strBox->set($xmlAuth)->base64Encode()->get();

        return $xmlAuth;
    }

    /**
     * getReqForInfo
     * Get XML request for info
     * 
     * @param array $data 
     * @return string
     */
    public function getReqForInfo($data = array()) {
        $crXml = $this->app["my"]->get('xml');
        $config = $this->app["my"]->get('config');
        $sysBox = $this->app['my']->get('system');
        $opts = $this->app['my.opts'];
        //-------------------

        $sessid = $data['sessid'];

        if (!$sessid) {
            $this->app->abort(404, "It is not possible to submit the request. No ID session.");
        }

        // Get XML request
        if ($opts['environment'] == 'production') {
            if ($this->app['debug']) {
                $file = $config->getProjectPath('views') . "/ubki/lib/info/reqGetInfo.xml";
            } else {
                $file = $config->getProjectPath('upload') . "/upload.xml";
            }
        } else {
            $file = $config->getProjectPath('views') . "/ubki/lib/info/reqGetInfo.xml";
        }

        if (!is_file($file)) {
            $this->app->abort(404, "File '{$file}' does not exist.");
        }

        $xmlStr = file_get_contents($file);

        // Valid this XML
        if (!$crXml->isValidXml($xmlStr)) {
            $this->app->abort(406, "Invalid format XML - '{$file}'");
        }

        // Load this XML
        $crXml->loadXML($xmlStr);

        // Check type xml is info
        if (!$this->_checkTypeXml($crXml, 'info')) {
            $this->app->abort(406, "Error type of request - '{$file}'");
        }


        // Encode the query using "base64_encode"
        $arrRequest = $crXml->search("request", FALSE);
        $nodeContent = $arrRequest[0]["nodeContent"];
        $nodeEncode = base64_encode($nodeContent);
        // Set a coded request
        $crXml->doc->ubki->req_envelope->req_xml = (string) $nodeEncode;
        // Set "sessid"
        $my_sessid = (string) $crXml->doc->ubki['sessid'];
        if (!$my_sessid) {
            $crXml->doc->ubki['sessid'] = $sessid;
        }

        $xml = $crXml->xml();

        if ($this->app['debug']) {
            $file = $config->getProjectPath('upload') . "/request.xml";

            $sysBox->saveLog($xml, $file, '');
        }

        return $xml;
    }

    /**
     * getReqSendData
     * Get XML request for send data
     * 
     * @param array $data
     * @return string
     */
    public function getReqSendData($data = array()) {
        $crXml = $this->app["my"]->get('xml');
        $config = $this->app["my"]->get('config');
        $strBox = $this->app['my']->get('string');
        $opts = $this->app['my.opts'];
        //-------------------

        $sessid = $data['sessid'];

        if (!$sessid) {
            $this->app->abort(404, "It is not possible to submit the request. No ID session.");
        }

        // Get XML for send data
        if ($opts['environment'] == 'production') {
            if ($this->app['debug']) {
                $file = $config->getProjectPath('views') . "/ubki/lib/data/reqSendData.xml";
            } else {
                $file = $config->getProjectPath('upload') . "/upload.xml";
            }
        } else {
            $file = $config->getProjectPath('views') . "/ubki/lib/data/reqSendData.xml";
        }

        if (!is_file($file)) {
            $this->app->abort(404, "File '{$file}' does not exist.");
        }

        $xmlStr = file_get_contents($file);

        // Valid this XML
        if (!$crXml->isValidXml($xmlStr)) {
            $this->app->abort(406, "Invalid format XML - '{$file}'");
        }

        // Load this XML
        $crXml->loadXML($xmlStr);

        // Check type xml is info
        if (!$this->_checkTypeXml($crXml, 'data')) {
            $this->app->abort(406, "Error type of request - '{$file}'");
        }

        // Set "sessid"
        $crXml->doc->ubki['sessid'] = $sessid;

        $xml = $crXml->xml();
        // Encode and pack string
        $gzdata = $strBox->set($xml)->base64Encode()->gzPack()->get();

        return $gzdata;
    }

    /**
     * getReqGetRegistry
     * Get XML request for get registry
     * 
     * @param array $data
     * @return string
     */
    public function getReqGetRegistry($data = array()) {
        $crXml = $this->app["my"]->get('xml');
        $config = $this->app["my"]->get('config');
        $strBox = $this->app['my']->get('string');
        $opts = $this->app['my.opts'];
        //-------------------

        $sessid = $data['sessid'];

        if (!$sessid) {
            $this->app->abort(404, "It is not possible to submit the request. No ID session.");
        }

        // Get XML for get registry
        if ($opts['environment'] == 'production') {
            if ($this->app['debug']) {
                $file = $config->getProjectPath('views') . "/ubki/lib/registry/reqGetRegistry.xml";
            } else {
                if (isset($data['todo'])) {
                    $file = $config->getProjectPath('views') . "/ubki/lib/registry/reqGetRegistry.xml";
                }else{
                    $file = $config->getProjectPath('upload') . "/upload.xml";
                }
                
            }
        } else {
            $file = $config->getProjectPath('views') . "/ubki/lib/registry/reqGetRegistry.xml";
        }

        if (!is_file($file)) {
            $this->app->abort(404, "File '{$file}' does not exist.");
        }

        $xmlStr = file_get_contents($file);

        // Valid this XML
        if (!$crXml->isValidXml($xmlStr)) {
            $this->app->abort(406, "Invalid format XML - '{$file}'");
        }

        // Load this XML
        $crXml->loadXML($xmlStr);

        // Check type xml is info
        if (!$this->_checkTypeXml($crXml, 'registry')) {
            $this->app->abort(406, "Error type of request - '{$file}'");
        }

        // Set XML values
        $crXml->doc->prot['sessid'] = $sessid;
        if (isset($data['todo'])) {
            $crXml->doc->prot['todo'] = $data['todo'];
            if ($data['todo'] == 'BIL') {
                $crXml->doc->prot['grp'] = $data['grp'];
            } else {
                $crXml->doc->prot['grp'] = "";
            }
        }
        if (isset($data['indate'])) {
            $crXml->doc->prot['indate'] = $data['indate'];
        }
        if (isset($data['idout'])) {
            $crXml->doc->prot['idout'] = $data['idout'];
        }
        if (isset($data['idalien'])) {
            $crXml->doc->prot['idalien'] = $data['idalien'];
        }
        if (isset($data['zip'])) {
            $crXml->doc->prot['zip'] = $data['zip'];
        }

        // Get XML
        $xml = $crXml->xml();
        // Encode string
        $encode = $strBox->set($xml)->base64Encode()->get();

        return $encode;
    }

    //============ HANDLING RESPONSES ==========

    /**
     * Handling XML authorization response
     * 
     * @return bool
     */
    public function handleResForAuth($res) {
        $crXml = $this->app["my"]->get('xml');
        $strBox = $this->app['my']->get('string');
        $results = array();
        $result = FALSE;
        //----------------------------
        // If response is HTML
        if ($strBox->set($res)->isHtml()) {// ERR
            $arRes = $strBox->cleanHtml();
            $message = "{$arRes['title']}! ";
            $message .= $arRes['content'];

            $this->app->abort(400, $message);
        }

        // Validate this XML
        if (!$crXml->isValidXml($res)) {
            $this->app->abort(406, "Invalid format XML");
        }

        // Load this XML
        $crXml->loadXML($res);

        $idSess = (string) $crXml->doc->auth['sessid'];

        // Check is error
        if (!$idSess) {
            $err_code = (int) $crXml->doc->auth['errcode'];
            $err_message = (string) $crXml->doc->auth['errtext'];
            $this->app->abort($err_code, $err_message);
        }

        // Set sess id and response
        $this->_setSessId($idSess);
        $this->_setResForAuth($res);
        $results['sessid'] = $idSess;

        return $results;
    }

    /**
     * Handling XML informations response
     * 
     * @param array $data 
     * @return bool
     */
    public function handleResForInfo($data) {
        $crXml = $this->app["my"]->get('xml');
        $strBox = $this->app['my']->get('string');
        //----------------------------
        // Get response
        $res = $data['response'];
        // If response is HTML
        if ($strBox->set($res)->isHtml()) {// ERR
            $arRes = $strBox->cleanHtml();
            $message = "{$arRes['title']}! ";
            $message .= $arRes['content'];

            $this->app->abort(400, $message);
        }

        // Validate this XML
        if (!$crXml->isValidXml($res)) {
            $this->app->abort(406, "Invalid format XML");
        }

        // Load this XML
        $crXml->loadXML($res);

        // Check errors
        $this->_checkErrors($crXml);

        // Save response
        $this->_setResForInfo($res);
    }

    /**
     * Handling XML data response
     * 
     * @param array $data 
     * @return bool
     */
    public function handleResSendData($data) {
        $crXml = $this->app["my"]->get('xml');
        $strBox = $this->app['my']->get('string');
        //----------------------------
        // Get response
        $res = $data['response'];
        // If response is HTML
        if ($strBox->set($res)->isHtml()) {// ERR
            $arRes = $strBox->cleanHtml();
            $message = "{$arRes['title']}! ";
            $message .= $arRes['content'];

            $this->app->abort(400, $message);
        }

        // Validate this XML
        if (!$crXml->isValidXml($res)) {
            $this->app->abort(406, "Invalid format XML");
        }

        // Load this XML
        $crXml->loadXML($res);

        // Check errors
        $this->_checkErrors($crXml);

        // Save response
        $this->_setResSendData($res);
    }

    /**
     * Handling XML registry response
     * 
     * @param array $data 
     * @return bool
     */
    public function handleResGetRegistry($data) {
        $crXml = $this->app["my"]->get('xml');
        $strBox = $this->app['my']->get('string');
        //----------------------------
        // Get response
        $res = $data['response'];
        $strBox->set($res);

        // If response is HTML
        if ($strBox->isHtml()) {// ERR
            $arRes = $strBox->cleanHtml();
            $message = "{$arRes['title']}! ";
            $message .= $arRes['content'];

            $this->app->abort(400, $message);
        }

        // If response is XML
        if ($strBox->isXml() && $crXml->isValidXml($res)) {
            // Load this XML
            $crXml->loadXML($res);

            // Check errors
            $this->_checkErrors($crXml);

            $xmlRes = $crXml->xml();
        } else {
            // Uncompress the response
            $zip = $data['zip'];
            if ($zip == 'ZLIB') {
                $xmlRes = $strBox->gzUncompress()->get();
            } else {
                $xmlRes = $strBox->gzUnpack()->get();
            }

            // Validate this XML
            if (!$crXml->isValidXml($xmlRes)) {
                $this->app->abort(406, "Invalid format XML");
            }

            // Load this XML
            $crXml->loadXML($xmlRes);

            // Check type XML
            if (!$this->_checkTypeXml($crXml, 'registry')) {
                // Save response
                $this->_setResSendData($crXml->xml(), TRUE);
                $errcode = 401;
                $errtext = (string) $crXml->doc;
                $this->app->abort($errcode, $errtext);
            }
        }

        // Save response
        $this->_setResGetRegistry($xmlRes);
    }

    //============ DATA OPERATIONS ==========

    /**
     * Post-authorization
     * 
     * @param string $res 
     * @return void
     */
    private function _setResForAuth($res) {
        $config = $this->app["my"]->get('config');
        //---------------------
        $path = $config->getProjectPath("sessid");
        $date_now = date("Ymd");
        $filePath = $path . "/resAuth_{$date_now}.xml";
        file_put_contents($filePath, $res, LOCK_EX);
    }

    /**
     * Post-information
     * 
     * @param string $res 
     * @param bool $isError 
     * @return void
     */
    private function _setResForInfo($res, $isError = false) {
        $config = $this->app["my"]->get('config');
        //---------------------
        $path = $config->getProjectPath("download");
        if ($isError) {
            $filePath = $path . "/response_err.xml";
        } else {
            $filePath = $path . "/response.xml";
        }

        file_put_contents($filePath, $res, LOCK_EX);
    }

    /**
     * Actions after data transfer
     * 
     * @param string $res 
     * @param bool $isError 
     * @return void
     */
    private function _setResSendData($res, $isError = NULL) {
        $config = $this->app["my"]->get('config');
        //---------------------
        $path = $config->getProjectPath("download");
        if ($isError === NULL) {
            $filePath = $path . "/response.xml";
            file_put_contents($filePath, $res, LOCK_EX);
            return;
        }

        if ($isError) {
            $filePath = $path . "/response_err.xml";
        } else {
            $filePath = $path . "/response_notice.xml";
        }

        file_put_contents($filePath, $res, LOCK_EX);
    }

    /**
     * Post-receiving register
     * 
     * @param string $res 
     * @param bool $isError 
     * @return void
     */
    private function _setResGetRegistry($res, $isError = false) {
        $config = $this->app["my"]->get('config');
        //---------------------
        $path = $config->getProjectPath("download");
        if ($isError) {
            $filePath = $path . "/response_err.xml";
        } else {
            $filePath = $path . "/response.xml";
        }

        file_put_contents($filePath, $res, LOCK_EX);
    }

    //============ ERRORS ==========

    /**
     * Check errors
     * 
     * @param CrXml $crXml 
     * @return void
     */
    private function _checkErrors($crXml = null) {
        $strBox = $this->app['my']->get('string');
        //----------------
        // Check for errors
        $errcode = (string) $crXml->ubkidata->tech->error['errtype'];
        if ($strBox->set($errcode)->isNumber()) {
            // Save response
            $this->_setResForInfo($crXml->xml(), TRUE);
            $errcode = intval($errcode);
            $errtext = (string) $crXml->ubkidata->tech->error['errtext'];
            $this->app->abort($errcode, $errtext);
        }

        // Check for errors
        $errcode = (string) $crXml->doc->auth['errcode'];
        if ($strBox->set($errcode)->isNumber()) {
            // Save response
            $this->_setResForInfo($crXml->xml(), TRUE);
            $errcode = intval($errcode);
            $errtext = (string) $crXml->doc->auth['errtext'];
            $this->app->abort($errcode, $errtext);
        }

        // Check for errors
        $state = (string) $crXml->doc->tech->sentdatainfo['state'];
        if ($state == 'er') {
            // Save response
            $this->_setResSendData($crXml->xml(), TRUE);
            $errcode = 406;
            $errtext = "The error in the transmitted data";
            $this->app->abort($errcode, $errtext);
        }

        if ($state == 'nt') {
            // Save response
            $this->_setResSendData($crXml->xml(), FALSE);
            $errcode = 415;
            $errtext = "Error data format";
            $this->app->abort($errcode, $errtext);
        }
    }

    /**
     * Check type xml
     * types: 'info', 'data', 'registry'
     * 
     * @param CrXml $crXml
     * @param string $type  types: 'info', 'data', 'registry'
     * @return bool
     */
    private function _checkTypeXml($crXml = null, $type = 'info') {
        $strBox = $this->app['my']->get('string');
        //----------------

        switch ($type) {
            case 'info':
                // Check request for get info
                $reqtype = (string) $crXml->doc->ubki->req_envelope->req_xml->request['reqtype'];
                return $strBox->set($reqtype)->isNumber();
                break;
            case 'data':
                // Check request for send data
                $reqtype = (string) $crXml->doc->ubki->req_envelope->req_xml->request['reqtype'];
                return !$strBox->set($reqtype)->isNumber();
                break;
            case 'registry':
                // Check request for get registry
                $indate = (string) $crXml->doc->prot['indate'];
                return $strBox->set($indate)->isNumber();
                break;
            default:
                break;
        }
    }

}
