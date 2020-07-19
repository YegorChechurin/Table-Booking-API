<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraints\GreaterThanValidator;

class GreaterByAtLeastHalfAnHourValidator extends GreaterThanValidator
{
    /**
     * @param string $value1 Finish time
     * @param string $value2 Starting time
     */
    protected function compareValues($value1, $value2)
    {
        $startingTime = new \DateTimeImmutable($value2);
        $finnishTime = new \DateTimeImmutable($value1);
        $startingTimeIncrementedByHalfAnHour = $startingTime->add(new \DateInterval('PT30M'));

        return $finnishTime >= $startingTimeIncrementedByHalfAnHour;
    }
}
