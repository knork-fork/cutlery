<?php
declare(strict_types=1);

namespace Cutlery\Routing;

use Symfony\Component\Yaml\Yaml;

final class RouteCache
{
    public static function load(string $configDir, string $cacheDir, bool $useCache = true): array
    {
        $yaml = $configDir.'/routes.yaml';
        if (!is_file($yaml)) return [];

        $cacheFile = $cacheDir.'/routes.cache.php';

        if (!$useCache) {
            return self::parse($yaml);
        }

        if (!is_file($cacheFile) || filemtime($yaml) > filemtime($cacheFile)) {
            $routes = self::parse($yaml);
            if (!is_dir($cacheDir)) @mkdir($cacheDir, 0775, true);
            file_put_contents($cacheFile, '<?php return '.var_export($routes, true).';');
            return $routes;
        }
        /** @var array $routes */
        $routes = require $cacheFile;
        return $routes;
    }

    private static function parse(string $yamlPath): array
    {
        return (array) Yaml::parseFile($yamlPath);
    }
}
