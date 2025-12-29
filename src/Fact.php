<?php

declare(strict_types=1);

namespace Vjik\Scaffolder;

use Symfony\Component\Console\Command\Command as SymfonyCommand;

/**
 * @template T
 */
abstract class Fact
{
    /**
     * @return T
     */
    abstract public static function resolve(Cli $cli, Context $context): mixed;

    public static function configureCommand(SymfonyCommand $command, Params $params): void
    {
        // do nothing by default
    }
}
