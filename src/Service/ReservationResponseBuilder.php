<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\ReservationParameters;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ReservationResponseBuilder
{
    public function buildBadRequestResponse(ConstraintViolationListInterface $requestValidationErrors): JsonResponse
    {
        $responseMessage['status'] = '400 Bad Request';
        foreach ($requestValidationErrors as $err) {
            $responseMessage['invalid_parameter_values'][$err->getPropertyPath()] = $err->getMessage();
        }

        return new JsonResponse($responseMessage, 400);
    }

    public function buildConflictResponse(array $conflictingReservations): JsonResponse
    {
        $responseMessage['status'] = '409 Conflict';
        foreach ($conflictingReservations as $conflict) {
            $startTime = $conflict['startTime']->format('Y-m-d H:i');
            $endTime = $conflict['endTime']->format('Y-m-d H:i');
            $responseMessage['conflicting_reservations']['table '.$conflict['tableId']] = $startTime.' - '.$endTime;
        }

        return new JsonResponse($responseMessage, 409);
    }

    public function buildOkResponse(ReservationParameters $reservationParams): JsonResponse
    {
        $responseMessage['status'] = '200 OK';
        $responseMessage['reservation_parameters'] = [
            'table_id' => $reservationParams->getTableId(),
            'from' => $reservationParams->getStartTime(),
            'to' => $reservationParams->getEndTime(),
            'price_in_roubles' => $reservationParams->getPrice(),
        ];

        return new JsonResponse($responseMessage);
    }
}
