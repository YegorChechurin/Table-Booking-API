<?php

declare(strict_types=1);

namespace App\Service;

class ReservationPriceCalculator
{
    private const PRICE_PER_HOUR_IN_ROUBLES = 300;

    private const HOURS_IN_YEAR = 365*24;

    private const HOURS_IN_DAY = 24;

    private const HOURS_IN_MINUTE = 1/60;

    public function getReservationPrice(\DateTimeInterface $startTime, \DateTimeInterface $endTime): string
    {
        $reservationDuration = $endTime->diff($startTime);
        $reservationTotalHours =
            $reservationDuration->h
            + self::HOURS_IN_YEAR * $reservationDuration->y
            + self::HOURS_IN_DAY * $reservationDuration->d
            + self::HOURS_IN_MINUTE * $reservationDuration->i;

        return (string) ($reservationTotalHours * self::PRICE_PER_HOUR_IN_ROUBLES);
    }
}
