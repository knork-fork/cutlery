<?php
declare(strict_types=1);

namespace Cutlery\Command;

use RuntimeException;

// todo: add OutputInterface later
final class Output
{
    /** @var resource */
    private $stream;

    public function __construct()
    {
        $stream = fopen('php://stdout', 'w');
        if ($stream === false) {
            throw new RuntimeException('Failed to open stdout stream.');
        }

        $this->stream = $stream;
    }

    public function writeLine(string $message): void
    {
        $this->write($message . \PHP_EOL);
    }

    public function write(string $message): void
    {
        @fwrite($this->stream, $message);

        fflush($this->stream);
    }
}
