<?php
declare(strict_types=1);

namespace AppDomain\Domain\User;

final class UserId
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        // tu możesz dodać walidację UUID itp.
        return new self($value);
    }

    public static function generate(): self
    {
        return new self(bin2hex(random_bytes(16))); // prosty uuid-like
    }

    public function toString(): string
    {
        return $this->value;
    }
}
