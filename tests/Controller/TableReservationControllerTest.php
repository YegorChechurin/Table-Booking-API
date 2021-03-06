<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Dto\ReceivedReservationRequestParameters;
use App\Service\ReservationPriceCalculator;
use App\Validator\Constraints\ExistentTableId;
use App\Validator\Constraints\FromDateIsSameAsDate;
use App\Validator\Constraints\GreaterByAtLeastHalfAnHour;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TableReservationControllerTest extends WebTestCase
{
    private const REQUEST_URL = '/api/v2/reservation';

    private const REQUEST_METHOD = 'POST';

    private const BAD_REQUEST_STATUS_CODE = 400;

    private const BAD_REQUEST_STATUS = '400 Bad Request';

    private const BAD_REQUEST_MESSAGE_TITLE = 'invalid_parameter_values';

    private const CONFLICT_STATUS_CODE = 409;

    private const CONFLICT_STATUS = '409 Conflict';

    private const CONFLICT_MESSAGE_TITLE = 'conflicting_reservations';

    private const OK_STATUS_CODE = 200;

    private const OK_STATUS = '200 OK';

    private const OK_MESSAGE_TITLE = 'reservation_parameters';

    private KernelBrowser $browser;

    protected function setUp(): void
    {
        $this->browser = static::createClient();
    }

    /**
     * @dataProvider invalidRequestParamsAndExpectedMessagesProvider
     */
    public function testInvalidRequestParametersProduceBadRequestResponse(
        array $requestParams,
        string $expectedResponseMessage
    ): void {
        $response = $this->getRouteResponse($requestParams);

        $this->assertRouteResponseMeetsExpectations(
            $response->getStatusCode(),
            $response->getContent(),
            self::BAD_REQUEST_STATUS_CODE,
            self::BAD_REQUEST_STATUS,
            $expectedResponseMessage
        );
    }

    public function invalidRequestParamsAndExpectedMessagesProvider(): array
    {
        return [
            0 => [
                'request parameters' => [],
                'expected message' => '"'.self::BAD_REQUEST_MESSAGE_TITLE.'":{"date":"'.ReceivedReservationRequestParameters::REQUIRED_PARAMETER_MESSAGE.'","from":"'.ReceivedReservationRequestParameters::REQUIRED_PARAMETER_MESSAGE.'","to":"'.ReceivedReservationRequestParameters::REQUIRED_PARAMETER_MESSAGE.'"}',
            ],
            1 => [
                'request parameters' => [
                    'date' => '2020-04-05',
                    'from' => '2020-04-05 07:00'
                ],
                'expected message' => '"'.self::BAD_REQUEST_MESSAGE_TITLE.'":{"to":"'.ReceivedReservationRequestParameters::REQUIRED_PARAMETER_MESSAGE.'"}',
            ],
            2 => [
                'request parameters' => [
                    'date' => '2030-04-05',
                    'from' => '2030-04-06 07:00',
                    'to' => '2030-04-06 09:00'
                ],
                'expected message' => '"'.self::BAD_REQUEST_MESSAGE_TITLE.'":{"from":"'.(new FromDateIsSameAsDate(''))->message.'"}',
            ],
            3 => [
                'request parameters' => [
                    'date' => '2030-04-05',
                    'from' => '2030-04-05 09:00',
                    'to' => '2030-04-05 09:29'
                ],
                'expected message' => '"'.self::BAD_REQUEST_MESSAGE_TITLE.'":{"to":"'.(new GreaterByAtLeastHalfAnHour(''))->message.'"}',
            ],
            4 => [
                'request parameters' => [
                    'date' => '2030-04-05',
                    'from' => '2030-04-05 09:00',
                    'to' => '2030-04-05 09:39',
                    'table_id' => 6,
                ],
                'expected message' => '"'.self::BAD_REQUEST_MESSAGE_TITLE.'":{"table_id":"'.(new ExistentTableId())->message.'"}',
            ],
        ];
    }

    /**
     * @dataProvider conflictRequestParamsAndExpectedMessagesProvider
     */
    public function testConflictResponse(
        array $requestParams,
        string $expectedResponseMessage
    ): void {
        $response = $this->getRouteResponse($requestParams);

        $this->assertRouteResponseMeetsExpectations(
            $response->getStatusCode(),
            $response->getContent(),
            self::CONFLICT_STATUS_CODE,
            self::CONFLICT_STATUS,
            $expectedResponseMessage
        );
    }

    public function conflictRequestParamsAndExpectedMessagesProvider(): array
    {
        return [
            0 => [
                'request parameters' => [
                    'date' => '2030-12-12',
                    'from' => '2030-12-12 10:00',
                    'to' => '2030-12-12 11:00',
                    'table_id' => 1,
                ],
                'expected message' => '"'.self::CONFLICT_MESSAGE_TITLE.'":{"table 1":"2030-12-12 10:00 - 2030-12-12 11:00"}',
            ],
            1 => [
                'request parameters' => [
                    'date' => '2030-12-12',
                    'from' => '2030-12-12 09:00',
                    'to' => '2030-12-12 10:10',
                    'table_id' => 5,
                ],
                'expected message' => '"'.self::CONFLICT_MESSAGE_TITLE.'":{"table 5":"2030-12-12 10:00 - 2030-12-12 11:00"}',
            ],
        ];
    }

    public function testOkResponse(): void
    {
        $requestParams = [
            'date' => '2030-12-12',
            'from' => '2030-12-12 11:00',
            'to' => '2030-12-12 12:00',
            'table_id' => 3,
        ];
        $reservationPrice = (new ReservationPriceCalculator())
            ->getReservationPrice(
                new \DateTime($requestParams['from']),
                new \DateTime($requestParams['to'])
            );
        $expectedResponseMessage = '"'.self::OK_MESSAGE_TITLE.'":{"table_id":"'.$requestParams['table_id'].'","from":"'.$requestParams['from'].'","to":"'.$requestParams['to'].'","price_in_roubles":"'.$reservationPrice.'"}';

        $response = $this->getRouteResponse($requestParams);

        $this->assertRouteResponseMeetsExpectations(
            $response->getStatusCode(),
            $response->getContent(),
            self::OK_STATUS_CODE,
            self::OK_STATUS,
            $expectedResponseMessage
        );
    }

    private function getRouteResponse(array $requestParameters): Response
    {
        $this->browser->request(
            self::REQUEST_METHOD,
            self::REQUEST_URL,
            $requestParameters
        );

        return $this->browser->getResponse();
    }

    private function assertRouteResponseMeetsExpectations(
        int $responseStatusCode,
        string $responseContent,
        int $expectedStatusCode,
        string $expectedStatus,
        string $expectedMessage
    ): void {
        $this->assertEquals($expectedStatusCode, $responseStatusCode);
        $this->assertStringContainsString($expectedStatus, $responseContent);
        $this->assertStringContainsString($expectedMessage, $responseContent);
    }
}
