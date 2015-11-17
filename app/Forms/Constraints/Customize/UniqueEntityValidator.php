<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forms\Constraints\Customize;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class UniqueEntityValidator extends ConstraintValidator {

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint) {
        $db = $constraint->app['db'];
        $field = strtolower($constraint->field);
        //--------------------

        if (!$constraint instanceof UniqueEntity) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\UniqueEntity');
        }

        $arr = explode("\\", $constraint->entity);
        $table = strtolower($arr[count($arr) - 1]);
        
        $sql = "SELECT * FROM {$table} WHERE {$field} = ?";
        $rows = $db->fetchAssoc($sql, array($value));
        if ($rows !== FALSE) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ value }}', $this->formatValue($value))
                        ->addViolation();
            } else {
                $this->buildViolation($constraint->message)
                        ->setParameter('{{ value }}', $this->formatValue($value))
                        ->addViolation();
            }
        }
    }

}
