<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Change;

use Vjik\Scaffolder\Change;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact;

use function sprintf;

final readonly class EnsureDirectoryWithGitkeep implements Change
{
    /**
     * @param string|class-string<Fact<string>> $directory
     */
    public function __construct(
        private string $directory,
    ) {}

    public function decide(Context $context): ?callable
    {
        /** @var string $directory */
        $directory = is_a($this->directory, Fact::class, true)
            ? $context->getFact($this->directory)
            : $this->directory;

        if ($context->fileExists($directory)
            && (
                !$context->isDirectory($directory)
                || !$context->isDirectoryEmpty($directory)
            )
        ) {
            return null;
        }

        return static fn(Cli $cli) => $cli->step(
            sprintf('Create `.gitkeep` in `%s`', $directory),
            static fn() => $context->writeTextFile($directory . '/.gitkeep'),
        );
    }
}
