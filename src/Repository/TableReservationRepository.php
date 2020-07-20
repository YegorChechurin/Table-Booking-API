<?php

namespace App\Repository;

use App\Entity\TableReservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TableReservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TableReservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TableReservation[]    findAll()
 * @method TableReservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TableReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TableReservation::class);
    }

    public function findConflictingReservations(
        string $date,
        string $startTime,
        string $endTime,
        ?int $tableId
    ): array {
        $qb = $this->createQueryBuilder('tr')
            ->select(['tr.tableId', 'tr.startTime', 'tr.endTime'])
            ->where('tr.date = :date')
            ->andWhere('tr.startTime < :requestEndTime')
            ->andWhere('tr.endTime > :requestStartTime')
            ->orderBy('tr.startTime, tr.tableId')
            ->setParameters([
                'date' => $date,
                'requestStartTime' => $startTime,
                'requestEndTime' => $endTime,
            ]);

        if (null !== $tableId) {
            $qb->andWhere('tr.tableId = :tableId')
                ->setParameter('tableId', $tableId);
        }

        return $qb->getQuery()
            ->getArrayResult();
    }
}
