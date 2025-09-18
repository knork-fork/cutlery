<?php
declare(strict_types=1);

namespace Cutlery\Command;

use Cutlery\Support\RootLocator;
use ReflectionClass;
use Throwable;

abstract class Command
{
    public const SUCCESS = 0;
    public const FAILURE = 1;

    /**
     * @var Argument[]
     */
    private array $arguments = [];
    /**
     * @var Option[]
     */
    private array $options = [];

    abstract protected function configure(): void;

    abstract protected function execute(): int;

    public function __construct(protected Input $input, protected Output $output)
    {
    }

    public static function executeCommand(): int
    {
        // output and input should be autowired by container inited in kernel...
        $output = new Output();
        try {
            $input = new Input();
        } catch (Throwable $e) {
            $output->writeLine('Error: ' . $e->getMessage());

            return self::FAILURE;
        }

        $callName = $input->getCommandName();
        $class = self::getCommandClassByName($callName);
        if ($class === false) {
            $output->writeLine("Command '{$callName}' not found.");

            return self::FAILURE;
        }

        /** @var Command $command */
        $command = new $class($input, $output);
        $command->configure();
        try {
            $input->loadArguments($command->arguments, $command->options);
        } catch (Throwable $e) {
            $output->writeLine('Error: ' . $e->getMessage());

            return self::FAILURE;
        }

        try {
            return $command->execute();
        } catch (Throwable $e) {
            $output->writeLine('Error: ' . $e->getMessage());

            return self::FAILURE;
        }
    }

    public static function getName(): ?string
    {
        if ($attribute = new ReflectionClass(static::class)->getAttributes(CommandDefinition::class)) {
            return $attribute[0]->newInstance()->name;
        }

        return null;
    }

    // Todo: add help command and then use this to get description
    public static function getDescription(): ?string
    {
        if ($attribute = new ReflectionClass(static::class)->getAttributes(CommandDefinition::class)) {
            return $attribute[0]->newInstance()->description;
        }

        return null;
    }

    protected function addArgument(string $name, bool $required = false, string $description = '', mixed $default = null): static
    {
        $this->arguments[] = new Argument($name, $required, $description, $default);

        return $this;
    }

    protected function addOption(string $name, string $description = '', mixed $default = null): static
    {
        $this->options[] = new Option($name, $description, $default);

        return $this;
    }

    private static function getCommandClassByName(string $name): string|false
    {
        $root = RootLocator::getProjectRoot();

        // todo: Commands should be registered in kernel, not picked manually from src/Command
        // maybe automatically assume App namespace (composer should define it as src/) if "app:" prefix is used?
        // todo2: add some way to include system migrations, or stick to assuming it out from prefix?
        $commandDir = $root . '/src/Command';
        /** @var string[] $files */
        $files = scandir($commandDir) ?: [];
        foreach ($files as $file) {
            if (!str_ends_with($file, 'Command.php')) {
                continue;
            }

            $class = 'App\Command\\' . str_replace('.php', '', $file);

            if (!is_subclass_of($class, self::class)) {
                continue;
            }

            if ($class::getName() === $name) {
                return $class;
            }
        }

        return false;
    }
}
