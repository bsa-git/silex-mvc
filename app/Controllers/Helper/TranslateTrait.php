<?php

// app/Controllers/Helper/TranslateTrait.php

namespace Controllers\Helper;

use Symfony\Component\Intl\Intl;
use Symfony\Component\Yaml\Yaml;

/**
 * Trait - TranslateTrait
 * translate operations
 *
 * @category Helper
 * @package  app\Controllers\Helper
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
trait TranslateTrait {

    /**
     * Translates the given message.
     *
     * @param string $id         The message id
     * @param array  $parameters An array of parameters for the message
     * @param string $domain     The domain for the message
     * @param string $locale     The locale
     *
     * @return string The translated string
     */
    public function trans($id, array $parameters = array(), $domain = 'messages', $locale = null) {
        if (!isset($this->app['translator'])) {
            throw new \LogicException('The \"TranslationServiceProvider\" is not registered in your application.');
        }

        return $this->app['translator']->trans($id, $parameters, $domain, $locale);
    }

    /**
     * Translates the given choice message by choosing a translation according to a number.
     *
     * @param string $id         The message id
     * @param int    $number     The number to use to find the indice of the message
     * @param array  $parameters An array of parameters for the message
     * @param string $domain     The domain for the message
     * @param string $locale     The locale
     *
     * @return string The translated string
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = 'messages', $locale = null) {
        if (!isset($this->app['translator'])) {
            throw new \LogicException('The \"TranslationServiceProvider\" is not registered in your application.');
        }

        return $this->app['translator']->transChoice($id, $number, $parameters, $domain, $locale);
    }

    /**
     * Get locale
     *
     * @return string The locale string
     */
    public function getLocale() {
        if ($this->app['session']->has('_locale')) {
            return $this->app['session']->get('_locale');
        } else {
            return $this->app['locale'];
        }
    }

    /**
     * Get lang hash
     * get hash for translation messages
     *
     * @return string The hash string
     */
    public function getLangHash() {
        // get translations
        $filePath = BASEPATH . "/app/Resources/translations/messages.{$this->getLocale()}.yml";
        if (!is_file($filePath)) {
            $this->app->abort(404, "File \"{$filePath}\" not found.");
        }
        $fileContent = file_get_contents($filePath);
        $hash = md5($fileContent);
        return $hash;
    }
    
    /**
     * Get lang messages
     * get lang messages array
     *
     * @return array The lang messages array
     */
    public function getLangMsgs() {
        // get translations
        $filePath = BASEPATH . "/app/Resources/translations/messages.{$this->getLocale()}.yml";
        if (!is_file($filePath)) {
            $this->app->abort(404, "File \"{$filePath}\" not found.");
        }
        $fileContent = file_get_contents($filePath);
        $arTrans = Yaml::parse($fileContent);
        return $arTrans;
    }

}
