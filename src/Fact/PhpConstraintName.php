<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Fact;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact;
use Vjik\Scaffolder\Params;

/**
 * @extends Fact<non-empty-string>
 */
final class PhpConstraintName extends Fact
{
    public const string VALUE_OPTION = 'php-constraint-name';

    public static function configureCommand(Command $command, Params $params): void
    {
        $command->addOption(
            self::VALUE_OPTION,
            mode: InputOption::VALUE_OPTIONAL,
            default: $params->get(self::VALUE_OPTION),
        );
    }

    public static function resolve(Cli $cli, Context $context): mixed
    {
        /** @var string|null $value */
        $value = $cli->getOption(self::VALUE_OPTION);
        if ($value !== null && $value !== '') {
            return $value;
        }

        $composerJson = $context->getFact(ComposerJson::class); // @phpstan-ignore argument.type
        if (isset($composerJson['require']['php-64bit'])) {
            return 'php-64bit';
        }

        return 'php';
    }
}
