<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Value;

final readonly class PackageAuthor
{
    /**
     * @param non-empty-string|null $name
     * @param non-empty-string|null $email
     * @param non-empty-string|null $homepage
     * @param non-empty-string|null $role
     */
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $homepage = null,
        public ?string $role = null,
    ) {}

    /**
     * @param string[] $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: isset($data['name']) && $data['name'] !== '' ? $data['name'] : null,
            email: isset($data['email']) && $data['email'] !== '' ? $data['email'] : null,
            homepage: isset($data['homepage']) && $data['homepage'] !== '' ? $data['homepage'] : null,
            role: isset($data['role']) && $data['role'] !== '' ? $data['role'] : null,
        );
    }

    /**
     * @return array{
     *     name?: non-empty-string,
     *     email?: non-empty-string,
     *     homepage?: non-empty-string,
     *     role?: non-empty-string,
     * }
     */
    public function toArray(): array
    {
        return array_filter(
            [
                'name' => $this->name,
                'email' => $this->email,
                'homepage' => $this->homepage,
                'role' => $this->role,
            ],
            static fn($value): bool => $value !== null,
        );
    }
}
