<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Fact;

use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact;
use Vjik\Scaffolder\Value\MinorPhpVersion;

/**
 * @extends Fact<list<MinorPhpVersion>>
 */
final class MinorPhpVersionRange extends Fact
{
    public static function resolve(Cli $cli, Context $context): mixed
    {
        return MinorPhpVersion::range(
            $context->getFact(LowestMinorPhpVersion::class),
            $context->getFact(HighestMinorPhpVersion::class),
        );
    }
}
