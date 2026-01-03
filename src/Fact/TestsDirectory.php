<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Fact;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputOption;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact;
use Vjik\Scaffolder\NormalizeInputException;
use Vjik\Scaffolder\Params;

/**
 * @extends Fact<non-empty-string>
 */
final class TestsDirectory extends Fact
{
    public const string VALUE_OPTION = 'tests-directory';
    public const string SUGGESTION_OPTION = 'tests-directory-suggestion';

    public static function configureCommand(SymfonyCommand $command, Params $params): void
    {
        $command->addOption(
            self::VALUE_OPTION,
            mode: InputOption::VALUE_OPTIONAL,
            default: $params->get(self::VALUE_OPTION),
        );
        $command->addOption(
            self::SUGGESTION_OPTION,
            mode: InputOption::VALUE_OPTIONAL,
            default: $params->get(self::SUGGESTION_OPTION),
        );
    }

    public static function resolve(Cli $cli, Context $context): mixed
    {
        /** @var string|null $value */
        $value = $cli->getOption(self::VALUE_OPTION);
        if ($value !== null && $value !== '') {
            return self::normalize($value);
        }

        /** @var string|null $suggestion */
        $suggestion = $cli->getOption(self::SUGGESTION_OPTION);

        return $cli->ask(
            question: 'Tests directory',
            default: $suggestion ?? 'tests/',
            normalizer: self::normalize(...),
        );
    }

    /**
     * @return non-empty-string
     * @throws NormalizeInputException
     */
    private static function normalize(string $input): string
    {
        $directory = trim($input);
        if ($directory === '') {
            throw new NormalizeInputException('Tests directory must not be empty.');
        }
        if (!str_ends_with($directory, '/')) {
            $directory .= '/';
        }
        return $directory;
    }
}
