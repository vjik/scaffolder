<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Change;

use Closure;
use Vjik\Scaffolder\Change;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact\ComposerJson;
use Vjik\Scaffolder\Fact\NamespaceX;
use Vjik\Scaffolder\Fact\PackageAuthors;
use Vjik\Scaffolder\Fact\PackageDescription;
use Vjik\Scaffolder\Fact\PackageLicense;
use Vjik\Scaffolder\Fact\PackageName;
use Vjik\Scaffolder\Fact\PackageType;
use Vjik\Scaffolder\Fact\PhpConstraint;
use Vjik\Scaffolder\Fact\PhpConstraintName;
use Vjik\Scaffolder\Fact\PrepareComposerAutoload;
use Vjik\Scaffolder\Fact\PrepareComposerAutoloadDev;
use Vjik\Scaffolder\Fact\SourceDirectory;
use Vjik\Scaffolder\Fact\TestsDirectory;
use Vjik\Scaffolder\Value\PackageAuthor;

use function dirname;

/**
 * @phpstan-type BumpAfterUpdateLogicClosure = Closure("dev"|"no-dev"|bool|null, Context): ("dev"|"no-dev"|bool|null)
 * @phpstan-type CustomChangeClosure = Closure(ComposerJsonArray, Context): array
 * @phpstan-import-type ComposerJsonArray from ComposerJson
 */
final readonly class PrepareComposerJson implements Change
{
    /**
     * @var BumpAfterUpdateLogicClosure|false
     */
    private Closure|false $bumpAfterUpdateLogic;

    /**
     * @param BumpAfterUpdateLogicClosure|false|null $bumpAfterUpdateLogic
     * @param CustomChangeClosure|null $customChange
     */
    public function __construct(
        Closure|false|null $bumpAfterUpdateLogic = null,
        private ?Closure $customChange = null,
    ) {
        $this->bumpAfterUpdateLogic = $bumpAfterUpdateLogic ?? $this->bumpAfterUpdateLogic(...);
    }

    public function decide(Context $context): ?array
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
        $new['require'][$context->getFact(PhpConstraintName::class)] ??= $context->getFact(PhpConstraint::class)->getPrettyString();
        $new['config']['sort-packages'] = true;
        $this->prepareAutoload($new, $context);
        $this->prepareBumpAfterUpdate($new, $context);

        if ($this->customChange !== null) {
            $new = ($this->customChange)($new, $context); // @phpstan-ignore argument.type
        }

        if ($new === $original) {
            return null;
        }

        $content = json_encode($new, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return [
            static fn(Cli $cli) => $cli->step(
                'Write `composer.json`',
                static fn() => $context->writeTextFile('composer.json', $content),
            ),
            static fn(Cli $cli) => $cli->step(
                'Normalize `composer.json`',
                static fn() => $context->execute(
                    dirname(__DIR__, 2) . '/composer-normalize.phar' . ' --no-check-lock --no-update-lock',
                ),
            ),
        ];
    }

    private function prepareAutoload(array &$composerJson, Context $context): void
    {
        /** @var ComposerJsonArray $composerJson */

        $isSrcNeeded = $context->getFact(PrepareComposerAutoload::class)
            && !isset($composerJson['autoload']['psr-4']);
        $isTestsNeeded = $context->getFact(PrepareComposerAutoloadDev::class)
            && !isset($composerJson['autoload-dev']['psr-4']);

        if ($isSrcNeeded || $isTestsNeeded) {
            $namespace = $context->getFact(NamespaceX::class);
            if ($isSrcNeeded) {
                $sourceDirectory = $context->getFact(SourceDirectory::class);
                $composerJson['autoload']['psr-4'][$namespace . '\\'] = $sourceDirectory;
            }
            if ($isTestsNeeded) {
                $testsDirectory = $context->getFact(TestsDirectory::class);
                $composerJson['autoload-dev']['psr-4'][$namespace . '\\Tests\\'] = $testsDirectory;
            }
        }
    }

    private function prepareBumpAfterUpdate(array &$composerJson, Context $context): void
    {
        if ($this->bumpAfterUpdateLogic === false) {
            return;
        }

        /** @var ComposerJsonArray $composerJson */

        $currentValue = $composerJson['config']['bump-after-update'] ?? null;
        $newValue = ($this->bumpAfterUpdateLogic)($currentValue, $context);

        if ($newValue === null) {
            unset($composerJson['config']['bump-after-update']);
            return;
        }

        $composerJson['config']['bump-after-update'] = $newValue;
    }

    /**
     * @param "dev"|"no-dev"|bool|null $currentValue
     * @return "dev"|"no-dev"|bool|null
     */
    private function bumpAfterUpdateLogic(string|bool|null $currentValue, Context $context): string|bool|null
    {
        if ($currentValue !== null) {
            return $currentValue;
        }
        return match ($context->getFact(PackageType::class)) {
            'library' => null,
            'project' => true,
            default => $currentValue,
        };
    }
}
