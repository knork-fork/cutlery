<?php
declare(strict_types=1);

namespace Cutlery\Routing;

use Cutlery\Support\RootLocator;
use Symfony\Component\Yaml\Yaml;

final class RouteCache
{
    public const CACHE_FILE = '/config/routes.cache.php';

    public static function buildIfOutdated(): void
    {
        $root = RootLocator::getProjectRoot();

        $yamlPath = $root . '/config/routes.yaml';
        $cachePath = $root . self::CACHE_FILE;

        if (!file_exists($cachePath) || filemtime($yamlPath) > filemtime($cachePath)) {
            $parsed = Yaml::parseFile($yamlPath);
            file_put_contents($cachePath, '<?php return ' . var_export($parsed, true) . ';');
        }
    }
}
