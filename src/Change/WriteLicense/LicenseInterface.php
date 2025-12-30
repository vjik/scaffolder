<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Change\WriteLicense;

use Vjik\Scaffolder\Context;

interface LicenseInterface
{
    public function render(Context $context): string;
}
