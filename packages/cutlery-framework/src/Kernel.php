<?php
declare(strict_types=1);

namespace Cutlery;

use Cutlery\Config\ConfigLocator;
use Cutlery\Routing\RouteCache;
use Cutlery\Support\Env;
use App\System\Router;
use Cutlery\Exception\BadRequestException;
use Cutlery\Exception\NotFoundException;
use Cutlery\Response\ExceptionResponse;

final class Kernel
{
    public function __construct(private ?string $rootHint = null) {}

    public function run(): void
    {
        $root = ConfigLocator::projectRoot($this->rootHint);

        // 1) env
        Env::load($root);

        // 2) routes (with cache)
        $configDir = $root.'/config';
        $cacheDir  = $root.'/var/cache';
        $cacheOn   = (getenv('APP_ENV') ?? 'dev') !== 'dev';
        $routes    = RouteCache::load($configDir, $cacheDir, $cacheOn);

        // 3) dispatch
        $uri = (string)($_SERVER['REQUEST_URI'] ?? '');
        $router = new Router($uri, $routes);

        try {
            $router->callEndpoint();
        } catch (\Throwable $e) {
            $suppress = $e instanceof NotFoundException || $e instanceof BadRequestException;
            $resp = new ExceptionResponse($e, $suppress);
            $resp->output();
            if (!$resp->suppressThrow) { throw $e; }
        }
    }
}
