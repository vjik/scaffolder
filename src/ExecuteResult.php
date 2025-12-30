<?php

declare(strict_types=1);

namespace Vjik\Scaffolder;

use RuntimeException;

use function sprintf;

final readonly class ExecuteResult
{
    /**
     * @param list<string> $lines
     */
    public function __construct(
        public string $command,
        public array $lines,
        public int $code,
    ) {
    }

    public function isSuccess(): bool
    {
        return $this->code === 0;
    }

    public function throwRuntimeException(): never
    {
        throw new RuntimeException(
            sprintf(
                "Command execution failed (code %d)\n$ %s\n%s",
                $this->code,
                $this->command,
                implode("\n", $this->lines),
            )
        );
    }
}
