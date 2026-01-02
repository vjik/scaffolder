<?php

declare(strict_types=1);

namespace Vjik\Scaffolder;

use function array_key_exists;

final readonly class Params
{
    /**
     * @param array<string, mixed> $params
     */
    public function __construct(
        private array $params,
    ) {
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->params);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }
}
