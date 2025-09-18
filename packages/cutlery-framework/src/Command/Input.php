<?php
declare(strict_types=1);

namespace Cutlery\Command;

use Exception;

// todo: add InputInterface later
final class Input
{
    /** @var string[] */
    private array $argv;
    /**
     * @var string[]
     */
    private array $rawArguments = [];
    /**
     * @var array<string, mixed>
     */
    private array $parsedArguments = [];
    private string $commandName;
    private bool $argumentsLoaded = false;

    /** @param string[]|null $argv */
    public function __construct(?array $argv = null)
    {
        /* @phpstan-ignore-next-line */
        $this->argv = $argv ?? (isset($_SERVER['argv']) && \is_array($_SERVER['argv']) ? $_SERVER['argv'] : []);
        $this->parseArguments();
    }

    private function parseArguments(): void
    {
        $arguments = $this->argv;

        // Remove script name
        array_shift($arguments);

        if (\count($arguments) === 0) {
            throw new Exception('No command provided.');
        }

        $this->commandName = $arguments[0];
        array_shift($arguments);
        $this->rawArguments = $arguments;
    }

    /**
     * @param Argument[] $arguments
     * @param Option[]   $options
     */
    public function loadArguments(array $arguments, array $options): void
    {
        // only load once
        if ($this->argumentsLoaded) {
            return;
        }
        $this->argumentsLoaded = true;

        foreach ($this->rawArguments as $rawArgument) {
            if (str_starts_with($rawArgument, '--')) {
                // it's an option
                $optionParts = explode('=', substr($rawArgument, 2), 2);
                $optionName = $optionParts[0];
                $optionValue = $optionParts[1] ?? true;

                // check if option is defined
                $found = false;
                foreach ($options as $option) {
                    if ($option->name === $optionName) {
                        $this->parsedArguments[$optionName] = $optionValue;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    throw new Exception("Unknown option '--{$optionName}'.");
                }
            } else {
                // it's a positional argument
                // find the next required or optional argument that hasn't been set yet
                $found = false;
                foreach ($arguments as $argument) {
                    if (!\array_key_exists($argument->name, $this->parsedArguments)) {
                        $this->parsedArguments[$argument->name] = $rawArgument;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    throw new Exception("Unexpected argument '{$rawArgument}'.");
                }
            }
        }

        // check for required arguments
        foreach ($arguments as $argument) {
            if ($argument->required && !\array_key_exists($argument->name, $this->parsedArguments)) {
                throw new Exception("Missing required argument '{$argument->name}'.");
            }
            // set default values for optional arguments if not provided
            if (!$argument->required && !\array_key_exists($argument->name, $this->parsedArguments)) {
                $this->parsedArguments[$argument->name] = $argument->default;
            }
        }
    }

    public function getArgument(string $name): mixed
    {
        if (\array_key_exists($name, $this->parsedArguments) === false) {
            throw new Exception("Argument '{$name}' not found.");
        }

        return $this->parsedArguments[$name];
    }

    public function getOption(string $name): mixed
    {
        if (\array_key_exists($name, $this->parsedArguments) === false) {
            return null;
        }

        return $this->parsedArguments[$name];
    }

    public function getCommandName(): string
    {
        return $this->commandName;
    }
}
