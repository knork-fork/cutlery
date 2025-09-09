<?php
declare(strict_types=1);

namespace Cutlery\Support;

use KnorkFork\LoadEnvironment\Environment;

final class Env
{
    public static function load(string $projectRoot, ?bool $allowHeaderOverride = null): void
    {
        $envFile = $projectRoot . '/.env';
        $allowOverride = $allowHeaderOverride ?? (getenv('ALLOW_ENV_OVERRIDE') === 'true');

        $overrideEnvToTest = false;
        if ($allowOverride) {
            $hdr = $_SERVER['HTTP_X_APP_ENV'] ?? null;
            if ($hdr === 'test') {
                $overrideEnvToTest = true;
            }
        }

        if ($overrideEnvToTest) {
            // loads [.env, .env.test]
            Environment::load($envFile, ['test']);
        } else {
            // loads [.env, .env.local]
            Environment::load($envFile);
        }
    }
}
