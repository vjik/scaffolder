<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Fact;

use Closure;

final readonly class CopyrightYearValue
{
    /**
     * @param Closure(string): string $renderer
     */
    private function __construct(
        private Closure $renderer
    ) {
    }

    public static function fromString(string $value): self
    {
        return new self(fn() => $value);
    }

    public static function fromRange(int $begin, ?int $end = null): self
    {
        $end ??= (int) date('Y');
        return new self(
            fn(string $separator) => $begin === $end
                ? (string) $begin
                : ($begin . $separator . $end),
        );
    }

    public static function now(): self
    {
        $year = date('Y');
        return new self(fn() => $year);
    }

    public function renderAscii(): string
    {
        return ($this->renderer)('-');
    }

    public function renderHuman(): string
    {
        return ($this->renderer)('—');
    }
}
