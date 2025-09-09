<?php
declare(strict_types=1);

namespace Cutlery\Config;

final class ConfigLocator
{
    public static function projectRoot(?string $hint = null): string
    {
        if ($hint && is_dir($hint)) return rtrim($hint, '/');
        $env = getenv('KNORK_CONFIG_DIR');
        if ($env) return dirname($env);
        // walk up from script dir
        $dir = dirname($_SERVER['SCRIPT_FILENAME'] ?? __DIR__, 0);
        while ($dir !== dirname($dir)) {
            if (is_file($dir.'/config/routes.yaml')) return $dir;
            $dir = dirname($dir);
        }
        throw new \RuntimeException('config/routes.yaml not found. Set KNORK_CONFIG_DIR or pass a root hint.');
    }

    public static function configDir(string $root): string { return $root.'/config'; }
}
