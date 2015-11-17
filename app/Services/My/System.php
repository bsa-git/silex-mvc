<?php

// app/Services/My/System.php

namespace Services\My;

use Silex\Application;


/**
 * Class - System
 * Running system tasks
 *
 * @category Service
 * @package  app\Services
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class System {

    /**
     * Container application
     * 
     * @var Application 
     */
    protected $app;

    //---------------------
    /**
     * Constructor
     *  
     * @param Application $app
     */
    public function __construct(Application $app) {

        $this->app = $app;
    }

    //========= PRINT FUNC ==========
    
    /**
     * Prints human-readable information about a variable
     * @param mixed $source
     * @param bool $return When the return parameter is TRUE, this function will return a string.
     * @return string|void
     */
    public function printR($source, $return = TRUE) {
        $result = '';
        //-----------------
        if($return){
            $result = '<pre>';
        }else{
            echo '<pre>';
        }
        if($return){
            $result .= print_r($source, TRUE);
        }else{
            print_r($source);
        }
        if($return){
            $result .= '</pre>'.'<br>';
            return $result;
        }else{
            echo '</pre>';
        }
    }
    
    
    /**
     * Outputs or returns a parsable string representation of a variable
     * @param mixed $source
     * @param bool $return When the return parameter is TRUE, this function will return a string.
     * @return string|void
     */
    public function varExport($source, $return = TRUE) {
        $result = '';
        //-----------------
        if($return){
            return var_export($source, TRUE);
        }else{
            var_export($source);
        }
    }
    
    //========= ARRAY FUNC ==========

    /**
     * Get an array of nested array of strings to display
     * 
     * @param array $arData 
     * @param bool $isTitle
     * @return array
     *  
     */
    public function ArrData2View($arData = array(), $isTitle = TRUE) {
        $results = array();
        //----------------------
        foreach ($arData as $key => $value) {
            $keyIsString = is_string($key);
            if (is_array($value)) {
                if (!count($value)) {
                    continue;
                }
                if ($isTitle && $keyIsString) {
                    $key = strtoupper($key);
                    $results[] = "-----{$key}-----";
                }
                foreach ($value as $key_ => $value_) {
                    if (!is_string($value_)) {
                        $value_ = var_export($value_, TRUE);
                    }
                    $keyIsString = is_string($key_);
                    $value_ = str_replace("\"", "'", $value_);
                    if ($keyIsString) {
                        $results[] = "{$key_} = \"{$value_}\"";
                    } else {
                        $results[] = "{$value_}";
                    }
                }
            } else {
                if (!is_string($value)) {
                    $value = var_export($value, TRUE);
                }
                $value = str_replace("\"", "'", $value);
                if ($keyIsString) {
                    $results[] = "{$key} = \"{$value}\"";
                } else {
                    $results[] = "{$value}";
                }
            }
        }
        return $results;
    }

    //============ FILE FUNC ======

    /** Get an array of file names sorted in descending order
     *
     * @param  string $prefix //prefix (begin file name - "my_")
     * @param  string $dir //Dir for file
     * @return array
     *
     * e.g. my_20100201.xml;my_20100202.xml;my_20100203.xml
     * or my1.xml;my3.xml;my5.xml -> select file my_5.xml
     */
    public function getNameFilesSortDesc($dir, $prefix = "") {
        $arrNameFiles = array();
        $name = "";
        //----------------------
        $dir = rtrim($dir, '/');
        $dir = rtrim($dir, '\\');
        $dir = "{$dir}/";
        $dirdata = scandir($dir, 1);
        foreach ($dirdata as $key => $element) {
            $isFile = is_file($dir . $element);
            if (is_null($prefix) OR ( $prefix == '')) {
                if ($isFile) {
                    $arrNameFiles[] = $element;
                }
            } else {
                $isPrefix = substr_count($element, $prefix);
                if ($isFile AND $isPrefix) {
                    $arrNameFiles[] = $element;
                }
            }
        }
        return $arrNameFiles;
    }

    /**
     * Create data dir
     * 
     * @param string $strDir
     * @return string|NULL
     */
    public function createDataDir($strDir) {

        //-------------------------------
        if (isset($strDir)) {
            if ($strDir) {
                $strDir = str_replace("\\", "/", $strDir);
                // Create a directory if it does not exist
                if (!is_dir($strDir) && !mkdir($strDir)) {
                    $this->app->abort(406, "Failed to create a directory '{$strDir}' ...");
                }
                $arrStr = str_split($strDir);
                if ($arrStr[strlen($strDir) - 1] !== "/") {
                    $strDir .= "/";
                }
            }
        }
        return $strDir;
    }

    /**
     * Remove files from the directory -> download
     *
     * @param array $excludes An array of prefixes files that can not be removed
     * @return int  // Count deleted files
     *
     */
    public function deleteDownloadFiles($excludes = array()) {
        $config = $this->app["my"]->get('config');
        $count = 0;
        $isPrefix = false;
        $defaults = array("result.txt", "upload.xml", "script.log", "error.log", "upload_",);
        //----------------------
        $excludes = $excludes + $defaults;
        $dir = $config->getProjectPath("download");
        $arrNameFiles = self::getNameFilesSortDesc($dir);
        foreach ($arrNameFiles as $fileName) {
            $fileName = strtolower($fileName);
            foreach ($excludes as $prefix) {
                if (!$isPrefix) {
                    $isPrefix = substr($fileName, 0, strlen($prefix)) == $prefix;
                }
            }
            if (!$isPrefix && unlink($dir . "/{$fileName}")) {
                $count++;
            }
            $isPrefix = false;
        }
        return $count;
    }

    /**
     * Save log
     * 
     * @param string $data 
     * @param string $file 
     * @param string $flags
     * @return int | bool 
     *  
     */
    public function saveLog($data, $file = NULL, $flags = FILE_APPEND) {
        $config = $this->app["my"]->get('config');
        //---------------------
        if (!$file) {
            $patch_dir = $config->getProjectPath("logs");
            $file = "{$patch_dir}user.log";
        }
        if($flags){
            return file_put_contents($file, $data, $flags | LOCK_EX);
        }else{
            return file_put_contents($file, $data, LOCK_EX);
        }
        
    }
    
    /**
     * Get file info
     * The SplFileInfo class offers a 
     * information for an individual file.
     * 
     * @param string $path 
     * 
     * @return SplFileInfo 
     */
    public function getFileInfo($path) {
        $info = new SplFileInfo($path);
        return $info;
    }

    //=========== XML FUNC =============

    /**
     * The main function for converting to an XML document.
     * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
     *
     * @param array $data
     * @param string $rootNodeName - what you want the root node to be - defaultsto data.
     * @param SimpleXMLElement $xml - should only be used recursively
     * @return string XML
     */
    public function Array2Xml($data, $rootNodeName = 'data', $xml = null) {
        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        if (ini_get('zend.ze1_compatibility_mode') == 1) {
            ini_set('zend.ze1_compatibility_mode', 0);
        }

        if ($xml == null) {
            $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
        }

        // loop through the data passed in.
        foreach ($data as $key => $value) {
            // no numeric keys in our xml please!
            if (is_numeric($key)) {
                // make string key...
                $key = "unknownNode_" . (string) $key;
            }

            // replace anything not alpha numeric
            $key = preg_replace('/[^a-z_0-9]/i', '', $key);

            // if there is another array found recrusively call this function
            if (is_array($value)) {
                $node = $xml->addChild($key);
                // recrusive call.
                self::Array2Xml($value, $rootNodeName, $node);
            } else {
                // add single node. htmlspecialchars()
                // $value = htmlentities($value);
                $value = htmlspecialchars($value);
                $xml->addChild($key, $value);
            }
        }
        // pass back as string. or simple xml object if you want!
        return $xml->asXML();
    }

    /**
     * Convert XML into an array
     * 
     * @param string $aXml
     * 
     * @return array
     */
    public function Xml2Array($aXml, Application $app) {
        $classJson = $app['zend']->get('json');
        //------------------------
        $json = $classJson::fromXml($aXml, true);
        $arrXml = $classJson::decode($json);
        return $arrXml;
    }

    /**
     * Validating XML
     * 
     * @param type $xmlStr
     * @return type
     */
    public function isValidXml($xmlStr = '') {
        // Load the XML formatted string into a Simple XML Element object.
        $simpleXmlElementObject = simplexml_load_string($xmlStr);
        $isError = $simpleXmlElementObject === NULL;
        return !$isError;
    }

    //========= COMPRESS FUNC ==========

    /**
     * Decompresses a string packaged by gzip
     * 
     * @param string $data The data for unzipping, packed with gzencode().
     * @param string $filename
     * @param string $error
     * @param int $maxlength
     * @return string|false Unpacked string or FALSE, if an error occurred.
     */
    public function gzdecode($data, &$filename = '', &$error = '', $maxlength = null) {
        $len = strlen($data);
        if ($len < 18 || strcmp(substr($data, 0, 2), "\x1f\x8b")) {
            $error = "Not in GZIP format.";
            return null;  // Not GZIP format (See RFC 1952)
        }
        $method = ord(substr($data, 2, 1));  // Compression method
        $flags = ord(substr($data, 3, 1));  // Flags
        if ($flags & 31 != $flags) {
            $error = "Reserved bits not allowed.";
            return null;
        }
        // NOTE: $mtime may be negative (PHP integer limitations)
        $mtime = unpack("V", substr($data, 4, 4));
        $mtime = $mtime[1];
        $xfl = substr($data, 8, 1);
        $os = substr($data, 8, 1);
        $headerlen = 10;
        $extralen = 0;
        $extra = "";
        if ($flags & 4) {
            // 2-byte length prefixed EXTRA data in header
            if ($len - $headerlen - 2 < 8) {
                return false;  // invalid
            }
            $extralen = unpack("v", substr($data, 8, 2));
            $extralen = $extralen[1];
            if ($len - $headerlen - 2 - $extralen < 8) {
                return false;  // invalid
            }
            $extra = substr($data, 10, $extralen);
            $headerlen += 2 + $extralen;
        }
        $filenamelen = 0;
        $filename = "";
        if ($flags & 8) {
            // C-style string
            if ($len - $headerlen - 1 < 8) {
                return false; // invalid
            }
            $filenamelen = strpos(substr($data, $headerlen), chr(0));
            if ($filenamelen === false || $len - $headerlen - $filenamelen - 1 < 8) {
                return false; // invalid
            }
            $filename = substr($data, $headerlen, $filenamelen);
            $headerlen += $filenamelen + 1;
        }
        $commentlen = 0;
        $comment = "";
        if ($flags & 16) {
            // C-style string COMMENT data in header
            if ($len - $headerlen - 1 < 8) {
                return false;    // invalid
            }
            $commentlen = strpos(substr($data, $headerlen), chr(0));
            if ($commentlen === false || $len - $headerlen - $commentlen - 1 < 8) {
                return false;    // Invalid header format
            }
            $comment = substr($data, $headerlen, $commentlen);
            $headerlen += $commentlen + 1;
        }
        $headercrc = "";
        if ($flags & 2) {
            // 2-bytes (lowest order) of CRC32 on header present
            if ($len - $headerlen - 2 < 8) {
                return false;    // invalid
            }
            $calccrc = crc32(substr($data, 0, $headerlen)) & 0xffff;
            $headercrc = unpack("v", substr($data, $headerlen, 2));
            $headercrc = $headercrc[1];
            if ($headercrc != $calccrc) {
                $error = "Header checksum failed.";
                return false;    // Bad header CRC
            }
            $headerlen += 2;
        }
        // GZIP FOOTER
        $datacrc = unpack("V", substr($data, -8, 4));
        $datacrc = sprintf('%u', $datacrc[1] & 0xFFFFFFFF);
        $isize = unpack("V", substr($data, -4));
        $isize = $isize[1];
        // decompression:
        $bodylen = $len - $headerlen - 8;
        if ($bodylen < 1) {
            // IMPLEMENTATION BUG!
            return null;
        }
        $body = substr($data, $headerlen, $bodylen);
        $data = "";
        if ($bodylen > 0) {
            switch ($method) {
                case 8:
                    // Currently the only supported compression method:
                    $data = gzinflate($body, $maxlength);
                    break;
                default:
                    $error = "Unknown compression method.";
                    return false;
            }
        }  // zero-byte body content is allowed
        // Verifiy CRC32
        $crc = sprintf("%u", crc32($data));
        $crcOK = $crc == $datacrc;
        $lenOK = $isize == strlen($data);
        if (!$lenOK || !$crcOK) {
            $error = ( $lenOK ? '' : 'Length check FAILED. ') . ( $crcOK ? '' : 'Checksum FAILED.');
            return false;
        }
        return $data;
    }

}
