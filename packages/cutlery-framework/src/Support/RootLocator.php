<?php
declare(strict_types=1);

namespace Cutlery\Support;

use RuntimeException;

final class RootLocator
{
    private static ?string $cachedRoot = null;

    public static function getProjectRoot(): string
    {
        // to-do: prepare for vendor install

        if (self::$cachedRoot !== null) {
            return self::$cachedRoot;
        }

        $relativeRoot = '../../../..';
        $root = realpath(__DIR__ . '/' . $relativeRoot);

        if ($root === false) {
            throw new RuntimeException('Could not determine project root directory.');
        }
        self::$cachedRoot = $root;

        return $root;
    }
}
