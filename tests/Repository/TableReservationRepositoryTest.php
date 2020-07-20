<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Dto\ReceivedReservationRequestParameters;
use App\Entity\TableReservation;
use App\Repository\TableReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use function count;

class TableReservationRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $em;

    private TableReservationRepository $repo;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repo = $this->em->getRepository(TableReservation::class);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null;
    }

    /**
     * @dataProvider reservationsProvider
     */
    public function testFindConflictingReservations(
        array $existingReservations,
        array $requestedReservation,
        array $expectedConflictingReservations
    ): void {
        $this->saveExistingReservationsToDatabase($existingReservations);

        $conflictingReservations = $this->formatRepositoryResponse(
            $this->repo->findConflictingReservations(
                $requestedReservation['date'],
                $requestedReservation['startTime'],
                $requestedReservation['endTime'],
                $requestedReservation['tableId']
            )
        );

        $this->assertSame(
            $expectedConflictingReservations,
            $conflictingReservations
        );
    }

    public function reservationsProvider(): array
    {
        return [
            0 => [
                'existing reservations' => [],
                'requested reservation' => [
                    'date' => '2020-07-20',
                    'startTime' => '2020-07-20 10:00',
                    'endTime' => '2020-07-20 11:00',
                    'tableId' => 1
                ],
                'expected conflicting reservations' => [],
            ],
            1 => [
                'existing reservations' => [
                    [
                        'date' => '2020-07-20',
                        'startTime' => '2020-07-20 10:00',
                        'endTime' => '2020-07-20 11:00',
                        'tableId' => 1
                    ],
                ],
                'requested reservation' => [
                    'date' => '2020-07-20',
                    'startTime' => '2020-07-20 10:00',
                    'endTime' => '2020-07-20 11:00',
                    'tableId' => 1
                ],
                'expected conflicting reservations' => [
                    [
                        'tableId' => 1,
                        'startTime' => '2020-07-20 10:00',
                        'endTime' => '2020-07-20 11:00',
                    ],
                ],
            ],
            2 => [
                'existing reservations' => [
                    [
                        'date' => '2020-07-20',
                        'startTime' => '2020-07-20 10:00',
                        'endTime' => '2020-07-20 11:00',
                        'tableId' => 1
                    ],
                ],
                'requested reservation' => [
                    'date' => '2020-07-20',
                    'startTime' => '2020-07-20 11:00',
                    'endTime' => '2020-07-20 12:00',
                    'tableId' => 1
                ],
                'expected conflicting reservations' => [],
            ],
            3 => [
                'existing reservations' => [
                    [
                        'date' => '2020-07-20',
                        'startTime' => '2020-07-20 10:00',
                        'endTime' => '2020-07-20 11:00',
                        'tableId' => 1
                    ],
                    [
                        'date' => '2020-07-20',
                        'startTime' => '2020-07-20 11:30',
                        'endTime' => '2020-07-20 12:00',
                        'tableId' => 1
                    ],
                ],
                'requested reservation' => [
                    'date' => '2020-07-20',
                    'startTime' => '2020-07-20 11:00',
                    'endTime' => '2020-07-20 11:30',
                    'tableId' => 1
                ],
                'expected conflicting reservations' => [],
            ],
            4 => [
                'existing reservations' => [
                    [
                        'date' => '2020-07-20',
                        'startTime' => '2020-07-20 10:00',
                        'endTime' => '2020-07-20 11:00',
                        'tableId' => 1
                    ],
                    [
                        'date' => '2020-07-20',
                        'startTime' => '2020-07-20 10:00',
                        'endTime' => '2020-07-20 11:00',
                        'tableId' => 2
                    ],
                ],
                'requested reservation' => [
                    'date' => '2020-07-20',
                    'startTime' => '2020-07-20 10:00',
                    'endTime' => '2020-07-20 11:30',
                    'tableId' => null
                ],
                'expected conflicting reservations' => [
                    [
                        'tableId' => 1,
                        'startTime' => '2020-07-20 10:00',
                        'endTime' => '2020-07-20 11:00',
                    ],
                    [
                        'tableId' => 2,
                        'startTime' => '2020-07-20 10:00',
                        'endTime' => '2020-07-20 11:00',
                    ],
                ],
            ],
        ];
    }

    private function saveExistingReservationsToDatabase(array $existingReservations): void
    {
        if (0 === count($existingReservations)) {
            return;
        }

        foreach ($existingReservations as $res) {
            $reservation = new TableReservation();
            $reservation->setDate(new \DateTime($res['date']))
                ->setStartTime(new \DateTime($res['startTime']))
                ->setEndTime(new \DateTime($res['endTime']))
                ->setTableId($res['tableId']);

            $this->em->persist($reservation);
        }

        $this->em->flush();
    }

    private function formatRepositoryResponse(array $repoResponse): array
    {
        if (0 === count($repoResponse)) {
            return $repoResponse;
        }

        foreach ($repoResponse as &$resp) {
            $resp['startTime'] = $resp['startTime']->format(
                ReceivedReservationRequestParameters::VALID_DATETIME_FORMAT
            );
            $resp['endTime'] = $resp['endTime']->format(
                ReceivedReservationRequestParameters::VALID_DATETIME_FORMAT
            );
        }

        return $repoResponse;
    }
}
