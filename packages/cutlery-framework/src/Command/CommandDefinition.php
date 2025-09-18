<?php
declare(strict_types=1);

namespace Cutlery\Command;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class CommandDefinition
{
    /**
     * @param string      $name        The name of the command, used when calling it (i.e. "cache:clear")
     * @param string|null $description The description of the command, displayed with the help page
     */
    public function __construct(
        public string $name,
        public ?string $description = null,
    ) {
    }
}
