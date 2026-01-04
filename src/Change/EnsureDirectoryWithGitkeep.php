<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Change;

use Vjik\Scaffolder\Change;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;

use function sprintf;

final readonly class EnsureDirectoryWithGitkeep implements Change
{
    public function __construct(
        private string $directory,
    ) {}

    public function decide(Context $context): ?callable
    {
        if ($context->fileExists($this->directory)
            && (
                !$context->isDirectory($this->directory)
                || !$context->isDirectoryEmpty($this->directory)
            )
        ) {
            return null;
        }

        return fn(Cli $cli) => $cli->step(
            sprintf('Create `.gitkeep` in `%s`', $this->directory),
            fn() => $context->writeFile($this->directory . '/.gitkeep'),
        );
    }
}
