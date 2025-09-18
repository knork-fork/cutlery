<?php
declare(strict_types=1);

namespace App\Controller;

use App\Dto\TestRequestDto;
use Cutlery\Response\JsonResponse;
use Cutlery\Response\PlainTextResponse;
use Cutlery\Response\Response;

final class SimpleController
{
    /*
    No input parameters
    */

    public static function minimalistGet(): PlainTextResponse
    {
        return new PlainTextResponse(
            'Minimalist GET'
        );
    }

    public static function simpleGet(): PlainTextResponse
    {
        return new PlainTextResponse(
            'Simple GET'
        );
    }

    public static function simplePost(): PlainTextResponse
    {
        return new PlainTextResponse(
            'Simple POST'
        );
    }

    public static function simpleJson(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Simple JSON',
        ]);
    }

    public static function differentStatus(): PlainTextResponse
    {
        return new PlainTextResponse(
            'Different status',
            Response::HTTP_CREATED,
        );
    }

    /*
    Uri input parameters
    */

    public static function parameterRight(string $param): PlainTextResponse
    {
        return new PlainTextResponse(
            'Parameter: ' . $param,
        );
    }

    public static function parameterMiddle(string $param): PlainTextResponse
    {
        return new PlainTextResponse(
            'Parameter: ' . $param,
        );
    }

    public static function parameterTwo(string $param1, string $param2): PlainTextResponse
    {
        return new PlainTextResponse(
            \sprintf('Parameters: %s, %s', $param1, $param2),
        );
    }

    /*
    DTO/body input parameters
    */

    public static function parameterDto(TestRequestDto $testRequestDto): PlainTextResponse
    {
        return new PlainTextResponse(
            \sprintf(
                'Parameters: %s, %d, %s',
                $testRequestDto->body_parameter_1,
                $testRequestDto->body_parameter_2 ?? 0,
                $testRequestDto->optional_string_parameter ?? 'null',
            ),
        );
    }

    public static function parameterMix(TestRequestDto $testRequestDto, string $param): PlainTextResponse
    {
        return new PlainTextResponse(
            \sprintf(
                'Parameters: %s, %d, %s, %s',
                $testRequestDto->body_parameter_1,
                $testRequestDto->body_parameter_2 ?? 0,
                $testRequestDto->optional_string_parameter ?? 'null',
                $param,
            ),
        );
    }

    /*
    Auth required - todo
    */
}
