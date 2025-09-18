<?php
declare(strict_types=1);

namespace Cutlery\Response;

final class PlainTextResponse extends Response
{
    public function __construct(
        string $data,
        int $statusCode = Response::HTTP_OK
    ) {
        parent::__construct($data, $statusCode, 'text/plain');
    }
}
