<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Change;

use Vjik\Scaffolder\Change;
use Vjik\Scaffolder\Cli;
use Vjik\Scaffolder\Context;

use function dirname;

final readonly class NormalizeComposerJson implements Change
{
    public function decide(Context $context): callable|array|null
    {
        $phar = dirname(__DIR__, 2) . '/composer-normalize.phar';

        $executeResult = $context->execute(
            $phar . ' --dry-run --no-check-lock 2>&1',
            throwExceptionOnError: false,
        );
        if ($executeResult->isSuccess()) {
            return null;
        }
        if ($executeResult->code !== 1) {
            $executeResult->throwRuntimeException();
        }

        return static fn(Cli $cli) => $cli->step(
            'Normalize `composer.json`',
            static fn() => $context->execute($phar . ' --no-check-lock --no-update-lock'),
        );
    }
}
