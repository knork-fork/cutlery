<?php
declare(strict_types=1);

namespace App\Command;

use Cutlery\Command\Argument;
use Cutlery\Command\Command;
use Cutlery\Command\CommandDefinition;

#[CommandDefinition(
    name: 'app:test',
    description: 'Example command, run with docker/console app:test',
)]
final class TestCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('testArgument', Argument::REQUIRED, 'An input argument.')
            ->addOption('testOption', description: 'An input option.')
            ->addArgument('optionalArgument', Argument::OPTIONAL, 'Another input argument.')
        ;
    }

    protected function execute(): int
    {
        $testArgument = $this->input->getArgument('testArgument');
        $testOption = $this->input->getOption('testOption');
        $optionalArgument = $this->input->getArgument('optionalArgument');

        $this->output->writeLine(\sprintf(
            'All inputs: testArgument=%s, testOption=%s, optionalArgument=%s',
            var_export($testArgument, true),
            var_export($testOption, true),
            var_export($optionalArgument, true)
        ));

        return self::SUCCESS;
    }
}
