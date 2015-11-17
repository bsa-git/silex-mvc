<?php

// app/Controllers/Helper/FormTrait.php

namespace Controllers\Helper;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormTypeInterface;

/**
 * Trait - FormTrait
 * form operations
 *
 * @category Helper
 * @package  app\Controllers\Helper
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
trait FormTrait
{
    /**
     * Creates and returns a Form instance from the type of the form.
     *
     * @param string|FormTypeInterface $type    The built type of the form
     * @param mixed                    $data    The initial data for the form
     * @param array                    $options Options for the form
     *
     * @return Form
     */
    public function createForm($type, $data = null, array $options = array()) {
        if (!isset($this->app['form.factory'])) {
            throw new \LogicException('The \"FormServiceProvider\" is not registered in your application.');
        }
        return $this->app['form.factory']->create($type, $data, $options);
    }

    /**
     * Creates and returns a form builder instance.
     *
     * @param mixed $data    The initial data for the form
     * @param array $options Options for the form
     *
     * @return FormBuilder
     */
    public function form($data = null, array $options = array()) {
        if (!isset($this->app['form.factory'])) {
            throw new \LogicException('The \"FormServiceProvider\" is not registered in your application.');
        }
        return $this->app['form.factory']->createBuilder('form', $data, $options);
    }
}
