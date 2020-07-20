<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\ReservationParameters;
use App\Dto\ValidReservationRequestParameters;
use App\Entity\TableReservation;
use Doctrine\ORM\EntityManagerInterface;

class TableReserver
{
    private EntityManagerInterface $em;

    private ReservationPriceCalculator $calculator;

    public function __construct(EntityManagerInterface $em, ReservationPriceCalculator $calculator)
    {
        $this->em = $em;
        $this->calculator = $calculator;
    }

    public function reserveTable(ValidReservationRequestParameters $reservationParams): ReservationParameters
    {
        $tableReservation = new TableReservation();
        $tableReservation->setDate(new \DateTime($reservationParams->getDate()))
            ->setStartTime(new \DateTime($reservationParams->getFrom()))
            ->setEndTime(new \DateTime($reservationParams->getTo()))
            ->setTableId($reservationParams->getTableId());

        $this->saveTableReservationToDatabase($tableReservation);

        return new ReservationParameters(
            (string) $reservationParams->getTableId(),
            $tableReservation->getStartTime()->format('Y-m-d H:i'),
            $tableReservation->getEndTime()->format('Y-m-d H:i'),
            $this->calculator->getReservationPrice($tableReservation->getStartTime(), $tableReservation->getEndTime()),
        );
    }

    private function saveTableReservationToDatabase(TableReservation $tableReservation): void
    {
        $this->em->persist($tableReservation);
        $this->em->flush();
    }
}
