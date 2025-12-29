<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Change;

use Vjik\Scaffolder\Change;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact;

final readonly class EnsureFact implements Change
{
    /**
     * @var list<class-string<Fact<mixed>>>
     */
    private array $facts;

    /**
     * @param class-string<Fact<mixed>> ...$fact
     * @no-named-arguments
     */
    public function __construct(string ...$fact)
    {
        $this->facts = $fact;
    }

    public function decide(Context $context): null
    {
        foreach ($this->facts as $fact) {
            $context->getFact($fact);
        }
        return null;
    }
}
