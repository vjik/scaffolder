<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Change;

use Closure;
use Stringable;
use Vjik\Scaffolder\Change;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;

use function sprintf;

final readonly class WriteFileIfNotExists implements Change
{
    /**
     * @param string|Stringable|(Closure(Context $context): (string|Stringable)) $content
     */
    public function __construct(
        private string $file,
        private string|Stringable|Closure $content,
    ) {}

    public function decide(Context $context): ?callable
    {
        if ($context->fileExists($this->file)) {
            return null;
        }

        $content = $this->content instanceof Closure
            ? ($this->content)($context)
            : $this->content;

        return fn(Cli $cli) => $cli->step(
            sprintf('Write `%s`', $this->file),
            fn() => $context->writeFile($this->file, $content),
        );
    }
}
