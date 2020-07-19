<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraints\AbstractComparison;

/**
 * @Annotation
 */
class FromDateIsSameAsDate extends AbstractComparison
{
    public $message = 'Date must be same as the one in parameter date';

    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }
}
