<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Change;

use Vjik\Scaffolder\Change;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact\ComposerJson;
use Vjik\Scaffolder\Fact\PackageName;
use Vjik\Scaffolder\Fact\PackageType;

final readonly class PrepareComposerJson implements Change
{
    public function decide(Context $context): callable|array|null
    {
        $new = $original = $context->getFact(ComposerJson::class); // @phpstan-ignore argument.type

        $new['name'] = $context->getFact(PackageName::class);
        $new['type'] = $context->getFact(PackageType::class);

        if ($new === $original) {
            return null;
        }

        $content = json_encode($new, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return static fn(Cli $cli) => $cli->step(
            'Write `composer.json`',
            fn() => $context->writeTextFile('composer.json', $content),
        );
    }
}
