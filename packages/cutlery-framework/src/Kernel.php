<?php
declare(strict_types=1);

namespace Cutlery;

use Cutlery\Routing\RouteCache;
use Cutlery\Support\Env;
use Cutlery\Exception\BadRequestException;
use Cutlery\Exception\NotFoundException;
use Cutlery\Response\ExceptionResponse;
use Cutlery\Routing\Router;

final class Kernel
{
    public function __construct() {}

    public function run(): void
    {
        Env::load();

        RouteCache::buildIfOutdated();

        // Dispatch request
        $uri = $_SERVER['REQUEST_URI'];
        $uri = is_string($uri) ? $uri : '';
        $router = new Router($uri);
        try {
            $router->callEndpoint();
        } catch (\Throwable $e) {
            // Suppress user-caused exceptions being thrown and logged
            $suppressThrow = $e instanceof NotFoundException
                || $e instanceof BadRequestException;

            $exception = new ExceptionResponse($e, $suppressThrow);
            $exception->output();

            if (!$exception->suppressThrow) {
                throw $e;
            }
        }
    }
}
