<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Fact;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputOption;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact;

/**
 * @extends Fact<CopyrightYearValue>
 */
final class CopyrightYear extends Fact
{
    private const string YEAR_VALUE_OPTION = 'copyright-year';
    private const string BEGIN_YEAR_VALUE_OPTION = 'copyright-year-begin';

    public static function configureCommand(SymfonyCommand $command, array $defaults): void
    {
        $command->addOption(
            self::YEAR_VALUE_OPTION,
            mode: InputOption::VALUE_OPTIONAL,
            default: $defaults[self::YEAR_VALUE_OPTION] ?? null,
        );
        $command->addOption(
            self::BEGIN_YEAR_VALUE_OPTION,
            mode: InputOption::VALUE_OPTIONAL,
            default: $defaults[self::BEGIN_YEAR_VALUE_OPTION] ?? date('Y'),
        );
    }

    public static function resolve(Cli $cli, Context $context): mixed
    {
        $year = $cli->getOption(self::YEAR_VALUE_OPTION);
        if ($year !== null) {
            return CopyrightYearValue::fromString($year);
        }

        return CopyrightYearValue::fromRange(
            (int) $cli->getOption(self::BEGIN_YEAR_VALUE_OPTION),
            (int) date('Y'),
        );
    }
}
