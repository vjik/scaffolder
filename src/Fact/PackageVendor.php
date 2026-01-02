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
final class PackageVendor extends Fact
{
    private const string VALUE_OPTION = 'package-vendor';
    private const string DEFAULT_OPTION = 'package-vendor-default';

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
            return $value;
        }

        $composerJson = $context->getFact(ComposerJson::class); // @phpstan-ignore argument.type
        if (isset($composerJson['name']) && str_contains($composerJson['name'], '/')) {
            $vendor = explode('/', $composerJson['name'], 2)[0];
            try {
                return self::normalize($vendor);
            } catch (NormalizeInputException) {
            }
        }

        /** @var string|null $default */
        $default = $cli->getOption(self::DEFAULT_OPTION);

        return $cli->ask(
            question: 'Package vendor',
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
            throw new NormalizeInputException('Package vendor must not be empty.');
        }
        if (preg_match('/^[a-z0-9]([_.-]?[a-z0-9]+)*$/', $value) !== 1) {
            throw new NormalizeInputException(
                'Package vendor must contain only lowercase letters, digits, and separators (_, ., -). It must start with a letter or digit, and separators cannot be consecutive.',
            );
        }
        return $value;
    }
}
