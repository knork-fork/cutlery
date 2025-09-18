<?php
declare(strict_types=1);

namespace App\Tests\Functional\Command;

use App\Command\TestCommand;
use Cutlery\Command\Command;
use Cutlery\Command\CommandTester;
use Cutlery\Tests\Common\FunctionalTestCase;

/**
 * @internal
 */
final class TestCommandTest extends FunctionalTestCase
{
    public function testExecuteDisplaysExpectedOutput(): void
    {
        $commandTester = new CommandTester(
            TestCommand::class,
            [
                'testArgument' => '123',
                '--testOption' => true,
            ]
        );

        self::assertSame(Command::SUCCESS, $commandTester->execute());

        $output = $commandTester->getDisplay();
        self::assertStringContainsString("All inputs: testArgument='123', testOption=true, optionalArgument=NULL", $output);
    }
}
