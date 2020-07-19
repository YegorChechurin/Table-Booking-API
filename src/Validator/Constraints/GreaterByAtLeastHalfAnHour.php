<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraints\AbstractComparison;

/**
 * @Annotation
 */
class GreaterByAtLeastHalfAnHour extends AbstractComparison
{
    public $message = 'Difference between starting time and finish time is less than 30 minutes';

    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }
}
