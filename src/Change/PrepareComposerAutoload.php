<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Change;

use Vjik\Scaffolder\Applier\WriteComposerJson;
use Vjik\Scaffolder\Change;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact\ComposerJson;
use Vjik\Scaffolder\Fact\NamespaceX;
use Vjik\Scaffolder\Fact\SourceDirectory;
use Vjik\Scaffolder\Fact\TestsDirectory;

final readonly class PrepareComposerAutoload implements Change
{
    public function decide(Context $context): callable|array|null
    {
        $new = $original = $context->getFact(ComposerJson::class); // @phpstan-ignore argument.type

        $isSrcNeeded = !isset($new['autoload']['psr-4']);
        $isTestsNeeded = !isset($new['autoload-dev']['psr-4']);

        if ($isSrcNeeded || $isTestsNeeded) {
            $namespace = $context->getFact(NamespaceX::class);
            if ($isSrcNeeded) {
                $sourceDirectory = $context->getFact(SourceDirectory::class);
                $new['autoload']['psr-4'][$namespace . '\\'] = $sourceDirectory;
            }
            if ($isTestsNeeded) {
                $testsDirectory = $context->getFact(TestsDirectory::class);
                $new['autoload-dev']['psr-4'][$namespace . '\\Tests\\'] = $testsDirectory;
            }
        }

        if ($new === $original) {
            return null;
        }

        return static fn(Cli $cli) => $cli->step(
            'Prepare autoload in `composer.json`',
            new WriteComposerJson($new, $context),
        );
    }
}
