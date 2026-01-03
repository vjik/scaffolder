<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Applier;

use Vjik\Scaffolder\Context;

final readonly class WriteComposerJson
{
    public function __construct(
        private array $composerJson,
        private Context $context,
    ) {}

    public function __invoke(): void
    {
        $content = json_encode($this->composerJson, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $this->context->writeTextFile('composer.json', $content);
    }
}
