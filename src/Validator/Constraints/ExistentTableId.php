<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ExistentTableId extends Constraint
{
    public $message = 'Invalid table number, valid table numbers are 1-5';

    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }
}
