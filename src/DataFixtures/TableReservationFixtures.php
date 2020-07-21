<?php

namespace App\DataFixtures;

use App\Entity\TableReservation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TableReservationFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $reservationTimes = [
            [
                'date' => '2030-12-12',
                'from' => '2030-12-12 10:00',
                'to' => '2030-12-12 11:00',
            ],
            [
                'date' => '2030-12-12',
                'from' => '2030-12-12 12:00',
                'to' => '2030-12-12 13:00',
            ],
        ];

        foreach ($reservationTimes as $rt) {
            for ($i = 1; $i <= 5; $i++) {
                $tableReservation = new TableReservation();
                $tableReservation->setDate(new \DateTime($rt['date']))
                    ->setStartTime(new \DateTime($rt['from']))
                    ->setEndTime(new \DateTime($rt['to']))
                    ->setTableId($i);
                $manager->persist($tableReservation);
            }
        }

        $manager->flush();
    }
}
