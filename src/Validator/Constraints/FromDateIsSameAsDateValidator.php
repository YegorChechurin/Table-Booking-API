<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraints\GreaterThanValidator;

class FromDateIsSameAsDateValidator extends GreaterThanValidator
{
    /**
     * @param string $value1 From parameter
     * @param string $value2 Date parameter
     */
    protected function compareValues($value1, $value2)
    {
        $fromDate = (new \DateTime($value1))->format('Y-m-d');
        $date = (new \DateTime($value2))->format('Y-m-d');

        return $fromDate === $date;
    }
}
