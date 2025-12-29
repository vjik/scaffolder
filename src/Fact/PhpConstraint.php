<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Fact;

use Composer\Semver\Constraint\ConstraintInterface;
use Composer\Semver\VersionParser;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Throwable;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact;
use Vjik\Scaffolder\NormalizeUserInputException;

/**
 * @extends Fact<ConstraintInterface>
 */
final  class PhpConstraint extends Fact
{
    public const string OPTION_NAME = 'php-constraint-default';
    private const string DEFAULT = '^8.5';

    public static function configureCommand(Command $command, array $defaults): void
    {
        $command->addOption(
            self::OPTION_NAME,
            mode: InputOption::VALUE_REQUIRED,
            default: $defaults[self::OPTION_NAME] ?? self::DEFAULT,
        );
    }

    public static function resolve(Cli $cli, Context $context): ConstraintInterface
    {
        $composerJson = $context->getFact(ComposerJson::class);

        if (isset($composerJson['require']['php'])) {
            try {
                return self::normalize($composerJson['require']['php']);
            } catch (InvalidArgumentException) {
            }
        }

        /** @var string $default */
        $default = $cli->getOption(self::OPTION_NAME);

        return $cli->ask(
            question: 'PHP constraint',
            default: $default,
            normalizer: self::normalize(...),
        );
    }

    private static function normalize(string $constraint): ConstraintInterface
    {
        static $parser = new VersionParser();

        try {
            return $parser->parseConstraints($constraint);
        } catch (Throwable $exception) {
            throw new NormalizeUserInputException($exception->getMessage());
        }
    }
}

