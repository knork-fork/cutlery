<?php
declare(strict_types=1);

namespace Cutlery\Command;

use RuntimeException;

final class CommandTester
{
    private Input $input;
    private Output $output;
    /** @var resource */
    private $stream;

    /**
     * @param array<string, mixed> $inputOptions
     */
    public function __construct(string $commandClassName, array $inputOptions = [])
    {
        // Build input stream
        $this->input = new Input(
            $this->getArgvArrayFromInputOptions($commandClassName, $inputOptions)
        );

        // Build output stream
        $stream = fopen('php://memory', 'w+'); // memory-based stream
        if ($stream === false) {
            throw new RuntimeException('Failed to open stdout stream.');
        }
        $this->stream = $stream;
        $this->output = new Output($this->stream);
    }

    public function execute(): int
    {
        return Command::executeCommand($this->input, $this->output);
    }

    public function getDisplay(): string
    {
        rewind($this->stream);

        return stream_get_contents($this->stream) ?: '';
    }

    /**
     * @param array<string, mixed> $inputOptions
     *
     * @return string[]
     */
    private function getArgvArrayFromInputOptions(string $commandClassName, array $inputOptions): array
    {
        $argv = ['bin/console', $commandClassName::getName()];

        /** @var string|bool|null $value */
        foreach ($inputOptions as $key => $value) {
            if (str_starts_with($key, '--')) {
                if ($value === true) {
                    $argv[] = $key;
                } else {
                    $argv[] = $key . '=' . (string) $value;
                }
            } else {
                $argv[] = (string) $value;
            }
        }

        return $argv;
    }
}
