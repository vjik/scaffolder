<?php

declare(strict_types=1);

namespace Vjik\Scaffolder;

interface Change
{
    /**
     * @return (callable(Cli): void)|null
     */
    public function decide(Context $context): ?callable;
}
