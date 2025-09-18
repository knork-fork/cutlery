<?php
declare(strict_types=1);

namespace Cutlery\Tests\Unit;

use Cutlery\Tests\Common\UnitTestCase;

/**
 * @internal
 */
final class ExampleUnitTest extends UnitTestCase
{
    public function testPhpunitWorks(): void
    {
        $str = 'hello world';

        self::assertSame('hello world', $str);
    }
}