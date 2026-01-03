<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Fact;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputOption;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact;
use Vjik\Scaffolder\Params;
use Vjik\Scaffolder\Value\CopyrightYear as CopyrightYearValue;

/**
 * @extends Fact<CopyrightYearValue>
 */
final class CopyrightYear extends Fact
{
    private const string YEAR_VALUE_OPTION = 'copyright-year';
    private const string BEGIN_YEAR_VALUE_OPTION = 'copyright-year-begin';

    public static function configureCommand(SymfonyCommand $command, Params $params): void
    {
        $command->addOption(
            self::YEAR_VALUE_OPTION,
            mode: InputOption::VALUE_OPTIONAL,
            default: $params->get(self::YEAR_VALUE_OPTION),
        );
        $command->addOption(
            self::BEGIN_YEAR_VALUE_OPTION,
            mode: InputOption::VALUE_OPTIONAL,
            default: $params->get(self::BEGIN_YEAR_VALUE_OPTION),
        );
    }

    public static function resolve(Cli $cli, Context $context): mixed
    {
        /** @var string|null $year */
        $year = $cli->getOption(self::YEAR_VALUE_OPTION);
        if ($year !== null) {
            return CopyrightYearValue::fromString($year);
        }

        /** @var string|null $beginYear */
        $beginYear = $cli->getOption(self::BEGIN_YEAR_VALUE_OPTION);
        if ($beginYear === null) {
            return CopyrightYearValue::now();
        }

        return CopyrightYearValue::fromRange(
            (int) $beginYear,
            (int) date('Y'),
        );
    }
}
