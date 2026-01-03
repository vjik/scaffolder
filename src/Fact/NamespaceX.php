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
use Yiisoft\Strings\Inflector;

/**
 * @extends Fact<non-empty-string>
 */
final class NamespaceX extends Fact
{
    private const string VALUE_OPTION = 'namespace';
    private const string REGEX = '/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*+(?>\\\[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*+)++$/';

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
            return self::normalize($value);
        }

        $composerJson = $context->getFact(ComposerJson::class); // @phpstan-ignore argument.type
        $namespace = array_key_first($composerJson['autoload']['psr-4'] ?? []);
        if ($namespace !== null) {
            try {
                return self::normalize(rtrim($namespace, '\\'));
            } catch (NormalizeInputException) {
            }
        }

        return $cli->ask(
            question: 'Namespace',
            default: self::createDefault($context),
            normalizer: self::normalize(...),
        );
    }

    private static function createDefault(Context $context): string
    {
        $inflector = new Inflector();
        $vendor = $context->getFact(PackageVendor::class);
        $project = $context->getFact(PackageProject::class);
        return $inflector->toPascalCase($vendor) . '\\' . $inflector->toPascalCase($project);
    }

    /**
     * @return non-empty-string
     * @throws NormalizeInputException
     */
    private static function normalize(string $input): string
    {
        $namespace = trim($input);
        if ($namespace === '') {
            throw new NormalizeInputException('Namespace must not be empty.');
        }
        if (preg_match(self::REGEX, $namespace) !== 1) {
            throw new NormalizeInputException('Invalid namespace format.');
        }
        return $namespace;
    }
}
