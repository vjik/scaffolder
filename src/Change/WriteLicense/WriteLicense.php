<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Change\WriteLicense;

use Vjik\Scaffolder\Change;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;

use function sprintf;

final readonly class WriteLicense implements Change
{
    public function __construct(
        private LicenseInterface $license,
        private string $file = 'LICENSE',
    ) {}

    public function decide(Context $context): ?callable
    {
        $text = $this->license->render($context);
        if ($context->tryReadFile($this->file) === $text) {
            return null;
        }

        return fn(Cli $cli) => $cli->step(
            sprintf('Write `%s`', $this->file),
            fn() => $context->writeFile($this->file, $text),
        );
    }
}
