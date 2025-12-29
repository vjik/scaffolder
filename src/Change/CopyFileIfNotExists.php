<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Change;

use Vjik\Scaffolder\Change;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;

use function sprintf;

final readonly class CopyFileIfNotExists implements Change
{
    public function __construct(
        private string $origin,
        private string $target,
    ) {
    }

    public function decide(Context $context): ?callable
    {
        if ($context->fileExists($this->target)) {
            return null;
        }

        return fn(Cli $cli) => $cli->step(
            sprintf('Write `%s`', $this->target),
            fn() => $context->copyFile($this->origin, $this->target),
        );
    }
}
