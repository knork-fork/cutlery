<?php
declare(strict_types=1);

namespace Cutlery\Routing;

use Cutlery\Exception\NotFoundException;
use Cutlery\Response\Response;
use Cutlery\Support\RootLocator;
use RuntimeException;

final class Endpoint
{
    public function __construct(
        public readonly string $path,
        public readonly string $controller,
        public readonly string $method,
        public readonly string $description,
        public readonly ?string $requestDtoClass,
        public readonly int $statusCode,
        public readonly bool $authRequired,
    ) {
    }
}

final class Router
{
    private string $path;
    private string $method;

    public function __construct(
        private string $uri,
    ) {
        $this->setPath();
        $this->setMethod();
    }

    public function callEndpoint(): void
    {
        [$endpoint, $args] = $this->resolveEndpoint();

        $controller = explode('::', $endpoint->controller);
        if (\count($controller) !== 2) {
            throw new RuntimeException('Invalid controller definition');
        }
        $class = $controller[0];
        $method = $controller[1];

        $controller = new $class();
        if (\count($args) === 0) {
            $response = $controller->{$method}();
        } else {
            $response = $controller->{$method}(...$args);
        }

        /* @var Response $response */
        $response->output($endpoint->statusCode);
    }

    /**
     * @return array{0: Endpoint, 1: mixed[]}
     */
    private function resolveEndpoint(): array
    {
        $route = $this->getMatchingRoute();
        $controller = explode('::', $route->controller);
        if (\count($controller) !== 2) {
            throw new RuntimeException('Invalid controller definition');
        }

        $dto = null;
        if ($route->requestDtoClass !== null) {
            $dto = ParameterLoader::getDto($route->requestDtoClass);
        }
        $uriParameters = ParameterLoader::getUriParameters($route->path, $this->path);

        if ($dto !== null) {
            // DTO will always be passed as the first argument, followed by URI parameters
            $args = array_merge([$dto], $uriParameters);
        } else {
            $args = $uriParameters;
        }

        return [
            $route,
            $args,
        ];
    }

    private function getMatchingRoute(): Endpoint
    {
        /** @var array<string, array<string, string|int|bool|null>> $routes */
        $routes = require RootLocator::getProjectRoot() . RouteCache::CACHE_FILE;

        $matchingRoutes = [];
        foreach ($routes as $route) {
            $path = (string) $route['path'];
            $method = (string) ($route['method'] ?? 'GET');
            if ($this->doesRequestMatchConfiguredRoute($method, $path)) {
                $matchingRoutes[] = $route;
            }
        }

        if (\count($matchingRoutes) === 0) {
            throw new NotFoundException('Path not found');
        }
        if (\count($matchingRoutes) > 1) {
            throw new RuntimeException('Config error, multiple routes found for the same method');
        }

        if (\array_key_exists('requestDto', $matchingRoutes[0]) === false) {
            $requestDto = null;
        } else {
            $requestDto = $matchingRoutes[0]['requestDto'] === null ? null : (string) $matchingRoutes[0]['requestDto'];
        }

        return new Endpoint(
            (string) $matchingRoutes[0]['path'],
            (string) $matchingRoutes[0]['controller'],
            (string) ($matchingRoutes[0]['method'] ?? 'GET'),
            (string) ($matchingRoutes[0]['description'] ?? ''),
            $requestDto,
            (int) ($matchingRoutes[0]['status_code'] ?? 200),
            (bool) ($matchingRoutes[0]['auth_required'] ?? false),
        );
    }

    private function doesRequestMatchConfiguredRoute(string $method, string $path): bool
    {
        if ($method !== $this->method) {
            return false;
        }

        return PathMatcher::doesPathMatch($path, $this->path);
    }

    private function setPath(): void
    {
        $this->path = explode('?', $this->uri)[0];
    }

    private function setMethod(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        $this->method = \is_string($method) ? $method : '';
    }
}
