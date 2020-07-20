<?php

declare(strict_types=1);

namespace App\Service;

use function array_diff;
use function array_pop;
use function array_rand;
use function count;
use function in_array;

class AvailableTableFinder
{
    private const ALL_TABLE_IDS = [1,2,3,4,5];

    public function findTableIdToReserve(array $conflictingReservations, ?int $requestedTableId): ?int
    {
        if (0 === count($conflictingReservations) && null !== $requestedTableId) {
            return $requestedTableId;
        } elseif (0 === count($conflictingReservations)) {
            return $this->getRandomTableIdOutOfAvailableTableIds(self::ALL_TABLE_IDS);
        } elseif (count($conflictingReservations) > 0 && null === $requestedTableId) {
            $availableTableIds = $this->findAvailableTableIds($conflictingReservations);
            if (count($availableTableIds) > 0) {
                return $this->getRandomTableIdOutOfAvailableTableIds($availableTableIds);
            }
        }

        return null;
    }

    private function findAvailableTableIds(array $conflictingReservations): array
    {
        $conflictingTableIds = [];
        foreach ($conflictingReservations as $conflict) {
            if (in_array($conflict['tableId'], $conflictingTableIds)) {
                continue;
            }
            $conflictingTableIds[] = $conflict['tableId'];
        }

        return array_diff(self::ALL_TABLE_IDS, $conflictingTableIds);
    }

    private function getRandomTableIdOutOfAvailableTableIds(array $availableTableIds): int
    {
        if (1 === count($availableTableIds)) {
            return array_pop($availableTableIds);
        }

        return $availableTableIds[array_rand($availableTableIds)];
    }
}
