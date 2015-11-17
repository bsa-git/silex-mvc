<?php

// app/Providers/YamlConfigServiceProvider.php

namespace Providers;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

/**
 * Class YamlConfigProvider
 *
 * @category ServiceProvider
 * @package  app\Providers
 * @author   Rafael Ernesto Espinosa Santiesteban <ralphlnx@gmail.com>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://fluency.inc.com
 */
class YamlConfigServiceProvider implements ServiceProviderInterface {

    public $_configSettings = array();
    public $_vars;

    /**
     * Class constructor. Set config paths and binding variables.
     *
     * @param array $vars Configuration variables
     */
    public function __construct(array $vars = array()) {
        $this->_vars = $vars;
    }

    /**
     * Merge configurations arrays recursively. Overwrite values on $config1
     * with $config2 if already exists.
     *
     * @param array $config1 Original configuration
     * @param array $config2 Configuration to be merged
     *
     * @return array
     */
    public function mergeConfigurations(array $config1, array $config2) {
        $merged = $config1;
        foreach ($config2 as $key => $value) {
            if (is_array($value) && isset($merged [$key]) && is_array($merged [$key])
            ) {
                $merged [$key] = $this->mergeConfigurations($merged [$key], $value);
            } else {
                $merged [$key] = $value;
            }
        }
        return $merged;
    }

    /**
     * Parses imports statement on YML file.
     *
     * @param array  $imports    Imported YML files to be parsed
     * @param string $configPath Path to master YML file
     *
     * @return void
     */
    public function parseImports(array $imports, $configPath) {
        foreach ($imports as $import) {
            $config = $this->parse(dirname($configPath) . '/' . $import['resource']);
            if ($config !== null) {
                $this->_configSettings = $this->mergeConfigurations(
                        $this->_configSettings, $config
                );
            }
        }
    }

    /**
     * Parses YAML into a PHP array.
     *
     * @param string $input                  Path to file or string containing YAML
     * @param bool   $exceptionOnInvalidType True if an exception must be thrown on
     *                                       invalid types false otherwise
     * @param bool   $objectSupport          True if object support is enabled,
     *                                       false otherwise
     *
     * @return array The YAML converted to a PHP array
     *
     * @throws ParseException If the YAML is not valid
     */
    public function parse($input, $exceptionOnInvalidType = false, $objectSupport = false
    ) {
        // if input is a file, process it
        $file = '';
        if (strpos($input, "\n") === false && is_file($input)) {
            if (false === is_readable($input)) {
                throw new ParseException(
                sprintf(
                        'Unable to parse "%s" as the file is not readable.', $input
                )
                );
            }

            $file = $input;
            $input = file_get_contents($file);
        }

        $input = str_replace(
                array_keys($this->_vars), array_values($this->_vars), $input
        );

        $yaml = new Parser();
        
        try {
            return $yaml->parse($input, $exceptionOnInvalidType, $objectSupport);
        } catch (ParseException $e) {
            if ($file) {
                $e->setParsedFile($file);
            }

            throw $e;
        }
    }

    /**
     * Sets YML parameters
     *
     * @return void
     */
    public function setYamlPatameters() {
        foreach ($this->_configSettings['parameters'] as $key => $value) {
            $this->_vars['%' . $key . '%'] = $value;
        }
    }

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     *
     * @return array
     */
    public function register(Application $app) {
        $self = $this;
        //-------------------------
        $app['config'] = $app->share(
                function () use ($app, $self) {
            if (!isset($app['config.parameters'])) {
                $app['config.parameters'] = 'parameters.yml';
            }
            $self->_configSettings = $self->parse(
                    $app['config.dir'] . '/' .
                    $app['config.parameters']
            );

            $self->setYamlPatameters();

            $self->_configSettings['base_path'] = $self->_vars['%base_path%'];
            $self->_configSettings['log_path'] = $self->_vars['%log_path%'];
            $self->_configSettings['cache_path'] = $self->_vars['%cache_path%'];

            foreach ($app['config.files'] as $fileName) {
                $configPath = $app['config.dir'] . '/' . $fileName;
                $config = $self->parse($configPath);

                if (isset($config['imports']) && is_array($config['imports'])) {
                    $self->parseImports($config['imports'], $configPath);
                }

                if ($config !== null) {
                    $self->_configSettings = $self->mergeConfigurations(
                            $self->_configSettings, $config
                    );
                }
            }
            return $self->_configSettings;
        }
        );
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     *
     * @param Application $app Application instance
     *
     * @return void
     */
    public function boot(Application $app) {
        
    }

}
