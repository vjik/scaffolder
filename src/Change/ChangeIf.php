<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Change;

use Vjik\Scaffolder\Change;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact;

final readonly class ChangeIf implements Change
{
    /**
     * @param class-string<Fact<bool>> $fact
     */
    public function __construct(
        private Change $change,
        private string $fact,
    ) {}

    public function decide(Context $context): callable|array|null
    {
        return $context->getFact($this->fact)
            ? $this->change->decide($context)
            : null;
    }
}
