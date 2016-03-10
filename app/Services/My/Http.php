<?php

// app/Services/My/Http.php

namespace Services\My;


/**
 * Class - Http
 * Sending data Http
 *
 * @category Service
 * @package  app\Services
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 */
class Http {

    const HTTP_CONTINUE = 100;
    const HTTP_SWITCHING_PROTOCOLS = 101;
    const HTTP_PROCESSING = 102;            // RFC2518
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    const HTTP_NO_CONTENT = 204;
    const HTTP_RESET_CONTENT = 205;
    const HTTP_PARTIAL_CONTENT = 206;
    const HTTP_MULTI_STATUS = 207;          // RFC4918
    const HTTP_ALREADY_REPORTED = 208;      // RFC5842
    const HTTP_IM_USED = 226;               // RFC3229
    const HTTP_MULTIPLE_CHOICES = 300;
    const HTTP_MOVED_PERMANENTLY = 301;
    const HTTP_FOUND = 302;
    const HTTP_SEE_OTHER = 303;
    const HTTP_NOT_MODIFIED = 304;
    const HTTP_USE_PROXY = 305;
    const HTTP_RESERVED = 306;
    const HTTP_TEMPORARY_REDIRECT = 307;
    const HTTP_PERMANENTLY_REDIRECT = 308;  // RFC7238
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    const HTTP_REQUEST_TIMEOUT = 408;
    const HTTP_CONFLICT = 409;
    const HTTP_GONE = 410;
    const HTTP_LENGTH_REQUIRED = 411;
    const HTTP_PRECONDITION_FAILED = 412;
    const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    const HTTP_REQUEST_URI_TOO_LONG = 414;
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const HTTP_EXPECTATION_FAILED = 417;
    const HTTP_I_AM_A_TEAPOT = 418;                                               // RFC2324
    const HTTP_UNPROCESSABLE_ENTITY = 422;                                        // RFC4918
    const HTTP_LOCKED = 423;                                                      // RFC4918
    const HTTP_FAILED_DEPENDENCY = 424;                                           // RFC4918
    const HTTP_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL = 425;   // RFC2817
    const HTTP_UPGRADE_REQUIRED = 426;                                            // RFC2817
    const HTTP_PRECONDITION_REQUIRED = 428;                                       // RFC6585
    const HTTP_TOO_MANY_REQUESTS = 429;                                           // RFC6585
    const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;                             // RFC6585
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;
    const HTTP_BAD_GATEWAY = 502;
    const HTTP_SERVICE_UNAVAILABLE = 503;
    const HTTP_GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;                        // RFC2295
    const HTTP_INSUFFICIENT_STORAGE = 507;                                        // RFC4918
    const HTTP_LOOP_DETECTED = 508;                                               // RFC5842
    const HTTP_NOT_EXTENDED = 510;                                                // RFC2774
    const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;

    /**
     * Status codes translation table.
     *
     * The list of codes is complete according to the
     * {@link http://www.iana.org/assignments/http-status-codes/ Hypertext Transfer Protocol (HTTP) Status Code Registry}
     * (last updated 2012-02-13).
     *
     * Unless otherwise noted, the status code is defined in RFC2616.
     *
     * @var array
     */
    public static $httpCodes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing', // RFC2518
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status', // RFC4918
        208 => 'Already Reported', // RFC5842
        226 => 'IM Used', // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect', // RFC7238
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot', // RFC2324
        422 => 'Unprocessable Entity', // RFC4918
        423 => 'Locked', // RFC4918
        424 => 'Failed Dependency', // RFC4918
        425 => 'Reserved for WebDAV advanced collections expired proposal', // RFC2817
        426 => 'Upgrade Required', // RFC2817
        428 => 'Precondition Required', // RFC6585
        429 => 'Too Many Requests', // RFC6585
        431 => 'Request Header Fields Too Large', // RFC6585
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates (Experimental)', // RFC2295
        507 => 'Insufficient Storage', // RFC4918
        508 => 'Loop Detected', // RFC5842
        510 => 'Not Extended', // RFC2774
        511 => 'Network Authentication Required', // RFC6585
    );
    public static $mimeTypes = array(
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',
        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',
        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',
        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',
        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );
    private $_data = NULL;
    private $_http_info = NULL;
    private $_headers = array();

//    private $_http_codes = NULL;

    /**
     * Constructor 
     * 
     * @return HttpBox 
     */
    public function __construct() {
        return $this;
    }

    /**
     * Get Info 
     * 
     * @return array 
     */
    function getInfo() {
        return $this->_http_info;
    }

    /**
     * Get Info 
     * 
     * @return array 
     */
    function getHeaders() {
        return $this->_headers;
    }

    /**
     * Set header
     * 
     * @param type $ch
     * @param string $header
     * @return int
     * 
     */
    private function _setHeader($ch, $header) {
        $this->_headers[] = $header;
        return strlen($header);
    }

    /**
     * Get Data 
     * 
     * @return array 
     */
    function getData() {
        return $this->_data;
    }

    /**
     * Get Data 
     * 
     * @return HttpBox 
     */
    function setData($data) {
        $this->_data = $data;
        return $this;
    }

    /**
     * Get HttpCodes
     * 
     * @return array 
     */
    function getHttpCodes() {
        return self::$httpCodes;
    }

    /**
     * Get MimeTypes
     * 
     * @return array 
     */
    function getMimeTypes() {
        return self::$mimeTypes;
    }

    /**
     * Send a POST requst using cURL 
     * @param string $url to request 
     * @param array $post values to send 
     * @param array $options for cURL 
     * @return string 
     */
    function post($url, array $options = array()) {
        $err = 0;
        $errmsg = '';
        //---------------------
        $data = (is_array($this->_data)) ? http_build_query($this->_data) : $this->_data;

        // Define whether the $url debugging
        $debug = $this->isDebugUrl($url);

        if ($debug) {
            $timeout_on_connect = 5;
            $timeout_on_response = 5;
        } else {
            $timeout_on_connect = 120;
            $timeout_on_response = 120;
        }

        $defaults = array(
            CURLOPT_RETURNTRANSFER => true, // return web page 
            CURLOPT_HEADER => false, // don't return headers 
            CURLOPT_FOLLOWLOCATION => true, // follow redirects 
            CURLOPT_ENCODING => "", // handle all encodings 
            CURLOPT_USERAGENT => "spider", // who am i 
            CURLOPT_AUTOREFERER => true, // set referer on redirect 
            CURLOPT_CONNECTTIMEOUT => $timeout_on_connect, // timeout on connect 
            CURLOPT_TIMEOUT => $timeout_on_response, // timeout on response 
            CURLOPT_MAXREDIRS => 10, // stop after 10 redirects 
            CURLOPT_POST => 1, // i am sending post data 
            CURLOPT_POSTFIELDS => $data, // this are my post vars 
            CURLOPT_SSL_VERIFYHOST => false, // don't verify ssl 
            CURLOPT_SSL_VERIFYPEER => false, // 
            CURLOPT_VERBOSE => $debug,
            CURLOPT_PROXY => "", // proxy.azot.local:3128
            CURLOPT_PROXYUSERPWD => "", // m5-iasup:m234ASUP
            CURLOPT_HEADERFUNCTION => array($this, '_setHeader')// this is a callback inside an object 
        );

        $ch = curl_init($url);
        $arReplace = array_replace($defaults, $options);
        curl_setopt_array($ch, $arReplace);
        $result = curl_exec($ch);
        if ($result === FALSE) {
            $err = curl_errno($ch);
            $errmsg = curl_error($ch);
            trigger_error(curl_error($ch));
        }

        // Get http info
        $this->_http_info = curl_getinfo($ch);
        if ($err) {
            $this->_http_info['errno'] = $err;
        } else {
            $this->_http_info['response'] = $result;
        }
        if ($errmsg) {
            $this->_http_info['errmsg'] = $errmsg;
        }
        $this->_http_info['request'] = $this->_data;

        $this->_http_info['http_code'] = $this->_http_info['http_code'] . " " . self::$httpCodes[$this->_http_info['http_code']];


        curl_close($ch);

        return $result;
    }

    /**
     * Send a GET requst using cURL 
     * 
     * @param string $url to request 
     * @param array $get values to send 
     * @param array $options for cURL 
     * @return string 
     */
    function get($url, array $options = array()) {
        $err = 0;
        $errmsg = '';
        //------------------------
        $data = (is_array($this->_data)) ? http_build_query($this->_data) : $this->_data;

        $url = $url . (strpos($url, '?') === FALSE ? '?' : '') . $data;

        // Define whether the $url debugging
        $debug = $this->isDebugUrl($url);

        if ($debug) {
            $timeout_on_connect = 3;
            $timeout_on_response = 3;
        } else {
            $timeout_on_connect = 120;
            $timeout_on_response = 120;
        }

        $defaults = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_VERBOSE => $debug
        );

        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            $err = curl_errno($ch);
            $errmsg = curl_error($ch);
            trigger_error(curl_error($ch));
        }

        // Get http info
        $this->_http_info = curl_getinfo($ch);
        if ($err) {
            $this->_http_info['errno'] = $err;
        } else {
            $this->_http_info['response'] = $result;
        }
        if ($errmsg) {
            $this->_http_info['errmsg'] = $errmsg;
        }
        $this->_http_info['request'] = $url;

        $this->_http_info['http_code'] = $this->_http_info['http_code'] . " " . self::$httpCodes[$this->_http_info['http_code']];

        curl_close($ch);
        return $result;
    }

    /**
     * Send a PUT requst using cURL 
     * if($_SERVER['REQUEST_METHOD'] == 'PUT') return $this->getInputPHP();
     * if($this->put('http://example.com/api/a/b/c', array('foo' => 'bar')) == 200) 
     *       // do something 
     *  else 
     *       // do something else.
     * 
     * 
     * @param type $url
     * @return int|boolean
     */
    function put($url) {
        $data = (is_array($this->_data)) ? http_build_query($this->_data) : $this->_data;
        if ($ch = curl_init($url)) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($data)));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_exec($ch);

            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            return (int) $status;
        } else {
            return false;
        }
    }

    /**
     * Send a DELETE requst using cURL 
     * if($_SERVER['REQUEST_METHOD'] == 'DELETE') return $this->getInputPHP();
     * if($this->delete('http://example.com/api/a/b/c', array('foo' => 'bar')) == 200) 
     *       // do something 
     *  else 
     *       // do something else.
     * 
     * 
     * @param type $url
     * @return int|boolean
     */
    function delete($url) {
        $data = (is_array($this->_data)) ? http_build_query($this->_data) : $this->_data;
        if ($ch = curl_init($url)) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($data)));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_exec($ch);

            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            return (int) $status;
        } else {
            return false;
        }
    }

    /**
     * Description: An example of the grab() function in order 
     * to grab contents from a website while remaining fully 
     * camouflaged by using a fake user agent and fake headers. 
     * 
     * @param string $url
     * @return string
     */
    function grab($url) {
        $curl = curl_init();

        // Setup headers - I used the same headers from Firefox version 2.0.0.6 
        // below was split up because php.net said the line was too long. :/ 
        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";
        $header[] = "Pragma: "; // browsers keep this blank. 

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Googlebot/2.1 (+http://www.google.com/bot.html)');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_REFERER, 'http://www.google.com');
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);

        $html = curl_exec($curl); // execute the curl command 
        curl_close($curl); // close the connection 

        return $html; // and finally, return $html
    }

    /**
     * Проверка является ли URL отладочным
     * 
     * @params string $url
     * @return bool 
     */
    function isDebugUrl($url) {
        $arrQuery = array();
        $debug = FALSE;
        //--------------------
        $arrUrl = parse_url($url);
        $host = $arrUrl["host"];
        if (isset($arrUrl["query"])) {
            $query = $arrUrl["query"];
            parse_str($query, $arrQuery);
            $debug = isset($arrQuery["XDEBUG_SESSION_START"]);
        }

        return $debug;
    }

    /**
     * Returns User IP Address
     * @params
     *        IN:  NONE
     *        OUT: ip address(0.0.0.0)
     */
    function getUserIP() {
        $ip = null;
        if ((isset($_SERVER['HTTP_X_FORWARDED_FOR'])) &&
                (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif ((isset($_SERVER['HTTP_CLIENT_IP'])) &&
                (!empty($_SERVER['HTTP_CLIENT_IP']))) {
            $ip = explode(".", $_SERVER['HTTP_CLIENT_IP']);
            $ip = "{$ip[3]}.{$ip[2]}.{$ip[1]}.{$ip[0]}";
        } elseif ((!isset($_SERVER['HTTP_X_FORWARDED_FOR'])) &&
                (empty($_SERVER['HTTP_X_FORWARDED_FOR'])) &&
                (!isset($_SERVER['HTTP_CLIENT_IP'])) &&
                (empty($_SERVER['HTTP_CLIENT_IP'])) &&
                (isset($_SERVER['REMOTE_ADDR']))) {
            $ip = ($_SERVER['REMOTE_ADDR']);
        } else {
            // ip is null
        }
        return ($ip);
    }

    /**
     * Get the contents of the buffer INPUT for PHP
     * 
     */
    function getInputPHP() {
        return file_get_contents("php://input");
    }

}
