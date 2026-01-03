<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Fact;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputOption;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact;
use Vjik\Scaffolder\Params;

use function is_bool;

/**
 * @extends Fact<bool>
 */
final class PrepareComposerAutoload extends Fact
{
    public const string VALUE_OPTION = 'prepare-composer-autoload';

    public static function configureCommand(SymfonyCommand $command, Params $params): void
    {
        $command->addOption(
            self::VALUE_OPTION,
            mode: InputOption::VALUE_OPTIONAL,
            default: $params->get(self::VALUE_OPTION, true),
        );
    }

    public static function resolve(Cli $cli, Context $context): mixed
    {
        /** @var string|bool|null $value */
        $value = $cli->getOption(self::VALUE_OPTION);

        if (is_bool($value)) {
            return $value;
        }

        if ($value === null || $value === '') {
            return true;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
