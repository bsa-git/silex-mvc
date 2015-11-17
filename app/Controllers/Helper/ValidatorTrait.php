<?php

// app/Controllers/Helper/ValidatorTrait.php

namespace Controllers\Helper;

use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Trait - ValidatorTrait
 * validate operations
 *
 * @category Helper
 * @package  app\Controllers\Helper
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
trait ValidatorTrait
{
    /**
     * Get validator
     *
     * @return Registry
     *
     * @throws \LogicException If DoctrineBundle is not available
     */
    public function getValidator() {
        if (!isset($this->app['validator'])) {
            throw new \LogicException('The \"ValidatorServiceProvider\" is not registered in your application.');
        }

        return $this->app['validator'];
    }

    /**
     * Validate value
     *
     * @param mixed $var 
     * @param ConstraintValidator $constraint 
     * @return true|ConstraintViolationList
     *
     * @throws \LogicException If ValidatorServiceProvider is not available
     */
    public function validateValue($var, $constraint) {
        if (!isset($this->app['validator'])) {
            throw new \LogicException('The \"ValidatorServiceProvider\" is not registered in your application.');
        }

        // We use a validator to check the value
        $errorList = $this->app['validator']->validateValue($var, $constraint);

        if (count($errorList) == 0) {
            return TRUE;
        }

        return $errorList;
    }
    
    /**
     * Validate object
     *
     * @param object $obj 
     * @return true|ConstraintViolationList
     *
     * @throws \LogicException If ValidatorServiceProvider is not available
     */
    public function validate($obj) {
        if (!isset($this->app['validator'])) {
            throw new \LogicException('The \"ValidatorServiceProvider\" is not registered in your application.');
        }

        // We use a validator to check the value
        $errorList = $this->app['validator']->validate($obj);

        if (count($errorList) == 0) {
            return TRUE;
        }

        return $errorList;
    }
}
