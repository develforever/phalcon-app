<?php
namespace AppDomain\Domain\User;

final class Email
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
