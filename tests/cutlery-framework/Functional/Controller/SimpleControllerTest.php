<?php
declare(strict_types=1);

namespace Cutlery\Tests\Functional\Controller;

use Cutlery\Tests\Common\FunctionalTestCase;
use Cutlery\Tests\Common\Request;
use Cutlery\Tests\Common\Response;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
final class SimpleControllerTest extends FunctionalTestCase
{
    public function testInvalidConfigThrowsError(): void
    {
        $response = $this->makeRequest(
            Request::METHOD_GET,
            '/invalid'
        );

        $json = $this->decodeJsonFromResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        self::assertArrayHasKey('error', $json);
        self::assertSame('Invalid controller definition', $json['error']);
    }

    /*
    No input parameters
    */

    public function testMinimalistConfigReturns200(): void
    {
        $response = $this->makeRequest(
            Request::METHOD_GET,
            '/minimalist'
        );

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame('Minimalist GET', $response->getContent());
    }

    public function testSimpleGetReturns200(): void
    {
        $response = $this->makeRequest(
            Request::METHOD_GET,
            '/simple'
        );

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame('Simple GET', $response->getContent());
    }

    public function testSimplePostReturns200(): void
    {
        $response = $this->makeRequest(
            Request::METHOD_POST,
            '/simple'
        );

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame('Simple POST', $response->getContent());
    }

    public function testSimpleJsonReturns200(): void
    {
        $response = $this->makeRequest(
            Request::METHOD_GET,
            '/simple-json'
        );

        $json = $this->decodeJsonFromResponse($response);
        self::assertArrayHasKey('message', $json);
        self::assertSame('Simple JSON', $json['message']);
    }

    public function testDifferentStatusReturns201(): void
    {
        $response = $this->makeRequest(
            Request::METHOD_GET,
            '/different-status'
        );

        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertSame('Different status', $response->getContent());
    }

    /*
    Uri input parameters
    */

    public function testParameterRightReturns200(): void
    {
        $response = $this->makeRequest(
            Request::METHOD_GET,
            '/parameter/value123'
        );

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame('Parameter: value123', $response->getContent());
    }

    public function testParameterMiddleReturns200(): void
    {
        $response = $this->makeRequest(
            Request::METHOD_GET,
            '/parameter/value456/test'
        );

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame('Parameter: value456', $response->getContent());
    }

    public function testParameterTwoReturns200(): void
    {
        $response = $this->makeRequest(
            Request::METHOD_GET,
            '/parameter/value123/test/value456'
        );

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame('Parameters: value123, value456', $response->getContent());
    }

    /*
    DTO/body input parameters
    */

    /**
     * @param array<string, string|int|null> $dtoData
     */
    #[DataProvider('provideParameterDtoReturnsResponseForValidInputCases')]
    public function testParameterDtoReturnsResponseForValidInput($dtoData): void
    {
        $expectedText = \sprintf(
            'Parameters: %s, %d, null',
            $dtoData['body_parameter_1'],
            $dtoData['body_parameter_2'],
        );

        $response = $this->makeRequest(
            Request::METHOD_POST,
            '/parameter-dto',
            $dtoData
        );

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame($expectedText, $response->getContent());
    }

    /**
     * @return array<mixed>
     */
    public static function provideParameterDtoReturnsResponseForValidInputCases(): iterable
    {
        return [
            [[
                'body_parameter_1' => 'more_text',
                'body_parameter_2' => 123,
            ]],
            [[
                'body_parameter_1' => 'more_text',
                'body_parameter_2' => null,
            ]],
        ];
    }

    /**
     * @param array<string, string|int|null> $dtoData
     */
    #[DataProvider('provideParameterDtoReturnsBadRequestForInvalidInputCases')]
    public function testParameterDtoReturnsBadRequestForInvalidInput($dtoData): void
    {
        $response = $this->makeRequest(
            Request::METHOD_POST,
            '/parameter-dto',
            $dtoData
        );

        $json = $this->decodeJsonFromResponse($response, Response::HTTP_BAD_REQUEST);
        self::assertArrayHasKey('error', $json);
        self::assertIsString($json['error']);
        self::assertStringStartsWith('Bad Request: ', $json['error']);
    }

    /**
     * @return array<mixed>
     */
    public static function provideParameterDtoReturnsBadRequestForInvalidInputCases(): iterable
    {
        return [
            [[
                'body_parameter_1' => 'more_text',
            ]],
            [[
                'body_parameter_1' => 'more_text',
                'body_parameter_2' => 'more text',
            ]],
            [[
                'body_parameter_1' => 456,
                'body_parameter_2' => 123,
            ]],
            [[
                'body_parameter_1' => null,
                'body_parameter_2' => 123,
            ]],
        ];
    }

    public function testParameterMixReturns200(): void
    {
        $response = $this->makeRequest(
            Request::METHOD_POST,
            '/parameter-mix/urivalue123',
            [
                'body_parameter_1' => 'more_text',
                'body_parameter_2' => 123,
            ],
        );

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame('Parameters: more_text, 123, null, urivalue123', $response->getContent());
    }

    /*
    Auth required - todo
    */
}
