<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Fact;

use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact;

/**
 * @extends Fact<non-empty-string>
 */
final class PackageName extends Fact
{
    public static function resolve(Cli $cli, Context $context): mixed
    {
        $vendor = $context->getFact(PackageVendor::class);
        $project = $context->getFact(PackageProject::class);

        return $vendor . '/' . $project;
    }
}
