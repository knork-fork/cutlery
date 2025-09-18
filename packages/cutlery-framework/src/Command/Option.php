<?php
declare(strict_types=1);

namespace Cutlery\Command;

final class Option
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly mixed $default,
    ) {
    }
}
