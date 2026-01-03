<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Fact;

use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact;
use Vjik\Scaffolder\Value\PackageAuthor;

/**
 * @extends Fact<list<PackageAuthor>>
 */
final class PackageAuthors extends Fact
{
    public const string VALUE_OPTION = 'package-authors';

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
                static fn(array $author): PackageAuthor => PackageAuthor::fromArray($author),
                $composerJson['authors'],
            );
        }

        return [
            new PackageAuthor(
                name: $context->getFact(UserName::class),
                email: $context->getFact(UserEmail::class),
            )];
    }
}
