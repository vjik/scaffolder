<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Fact;

use InvalidArgumentException;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact;
use Vjik\Scaffolder\Value\PackageAuthor;

use function array_map;
use function get_debug_type;
use function implode;
use function is_string;
use function sprintf;

/**
 * @extends Fact<list<PackageAuthor>>
 */
final class PackageAuthors extends Fact
{
    public const string VALUE_OPTION = 'package-authors';
    public const string DEFAULT_SOURCE_OPTION = 'package-authors-default-source';

    public static function resolve(Cli $cli, Context $context): mixed
    {
        /** @var list<PackageAuthor>|null $authors */
        $authors = $context->getParam(self::VALUE_OPTION);
        if ($authors !== null) {
            return $authors;
        }

        $composerJson = $context->getFact(ComposerJson::class); // @phpstan-ignore argument.type
        if (isset($composerJson['authors'])) {
            return array_map(
                PackageAuthor::fromArray(...),
                $composerJson['authors'],
            );
        }

        return match (self::resolveDefaultSource($context)) {
            PackageAuthorsDefaultSource::User => [
                new PackageAuthor(
                    name: $context->getFact(UserName::class),
                    email: $context->getFact(UserEmail::class),
                ),
            ],
            PackageAuthorsDefaultSource::EmptyList => [],
        };
    }

    private static function resolveDefaultSource(Context $context): PackageAuthorsDefaultSource
    {
        $source = $context->getParam(self::DEFAULT_SOURCE_OPTION, PackageAuthorsDefaultSource::User);

        if ($source instanceof PackageAuthorsDefaultSource) {
            return $source;
        }

        if (is_string($source)) {
            $result = PackageAuthorsDefaultSource::tryFrom($source);
            if ($result === null) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid value "%s" for param "%s". Allowed values: "%s".',
                        $source,
                        self::DEFAULT_SOURCE_OPTION,
                        implode('", "', array_map(
                            static fn(PackageAuthorsDefaultSource $case): string => $case->value,
                            PackageAuthorsDefaultSource::cases(),
                        )),
                    ),
                );
            }
            return $result;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Param "%s" must be a string or %s, %s given.',
                self::DEFAULT_SOURCE_OPTION,
                PackageAuthorsDefaultSource::class,
                get_debug_type($source),
            ),
        );
    }
}
