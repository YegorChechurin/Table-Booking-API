<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\ReceivedReservationRequestParameters;
use App\Dto\ValidReservationRequestParameters;
use App\Repository\TableReservationRepository;
use App\Service\AvailableTableFinder;
use App\Service\ReservationResponseBuilder;
use App\Service\TableReserver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function count;

class TableReservationController extends AbstractController
{
    /**
     * @Route("/reservation", methods={"POST"})
     */
    public function index(
        Request $request,
        ValidatorInterface $validator,
        TableReservationRepository $repo,
        AvailableTableFinder $availableTableFinder,
        TableReserver $tableReserver,
        ReservationResponseBuilder $responseBuilder
    ): JsonResponse
    {
        $resReq = new ReceivedReservationRequestParameters();
        $resReq->setDate($request->get('date'))
            ->setFrom($request->get('from'))
            ->setTo($request->get('to'));

        if (!empty($request->get('table_id'))) {
            $resReq->setTableId((int) $request->get('table_id'));
        }

        $errors = $validator->validate($resReq);
        if (count($errors) > 0) {
            return $responseBuilder->buildBadRequestResponse($errors);
        }

        $conflictingReservations = $repo->findConflictingReservations(
            $resReq->getDate(),
            $resReq->getFrom(),
            $resReq->getTo(),
            $resReq->getTableId()
        );

        $tableIdToReserve = $availableTableFinder->findTableIdToReserve(
            $conflictingReservations,
            $resReq->getTableId()
        );

        if (null === $tableIdToReserve) {
            return $responseBuilder->buildConflictResponse($conflictingReservations);
        }

        $validReservationRequestParams = new ValidReservationRequestParameters(
            $resReq->getDate(),
            $resReq->getFrom(),
            $resReq->getTo(),
            $tableIdToReserve
        );

        return $responseBuilder->buildOkResponse(
            $tableReserver->reserveTable($validReservationRequestParams)
        );
    }
}
