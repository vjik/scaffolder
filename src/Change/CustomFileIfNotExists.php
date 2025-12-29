<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Change;

use Closure;
use Stringable;
use Vjik\Scaffolder\Change;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;

use function is_callable;
use function sprintf;

final readonly class CustomFileIfNotExists implements Change
{
    /**
     * @param string|Stringable|(Closure(Context $context): string|Stringable) $callable
     */
    public function __construct(
        private string $file,
        private string|Stringable|Closure $content,
    ) {
    }

    public function decide(Context $context): ?callable
    {
        if ($context->fileExists($this->file)) {
            return null;
        }

        $content = is_callable($this->content)
            ? ($this->content)($context)
            : $this->content;

        return fn(Cli $cli) => $cli->step(
            sprintf('Write `%s`', $this->file),
            fn() => $context->writeFile($this->file, $content),
        );
    }
}
