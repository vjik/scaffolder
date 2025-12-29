<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Fact;

use Composer\Semver\Constraint\Constraint;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact;
use Vjik\Scaffolder\Value\MinorPhpVersion;

/**
 * @extends Fact<MinorPhpVersion>
 */
final class LowestMinorPhpVersion extends Fact
{
    public static function resolve(Cli $cli, Context $context): mixed
    {
        $constraint = $context->getFact(PhpConstraint::class);

        $result = array_find_key(
            [
                '7.4' => '7.4.9999999',
                '8.0' => '8.0.9999999',
                '8.1' => '8.1.9999999',
                '8.2' => '8.2.9999999',
                '8.3' => '8.3.9999999',
                '8.4' => '8.4.9999999',
                '8.5' => '8.5.9999999',
            ],
            static fn($version) => $constraint->matches(new Constraint('==', $version)),
        );

        return $result === null ? MinorPhpVersion::UNKNOWN : MinorPhpVersion::from($result);
    }
}
