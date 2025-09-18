<?php
declare(strict_types=1);

namespace Cutlery\Command;

final class Argument
{
    public const REQUIRED = true;
    public const OPTIONAL = false;

    public function __construct(
        public readonly string $name,
        public readonly bool $required,
        public readonly string $description,
        public readonly mixed $default,
    ) {
    }
}
