<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Change;

use Vjik\Scaffolder\Change;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact\ComposerJson;
use Vjik\Scaffolder\Fact\PackageName;

final readonly class PrepareComposerJson implements Change
{
    public function __construct(
        private ?string $type = null,
    ) {}

    public function decide(Context $context): callable|array|null
    {
        $new = $original = $context->getFact(ComposerJson::class); // @phpstan-ignore argument.type

        $new['name'] = $context->getFact(PackageName::class);
        if ($this->type !== null) {
            $new['type'] = $this->type;
        }

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
