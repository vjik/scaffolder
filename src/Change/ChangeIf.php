<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Change;

use Vjik\Scaffolder\Change;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact;

use function is_array;
use function is_callable;

final readonly class ChangeIf implements Change
{
    /**
     * @var list<Change>
     */
    private array $changes;

    /**
     * @param Change|list<Change> $change
     * @param class-string<Fact<bool>> $fact
     */
    public function __construct(
        Change|array $change,
        private string $fact,
    ) {
        $this->changes = is_array($change) ? $change : [$change];
    }

    public function decide(Context $context): callable|array|null
    {
        if (!$context->getFact($this->fact)) {
            return null;
        }

        $appliers = [];
        foreach ($this->changes as $change) {
            $applier = $change->decide($context);
            if ($applier === null) {
                continue;
            }
            if (is_callable($applier)) {
                $appliers[] = $applier;
            } else {
                $appliers = array_merge($appliers, $applier);
            }
        }

        return $appliers === [] ? null : $appliers;
    }
}
