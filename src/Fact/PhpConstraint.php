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
final class PhpConstraint extends Fact
{
    public const string SUGGESTION_OPTION = 'php-constraint-suggestion';
    private const string SUGGESTION = '^8.5';

    public static function configureCommand(Command $command, array $defaults): void
    {
        $command->addOption(
            self::SUGGESTION_OPTION,
            mode: InputOption::VALUE_REQUIRED,
            default: $defaults[self::SUGGESTION_OPTION] ?? self::SUGGESTION,
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

        /** @var string $suggestion */
        $suggestion = $cli->getOption(self::SUGGESTION_OPTION);

        return $cli->ask(
            question: 'PHP constraint',
            default: $suggestion,
            normalizer: self::normalize(...),
        );
    }

    /**
     * @throws NormalizeUserInputException
     */
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

