<?php
declare(strict_types=1);

namespace Cutlery\Tests\Unit\Routing;

use Cutlery\Routing\PathMatcher;
use Cutlery\Tests\Common\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
final class PathMatcherTest extends UnitTestCase
{
    #[DataProvider('provideDoesPathMatchForMatchReturnsTrueCases')]
    public function testDoesPathMatchForMatchReturnsTrue(string $path, string $uri): void
    {
        self::assertTrue(PathMatcher::doesPathMatch($path, $uri));
    }

    /**
     * @return array<mixed>
     */
    public static function provideDoesPathMatchForMatchReturnsTrueCases(): iterable
    {
        return [
            ['/test', '/test'],
            ['/{test}', '/blabla'],
            ['/{test}', '/'],
            ['/test/{parameter_1}', '/test/blabla'],
            ['/test/test2/{parameter_1}', '/test/test2/blabla'],
            ['/test/{parameter_1}/{parameter_2}', '/test/blabla/123'],
            ['/test/{parameter_1}/parameter_2', '/test/123/parameter_2'],
        ];
    }

    #[DataProvider('provideDoesPathMatchForNoMatchReturnsFalseCases')]
    public function testDoesPathMatchForNoMatchReturnsFalse(string $path, string $uri): void
    {
        self::assertFalse(PathMatcher::doesPathMatch($path, $uri));
    }

    /**
     * @return array<mixed>
     */
    public static function provideDoesPathMatchForNoMatchReturnsFalseCases(): iterable
    {
        return [
            ['/test', '/test2'],
            ['/test', '/test/'],
            ['/test', '/test/123'],
            ['/test/test2', '/test/blabla'],
            ['/test/{parameter_1}/parameter_2', '/test/123/blabla'],
            ['/test/{parameter_1}/{parameter_2}', '/test/blabla'],
            ['/test/{parameter_1}/{parameter_2}', '/test/'],
            ['/test/{parameter_1}/{parameter_2}', '/test'],
        ];
    }
}
