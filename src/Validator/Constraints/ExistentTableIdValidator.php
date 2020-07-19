<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ExistentTableIdValidator extends ConstraintValidator
{
    /**
     * @param string $value table_id reservation request parameter
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ExistentTableId) {
            throw new UnexpectedTypeException($constraint, ExistentTableId::class);
        }

        if (null === $value) {
            return;
        }

        if (!is_int($value)) {
            throw new UnexpectedValueException($value, 'integer');
        }

        if (!(1 <= $value) || !(5 >= $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
