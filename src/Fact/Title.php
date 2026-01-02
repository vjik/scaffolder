<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Fact;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputOption;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact;
use Vjik\Scaffolder\Params;

/**
 * @extends Fact<non-empty-string>
 */
final class Title extends Fact
{
    private const string VALUE_OPTION = 'title';

    public static function configureCommand(SymfonyCommand $command, Params $params): void
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

        $packageName = $context->getFact(PackageName::class);

        $title = preg_replace_callback(
            '~[-_./]+(\w)~',
            static fn(array $matches): string => ' ' . strtoupper($matches[1]),
            $packageName,
        );

        return ucfirst($title);
    }
}
