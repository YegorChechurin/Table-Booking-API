<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CurrentDateOrGreater extends Constraint
{
    public $message = 'Date cannot be less than current date';

    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }
}
