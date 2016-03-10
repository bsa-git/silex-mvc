<?php

// app/Resources/Config/Config.php

namespace Config;

use Silex\Application;

/**
 * Class - Config
 * configuration application
 *
 * @category Config
 * @package  app\Resources\Config
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 */
class Config {

    /**
     * Container application
     * 
     * @var Application
     */
    private $app;
    
    /**
     * Application paths
     * 
     * @var array
     */
    private $arrAppPaths = array(
        "cache" => "/data/cache",
        "cache_twig" => "/data/cache/twig",
        "download" => "/data/download",
        "upload" => "/data/upload",
        "logs" => "/data/logs",
        "temp" => "/data/temp",
        "db" => "/app/Resources/db",
        );
    
    //---------------------------
    /**
     * Constructor
     *  
     * @param Application $app
     */
    public function __construct(Application $app) {// , $env = 
        $this->app = $app;

    }


    /**
     *  Get config params
     * 
     * @return array
     */
    public function getParams() {
        return $this->app['config']['parameters'];
    }
    
    /**
     *  Get config param
     * 
     * @param string $key
     * @return array
     */
    public function getParam($key) {
        if(isset($this->app['config']['parameters'][$key])){
            return $this->app['config']['parameters'][$key];
        }
        return NULL;
    }
    
    /**
     *  Get the path to the main project working directory
     * 
     * @param  string $aPath 
     * @return string
     */
    public function getProjectPath($aPath) {
        $patch = "";
        $newDir = "";
        $opts = $this->app['my.opts'];
        $params = $this->app['my.params'];
        //-------------------------
        
        // Get data dir (e.g. с:/mydir/)
        if (isset($this->app['my.opts']['data_dir'])) {
            $data_dir = $this->app['my.opts']['data_dir'];
            $newDir = str_replace("\\", "/", $data_dir);
            $newDir = rtrim($newDir, "/");
        }
        //Set DOCUMENT_ROOT
        $rootDocument = $this->app['basepath'];

        switch ($aPath) {
            case "application":
                $patch = $rootDocument . "/app";
                break;
            case "services":
                $patch = $rootDocument . "/app/Services";
                break;
            case "config":
                $patch = $rootDocument . "/app/Resources/Config";
                break;
            case "controllers":
                $patch = $rootDocument . "/app/Controllers";
                break;
            case "models":
                $patch = $rootDocument . "/app/Models";
                break;
            case "views":
                if($this->app['is_console']){
                    $patch = $rootDocument . "/app/Console/Views";
                }else{
                    $patch = $rootDocument . "/app/Views";
                }
                break;
            case "cache":
                $patch = $rootDocument . "/data/cache";
                break;
            case "download":
                if ($newDir) {
                    $patch = $newDir;
                } else {
                    $patch = $rootDocument . "/data/download";
                }
                break;
            case "download_srv":
                $patch = $rootDocument . "/data/download";
                break;        
            case "upload":
                if ($newDir) {
                    $patch = $newDir;
                } else {
                    $patch = $rootDocument . "/data/upload";
                }
                break;
            case "logs":
                if ($newDir) {
                    $patch = $newDir;
                } else {
                    $patch = $rootDocument . "/data/logs";
                }
                break;
            case "fakedata":
                $patch = $rootDocument . "/app/Resources/fakedata";
                break;
        }
        return $patch;
    }
    
    /**
     *  Create application paths
     * 
     * @param  int $mode 
     */
    public function createAppPaths($mode = 0777) {
        //Set DOCUMENT_ROOT
        $rootDocument = $this->app['basepath'];
        foreach ($this->arrAppPaths as $key => $path) {
            $strPath = $rootDocument . $path;
            if(!is_dir($strPath)){
                $trimPath = trim($path, "/");
                $arrPath = explode('/', $trimPath);
                $strPath = $rootDocument;
                foreach ($arrPath as $itemPath) {
                    $strPath .= "/{$itemPath}";
                    if(!is_dir($strPath) && !mkdir($strPath, $mode)){
                        $this->app->abort(406, "Failed to create a directory '{$strPath}' ...");
                    }
                }
            }
        }
    }

}
