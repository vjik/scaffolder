<?php

declare(strict_types=1);

namespace Vjik\Scaffolder;

/**
 * @phpstan-type ApplierCallable = callable(Cli): void
 */
interface Change
{
    /**
     * @return ApplierCallable|list<ApplierCallable>|null
     */
    public function decide(Context $context): callable|array|null;
}
