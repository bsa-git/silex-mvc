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
 * @author   Sergii Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 */
class Zf2Service {

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
        // Ini filter
        $this->getFilter();
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
        if (!isset($this->app['zf2.json'])) {
            $this->app['zf2.json'] = function () {
                if(!class_exists('\Zend\Json\Json') ){
                    $this->app->abort(404, "'\Zend\Json\Json' class does not exist");
                }
                return '\Zend\Json\Json';
            };
        }
        return $this->app['zf2.json'];
    }

    /**
     * Get filter
     * 
     * @return Zend\Filter\FilterChain
     */
    public function getFilter() {
        if (!isset($this->app['zf2.filter'])) {
            // Ini filters
            $this->_iniStandardFilters();
            $this->_iniWordFilters();
            $this->_iniFileFilters();
            $this->app['zf2.filter'] = function () {
                if(!class_exists('\Zend\Filter\FilterChain') ){
                    $this->app->abort(404, "'\Zend\Filter\FilterChain' class does not exist");
                }  else {
                    return new \Zend\Filter\FilterChain();
                }
            };
        }
        return $this->app['zf2.filter'];
    }

    /**
     * Ini standard filters
     * 
     * @return void
     */
    private function _iniStandardFilters() {

        // -- BaseName --
        // allows you to filter a string which contains the path to a file and it will return the base name of this file
        if (!isset($this->app['zf2.filter.base_name'])) {
            $this->app['zf2.filter.base_name'] = function () {
                return new \Zend\Filter\BaseName();
            };
        }
        
        // -- Blacklist --
        // This filter will return null if the value being filtered is present in the filter’s list of values. If the value is not present, it will return that value.
        // The following options are supported for Zend\Filter\Blacklist:
        // - strict: Uses strict mode when comparing: passed through to in_array‘s third argument..
        // - list: An array of forbidden values.
        if (!isset($this->app['zf2.filter.black_list'])) {
            $this->app['zf2.filter.black_list'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\Blacklist($options);
            });
        }
        
        // -- Boolean --
        // This filter changes a given input to be a BOOLEAN value
        // The following options are supported for Zend\Filter\Boolean:
        // - casting: When this option is set to TRUE then any given input will be casted to boolean. This option defaults to TRUE.
        // - translations: This option sets the locale which will be used to detect localized input.
        // - type: The type option sets the boolean type which should be used. Read the following for details.
        if (!isset($this->app['zf2.filter.boolean'])) {
            $this->app['zf2.filter.boolean'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\Boolean($options);
            });
        }

        // -- Callback --
        // This filter allows you to use own methods in conjunction with Zend_Filter. You don't have to create a new filter when you already have a method which does the job.
        // The following options are supported for Zend\Filter\Callback:
        // - callback: This sets the callback which should be used.
        // - callback_params: This property sets the options which are used when the callback is processed.
        if (!isset($this->app['zf2.filter.callback'])) {
            $this->app['zf2.filter.callback'] = $this->app->protect(function ($options) {
                return new \Zend\Filter\Callback($options);
            });
        }

        // -- Compress --
        // This filter are capable of compressing strings, files, and directories.
        // The following options are supported for Zend\Filter\Compress:
        // - adapter: The compression adapter which should be used. It defaults to Gz.
        // - options: Additional options which are given to the adapter at initiation. Each adapter supports it's own options.
        if (!isset($this->app['zf2.filter.compress'])) {
            $this->app['zf2.filter.compress'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\Compress($options);
            });
        }

        // -- Decompress --
        // This filter are capable of decompressing  strings, files, and directories.
        // The following options are supported for Zend\Filter\Decompress:
        // - adapter: The compression adapter which should be used. It defaults to Gz.
        // - options: Additional options which are given to the adapter at initiation. Each adapter supports it's own options.
        if (!isset($this->app['zf2.filter.decompress'])) {
            $this->app['zf2.filter.decompress'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\Decompress($options);
            });
        }

        // -- Digits --
        // Returns the string $value, removing all but digits.
        if (!isset($this->app['zf2.filter.digits'])) {
            $this->app['zf2.filter.digits'] = function () {
                return new \Zend\Filter\Digitss();
            };
        }

        // -- Dir --
        // Given a string containing a path to a file, this function will return the name of the directory
        if (!isset($this->app['zf2.filter.dir'])) {
            $this->app['zf2.filter.dir'] = function () {
                return new \Zend\Filter\Dir();
            };
        }

        // -- Encrypt --
        // This filter allow to encrypt any given string. Therefor they make use of Adapters. 
        // Actually there are adapters for the Mcrypt and OpenSSL extensions from PHP.
        // The following options are supported for Zend\Filter\Encrypt:
        // - adapter: This sets the encryption adapter which should be used
        // - algorithm: algorithm: Only BlockCipher. The algorithm which has to be used by the adapter Zend\Crypt\Symmetric\Mcrypt. It should be one of the algorithm ciphers supported by Zend\Crypt\Symmetric\Mcrypt (see the getSupportedAlgorithms() method). If not set it defaults to aes, the Advanced Encryption Standard (see Zend\Crypt\BlockCipher for more details).
        // - compression: If the encrypted value should be compressed. Default is no compression.
        // - envelope: Only OpenSSL. The encrypted envelope key from the user who encrypted the content. You can either provide the path and filename of the key file, or just the content of the key file itself. When the package option has been set, then you can omit this parameter.
        // - key:  Only BlockCipher. The encryption key with which the input will be encrypted. You need the same key for decryption.
        // - mode: Only BlockCipher. The encryption mode which has to be used. It should be one of the modes which can be found under PHP’s mcrypt modes. If not set it defaults to ‘cbc’.
        // - mode_directory: Only BlockCipher. The directory where the mode can be found. If not set it defaults to the path set within the Mcrypt extension.
        // - package: Only OpenSSL. If the envelope key should be packed with the encrypted value. Default is FALSE.
        // - private: Only OpenSSL. Your private key which will be used for encrypting the content. You can either provide the path and filename of the key file, or just the content of the key file itself.
        // - public: Only OpenSSL. The public key of the user whom you want to provide the encrypted content. You can either provide the path and filename of the key file, or just the content of the key file itself.
        // - vector: Only BlockCipher. The initialization vector which shall be used. If not set it will be a random vector.
        if (!isset($this->app['zf2.filter.encrypt'])) {
            $this->app['zf2.filter.encrypt'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\Encrypt($options);
            });
        }

        // -- Decrypt --
        // This filter allow to decrypt any given string. Therefor they make use of Adapters. 
        // Actually there are adapters for the Mcrypt and OpenSSL extensions from PHP.
        // The following options are supported for Zend\Filter\Decrypt:
        // - adapter: This sets the encryption adapter which should be used
        // - algorithm: algorithm: Only BlockCipher. The algorithm which has to be used by the adapter Zend\Crypt\Symmetric\Mcrypt. It should be one of the algorithm ciphers supported by Zend\Crypt\Symmetric\Mcrypt (see the getSupportedAlgorithms() method). If not set it defaults to aes, the Advanced Encryption Standard (see Zend\Crypt\BlockCipher for more details).
        // - compression: If the encrypted value should be compressed. Default is no compression.
        // - envelope: Only OpenSSL. The encrypted envelope key from the user who encrypted the content. You can either provide the path and filename of the key file, or just the content of the key file itself. When the package option has been set, then you can omit this parameter.
        // - key:  Only BlockCipher. The encryption key with which the input will be encrypted. You need the same key for decryption.
        // - mode: Only BlockCipher. The encryption mode which has to be used. It should be one of the modes which can be found under PHP’s mcrypt modes. If not set it defaults to ‘cbc’.
        // - mode_directory: Only BlockCipher. The directory where the mode can be found. If not set it defaults to the path set within the Mcrypt extension.
        // - package: Only OpenSSL. If the envelope key should be packed with the encrypted value. Default is FALSE.
        // - private: Only OpenSSL. Your private key which will be used for encrypting the content. You can either provide the path and filename of the key file, or just the content of the key file itself.
        // - public: Only OpenSSL. The public key of the user whom you want to provide the encrypted content. You can either provide the path and filename of the key file, or just the content of the key file itself.
        // - vector: Only BlockCipher. The initialization vector which shall be used. If not set it will be a random vector.
        if (!isset($this->app['zf2.filter.decrypt'])) {
            $this->app['zf2.filter.decrypt'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\Decrypt($options);
            });
        }

        // -- HtmlEntities --
        // Returns the string $value, converting characters to their corresponding HTML entity equivalents where they exist.
        // The following options are supported for Zend\Filter\HtmlEntities:
        // - quotestyle: Equivalent to the PHP htmlentities native function parameter quote_style. This allows you to define what will be done with 'single' and "double" quotes. The following constants are accepted: ENT_COMPAT, ENT_QUOTES ENT_NOQUOTES with the default being ENT_COMPAT.
        // - charset: Equivalent to the PHP htmlentities native function parameter charset. This defines the character set to be used in filtering. Unlike the PHP native function the default is 'UTF-8'. See "http://php.net/htmlentities" for a list of supported character sets.
        //   Note: This option can also be set via the $options parameter as a Zend_Config object or array. The option key will be accepted as either charset or encoding. 
        // - doublequote: Equivalent to the PHP htmlentities native function parameter double_encode. If set to false existing html entities will not be encoded. The default is to convert everything (true).
        //   Note: This option must be set via the $options parameter or the setDoubleEncode() method. 
        if (!isset($this->app['zf2.filter.html_entities'])) {
            $this->app['zf2.filter.html_entities'] = $this->app->protect(function ($options = array()) {
                return new \Zend\Filter\HtmlEntities($options);
            });
        }
        
        // -- ToInt --
        // allows you to transform a sclar value which contains into an integer
        if (!isset($this->app['zf2.filter.to_int'])) {
            $this->app['zf2.filter.to_int'] = function () {
                return new \Zend\Filter\ToInt();
            };
        }
        
        // -- ToNull --
        // This filter will change the given input to be NULL if it meets specific criteria. 
        // This is often necessary when you work with databases and want to have a NULL value instead of a boolean or any other type.
        // The following options are supported for Zend\Filter\ToNull:
        // - type: The variable type which should be supported.
        if (!isset($this->app['zf2.filter.null'])) {
            $this->app['zf2.filter.null'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\ToNull($options);
            });
        }

        // -- PregReplace --
        //  performs a search using regular expressions and replaces all found elements 
        // The following options are supported for Zend\Filter\PregReplace:
        // - match: The pattern which will be searched for.
        // - replace: The string which is used as replacement for the matches.
        if (!isset($this->app['zf2.filter.preg_replace'])) {
            $this->app['zf2.filter.preg_replace'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\PregReplace($options);
            });
        }

        // -- RealPath --
        //  This filter will resolve given links and pathnames and returns canonicalized absolute pathnames
        // The following options are supported for Zend\Filter\RealPath:
        // - exists: This option defaults to TRUE which checks if the given path really exists.
        if (!isset($this->app['zf2.filter.real_path'])) {
            $this->app['zf2.filter.real_path'] = $this->app->protect(function ($options = true) {
                return new \Zend\Filter\RealPath($options);
            });
        }

        // -- StringToLower --
        //  This filter converts any input to be lowercased.
        // The following options are supported for Zend\Filter\StringToLower:
        // - encoding: This option can be used to set an encoding which has to be used
        if (!isset($this->app['zf2.filter.string_to_lower'])) {
            $this->app['zf2.filter.string_to_lower'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\StringToLower($options);
            });
        }

        // -- StringToUpper --
        //  This filter converts any input to be uppercased.
        // The following options are supported for Zend\Filter\StringToUpper:
        // - encoding: This option can be used to set an encoding which has to be used
        if (!isset($this->app['zf2.filter.string_to_upper'])) {
            $this->app['zf2.filter.string_to_upper'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\StringToUpper($options);
            });
        }

        // -- StringTrim --
        //  This filter modifies a given string such that certain characters are removed from the beginning and end.
        // The following options are supported for Zend_Filter_StringTrim:
        // - charlist: List of characters to remove from the beginning and end of the string. 
        // If this is not set or is null, the default behavior will be invoked, which is to remove only whitespace from the beginning and end of the string.
        if (!isset($this->app['zf2.filter.string_trim'])) {
            $this->app['zf2.filter.string_trim'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\StringTrim($options);
            });
        }

        // -- StripNewlines --
        // This filter modifies a given string and removes all new line characters within that string.
        if (!isset($this->app['zf2.filter.strip_new_lines'])) {
            $this->app['zf2.filter.strip_new_lines'] = function () {
                return new \Zend\Filter\StripNewlines();
            };
        }

        // -- StripTags --
        //  This filter can strip XML and HTML tags from given content.
        //  Warning!!! Zend_Filter_StripTags is potentially unsecure
        // The following options are supported for Zend\Filter\StripTags:
        // - allowAttribs: This option sets the attributes which are accepted. All other attributes are stripped from the given content
        // - allowTags: This option sets the tags which are accepted. All other tags will be stripped from the given content
        if (!isset($this->app['zf2.filter.strip_tags'])) {
            $this->app['zf2.filter.strip_tags'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\StripTags($options);
            });
        }
        
        // -- UriNormalize --
        //  This filter can set a scheme on an URI, if a scheme is not present. If a scheme is present, that scheme will not be affected, even if a different scheme is enforced.
        // The following options are supported for Zend\Filter\UriNormalize:
        // - defaultScheme: This option can be used to set the default scheme to use when parsing scheme-less URIs.
        // - enforcedScheme: Set a URI scheme to enforce on schemeless URIs.
        if (!isset($this->app['zf2.filter.uri_normalize'])) {
            $this->app['zf2.filter.uri_normalize'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\UriNormalize($options);
            });
        }
        
        // -- Whitelist --
        //  This filter will return null if the value being filtered is not present the filter’s allowed list of values. If the value is present, it will return that value.
        // The following options are supported for Zend\Filter\Whitelist:
        // - strict: Uses strict mode when comparing: passed through to in_array‘s third argument.
        // - list: An array of allowed values.
        if (!isset($this->app['zf2.filter.white_list'])) {
            $this->app['zf2.filter.white_list'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\Whitelist($options);
            });
        }
    }
    
    /**
     * Ini word filters
     * 
     * @return void
     */
    private function _iniWordFilters() {
        
        // -- CamelCaseToDash --
        // This filter modifies a given string such that ‘CamelCaseWords’ are converted to ‘Camel-Case-Words’.
        if (!isset($this->app['zf2.filter.camelcase_to_dash'])) {
            $this->app['zf2.filter.camelcase_to_dash'] = function () {
                return new \Zend\Filter\Word\CamelCaseToDash();
            };
        }
        
        // -- CamelCaseToSeparator --
        // This filter modifies a given string such that ‘CamelCaseWords’ are converted to ‘Camel Case Words’.
        // The following options are supported for Zend\Filter\Word\CamelCaseToSeparator:
        // - separator: A separator char. If this is not set the separator will be a space character.
        if (!isset($this->app['zf2.filter.camelcase_to_separator'])) {
            $this->app['zf2.filter.camelcase_to_separator'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\Word\CamelCaseToSeparator($options);
            });
        }
        
        // -- CamelCaseToUnderscore --
        // This filter modifies a given string such that ‘CamelCaseWords’ are converted to ‘Camel_Case_Words’.
        if (!isset($this->app['zf2.filter.camelcase_to_underscore'])) {
            $this->app['zf2.filter.camelcase_to_underscore'] = function () {
                return new \Zend\Filter\Word\CamelCaseToUnderscore();
            };
        }
        
        // -- DashToCamelCase --
        // This filter modifies a given string such that ‘words-with-dashes’ are converted to ‘WordsWithDashes’.
        if (!isset($this->app['zf2.filter.dash_to_camelcase'])) {
            $this->app['zf2.filter.dash_to_camelcase'] = function () {
                return new \Zend\Filter\Word\DashToCamelCase();
            };
        }
        
        // -- DashToSeparator --
        // This filter modifies a given string such that ‘words-with-dashes’ are converted to ‘words with dashes’.
        // The following options are supported for Zend\Filter\Word\DashToSeparator:
        // - separator: A separator char. If this is not set the separator will be a space character.
        if (!isset($this->app['zf2.filter.dash_to_separator'])) {
            $this->app['zf2.filter.dash_to_separator'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\Word\DashToSeparator($options);
            });
        }
        
        // -- SeparatorToCamelCase --
        // This filter modifies a given string such that ‘words with separators’ are converted to ‘WordsWithSeparators’.
        // The following options are supported for Zend\Filter\Word\SeparatorToCamelCase:
        // - separator: A separator char. If this is not set the separator will be a space character.
        if (!isset($this->app['zf2.filter.separator_to_camelcase'])) {
            $this->app['zf2.filter.separator_to_camelcase'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\Word\SeparatorToCamelCase($options);
            });
        }
        
        // -- SeparatorToDash --
        // This filter modifies a given string such that ‘words with separators’ are converted to ‘words-with-separators’.
        // The following options are supported for Zend\Filter\Word\SeparatorToDash:
        // - separator: A separator char. If this is not set the separator will be a space character.
        if (!isset($this->app['zf2.filter.separator_to_dash'])) {
            $this->app['zf2.filter.separator_to_dash'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\Word\SeparatorToDash($options);
            });
        }
        
        // -- SeparatorToSeparator --
        // This filter modifies a given string such that ‘words with separators’ are converted to ‘words-with-separators’.
        // The following options are supported for Zend\Filter\Word\SeparatorToSeparator:
        // - searchSeparator: The search separator char. If this is not set the separator will be a space character.
        // - replaceSeparator: The replace separator char. If this is not set the separator will be a dash.
        if (!isset($this->app['zf2.filter.separator_to_separator'])) {
            $this->app['zf2.filter.separator_to_separator'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\Word\SeparatorToSeparator($options);
            });
        }
        
        // -- UnderscoreToCamelCase --
        // This filter modifies a given string such that ‘words_with_underscores’ are converted to ‘WordsWithUnderscores’.
        if (!isset($this->app['zf2.filter.underscore_to_camelcase'])) {
            $this->app['zf2.filter.underscore_to_camelcase'] = function () {
                return new \Zend\Filter\Word\UnderscoreToCamelCase();
            };
        }
        
        // -- UnderscoreToSeparator --
        // This filter modifies a given string such that ‘words_with_underscores’ are converted to ‘words with separator’.
        // The following options are supported for Zend\Filter\Word\UnderscoreToSeparator:
        // - separator: A separator char. If this is not set the separator will be a space character.
        if (!isset($this->app['zf2.filter.underscore_to_separator'])) {
            $this->app['zf2.filter.underscore_to_separator'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\Word\UnderscoreToSeparator($options);
            });
        }
        
        // -- UnderscoreToDash --
        // This filter modifies a given string such that ‘words_with_underscores’ are converted to ‘words-with-underscores’.
        if (!isset($this->app['zf2.filter.underscore_to_dash'])) {
            $this->app['zf2.filter.underscore_to_dash'] = function () {
                return new \Zend\Filter\Word\UnderscoreToDash();
            };
        }
    }
    
    /**
     * Ini file filters
     * 
     * @return void
     */
    private function _iniFileFilters() {
        
        // -- File Encrypt --
        // This filter allow to encrypt any given file. Therefor they make use of Adapters. 
        // Actually there are adapters for the Mcrypt and OpenSSL extensions from PHP.
        // The following options are supported for Zend\Filter\File\Encrypt:
        // - adapter: This sets the encryption adapter which should be used
        // - algorithm: algorithm: Only BlockCipher. The algorithm which has to be used by the adapter Zend\Crypt\Symmetric\Mcrypt. It should be one of the algorithm ciphers supported by Zend\Crypt\Symmetric\Mcrypt (see the getSupportedAlgorithms() method). If not set it defaults to aes, the Advanced Encryption Standard (see Zend\Crypt\BlockCipher for more details).
        // - compression: If the encrypted value should be compressed. Default is no compression.
        // - envelope: Only OpenSSL. The encrypted envelope key from the user who encrypted the content. You can either provide the path and filename of the key file, or just the content of the key file itself. When the package option has been set, then you can omit this parameter.
        // - key:  Only BlockCipher. The encryption key with which the input will be encrypted. You need the same key for decryption.
        // - mode: Only BlockCipher. The encryption mode which has to be used. It should be one of the modes which can be found under PHP’s mcrypt modes. If not set it defaults to ‘cbc’.
        // - mode_directory: Only BlockCipher. The directory where the mode can be found. If not set it defaults to the path set within the Mcrypt extension.
        // - package: Only OpenSSL. If the envelope key should be packed with the encrypted value. Default is FALSE.
        // - private: Only OpenSSL. Your private key which will be used for encrypting the content. You can either provide the path and filename of the key file, or just the content of the key file itself.
        // - public: Only OpenSSL. The public key of the user whom you want to provide the encrypted content. You can either provide the path and filename of the key file, or just the content of the key file itself.
        // - vector: Only BlockCipher. The initialization vector which shall be used. If not set it will be a random vector.
        if (!isset($this->app['zf2.filter.file_encrypt'])) {
            $this->app['zf2.filter.file_encrypt'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\File\Encrypt($options);
            });
        }

        // -- File Decrypt --
        // This filter allow to decrypt any given file. Therefor they make use of Adapters. 
        // Actually there are adapters for the Mcrypt and OpenSSL extensions from PHP.
        // The following options are supported for Zend\Filter\File\Decrypt:
        // - adapter: This sets the encryption adapter which should be used
        // - algorithm: algorithm: Only BlockCipher. The algorithm which has to be used by the adapter Zend\Crypt\Symmetric\Mcrypt. It should be one of the algorithm ciphers supported by Zend\Crypt\Symmetric\Mcrypt (see the getSupportedAlgorithms() method). If not set it defaults to aes, the Advanced Encryption Standard (see Zend\Crypt\BlockCipher for more details).
        // - compression: If the encrypted value should be compressed. Default is no compression.
        // - envelope: Only OpenSSL. The encrypted envelope key from the user who encrypted the content. You can either provide the path and filename of the key file, or just the content of the key file itself. When the package option has been set, then you can omit this parameter.
        // - key:  Only BlockCipher. The encryption key with which the input will be encrypted. You need the same key for decryption.
        // - mode: Only BlockCipher. The encryption mode which has to be used. It should be one of the modes which can be found under PHP’s mcrypt modes. If not set it defaults to ‘cbc’.
        // - mode_directory: Only BlockCipher. The directory where the mode can be found. If not set it defaults to the path set within the Mcrypt extension.
        // - package: Only OpenSSL. If the envelope key should be packed with the encrypted value. Default is FALSE.
        // - private: Only OpenSSL. Your private key which will be used for encrypting the content. You can either provide the path and filename of the key file, or just the content of the key file itself.
        // - public: Only OpenSSL. The public key of the user whom you want to provide the encrypted content. You can either provide the path and filename of the key file, or just the content of the key file itself.
        // - vector: Only BlockCipher. The initialization vector which shall be used. If not set it will be a random vector.
        if (!isset($this->app['zf2.filter.file_decrypt'])) {
            $this->app['zf2.filter.file_decrypt'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\File\Decrypt($options);
            });
        }
        
        // -- File LowerCase --
        //  This filter does a lowercase on the content of the given file.
        // The following options are supported for Zend\Filter\File\LowerCase:
        // - encoding: This option can be used to set an encoding which has to be used
        if (!isset($this->app['zf2.filter.file_lowercase'])) {
            $this->app['zf2.filter.file_lowercase'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\File\LowerCase($options);
            });
        }
        
        // -- File UpperCase --
        //  This filter does a lowercase on the content of the given file.
        // The following options are supported for Zend\Filter\File\LowerCase:
        // - encoding: This option can be used to set an encoding which has to be used
        if (!isset($this->app['zf2.filter.file_uppercase'])) {
            $this->app['zf2.filter.file_uppercase'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\File\UpperCase($options);
            });
        }
        
        // -- File Rename --
        //  This filter can be used to rename a file and/or move a file to a new path.
        // The following options are supported for Zend\Filter\File\Rename:
        // - target (string) default: "*" Target filename or directory, the new name of the source file.
        // - source (string) default: "*" Source filename or directory which will be renamed. Used to match the filtered file with an options set.
        // - overwrite (boolean) default: false Shall existing files be overwritten? If the file is unable to be moved into the target path, a Zend\Filter\Exception\RuntimeException will be thrown.
        // - randomize (boolean) default: false Shall target files have a random postfix attached? The random postfix will be a uniqid('_') after the file name and before the extension. For example, "file.txt" will be randomized to "file_4b3403665fea6.txt"
        if (!isset($this->app['zf2.filter.file_rename'])) {
            $this->app['zf2.filter.file_rename'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\File\Rename($options);
            });
        }
        
        // -- File RenameUpload  --
        //  This filter can be used to rename or move an uploaded file to a new path.
        // The following options are supported for Zend\Filter\File\RenameUpload :
        // - target (string) default: "*" Target directory or full filename path.
        // - overwrite (boolean) default: false Shall existing files be overwritten? If the file is unable to be moved into the target path, a Zend\Filter\Exception\RuntimeException will be thrown.
        // - randomize (boolean) default: false Shall target files have a random postfix attached? The random postfix will be a uniqid('_') after the file name and before the extension. For example, "file.txt" will be randomized to "file_4b3403665fea6.txt"
        // - use_upload_name (boolean) default: false When true, this filter will use the $_FILES[‘name’] as the target filename. Otherwise, the default target rules and the $_FILES['tmp_name'] will be used.
        // - use_upload_extension (boolean) default: false When true, the uploaded file will maintains its original extension if not specified. For example, if the uploaded file is "file.txt" and the target is something like "mynewfile", the upload will be renamed to "mynewfile.txt".
        if (!isset($this->app['zf2.filter.file_rename_upload '])) {
            $this->app['zf2.filter.file_rename_upload'] = $this->app->protect(function ($options = null) {
                return new \Zend\Filter\File\RenameUpload ($options);
            });
        }
    }

}
