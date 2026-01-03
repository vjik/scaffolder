<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Change;

use Vjik\Scaffolder\Applier\WriteComposerJson;
use Vjik\Scaffolder\Change;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact\ComposerJson;
use Vjik\Scaffolder\Fact\PackageAuthors;
use Vjik\Scaffolder\Fact\PackageDescription;
use Vjik\Scaffolder\Fact\PackageLicense;
use Vjik\Scaffolder\Fact\PackageName;
use Vjik\Scaffolder\Fact\PackageType;
use Vjik\Scaffolder\Fact\PhpConstraint;
use Vjik\Scaffolder\Value\PackageAuthor;

final readonly class PrepareComposerJson implements Change
{
    public function decide(Context $context): callable|array|null
    {
        $new = $original = $context->getFact(ComposerJson::class); // @phpstan-ignore argument.type

        $new['name'] = $context->getFact(PackageName::class);
        $new['type'] = $context->getFact(PackageType::class);
        $new['description'] = $context->getFact(PackageDescription::class);
        $new['license'] = $context->getFact(PackageLicense::class);
        $new['authors'] = array_map(
            static fn(PackageAuthor $author) => $author->toArray(),
            $context->getFact(PackageAuthors::class),
        );
        $new['require']['php'] ??= $context->getFact(PhpConstraint::class)->getPrettyString();
        $new['config']['sort-packages'] = true;

        if ($new === $original) {
            return null;
        }

        return static fn(Cli $cli) => $cli->step(
            'Write `composer.json`',
            new WriteComposerJson($new, $context),
        );
    }
}
