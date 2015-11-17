<?php

// app/Services/SysBox.php

namespace Services;

use Silex\Application;

/**
 * Class - ZendService
 * Add zend service
 *
 * @category Service
 * @package  app\Services
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class ZendService {

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
     * Get service for ZF
     * 
     * @param mixed $service
     * @return mixed
     */
    public function get($service) {

        $method = 'get';
        $service = strtolower($service);
        $arService = explode('_', $service);
        foreach ($arService as $item) {
            $method .= ucfirst($item);
        }
        if (method_exists($this, $method)) {
            return $this->$method();
        } else {
            $this->app->abort(404, "Service {$service} not Found");
        }
    }

    /**
     * Get class Json
     * this class to json operations
     * 
     * @return Zend_Json
     */
    public function getJson() {
        if (!isset($this->app['zend.json'])) {
            $this->app['zend.json'] = function () {
                require_once "Zend/Json.php";
                return Zend_Json;
            };
        }
        return $this->app['zend.json'];
    }

    /**
     * Get filter
     * 
     * @return Zend_Filter
     */
    public function getFilter() {
        if (!isset($this->app['zend.filter'])) {
            $this->iniFilter();
            $this->app['zend.filter'] = function () {
                require_once "Zend/Filter.php";
                return new \Zend_Filter();
            };
        }
        return $this->app['zend.filter'];
    }

    /**
     * Ini filter
     * 
     * @return void
     */
    public function iniFilter() {

        // -- Alnum --
        //  returns the string $value, removing all but alphabetic characters. This filter includes an option to also allow white space characters
        // - allowwhitespace: If this option is set then whitespace characters are allowed. Otherwise they are supressed. Per default whitespaces are not allowed
        if (!isset($this->app['zend.filter.alnum'])) {
            $this->app['zend.filter.alnum'] = $this->app->protect(function ($allowwhitespace = false) {
                require_once "Zend/Filter/Alnum.php";
                return new \Zend_Filter_Alnum($allowwhitespace);
            });
        }


        // -- Alpha --
        // returns the string $value, removing all but alphabetic characters. This filter includes an option to also allow white space characters
        // - allowwhitespace: If this option is set then whitespace characters are allowed. Otherwise they are suppressed. By default whitespace characters are not allowed.
        if (!isset($this->app['zend.filter.alpha'])) {
            $this->app['zend.filter.alpha'] = $this->app->protect(function ($allowwhitespace = false) {
                require_once "Zend/Filter/Alpha.php";
                return new \Zend_Filter_Alpha($allowwhitespace);
            });
        }

        // -- BaseName --
        // allows you to filter a string which contains the path to a file and it will return the base name of this file
        if (!isset($this->app['zend.filter.basename'])) {
            $this->app['zend.filter.basename'] = function () {
                require_once "Zend/Filter/BaseName.php";
                return new \Zend_Filter_BaseName();
            };
        }

        // -- Boolean --
        // This filter changes a given input to be a BOOLEAN value
        // The following options are supported for Zend_Filter_Boolean:
        // - casting: When this option is set to TRUE then any given input will be casted to boolean. This option defaults to TRUE.
        // - locale: This option sets the locale which will be used to detect localized input.
        // - type: The type option sets the boolean type which should be used. Read the following for details.
        if (!isset($this->app['zend.filter.boolean'])) {
            $this->app['zend.filter.boolean'] = $this->app->protect(function ($options = null) {
                require_once "Zend/Filter/Boolean.php";
                return new \Zend_Filter_Boolean($options);
            });
        }
        
        // -- Int --
        // allows you to transform a sclar value which contains into an integer
        if (!isset($this->app['zend.filter.int'])) {
            $this->app['zend.filter.int'] = function () {
                require_once "Zend/Filter/Int.php";
                return new \Zend_Filter_Int();
            };
        }

        // -- Callback --
        // This filter allows you to use own methods in conjunction with Zend_Filter. You don't have to create a new filter when you already have a method which does the job.
        // The following options are supported for Zend_Filter_Callback:
        // - callback: This sets the callback which should be used.
        // - options: This property sets the options which are used when the callback is processed.
        if (!isset($this->app['zend.filter.callback'])) {
            $this->app['zend.filter.callback'] = $this->app->protect(function ($options) {
                require_once "Zend/Filter/Callback.php";
                return new \Zend_Filter_Callback($options);
            });
        }

        // -- Compress --
        // This filter are capable of compressing strings, files, and directories.
        // The following options are supported for Zend_Filter_Compress:
        // - adapter: The compression adapter which should be used. It defaults to Gz.
        // - options: Additional options which are given to the adapter at initiation. Each adapter supports it's own options.
        if (!isset($this->app['zend.filter.compress'])) {
            $this->app['zend.filter.compress'] = $this->app->protect(function ($options = null) {
                require_once "Zend/Filter/Compress.php";
                return new \Zend_Filter_Compress($options);
            });
        }

        // -- Decompress --
        // This filter are capable of decompressing  strings, files, and directories.
        // The following options are supported for Zend_Filter_Decompress:
        // - adapter: The compression adapter which should be used. It defaults to Gz.
        // - options: Additional options which are given to the adapter at initiation. Each adapter supports it's own options.
        if (!isset($this->app['zend.filter.decompress'])) {
            $this->app['zend.filter.decompress'] = $this->app->protect(function ($options = null) {
                require_once "Zend/Filter/Decompress.php";
                return new \Zend_Filter_Decompress($options);
            });
        }

        // -- Digits --
        // Returns the string $value, removing all but digits.
        if (!isset($this->app['zend.filter.digits'])) {
            $this->app['zend.filter.digits'] = function () {
                require_once "Zend/Filter/Digits.php";
                return new \Zend_Filter_Digits();
            };
        }

        // -- Dir --
        // Given a string containing a path to a file, this function will return the name of the directory
        if (!isset($this->app['zend.filter.dir'])) {
            $this->app['zend.filter.dir'] = function () {
                require_once "Zend/Filter/Dir.php";
                return new \Zend_Filter_Dir();
            };
        }

        // -- Encrypt --
        // This filter allow to encrypt any given string. Therefor they make use of Adapters. 
        // Actually there are adapters for the Mcrypt and OpenSSL extensions from PHP.
        // The following options are supported for Zend_Filter_Encrypt:
        // - adapter: This sets the encryption adapter which should be used
        // - algorithm: Only MCrypt. The algorithm which has to be used. It should be one of the algorithm ciphers which can be found under » PHP's mcrypt ciphers. If not set it defaults to blowfish.
        // - algorithm_directory: Only MCrypt. The directory where the algorithm can be found. If not set it defaults to the path set within the mcrypt extension.
        // - compression: If the encrypted value should be compressed. Default is no compression.
        // - envelope: Only OpenSSL. The encrypted envelope key from the user who encrypted the content. You can either provide the path and filename of the key file, or just the content of the key file itself. When the package option has been set, then you can omit this parameter.
        // - key: Only MCrypt. The encryption key with which the input will be encrypted. You need the same key for decryption.
        // - mode: Only MCrypt. The encryption mode which has to be used. It should be one of the modes which can be found under » PHP's mcrypt modes. If not set it defaults to 'cbc'.
        // - mode_directory: Only MCrypt. The directory where the mode can be found. If not set it defaults to the path set within the Mcrypt extension.
        // - package: Only OpenSSL. If the envelope key should be packed with the encrypted value. Default is FALSE.
        // - private: Only OpenSSL. Your private key which will be used for encrypting the content. Also the private key can be either a filename with path of the key file, or just the content of the key file itself.
        // - public: Only OpenSSL. The public key of the user whom you want to provide the encrpted content. You can give multiple public keys by using an array. You can eigther provide the path and filename of the key file, or just the content of the key file itself.
        // - salt: Only MCrypt. If the key should be used as salt value. The key used for encryption will then itself also be encrypted. Default is FALSE.
        // - vector: Only MCrypt. The initialization vector which shall be used. If not set it will be a random vector.
        if (!isset($this->app['zend.filter.encrypt'])) {
            $this->app['zend.filter.encrypt'] = $this->app->protect(function ($options = null) {
                require_once "Zend/Filter/Encrypt.php";
                return new \Zend_Filter_Encrypt($options);
            });
        }

        // -- Decrypt --
        // This filter allow to decrypt any given string. Therefor they make use of Adapters. 
        // Actually there are adapters for the Mcrypt and OpenSSL extensions from PHP.
        // The following options are supported for Zend_Filter_Decrypt:
        // - adapter: This sets the encryption adapter which should be used
        // - algorithm: Only MCrypt. The algorithm which has to be used. It should be one of the algorithm ciphers which can be found under » PHP's mcrypt ciphers. If not set it defaults to blowfish.
        // - algorithm_directory: Only MCrypt. The directory where the algorithm can be found. If not set it defaults to the path set within the mcrypt extension.
        // - compression: If the encrypted value should be compressed. Default is no compression.
        // - envelope: Only OpenSSL. The encrypted envelope key from the user who encrypted the content. You can either provide the path and filename of the key file, or just the content of the key file itself. When the package option has been set, then you can omit this parameter.
        // - key: Only MCrypt. The encryption key with which the input will be encrypted. You need the same key for decryption.
        // - mode: Only MCrypt. The encryption mode which has to be used. It should be one of the modes which can be found under » PHP's mcrypt modes. If not set it defaults to 'cbc'.
        // - mode_directory: Only MCrypt. The directory where the mode can be found. If not set it defaults to the path set within the Mcrypt extension.
        // - package: Only OpenSSL. If the envelope key should be packed with the encrypted value. Default is FALSE.
        // - private: Only OpenSSL. Your private key which will be used for encrypting the content. Also the private key can be either a filename with path of the key file, or just the content of the key file itself.
        // - public: Only OpenSSL. The public key of the user whom you want to provide the encrpted content. You can give multiple public keys by using an array. You can eigther provide the path and filename of the key file, or just the content of the key file itself.
        // - salt: Only MCrypt. If the key should be used as salt value. The key used for encryption will then itself also be encrypted. Default is FALSE.
        // - vector: Only MCrypt. The initialization vector which shall be used. If not set it will be a random vector.
        if (!isset($this->app['zend.filter.decrypt'])) {
            $this->app['zend.filter.decrypt'] = $this->app->protect(function ($options = null) {
                require_once "Zend/Filter/Decrypt.php";
                return new \Zend_Filter_Decrypt($options);
            });
        }

        // -- HtmlEntities --
        // Returns the string $value, converting characters to their corresponding HTML entity equivalents where they exist.
        // The following options are supported for Zend_Filter_HtmlEntities:
        // - quotestyle: Equivalent to the PHP htmlentities native function parameter quote_style. This allows you to define what will be done with 'single' and "double" quotes. The following constants are accepted: ENT_COMPAT, ENT_QUOTES ENT_NOQUOTES with the default being ENT_COMPAT.
        // - charset: Equivalent to the PHP htmlentities native function parameter charset. This defines the character set to be used in filtering. Unlike the PHP native function the default is 'UTF-8'. See "http://php.net/htmlentities" for a list of supported character sets.
        //   Note: This option can also be set via the $options parameter as a Zend_Config object or array. The option key will be accepted as either charset or encoding. 
        // - doublequote: Equivalent to the PHP htmlentities native function parameter double_encode. If set to false existing html entities will not be encoded. The default is to convert everything (true).
        //   Note: This option must be set via the $options parameter or the setDoubleEncode() method. 
        if (!isset($this->app['zend.filter.htmlentities'])) {
            $this->app['zend.filter.htmlentities'] = $this->app->protect(function ($options = array()) {
                require_once "Zend/Filter/HtmlEntities.php";
                return new \Zend_Filter_HtmlEntities($options);
            });
        }

        // -- LocalizedToNormalized --
        // These two filters can change given localized input to it's normalized representation and reverse. They use in Background Zend_Locale to do this transformation for you
        // The following options are supported for Zend_Filter_LocalizedToNormalized:
        // - date_format: This sets the date format to use for normalization and to detect the localized date format
        // - locale: This sets the locale to use for normalization and to detect the localized format
        // - precision: This sets the precision to use for number conversion
        if (!isset($this->app['zend.filter.localizedtonormalized'])) {
            $this->app['zend.filter.localizedtonormalized'] = $this->app->protect(function ($options = null) {
                require_once "Zend/Filter/LocalizedToNormalized.php";
                return new \Zend_Filter_LocalizedToNormalized($options);
            });
        }

        // -- NormalizedToLocalized --
        // These two filters can change given localized input to it's normalized representation and reverse. They use in Background Zend_Locale to do this transformation for you
        // The following options are supported for Zend_Filter_LocalizedToNormalized:
        // - date_format: This sets the date format to use for normalization and to detect the localized date format
        // - locale: This sets the locale to use for normalization and to detect the localized format
        // - precision: This sets the precision to use for number conversion
        if (!isset($this->app['zend.filter.normalizedtolocalized'])) {
            $this->app['zend.filter.normalizedtolocalized'] = $this->app->protect(function ($options = null) {
                require_once "Zend/Filter/NormalizedToLocalized.php";
                return new \Zend_Filter_NormalizedToLocalized($options);
            });
        }

        // -- Null --
        // This filter will change the given input to be NULL if it meets specific criteria. 
        // This is often necessary when you work with databases and want to have a NULL value instead of a boolean or any other type.
        // The following options are supported for Zend_Filter_Null:
        // - type: The variable type which should be supported.
        if (!isset($this->app['zend.filter.null'])) {
            $this->app['zend.filter.null'] = $this->app->protect(function ($options = null) {
                require_once "Zend/Filter/Null.php";
                return new \Zend_Filter_Null($options);
            });
        }

        // -- PregReplace --
        //  performs a search using regular expressions and replaces all found elements 
        // The following options are supported for Zend_Filter_PregReplace:
        // - match: The pattern which will be searched for.
        // - replace: The string which is used as replacement for the matches.
        if (!isset($this->app['zend.filter.pregreplace'])) {
            $this->app['zend.filter.pregreplace'] = $this->app->protect(function ($options = null) {
                require_once "Zend/Filter/PregReplace.php";
                return new \Zend_Filter_PregReplace($options);
            });
        }

        // -- RealPath --
        //  This filter will resolve given links and pathnames and returns canonicalized absolute pathnames
        // The following options are supported for Zend_Filter_RealPath:
        // - exists: This option defaults to TRUE which checks if the given path really exists.
        if (!isset($this->app['zend.filter.realpath'])) {
            $this->app['zend.filter.realpath'] = $this->app->protect(function ($options = true) {
                require_once "Zend/Filter/RealPath.php";
                return new \Zend_Filter_RealPath($options);
            });
        }

        // -- StringToLower --
        //  This filter converts any input to be lowercased.
        // The following options are supported for Zend_Filter_StringToLower:
        // - encoding: This option can be used to set an encoding which has to be used
        if (!isset($this->app['zend.filter.stringtolower'])) {
            $this->app['zend.filter.stringtolower'] = $this->app->protect(function ($options = null) {
                require_once "Zend/Filter/StringToLower.php";
                return new \Zend_Filter_StringToLower($options);
            });
        }

        // -- StringToUpper --
        //  This filter converts any input to be uppercased.
        // The following options are supported for Zend_Filter_StringToUpper:
        // - encoding: This option can be used to set an encoding which has to be used
        if (!isset($this->app['zend.filter.stringtoupper'])) {
            $this->app['zend.filter.stringtoupper'] = $this->app->protect(function ($options = null) {
                require_once "Zend/Filter/StringToUpper.php";
                return new \Zend_Filter_StringToUpper($options);
            });
        }

        // -- StringTrim --
        //  This filter modifies a given string such that certain characters are removed from the beginning and end.
        // The following options are supported for Zend_Filter_StringTrim:
        // - charlist: List of characters to remove from the beginning and end of the string. 
        // If this is not set or is null, the default behavior will be invoked, which is to remove only whitespace from the beginning and end of the string.
        if (!isset($this->app['zend.filter.stringtrim'])) {
            $this->app['zend.filter.stringtrim'] = $this->app->protect(function ($options = null) {
                require_once "Zend/Filter/StringTrim.php";
                return new \Zend_Filter_StringTrim($options);
            });
        }

        // -- StripNewlines --
        // This filter modifies a given string and removes all new line characters within that string.
        if (!isset($this->app['zend.filter.stripnewlines'])) {
            $this->app['zend.filter.stripnewlines'] = function () {
                require_once "Zend/Filter/StripNewlines.php";
                return new \Zend_Filter_StripNewlines();
            };
        }

        // -- StripTags --
        //  This filter can strip XML and HTML tags from given content.
        //  Warning!!! Zend_Filter_StripTags is potentially unsecure
        // The following options are supported for Zend_Filter_StripTags:
        // - allowAttribs: This option sets the attributes which are accepted. All other attributes are stripped from the given content
        // - allowTags: This option sets the tags which are accepted. All other tags will be stripped from the given content
        if (!isset($this->app['zend.filter.striptags'])) {
            $this->app['zend.filter.striptags'] = $this->app->protect(function ($options = null) {
                require_once "Zend/Filter/StripTags.php";
                return new \Zend_Filter_StripTags($options);
            });
        }
    }

}
