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
final class PackageProject extends Fact
{
    private const string VALUE_OPTION = 'package-project';
    private const string DEFAULT_OPTION = 'package-project-default';

    public static function configureCommand(SymfonyCommand $command, Params $params): void
    {
        $command->addOption(
            self::VALUE_OPTION,
            mode: InputOption::VALUE_OPTIONAL,
            default: $params->get(self::VALUE_OPTION),
        );
        $command->addOption(
            self::DEFAULT_OPTION,
            mode: InputOption::VALUE_OPTIONAL,
            default: $params->get(self::DEFAULT_OPTION),
        );
    }

    public static function resolve(Cli $cli, Context $context): mixed
    {
        /** @var string|null $value */
        $value = $cli->getOption(self::VALUE_OPTION);
        if ($value !== null && $value !== '') {
            return self::normalize($value);
        }

        $composerJson = $context->getFact(ComposerJson::class); // @phpstan-ignore argument.type
        if (isset($composerJson['name']) && str_contains($composerJson['name'], '/')) {
            $project = explode('/', $composerJson['name'], 2)[1];
            try {
                return self::normalize($project);
            } catch (NormalizeInputException) {
            }
        }

        /** @var string|null $default */
        $default = $cli->getOption(self::DEFAULT_OPTION);

        return $cli->ask(
            question: 'Package project',
            default: $default,
            normalizer: self::normalize(...),
        );
    }

    /**
     * @return non-empty-string
     * @throws NormalizeInputException
     */
    private static function normalize(string $input): string
    {
        $value = trim($input);
        if ($value === '') {
            throw new NormalizeInputException('Package project must not be empty.');
        }
        if (preg_match('/^[a-z0-9](([_.]|-{1,2})?[a-z0-9]+)*$/', $value) !== 1) {
            throw new NormalizeInputException(
                'Package project must contain only lowercase letters, digits, and separators (_, ., -). It must start with a letter or digit, single dashes can be doubled.',
            );
        }
        return $value;
    }
}
