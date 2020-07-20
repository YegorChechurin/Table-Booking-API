<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TableReservationControllerTest extends WebTestCase
{
    private const REQUEST_URL = '/api/v2/reservation';

    private const REQUEST_METHOD = 'POST';

    private const BAD_REQUEST_STATUS_CODE = 400;

    private const BAD_REQUEST_STATUS = '400 Bad Request';

    private const CONFLICT_STATUS_CODE = 409;

    private const CONFLICT_STATUS = '409 Conflict';

    private const OK_STATUS_CODE = 200;

    private const OK_STATUS = '200 OK';

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
                'expected message' => '"invalid_parameter_values":{"date":"Required parameter","from":"Required parameter","to":"Required parameter"}',
            ],
            1 => [
                'request parameters' => [
                    'date' => '2020-04-05',
                    'from' => '2020-04-05 07:00'
                ],
                'expected message' => '"invalid_parameter_values":{"to":"Required parameter"}',
            ],
            2 => [
                'request parameters' => [
                    'date' => '2030-04-05',
                    'from' => '2030-04-06 07:00',
                    'to' => '2030-04-06 09:00'
                ],
                'expected message' => '"invalid_parameter_values":{"from":"Date must be same as the one in parameter date"}',
            ],
            3 => [
                'request parameters' => [
                    'date' => '2030-04-05',
                    'from' => '2030-04-05 09:00',
                    'to' => '2030-04-05 09:29'
                ],
                'expected message' => '"invalid_parameter_values":{"to":"Difference between starting time and finish time is less than 30 minutes"',
            ],
            4 => [
                'request parameters' => [
                    'date' => '2030-04-05',
                    'from' => '2030-04-05 09:00',
                    'to' => '2030-04-05 09:39',
                    'table_id' => 6,
                ],
                'expected message' => '"invalid_parameter_values":{"table_id":"Invalid table number, valid table numbers are 1-5"}',
            ],
        ];
    }

//    /**
//     * @dataProvider conflictRequestParamsAndExpectedMessagesProvider
//     */
//    public function testConflictResponse(
//        array $requestParams,
//        string $expectedResponseMessage
//    ): void {
//        $response = $this->getRouteResponse($requestParams);
//
//        $this->assertRouteResponseMeetsExpectations(
//            $response->getStatusCode(),
//            $response->getContent(),
//            self::CONFLICT_STATUS_CODE,
//            self::CONFLICT_STATUS,
//            $expectedResponseMessage
//        );
//    }
//
//    public function conflictRequestParamsAndExpectedMessagesProvider(): array
//    {
//        return [
//            0 => [
//                'request parameters' => [],
//                'expected message' => [],
//            ],
//        ];
//    }

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
