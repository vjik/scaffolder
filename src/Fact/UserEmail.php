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
final class UserEmail extends Fact
{
    private const string VALUE_OPTION = 'user-email';
    private const string SUGGESTION_OPTION = 'user-email-suggestion';

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
            question: 'Your email',
            default: $suggestion,
            normalizer: self::normalize(...),
        );
    }

    /**
     * @return non-empty-string
     * @throws NormalizeInputException
     */
    private static function normalize(string $input): string
    {
        $input = trim($input);
        if ($input === '') {
            throw new NormalizeInputException('Email must not be empty.');
        }
        if (filter_var($input, FILTER_VALIDATE_EMAIL) === false) {
            throw new NormalizeInputException('Invalid email format.');
        }
        return $input;
    }
}
