<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class CurrentDateOrGreaterValidator extends ConstraintValidator
{
    /**
     * @param string $value Provided date
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof CurrentDateOrGreater) {
            throw new UnexpectedTypeException($constraint, CurrentDateOrGreater::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $providedDate = (new \DateTime($value))->format('Y-m-d');
        $today = (new \DateTime())->format('Y-m-d');
        if (!($providedDate >= $today)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
